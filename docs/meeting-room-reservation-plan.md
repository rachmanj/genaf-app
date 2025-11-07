# Meeting Room Reservation Module - Action Plan & Features

**Module**: Meeting Room & Consumption Request Management  
**Priority**: High  
**Estimated Time**: 3-4 days  
**Status**: 🚧 Implementation Phase - In Progress

## 📋 Overview

Employee meeting room booking system with consumption request management for internal company meetings. Includes two-level approval workflow (Department Head → GA Admin), room allocation, and consumption tracking (coffee breaks, lunch, dinner).

**Note**: This is separate from the existing `room_reservations` table which handles guest/mess accommodations. This module is specifically for **meeting rooms** used by employees for internal meetings.

**Clarifications Received**:

1. ✅ Create separate `meeting_rooms` table (not reuse existing rooms)
2. ✅ Use existing FormNumberService (format: YY[code]-00001, code='33')
3. ✅ Consumption requests: checkbox for each day (multi-day meetings need separate consumption entries per day)
4. ✅ User must provide both date and time (start and end required, no "selesai" handling)
5. ✅ Location: free text field (users can request different places, not just HO Balikpapan)
6. ✅ Consumption is just tracking (no link to supply management)
7. ✅ GA Admin can allocate different rooms than requested
8. ✅ Notifications: both email and in-app
9. ✅ GA Admin needs room allocation diagram/view (calendar/grid visualization)

---

## 🎯 Core Features

### 1. **Meeting Room Reservation Form (Room & Consumption Request Form)**

**Request Data Fields** (from form image):

-   **Reg. No** (Registration Number) - Auto-generated form number
-   **Nama Ruangan** (Room Name) - Meeting room selection
-   **Lokasi** (Location) - Building/location (e.g., "HO Balikpapan")
-   **Divisi / Departemen** (Division/Department) - Auto-filled from user's department
-   **Fasilitas** (Facilities) - Available facilities in the room (e.g., "Ruang meeting & TV")
-   **Tanggal Meeting** (Meeting Date) - Date or date range (support multi-day meetings)
-   **Judul Meeting** (Meeting Title) - Purpose/subject of the meeting
-   **Waktu Meeting** (Meeting Time) - Start time (e.g., "08.30") and end time
-   **Jumlah Peserta** (Number of Participants) - Expected number of attendees

**Consumption Request Fields**:

-   **Coffee Break Pagi** (Morning Coffee Break) - Checkbox + Description field
-   **Coffee Break Sore** (Afternoon Coffee Break) - Checkbox + Description field
-   **Lunch** - Checkbox + Description field
-   **Dinner** - Checkbox + Description field

### 2. **Two-Level Approval Workflow**

```
[pending_dept_head]
         ↓
Department Head Reviews
  Approve │ Reject
         ↓ │
[pending_ga_admin] [rejected]
         ↓
GA Admin Reviews & Allocates Room
  Approve │ Reject
         ↓ │
[approved] [rejected]
         ↓
GA Admin Sends Response to Requestor
         ↓
[confirmed] (Room allocated, response sent)
```

**Status Flow**:

1. **pending_dept_head** - Request submitted, waiting for Department Head approval
2. **pending_ga_admin** - Approved by Department Head, waiting for GA Admin
3. **approved** - Approved by GA Admin, room allocated
4. **confirmed** - Response sent to requestor
5. **rejected** - Rejected by Department Head or GA Admin (with reason)

### 3. **Room Allocation Management**

-   GA Admin can allocate specific room after approval
-   Check room availability (no overlapping reservations)
-   Support for multi-day meetings
-   Room capacity validation (participant count vs room capacity)
-   Facility requirements matching (TV, projector, etc.)

### 4. **Consumption Management**

-   Track consumption items requested (coffee breaks, lunch, dinner)
-   Optional descriptions for each consumption type
-   GA Admin can approve/modify consumption requests
-   Consumption fulfillment tracking (separate from room allocation)

### 5. **Requestor Response System**

-   GA Admin can send response to requestor after room allocation
-   Response includes allocated room details, consumption status
-   Requestor receives notification/confirmation

---

## 🗃️ Database Schema

### Meeting Rooms Table

```sql
meeting_rooms
├── id (primary key)
├── name (string, unique) - Room name (e.g., "Rose", "Jasmin", "Lotus", "Platinum")
├── location (string) - Default location (e.g., "HO Balikpapan")
├── capacity (integer) - Maximum number of participants
├── facilities (text, nullable) - Available facilities (e.g., "Ruang meeting & TV")
├── is_active (boolean, default: true)
├── created_at
└── updated_at
```

### Meeting Room Reservations Table

```sql
meeting_room_reservations
├── id (primary key)
├── form_number (unique) - Auto-generated using FormNumberService (code='33', format: YY33-00001)
├── requestor_id (FK → users) - Employee who created the request
├── department_id (FK → departments) - Requestor's department
├── requested_room_id (FK → meeting_rooms, nullable) - Requested room (user's preference)
├── allocated_room_id (FK → meeting_rooms, nullable) - Allocated room (set by GA Admin, can be different)
├── location (string) - Requested location (free text, e.g., "HO Balikpapan" or other location)
├── meeting_title (string) - Meeting subject/purpose
├── meeting_date_start (date) - Start date
├── meeting_date_end (date) - End date (nullable, same as start for single-day)
├── meeting_time_start (time) - Start time (required, e.g., "08:30")
├── meeting_time_end (time) - End time (required, e.g., "17:00")
├── participant_count (integer) - Number of participants
├── required_facilities (text, nullable) - Required facilities description
├── status (enum: pending_dept_head, pending_ga_admin, approved, confirmed, rejected, cancelled)
├── department_head_approved_by (FK → users, nullable)
├── department_head_approved_at (timestamp, nullable)
├── department_head_rejection_reason (text, nullable)
├── ga_admin_approved_by (FK → users, nullable)
├── ga_admin_approved_at (timestamp, nullable)
├── ga_admin_rejection_reason (text, nullable)
├── room_allocated_at (timestamp, nullable) - When GA Admin allocated room
├── response_sent_at (timestamp, nullable) - When response sent to requestor
├── response_notes (text, nullable) - Response message to requestor
├── notes (text, nullable) - General notes
├── created_at
└── updated_at

Indexes:
- (requestor_id, meeting_date_start)
- (allocated_room_id, meeting_date_start, meeting_date_end)
- (requested_room_id, meeting_date_start, meeting_date_end)
- (status, meeting_date_start)
- (department_id, status)
```

### Consumption Requests Table (Day-by-Day)

```sql
meeting_consumption_requests
├── id (primary key)
├── reservation_id (FK → meeting_room_reservations)
├── consumption_date (date) - Specific date for this consumption (for multi-day meetings)
├── consumption_type (enum: coffee_break_morning, coffee_break_afternoon, lunch, dinner)
├── requested (boolean, default: false) - Whether this consumption is requested for this day
├── description (text, nullable) - Description/type of food/drink
├── fulfilled (boolean, default: false) - Whether consumption was fulfilled
├── fulfilled_at (timestamp, nullable)
├── fulfilled_by (FK → users, nullable)
├── notes (text, nullable)
├── created_at
└── updated_at

Indexes:
- (reservation_id, consumption_date, consumption_type)
- Unique: (reservation_id, consumption_date, consumption_type)
```

---

## 🏗️ Architecture & Implementation Plan

### Phase 1: Database & Models (Day 1)

**Tasks**:

1. Create migration for `meeting_room_reservations` table
2. Create migration for `meeting_consumption_requests` table
3. Create `MeetingRoomReservation` model with relationships
4. Create `MeetingConsumptionRequest` model
5. Update `Room` model to add helper methods for meeting room availability
6. Update `User` model to add `meetingRoomReservations` relationship
7. Update `Department` model if needed

**Models Structure**:

-   `MeetingRoomReservation` belongsTo `requestor` (User), `department`, `room`, `deptHeadApprover` (User), `gaAdminApprover` (User)
-   `MeetingRoomReservation` hasMany `consumptionRequests`
-   `MeetingConsumptionRequest` belongsTo `reservation`

**Form Number Generation**:

-   Use `FormNumberService` with prefix for meeting room reservations (e.g., "014/ARKA-HR-II.06/IV/2021")
-   Pattern: `{prefix}/ARKA-HR-II.06/{month}/{year}`

### Phase 2: Controllers & Routes (Day 1-2)

**Controller**: `MeetingRoomReservationController`

**Routes Structure**:

```php
Route::prefix('meeting-rooms')->middleware('auth')->name('meeting-rooms.')->group(function () {
    Route::resource('reservations', MeetingRoomReservationController::class);
    Route::post('reservations/{reservation}/approve-dept-head', [MeetingRoomReservationController::class, 'approveDeptHead'])->name('reservations.approve-dept-head');
    Route::post('reservations/{reservation}/reject-dept-head', [MeetingRoomReservationController::class, 'rejectDeptHead'])->name('reservations.reject-dept-head');
    Route::post('reservations/{reservation}/approve-ga-admin', [MeetingRoomReservationController::class, 'approveGAAdmin'])->name('reservations.approve-ga-admin');
    Route::post('reservations/{reservation}/reject-ga-admin', [MeetingRoomReservationController::class, 'rejectGAAdmin'])->name('reservations.reject-ga-admin');
    Route::post('reservations/{reservation}/allocate-room', [MeetingRoomReservationController::class, 'allocateRoom'])->name('reservations.allocate-room');
    Route::post('reservations/{reservation}/send-response', [MeetingRoomReservationController::class, 'sendResponse'])->name('reservations.send-response');
    Route::post('reservations/check-availability', [MeetingRoomReservationController::class, 'checkAvailability'])->name('reservations.check-availability');
});
```

**Controller Methods**:

-   `index()` - List reservations (filtered by status, department, date range)
-   `create()` - Show reservation form
-   `store()` - Create new reservation
-   `show()` - View reservation details
-   `edit()` - Edit reservation (only if pending_dept_head)
-   `update()` - Update reservation
-   `destroy()` - Cancel/delete reservation (only if pending_dept_head)
-   `approveDeptHead()` - Department Head approval
-   `rejectDeptHead()` - Department Head rejection
-   `approveGAAdmin()` - GA Admin approval
-   `rejectGAAdmin()` - GA Admin rejection
-   `allocateRoom()` - GA Admin allocates room
-   `sendResponse()` - GA Admin sends response to requestor
-   `checkAvailability()` - Check room availability (AJAX endpoint)

### Phase 3: Views & UI (Day 2-3)

**Views Structure**:

```
resources/views/meeting-room-reservation/
├── reservations/
│   ├── index.blade.php - List with DataTables
│   ├── create.blade.php - Reservation form
│   ├── edit.blade.php - Edit form (only for pending)
│   ├── show.blade.php - Detail view with approval workflow
│   └── partials/
│       ├── consumption-request-form.blade.php
│       └── approval-actions.blade.php
```

**UI Components**:

-   AdminLTE card layout matching existing modules
-   DataTables with server-side processing
-   Filter by status, department, date range
-   Date range picker for multi-day meetings
-   Time picker for meeting start/end times
-   Room selection dropdown (filtered by location/building)
-   Consumption request checkboxes with description fields
-   Approval workflow UI (similar to SupplyRequests)
-   Room allocation interface for GA Admin

**Form Number Display**:

-   Display generated form number in format matching the form image
-   Show in reservation detail view and print-friendly format

### Phase 4: Permissions & Security (Day 3)

**Permissions to Create**:

-   `view meeting room reservations`
-   `create meeting room reservations`
-   `edit meeting room reservations`
-   `delete meeting room reservations`
-   `approve meeting room reservations dept head`
-   `approve meeting room reservations ga admin`
-   `allocate meeting room reservations`
-   `send response meeting room reservations`

**Security Rules**:

-   Employees can only view their own reservations (unless admin/GA admin)
-   Department Head can only approve requests from their department
-   GA Admin can view all reservations
-   Only GA Admin can allocate rooms and send responses
-   Edit/delete only allowed for pending_dept_head status

### Phase 5: Room Availability Logic (Day 3-4)

**Availability Check**:

-   Check for overlapping reservations (same room, date range, time)
-   Exclude cancelled and rejected reservations
-   Consider room maintenance schedules (if integrated)
-   Validate room capacity vs participant count
-   Validate facility requirements (if room has required facilities)

**Helper Methods**:

-   `Room::isAvailableForMeeting($dateStart, $dateEnd, $timeStart, $timeEnd, $excludeReservationId = null)`
-   `MeetingRoomReservation::checkRoomAvailability($roomId, $dateStart, $dateEnd, $timeStart, $timeEnd)`

### Phase 6: Testing & Documentation (Day 4)

**Testing**:

-   Browser automation testing (Chrome DevTools MCP)
-   Test complete workflow: Create → Dept Head Approve → GA Admin Approve → Allocate Room → Send Response
-   Test rejection workflows
-   Test room availability validation
-   Test multi-day meetings
-   Test consumption request management
-   Test permissions and access control

**Documentation Updates**:

-   Update `docs/architecture.md` with new module
-   Update `docs/todo.md` with completed tasks
-   Update `MEMORY.md` with key decisions
-   Add to `docs/decisions.md` if architectural decisions made

---

## 🔄 Integration Points

### Existing Modules

1. **Rooms Module** (`rooms` table):

    - Use existing `rooms` table but filter for meeting rooms
    - Add `room_type` or `purpose` field to distinguish meeting rooms from guest rooms
    - Or create separate `meeting_rooms` table if needed

2. **Departments Module**:

    - Auto-fill department from requestor
    - Department-based filtering for Department Head approvals

3. **Users Module**:

    - Requestor relationship
    - Department Head and GA Admin roles

4. **Form Number Service**:
    - Use existing `FormNumberService` for form number generation

### Future Enhancements

1. **Notifications**:

    - Email notifications for approval requests
    - Email notifications for room allocation/response

2. **Calendar Integration**:

    - Calendar view for room bookings
    - iCal export for meeting invitations

3. **Consumption Fulfillment Tracking**:

    - Link to supply management system
    - Track actual consumption vs requested

4. **Reporting**:
    - Room utilization reports
    - Consumption cost tracking
    - Department-wise meeting statistics

---

## ❓ Clarifications Needed

1. **Room Management**:

    - Should we use existing `rooms` table with a filter for meeting rooms, or create a separate `meeting_rooms` table?
    - How do we distinguish meeting rooms from guest/mess rooms in the existing system?

2. **Form Number Format**:

    - What is the exact pattern for form numbers? Example: "014/ARKA-HR-II.06/IV/2021"
    - Should it be sequential (014, 015, 016) or date-based?

3. **Multi-Day Meetings**:

    - Can meetings span multiple days (e.g., "27 – 28 Oktober 2025")?
    - Should consumption requests apply to all days or specific days?

4. **Meeting Time "Selesai"**:

    - When time is "selesai" (finished), how do we handle this in the database?
    - Should we allow end time to be nullable and interpret "selesai" as "until end of day"?

5. **Location Field**:

    - Should "Location" be a free text field or a dropdown (linked to buildings)?
    - Should we link to the existing `buildings` table?

6. **Consumption Fulfillment**:

    - Should consumption requests link to the supply management system?
    - Or is it just for tracking/approval purposes?

7. **Room Allocation**:

    - Can GA Admin allocate a different room than requested?
    - Should the system suggest available rooms based on requirements?

8. **Response System**:
    - How should GA Admin send response to requestor?
    - Email notification, in-app notification, or both?

---

## 📝 Recommendations

1. **Separate Meeting Rooms from Guest Rooms**:

    - Add `room_type` enum to `rooms` table: `['guest', 'meeting']`
    - Or add `purpose` field to distinguish room types
    - Filter meeting room reservations to only show meeting-type rooms

2. **Form Number Pattern**:

    - Use sequential numbering per year: `{seq}/ARKA-HR-II.06/{month}/{year}`
    - Implement in `FormNumberService` with proper sequence tracking

3. **Location as Building Reference**:

    - Link to `buildings` table instead of free text
    - Ensure consistency and enable building-based filtering

4. **Time Handling**:

    - Store `meeting_time_end` as nullable
    - Use "23:59" or NULL to represent "selesai" (until finished)
    - Display "selesai" in UI when end time is null

5. **Consumption as Separate Module**:

    - Keep consumption requests simple for now (just tracking/approval)
    - Future enhancement: Link to supply management for fulfillment

6. **Room Suggestion Feature**:

    - GA Admin can see suggested rooms based on:
        - Capacity (participant count)
        - Required facilities
        - Availability
        - Location preference

7. **Notification System**:
    - Start with in-app notifications (Laravel notifications)
    - Future enhancement: Email notifications

---

## ✅ Success Criteria

1. ✅ Users can create meeting room reservation requests with consumption items
2. ✅ Department Head can approve/reject requests from their department
3. ✅ GA Admin can approve/reject requests and allocate rooms
4. ✅ GA Admin can send response to requestor
5. ✅ Room availability validation prevents double-booking
6. ✅ Multi-day meetings are supported
7. ✅ Form numbers match the required format
8. ✅ All permissions are properly enforced
9. ✅ DataTables integration works with server-side processing
10. ✅ Complete workflow tested via browser automation

---

**Next Steps**: Review this plan and provide clarifications for the questions above. Once approved, implementation will begin following the phases outlined.
