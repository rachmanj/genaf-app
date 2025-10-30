# Office Supplies Management - Training Material

**Module**: Office Supplies Management System  
**Version**: 1.0  
**Last Updated**: January 2025  
**Target Audience**: Employees, Department Heads, GA Administrators

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [User Roles & Permissions](#user-roles--permissions)
3. [Module Components](#module-components)
4. [Supply Master Data](#supply-master-data)
5. [Supply Request Workflow](#supply-request-workflow)
6. [Stock Transactions](#stock-transactions)
7. [Department Stock Management](#department-stock-management)
8. [Stock Opname](#stock-opname)
9. [Common Tasks & Procedures](#common-tasks--procedures)
10. [Troubleshooting](#troubleshooting)

---

## Overview

The Office Supplies Management module is a comprehensive system for managing office supplies inventory, employee requests, and stock tracking. This module supports a two-level approval workflow and department-based stock allocation.

### Key Features

- ✅ **Supply Master Data**: Manage supply items with categories, units, and stock levels
- ✅ **Supply Requests**: Two-level approval workflow (Department Head → GA Admin)
- ✅ **Stock Transactions**: Track incoming and outgoing stock with source tracking
- ✅ **Supply Fulfillment**: Partial fulfillment with distribution tracking
- ✅ **Department Stock**: Department-based stock allocation and reporting
- ✅ **Stock Opname**: Physical inventory count with gradual counting support

### Business Benefits

- **Efficient Request Management**: Streamlined approval process reduces approval time
- **Inventory Control**: Real-time stock tracking prevents shortages and overstock
- **Accountability**: Department-based tracking ensures responsible resource usage
- **Cost Management**: Track supply costs and optimize purchasing decisions
- **Audit Trail**: Complete transaction history for compliance and reporting

---

## User Roles & Permissions

### Employee

**Permissions**:
- View supplies (catalog)
- Create supply requests
- View own supply requests
- View stock opname
- Count stock opname items

**Responsibilities**:
- Submit supply requests for department needs
- Participate in stock counting activities
- View available supplies and stock levels

### Department Head

**Permissions**:
- View supplies (catalog)
- View supply requests
- Create supply requests
- Approve department head supply requests
- Reject department head supply requests
- View department stock
- View stock opname
- Count stock opname

**Responsibilities**:
- Review and approve/reject supply requests from department employees
- Monitor department stock levels
- Participate in stock counting and verification

### GA Administrator (General Affairs)

**Permissions**:
- View supplies (full catalog)
- Create supplies
- Edit supplies
- Delete supplies
- View supply requests (all departments)
- Create supply requests
- Edit supply requests
- Approve GA admin supply requests
- Reject GA admin supply requests
- View supply transactions (all)
- Create supply transactions
- Delete supply transactions
- View supply fulfillment
- Fulfill supply requests
- View department stock (all departments)
- View stock opname (all sessions)
- Count stock opname
- Approve stock opname

**Responsibilities**:
- Manage supply master data
- Review and approve/reject supply requests (final approval)
- Manage stock transactions (incoming/outgoing)
- Fulfill approved supply requests
- Monitor overall inventory levels
- Manage stock opname sessions

### Manager (Department Manager)

**Permissions**:
- Full access to supplies management
- View all supply requests
- Approve/reject supply requests
- View all transactions
- Manage stock opname

**Responsibilities**:
- Oversee department supply management
- Approve department supply requests
- Review inventory reports

### Administrator (System Admin)

**Permissions**:
- Full system access
- Manage all modules and configurations

**Responsibilities**:
- System administration and maintenance
- User management
- Configuration and setup

---

## Module Components

### 1. Supply Master Data

**Location**: `/supplies`  
**Purpose**: Manage supply items catalog (similar to product master data)

#### Supply Attributes

- **Code**: Unique identifier (e.g., ATK001, CLN002)
- **Name**: Supply item name (e.g., "Printer Paper A4")
- **Category**: Supply category
  - ATK (Office Stationery)
  - Cleaning (Cleaning Supplies)
  - Pantry (Pantry/Kitchen Items)
  - IT (IT Equipment and Supplies)
  - Office (Office Furniture/Equipment)
  - Other
- **Unit**: Measurement unit
  - pcs (pieces)
  - box
  - pack
  - roll
  - bottle
  - kg (kilogram)
  - liter
  - meter
- **Current Stock**: Current available quantity
- **Min Stock**: Minimum stock level (low stock alert threshold)
- **Price**: Unit price in Indonesian Rupiah (IDR)
- **Description**: Additional item details
- **Status**: Active/Inactive

#### Stock Status

The system automatically calculates stock status:

- **In Stock** (Green): `current_stock > min_stock`
- **Low Stock** (Yellow): `current_stock <= min_stock` and `current_stock > 0`
- **Out of Stock** (Red): `current_stock = 0`

#### Viewing Supply Catalog

1. Navigate to **Supplies** in the sidebar menu
2. The supply list displays with DataTables (search, sort, pagination)
3. View stock status badges (color-coded)
4. Click **View** to see detailed information
5. Click **Edit** to modify supply details (GA Admin only)

### 2. Supply Requests

**Location**: `/supplies/requests`  
**Purpose**: Submit and manage supply requests with two-level approval workflow

#### Request Workflow

```
Employee Submits Request
         ↓
   [pending_dept_head]
         ↓
Department Head Reviews
  Approve │ Reject
         ↓ │
[pending_ga_admin] [rejected]
         ↓
GA Admin Reviews
  Approve │ Reject
         ↓ │
[approved] [rejected]
         ↓
GA Admin Fulfills
         ↓
[partially_fulfilled] or [fulfilled]
```

#### Request Statuses

1. **pending_dept_head** (Yellow Badge)
   - Request submitted by employee
   - Waiting for department head approval

2. **pending_ga_admin** (Orange Badge)
   - Approved by department head
   - Waiting for GA admin final approval

3. **approved** (Green Badge)
   - Approved by GA admin
   - Ready for fulfillment

4. **partially_fulfilled** (Blue Badge)
   - Some items fulfilled, some pending
   - GA admin can continue fulfillment

5. **fulfilled** (Green Badge)
   - All items fulfilled
   - Request completed

6. **rejected** (Red Badge)
   - Rejected by department head or GA admin
   - Includes rejection reason

#### Submitting a Supply Request

**As Employee or Department Head**:

1. Navigate to **Supplies → Requests**
2. Click **Create New Request** button
3. Fill in request details:
   - **Employee**: Auto-selected (logged-in user)
   - **Department**: Auto-selected (user's department)
   - **Request Date**: Today's date (default)
   - **Notes**: Optional remarks
4. Add supply items:
   - Click **Add Item** button
   - Select supply from dropdown
   - Enter quantity
   - Add additional items as needed
5. Click **Submit Request**
6. Request status: **pending_dept_head**

#### Approving as Department Head

1. Navigate to **Supplies → Requests**
2. View requests with status **pending_dept_head**
3. Click **View** on the request
4. Review request details and items
5. Choose action:
   - **Approve** → Status changes to **pending_ga_admin**
   - **Reject** → Enter rejection reason → Status changes to **rejected**

#### Approving as GA Admin

1. Navigate to **Supplies → Requests**
2. View requests with status **pending_ga_admin**
3. Click **Approve** button
4. Review and adjust approved quantities (if needed)
   - You can approve less than requested quantity
   - Example: Request 10 units, approve 5 units
5. Click **Confirm Approval**
6. Status changes to **approved**
7. Proceed to fulfillment

#### Rejecting a Request

**Department Head or GA Admin**:

1. Open the request details
2. Click **Reject** button
3. Enter rejection reason (required, max 1000 characters)
4. Click **Confirm Rejection**
5. Status changes to **rejected**
6. Employee receives notification

### 3. Supply Fulfillment

**Location**: `/supplies/fulfillment`  
**Purpose**: Fulfill approved supply requests and manage stock distribution

#### Fulfilling a Request

**As GA Admin**:

1. Navigate to **Supplies → Fulfillment**
2. View list of approved requests
3. Click **View** or **Fulfill** on the request
4. Review approved items and quantities
5. For each item:
   - Available stock is displayed
   - Enter fulfillment quantity (can be less than approved)
   - System validates stock availability
6. Select distribution department (if applicable)
7. Add fulfillment notes
8. Click **Complete Fulfillment**

#### Partial Fulfillment

The system supports partial fulfillment:

- **Scenario**: Request approved for 10 units, but only 5 available
- **Action**: Fulfill 5 units now, remainder later
- **Status**: Request shows **partially_fulfilled**
- **Follow-up**: Fulfill remaining items when stock available

#### Fulfillment History

1. Navigate to **Supplies → Fulfillment → History**
2. View all fulfilled requests
3. Filter by date range, status, or employee
4. Export data for reporting

### 4. Stock Transactions

**Location**: `/supplies/transactions`  
**Purpose**: Record incoming and outgoing stock movements

#### Transaction Types

1. **Incoming (IN)**
   - Stock received from supplier
   - Stock received from purchase
   - Stock adjustment (increase)
   - Source tracking: SAP or Manual

2. **Outgoing (OUT)**
   - Stock distributed to departments
   - Stock issued to fulfill requests
   - Stock adjustment (decrease)
   - Source tracking: Request fulfillment

#### Creating Stock Transaction

**As GA Admin**:

1. Navigate to **Supplies → Transactions**
2. Click **Create Transaction** button
3. Fill in transaction details:
   - **Supply**: Select supply item
   - **Type**: Incoming or Outgoing
   - **Source**: SAP or Manual
   - **Quantity**: Transaction quantity
   - **Transaction Date**: Date of transaction
   - **Reference No**: PO number, invoice number, etc.
   - **Department**: Target department (for outgoing)
   - **Supplier Name**: For incoming transactions
   - **Purchase Order No**: PO reference
   - **Notes**: Additional information
4. Click **Save Transaction**
5. System automatically updates supply current stock

#### Stock Update Logic

**Incoming Transaction**:
```
New Stock = Old Stock + Incoming Quantity
```

**Outgoing Transaction**:
```
New Stock = Old Stock - Outgoing Quantity
Validation: New Stock >= 0 (prevent negative stock)
```

#### Viewing Transaction History

1. Navigate to **Supplies → Transactions**
2. DataTables displays all transactions
3. Filter by:
   - Transaction type (In/Out)
   - Supply item
   - Department
   - Date range
   - User who recorded the transaction
4. Export data for reporting

### 5. Department Stock Management

**Location**: `/departments/{id}/stock`  
**Purpose**: View and manage department-specific stock allocations

#### Features

- **Stock Allocation**: Track which supplies are allocated to each department
- **Stock Levels**: View department-level inventory
- **Stock History**: Transaction history for department
- **Reporting**: Department stock usage reports

#### Viewing Department Stock

**As Department Head or GA Admin**:

1. Navigate to **Departments** in sidebar
2. Click on a department
3. View **Stock** tab
4. See allocated supplies and quantities

### 6. Stock Opname (Physical Inventory Count)

**Location**: `/stock-opname`  
**Purpose**: Conduct physical inventory counts and reconcile stock discrepancies

#### Opname Workflow

```
Create Session
     ↓
[started]
     ↓
Assign Items to Count
     ↓
Counting (Work in Progress)
     ↓
[pending/counting] → [counted] → [verified]
     ↓
Approval
     ↓
[approved]
     ↓
Stock Adjustment
```

#### Creating Stock Opname Session

**As GA Admin**:

1. Navigate to **Stock Opname**
2. Click **Create New Session**
3. Fill in session details:
   - **Title**: Session title (e.g., "Monthly Stock Count - January 2025")
   - **Description**: Purpose and scope
   - **Type**: Manual or Scheduled
   - **Schedule Type**: Monthly, Quarterly, Yearly (if scheduled)
   - **Notes**: Additional information
4. Click **Create Session**
5. Status: **started**

#### Assigning Items to Count

1. Open the stock opname session
2. Click **Add Items**
3. Select supplies to count
4. Add selected items to the session
5. Items start with status: **pending**

#### Counting Items (Gradual Counting)

**As GA Admin or Department Head**:

The system supports gradual counting - you can count items one by one and save progress.

1. Open the stock opname session
2. View items list
3. For each item:
   - Click **Count** or **Edit** button
   - Enter **System Quantity** (from database)
   - Enter **Physical Quantity** (actual count)
   - System calculates variance automatically:
     ```
     Variance = Physical Quantity - System Quantity
     ```
   - If variance exists, enter variance reason:
     - Damaged goods
     - Theft
     - Data entry error
     - Expired items
     - Other
   - Upload photo evidence (optional)
   - Click **Save Count**
4. Status changes to **counting** (work in progress)
5. Status changes to **counted** when completed

#### Saving Draft (Work in Progress)

You can save your count progress at any time:

1. Click **Save Draft** after counting some items
2. Items with status **counting** are saved as work in progress
3. Continue counting later
4. Click **Finalize Count** when all items are counted

#### Finalizing Stock Opname

1. Ensure all items are counted
2. Click **Finalize Count** button
3. System calculates totals:
   - Total items counted
   - Items with variance
   - Total variance value
4. Review variance summary
5. Session status: **completed**

#### Approving Stock Opname

**As GA Admin or Manager**:

1. View stock opname session with status **completed**
2. Click **Approve** button
3. Review all counts and variances
4. Click **Confirm Approval**
5. System automatically adjusts stock based on physical count
6. Session status: **approved**

#### Automatic Stock Adjustment

When opname session is approved:

1. System calculates stock adjustments for each item with variance
2. Creates adjustment records
3. Updates supply current stock to match physical quantity
4. Adjustments are logged in stock transactions

---

## Common Tasks & Procedures

### Task 1: Request Office Supplies (Employee)

**Scenario**: Employee needs printer paper for the department

**Steps**:

1. **Submit Request**:
   - Login to system
   - Navigate to **Supplies → Requests**
   - Click **Create New Request**
   - Add item: "Printer Paper A4", Quantity: 5 boxes
   - Add notes: "Urgent - needed for upcoming training"
   - Click **Submit Request**

2. **Monitor Request**:
   - Wait for department head approval
   - Check status: Should show "Pending Dept Head"
   - Once approved by dept head, status changes to "Pending GA Admin"

3. **Receive Approval**:
   - Status changes to "Approved"
   - GA admin will fulfill the request

4. **Collect Supplies**:
   - Receive notification when request is fulfilled
   - Collect supplies from GA department

### Task 2: Approve Supply Request (Department Head)

**Scenario**: Department head reviews employee supply request

**Steps**:

1. **Review Request**:
   - Login as department head
   - Navigate to **Supplies → Requests**
   - Filter by status: "Pending Dept Head"
   - Click **View** on the request

2. **Evaluate Request**:
   - Check requested items and quantities
   - Review department budget and need
   - Check if items are reasonable

3. **Make Decision**:
   - **If Approving**:
     - Click **Approve** button
     - Status changes to "Pending GA Admin"
     - Request proceeds to GA admin
   - **If Rejecting**:
     - Click **Reject** button
     - Enter rejection reason: "Insufficient budget for this quarter"
     - Click **Confirm Rejection**
     - Status changes to "Rejected"

### Task 3: Fulfill Supply Request (GA Admin)

**Scenario**: GA admin fulfills approved supply request

**Steps**:

1. **Review Approved Requests**:
   - Login as GA admin
   - Navigate to **Supplies → Fulfillment**
   - View list of approved requests

2. **Check Stock Availability**:
   - Click **View** on the request
   - Review requested items and quantities
   - Check current stock for each item

3. **Fulfill Request**:
   - Click **Fulfill** button
   - For each item:
     - Check available stock
     - Enter fulfillment quantity (if less than approved)
     - Add distribution notes if needed
   - Click **Complete Fulfillment**

4. **Stock Update**:
   - System automatically deducts stock
   - Creates stock transaction record
   - Request status: "Fulfilled"

### Task 4: Record Stock Incoming (GA Admin)

**Scenario**: New supplies received from supplier

**Steps**:

1. **Create Transaction**:
   - Navigate to **Supplies → Transactions**
   - Click **Create Transaction**

2. **Fill Transaction Details**:
   - **Supply**: Select item (e.g., "Printer Paper A4")
   - **Type**: Incoming
   - **Source**: Manual or SAP
   - **Quantity**: 20 boxes
   - **Supplier Name**: "PT Stationery Supplier"
   - **Purchase Order No**: "PO-2025-001"
   - **Reference No**: "INV-2025-123"
   - **Transaction Date**: Today's date
   - **Notes**: "Bulk purchase for Q1"

3. **Save Transaction**:
   - Click **Save Transaction**
   - System updates current stock automatically
   - Stock increase: +20 boxes

### Task 5: Conduct Stock Opname (GA Admin)

**Scenario**: Monthly physical inventory count

**Steps**:

1. **Create Opname Session**:
   - Navigate to **Stock Opname**
   - Click **Create New Session**
   - Title: "Monthly Stock Count - January 2025"
   - Type: Scheduled, Schedule: Monthly
   - Click **Create**

2. **Add Items to Count**:
   - Open the session
   - Click **Add Items**
   - Select supplies to count
   - Add items to session

3. **Count Items**:
   - Click **Count** on an item
   - Enter system quantity (from database)
   - Enter physical quantity (actual count)
   - System calculates variance
   - If variance: select reason and upload photo
   - Click **Save Count**

4. **Finalize Count**:
   - After counting all items
   - Click **Finalize Count**
   - Review variance summary
   - Session status: "Completed"

5. **Approve and Adjust**:
   - Click **Approve**
   - System adjusts stock to match physical count
   - Session status: "Approved"

---

## Troubleshooting

### Issue 1: Cannot Submit Supply Request

**Problem**: "Create New Request" button is not visible

**Possible Causes**:
- User lacks "create supply requests" permission
- Route not accessible

**Solution**:
1. Check user role and permissions
2. Contact system administrator to assign permissions
3. Verify user is assigned to a department

---

### Issue 2: Cannot Approve Supply Request

**Problem**: "Approve" button not available

**Possible Causes**:
- User is not department head
- Request is already approved
- Request belongs to different department

**Solution**:
1. Verify user has department head role
2. Check request status (should be "Pending Dept Head")
3. Ensure request belongs to user's department
4. Contact admin if issue persists

---

### Issue 3: Stock Not Updating After Transaction

**Problem**: Current stock not reflecting new transaction

**Possible Causes**:
- Transaction not saved properly
- Database error
- Cache issue

**Solution**:
1. Verify transaction was created successfully
2. Check transaction record in transaction list
3. Refresh the page
4. Clear browser cache
5. Contact system administrator if issue persists

---

### Issue 4: Cannot Fulfill Request Due to Low Stock

**Problem**: "Insufficient stock" error when fulfilling

**Possible Causes**:
- Stock level below requested quantity
- Stock not updated after receiving supplies
- Request approved for more than available

**Solution**:
1. Check current stock level for the item
2. Create stock transaction to add incoming stock
3. Fulfill partial quantity if acceptable
4. Update request status to "Partially Fulfilled"
5. Fulfill remainder when stock available

---

### Issue 5: Stock Opname Approval Failed

**Problem**: Error when approving stock opname

**Possible Causes**:
- Some items not counted
- Session not finalized
- Database transaction error

**Solution**:
1. Ensure all items have status "counted"
2. Finalize the session before approval
3. Check session status (should be "completed")
4. Review variance calculations
5. Try approval again
6. Contact system administrator if issue persists

---

## Best Practices

### For Employees

1. **Plan Ahead**: Submit supply requests in advance to allow approval time
2. **Be Specific**: Provide clear notes about why supplies are needed
3. **Check Availability**: View supply catalog to see available items and stock
4. **Reasonable Quantities**: Request only what is truly needed
5. **Follow Up**: Monitor request status and follow up if delayed

### For Department Heads

1. **Timely Review**: Review and approve/reject requests promptly
2. **Budget Awareness**: Consider department budget when approving
3. **Communication**: Provide clear rejection reasons when declining requests
4. **Monitor Usage**: Review department stock levels regularly
5. **Educate Team**: Guide employees on proper request procedures

### For GA Administrators

1. **Maintain Data**: Keep supply master data accurate and up-to-date
2. **Monitor Stock**: Set appropriate minimum stock levels for alerts
3. **Timely Fulfillment**: Fulfill approved requests promptly
4. **Stock Management**: Record all incoming and outgoing transactions
5. **Regular Opname**: Conduct regular stock counts to ensure accuracy
6. **Reporting**: Generate and review inventory reports regularly
7. **Supplier Relations**: Track suppliers and purchase orders for reference

### For All Users

1. **Data Accuracy**: Enter accurate information in all forms
2. **Documentation**: Add notes for context when needed
3. **Security**: Log out when done, protect credentials
4. **Training**: Complete module training before using the system
5. **Feedback**: Report issues or suggest improvements to system administrator

---

## Support & Resources

### Getting Help

- **System Support**: Contact IT department
- **Training**: Contact HR for additional training
- **Documentation**: Refer to this guide and system help documentation

### Additional Resources

- System Architecture: `docs/architecture.md`
- Technical Decisions: `docs/decisions.md`
- Implementation Plan: `docs/genaf-enterprise-app.plan.md`

---

**End of Training Material**

*For questions or additional assistance, please contact the system administrator.*

