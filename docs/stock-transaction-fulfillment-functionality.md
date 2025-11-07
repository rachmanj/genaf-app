# Stock Transaction & Fulfillment Functionality

## Overview

The Office Supplies module has two key components for managing stock movements:

1. **Stock Transaction** - Manual recording of stock movements (incoming/outgoing)
2. **Fulfillment** - Automated fulfillment of approved supply requests with stock distribution

---

## 1. Stock Transaction (`/supplies/transactions`)

### Purpose
Records **manual stock movements** that are not tied to supply requests. Used for:
- Stock received from suppliers/purchases
- Stock adjustments (increases/decreases)
- Manual stock corrections
- Stock movements not related to requests

### Key Features

#### Transaction Types

**1. Incoming (IN)**
- Stock received from external sources
- Increases supply `current_stock`
- Source tracking: `SAP` or `Manual`
- Can include supplier information, PO numbers

**2. Outgoing (OUT)**
- Stock dispensed/issued out
- Decreases supply `current_stock`
- Validation: Prevents negative stock
- Can be linked to a department

#### Fields

```
- supply_id: Which supply item
- type: 'in' or 'out'
- source: 'SAP' or 'manual'
- supplier_name: For incoming transactions
- purchase_order_no: PO reference
- department_id: Target department (for outgoing)
- quantity: Amount of stock
- reference_no: Internal reference number
- transaction_date: When transaction occurred
- notes: Additional information
- user_id: Who recorded the transaction
```

#### Workflow

1. **Create Transaction**
   - Navigate to `Supplies → Transactions → Create Transaction`
   - Select supply item
   - Choose type (IN/OUT)
   - Fill in quantity and details
   - For OUT: System validates stock availability
   - Save transaction

2. **Automatic Stock Update**
   ```
   IN: current_stock = current_stock + quantity
   OUT: current_stock = current_stock - quantity (if available)
   ```

3. **Validation**
   - OUT transactions check: `current_stock >= quantity`
   - Error if insufficient stock: "Insufficient stock. Available: X unit"

4. **View History**
   - DataTable with all transactions
   - Filter by type, supply, department, date range
   - Color-coded badges (green for IN, red for OUT)

5. **Delete Transaction** (if permission granted)
   - Automatically reverses stock adjustment
   - IN deleted → stock decreases
   - OUT deleted → stock increases

#### Use Cases

**Incoming Transaction:**
- New purchase received from supplier
- Stock adjustment (found items)
- Stock correction (system error)
- Returned items from department

**Outgoing Transaction:**
- Direct stock issue (not via request)
- Stock adjustment (damaged/expired items)
- Stock correction (manual decrease)
- Emergency stock distribution

---

## 2. Fulfillment (`/supplies/fulfillment`)

### Purpose
**Automated fulfillment** of approved supply requests. Creates distribution records and manages stock automatically.

### Key Features

#### Workflow Overview

```
Supply Request (approved)
    ↓
Fulfillment Process
    ↓
Creates: Distribution Record + Stock Transaction (OUT)
    ↓
Updates: Supply Stock, Request Status, Fulfillment Status
```

#### Steps in Fulfillment Process

1. **View Approved Requests**
   - Lists all requests with status: `approved` or `partially_fulfilled`
   - Shows: Request ID, Department, Date, Items Count, Status

2. **Fulfill Request**
   - Click "Fulfill" button on request
   - View approved items with quantities
   - For each item:
     - See: Requested Qty, Approved Qty, Current Stock
     - Enter: Fulfill Qty (can be less than approved)
     - Validation: Check stock availability
     - Validation: Cannot exceed remaining quantity

3. **Partial Fulfillment Support**
   - If stock insufficient, fulfill partial amount
   - Example: Approved 10, Stock 5 → Fulfill 5 now
   - Status changes to `partially_fulfilled`
   - Remaining items can be fulfilled later

4. **Automatic Operations**
   - Creates `supply_distributions` record
   - Creates `supply_transactions` (OUT type)
   - Decreases supply `current_stock`
   - Updates `fulfilled_quantity` on request items
   - Updates `fulfillment_status` (pending/partial/completed)
   - Updates request status (fulfilled/partially_fulfilled)

#### Distribution Record

When fulfillment occurs, a `supply_distributions` record is created:

```
- form_number: Auto-generated unique form number (format: 12-YYYYMMDD-XXXX)
- supply_id: Supply item distributed
- department_id: Department receiving the distribution
- request_item_id: Links to the supply_request_items record
- quantity: Amount distributed
- distribution_date: Date of distribution
- distributed_by: User who performed fulfillment
- notes: Additional notes
```

#### Stock Transaction (Auto-created)

An outgoing transaction is automatically created:

```
- supply_id: Same as distribution
- type: 'out'
- source: 'manual'
- department_id: Department receiving stock
- quantity: Distribution quantity
- reference_no: 'DIST-{distribution_id}'
- transaction_date: Distribution date
- notes: "Distribution for request #{request_id}"
- user_id: User who fulfilled
```

#### Status Management

**Request Item Status:**
- `pending`: Not fulfilled yet
- `partial`: Partially fulfilled
- `completed`: Fully fulfilled

**Request Status:**
- `approved`: Ready for fulfillment
- `partially_fulfilled`: Some items fulfilled, remainder pending
- `fulfilled`: All items completed

#### Validation Rules

1. **Stock Availability**
   ```
   fulfill_quantity <= current_stock
   Error: "Insufficient stock for {supply_name}. Available: X, Requested: Y"
   ```

2. **Quantity Limits**
   ```
   fulfill_quantity <= remaining_quantity
   remaining_quantity = approved_quantity - fulfilled_quantity
   Error: "Fulfill quantity cannot exceed remaining quantity"
   ```

3. **Transaction Safety**
   - All operations wrapped in database transaction
   - If any validation fails, entire operation rolls back
   - Ensures data consistency

#### Fulfillment History

- View all completed distributions
- Filter by date, department, supply
- Shows distribution details and linked request

---

## Differences & When to Use

### Stock Transaction
**Use when:**
- Recording stock received from suppliers
- Manual stock adjustments
- Stock corrections
- Direct stock issues (not via request)
- Stock movements not related to requests

**Characteristics:**
- Manual entry required
- No link to supply requests
- Flexible (can be any quantity if stock available)

### Fulfillment
**Use when:**
- Fulfilling approved supply requests
- Distributing stock to departments via request workflow
- Tracking request-to-distribution linkage
- Managing partial fulfillment scenarios

**Characteristics:**
- Automated workflow
- Linked to supply requests
- Creates distribution + transaction records
- Manages fulfillment tracking

---

## Integration Points

### Stock Transaction Integration
- Updates `supplies.current_stock` automatically
- Can be linked to departments
- Tracks user who recorded transaction
- Can reference suppliers and purchase orders

### Fulfillment Integration
- Uses approved supply requests
- Creates `supply_distributions` records
- Creates `supply_transactions` (OUT) automatically
- Updates `supply_request_items.fulfilled_quantity`
- Updates `supply_requests.status`
- Updates `supplies.current_stock`

---

## Database Relationships

```
supply_transactions
├── supply_id → supplies.id
├── department_id → departments.id (nullable)
└── user_id → users.id

supply_distributions
├── supply_id → supplies.id
├── department_id → departments.id
├── request_item_id → supply_request_items.id (nullable)
└── distributed_by → users.id

supply_request_items
├── request_id → supply_requests.id
├── supply_id → supplies.id
└── fulfilled_quantity (tracks fulfillment progress)
```

---

## Permissions

### Stock Transaction
- `view supply transactions` - View transaction list
- `create supply transactions` - Create new transactions
- `delete supply transactions` - Delete transactions (with stock reversal)

### Fulfillment
- `view supply fulfillment` - View fulfillment list
- `fulfill supply requests` - Perform fulfillment operations
- `view fulfillment history` - View distribution history

---

## Examples

### Example 1: Stock Received from Supplier
1. Go to **Stock Transactions → Create**
2. Select supply: "A4 Paper"
3. Type: **IN**
4. Source: **SAP**
5. Supplier: "PT Paper Supply"
6. PO Number: "PO-2025-001"
7. Quantity: 100
8. Save
9. Result: `current_stock` increases by 100

### Example 2: Fulfill Approved Request
1. Go to **Fulfillment**
2. Select Request #5 (IT Department, 10 Ballpoint Pens)
3. Approved Qty: 10, Current Stock: 8
4. Enter Fulfill Qty: 8
5. Click "Complete Fulfillment"
6. Result:
   - Distribution record created
   - OUT transaction created (8 units)
   - Stock decreases by 8
   - Request status: `partially_fulfilled`
   - Remaining 2 units can be fulfilled later

### Example 3: Manual Stock Issue (Not via Request)
1. Go to **Stock Transactions → Create**
2. Select supply: "Coffee"
3. Type: **OUT**
4. Source: **Manual**
5. Department: "Pantry"
6. Quantity: 5
7. Notes: "Direct issue for event"
8. Save
9. Result: Stock decreases by 5, no request linkage

---

## Summary

**Stock Transaction** = Manual stock movement recording (flexible, standalone)

**Fulfillment** = Automated request fulfillment (structured, linked to requests)

Both update stock levels, but fulfillments also track distributions and maintain request fulfillment status for better traceability and reporting.
