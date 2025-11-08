**Purpose**: AI's persistent knowledge base for project context and learnings
**Last Updated**: [Auto-updated by AI]

## Memory Maintenance Guidelines

### Structure Standards

-   Entry Format: ### [ID] [Title (YYYY-MM-DD)] ✅ STATUS
-   Required Fields: Date, Challenge/Decision, Solution, Key Learning
-   Length Limit: 3-6 lines per entry (excluding sub-bullets)
-   Status Indicators: ✅ COMPLETE, ⚠️ PARTIAL, ❌ BLOCKED

### Content Guidelines

-   Focus: Architecture decisions, critical bugs, security fixes, major technical challenges
-   Exclude: Routine features, minor bug fixes, documentation updates
-   Learning: Each entry must include actionable learning or decision rationale
-   Redundancy: Remove duplicate information, consolidate similar issues

### File Management

-   Archive Trigger: When file exceeds 500 lines or 6 months old
-   Archive Format: `memory-YYYY-MM.md` (e.g., `memory-2025-01.md`)
-   New File: Start fresh with current date and carry forward only active decisions

---

## Project Memory Entries

### [MEM-001] AdminLTE Integration Success (2025-01-24) ✅ COMPLETE

**Challenge**: Migrate from Tailwind CSS to AdminLTE 3.x for professional enterprise UI
**Solution**: Leveraged existing AdminLTE assets in `public/adminlte/`, updated layout structure, integrated DataTables/SweetAlert2/Toastr
**Key Learning**: AdminLTE provides complete admin dashboard solution with professional components, faster development than utility-first CSS for admin interfaces

### [MEM-002] DataTables Server-side Processing Implementation (2025-01-24) ✅ COMPLETE

**Challenge**: Efficient data display for large datasets with search/sort/pagination
**Solution**: Implemented DataTables with server-side processing, AJAX endpoints in controllers, proper column configuration
**Key Learning**: Server-side processing essential for enterprise applications, reduces client load and improves performance significantly

### [MEM-003] Comprehensive RBAC with Spatie Laravel Permission (2025-01-24) ✅ COMPLETE

**Challenge**: Implement granular permission system for enterprise application
**Solution**: Used Spatie package with 58 comprehensive permissions, role hierarchy (Admin > Manager > Employee), Blade `@can` directives
**Key Learning**: Spatie package provides robust RBAC foundation, comprehensive permission system enables future module development

### [MEM-004] Permission Check Bug Fix in Roles Index (2025-01-24) ✅ COMPLETE

**Challenge**: "Add New Role" button not visible despite admin permissions
**Solution**: Fixed permission check from `@can('roles.create')` to `@can('create roles')` (space vs dot notation)
**Key Learning**: Spatie permission names use spaces, not dots - critical for proper permission checking in Blade templates

### [MEM-005] JavaScript Stack Integration with AdminLTE (2025-01-24) ✅ COMPLETE

**Challenge**: Integrate modern JavaScript libraries with AdminLTE's jQuery-based architecture
**Solution**: Used jQuery-compatible libraries (DataTables, SweetAlert2, Toastr, Select2) for seamless integration
**Key Learning**: AdminLTE's jQuery foundation requires compatible libraries, modern frameworks would require significant refactoring

### [MEM-006] Browser Automation Testing Strategy (2025-01-24) ✅ COMPLETE

**Challenge**: Comprehensive testing of AdminLTE UI and user workflows
**Solution**: Implemented Playwright browser automation testing, validated complete user journeys, tested responsive design
**Key Learning**: Browser automation essential for enterprise applications, catches integration issues between frontend/backend

### [MEM-007] Documentation Architecture Pattern (2025-01-24) ✅ COMPLETE

**Challenge**: Maintain comprehensive documentation following .cursorrules guidelines
**Solution**: Created structured documentation system: plan.md (implementation status), architecture.md (technical reference), decisions.md (ADR), todo.md (task tracking)
**Key Learning**: Structured documentation enables better AI assistance and team collaboration, follows enterprise development standards

### [MEM-008] Notification System Consistency Fix (2025-01-24) ✅ COMPLETE

**Challenge**: Inconsistent notification system - some views used Bootstrap alerts instead of Toastr
**Solution**: Updated all views (supplies, roles, users) to use Toastr for notifications and SweetAlert2 for confirmations, removed Bootstrap alert blocks
**Key Learning**: Consistency in notification system is crucial for professional UX, Toastr provides better user experience than static Bootstrap alerts

### [MEM-011] Route Hierarchy Restructuring (2025-01-24) ✅ COMPLETE

**Challenge**: User requested proper route hierarchy - stock transactions and supply requests should be nested under supplies
**Solution**: Restructured routes to `/supplies/requests` and `/supplies/transactions`, updated all controllers, views, and sidebar links, reordered routes to prevent conflicts
**Key Learning**: Route ordering is critical in Laravel - more specific routes must come before generic parameterized routes to prevent conflicts

### [MEM-012] Office Supplies Module Complete Implementation (2025-01-25) ✅ COMPLETE

**Challenge**: Implement comprehensive Office Supplies Management with two-level approval, fulfillment, and inventory tracking
**Solution**: Created SupplyRequests with department-based workflow, SupplyFulfillment with partial fulfillment support, SupplyTransactions with source tracking, DepartmentStock allocation, Departments CRUD with API sync preparation, comprehensive permissions and routes
**Key Learning**: Two-level approval workflows (Dept Head → GA Admin) with partial fulfillment enable realistic enterprise supply management, department-based allocation provides accountability

### [MEM-013] Stock Opname Module with Gradual Counting (2025-01-25) ✅ COMPLETE

**Challenge**: Implement physical inventory count system with gradual counting, save progress, and complete later functionality
**Solution**: Created StockOpnameSession/Items models with status workflow (pending → counting → counted → verified), implemented saveDraft and finalizeCount methods, added photo evidence upload, variance calculation with reason codes, automatic stock adjustment after approval, fixed approval workflow with database enum migrations
**Key Learning**: Gradual counting with draft save enables realistic stock opname workflows - items can be in 'counting' status (work-in-progress) before finalization, concurrent counting supported, status-based workflow provides clear progress tracking. Database enum constraints must be extended when adding new transaction types (added 'adjustment' to type, 'stock_opname' to source).

### [MEM-014] User Schema & Login Update (2025-10-30) ✅ COMPLETE

-   Challenge/Decision: Add `username` (nullable, unique) and `nik` (nullable, unique); change `department` to `department_id` FK; enable login via email or username; remove legacy `role` enum.
-   Solution: Updated base users migration; requests validation; controller and views; login detection in `LoginRequest`; updated seeder; guarded later migrations (drop role, add nik, add department_id) to be idempotent; deferred FK creation where needed.
-   Key Learning: When modifying base tables, later delta migrations must guard for presence/absence to support migrate:fresh; FK constraints must respect creation order or be added after both tables exist.

### [MEM-015] Ticket Reservations Module Implementation (2025-10-30) ✅ COMPLETE

**Challenge**: Build ticket reservation system with approval workflow, document management, and DataTables integration  
**Solution**: Created TicketReservation and ReservationDocument models, TicketReservationController with CRUD and workflow methods (approve, reject, markBooked, markCompleted), implemented file upload/delete, added 12 permissions, created DataTables index with advanced filtering, fixed FK relationship issue (`reservation_id` vs `ticket_reservation_id`)  
**Key Learning**: Eloquent relationship foreign keys must be explicitly specified when naming differs from convention (e.g., `reservation_id` not `ticket_reservation_id`). Removed eager loading of problematic relationships from DataTables queries to prevent FK errors.

### [MEM-016] Vehicle Administration Base Implementation (2025-10-30) ✅ COMPLETE

-   Challenge/Decision: Introduce Vehicle module with fuel, documents, maintenance, and alerts; choose global list routes for fuel/maintenance and secure document storage.
-   Solution: Added routes, controllers, models with helpers, AdminLTE views, sidebar wiring, dashboard widgets (expiring docs, upcoming services), and secure upload/download using public disk; seeded new permissions.
-   Key Learning: Splitting sub-resources into dedicated controllers with global lists simplifies UX and navigation; enforce permissions including download; helper scopes (`expiringWithin`, `upcoming`) aid dashboard and alerts.

### [MEM-017] Vehicle Pages UI Alignment & DevTools Verification (2025-10-30) ✅ COMPLETE

-   Challenge/Decision: Vehicle pages lacked consistent layout and table pattern vs supplies; needed quick alignment and validation using Chrome DevTools.
-   Solution: Updated `vehicles`, `fuel-records`, `maintenance` index views to use `layouts.main`, breadcrumbs, AdminLTE card-outline; added client-side DataTables with index column and responsive options; Vehicles filters (Type/Status). Verified via DevTools Elements/Network.
-   Key Learning: Standardizing layout and table options across modules speeds UX consistency; client-side DataTables is acceptable interim before server-side scaling; use `@push('js')` for script stack consistency.

### [MEM-018] Department-Based Supply Request Scoping & Security (2025-01-30) ✅ COMPLETE

-   Challenge/Decision: Implement department-based visibility and approval security for supply requests - users should only see/approve requests from their department unless they're admin/ga admin.
-   Solution: Added `canViewAllDepartments()` helper to User model checking for admin/ga admin roles, implemented department filtering in SupplyRequestController index method, added security checks in approveDeptHead and rejectDeptHead methods preventing cross-department approvals.
-   Key Learning: Explicit role checks using `canViewAllDepartments()` pattern centralizes authorization logic, department filtering at query level ensures data never reaches unauthorized users, security checks in action methods provide defense-in-depth against potential exploits.

### [MEM-019] Department Filtering Browser Testing & Data Integrity Fix (2025-01-31) ✅ COMPLETE

-   Challenge/Decision: Validate department-based filtering with real browser testing, discovered Finance users incorrectly assigned to department 7 (Design & Construction) instead of department 8 (Finance).
-   Solution: Conducted comprehensive browser tests with Finance Employee and Finance Dept Head roles, verified filtering works correctly, discovered and fixed department_id mismatch by updating user assignments to correct department.
-   Key Learning: Browser testing with realistic user data reveals data integrity issues that schema/unit tests miss, always verify seed data matches intended test scenarios, department_id validation critical for proper access control.

### [MEM-020] Stock Opname Approval Workflow Fix (2025-01-31) ✅ COMPLETE

-   Challenge/Decision: Stock opname approval workflow failed with 422 error due to database enum constraints not allowing 'adjustment' type and 'stock_opname' source in supply_transactions.
-   Solution: Created migrations to extend enum types: added 'adjustment' to `type` enum (in, out, adjustment) and 'stock_opname' to `source` enum (SAP, manual, stock_opname), added eager loading of items.supply before approval in controller.
-   Key Learning: When adding new transaction types/sources, must create database migrations to extend enum constraints. Eager loading relationships before processing prevents N+1 queries and ensures data availability.

### [MEM-021] Controller Module-Based Reorganization (2025-11-01) ✅ COMPLETE

-   Challenge/Decision: Controllers accumulated in single Admin folder making navigation/maintenance difficult; needed scalable structure aligned with business modules.
-   Solution: Reorganized 24 controllers into module directories: Admin (4), OfficeSupplies (9), PropertyManagement (3), Vehicle (4), TicketReservation (1), Common (1); updated all namespaces and route references in routes/web.php.
-   Key Learning: Module-based organization improves scalability and maintainability for growing applications; reflects business structure; supports future module extraction. Route references must be updated systematically when reorganizing controllers.

### [MEM-022] Views Module-Based Reorganization (2025-11-02) ✅ COMPLETE

-   Challenge/Decision: Views remained in single admin/ folder after controller reorganization, creating inconsistency and making module-specific views difficult to locate.
-   Solution: Reorganized 59+ view files into module directories matching controller structure: admin/, office-supplies/, property-management/, vehicle/, ticket-reservation/, common/; updated all controller view() references and view includes (e.g., profile.partials._ → common.profile.partials._).
-   Key Learning: Views should align with controller organization for consistency and maintainability; kebab-case directory naming (office-supplies vs office_supplies) follows Laravel conventions; systematic update of both controller references and view includes critical for successful reorganization.

### [MEM-023] Supply Fulfillment Testing & Department Assignment Fix (2025-11-03) ✅ COMPLETE

-   Challenge/Decision: Fulfillment testing revealed users without department assignments blocking distribution creation. supply_distributions.department_id is NOT NULL but users/requests lacked departments.
-   Solution: Updated DatabaseSeeder to assign default departments to all users (General Department id=1), corrected department IDs for Finance (8) and IT (17), ran migrate:fresh to apply clean state. Tested end-to-end: created request, approved via dept head & GA admin, partial fulfillment (5 of 10 units), verified stock reduction, distribution record, transaction record, request status update.
-   Key Learning: Department assignment is critical for fulfillment workflows. All users must have departments to enable proper data integrity in distribution tracking. Partial fulfillment correctly updates fulfilled_quantity and request status to 'partially_fulfilled', remaining 5 units can be fulfilled later.

### [MEM-024] Partial Fulfillment Workflow Fix (2025-11-03) ✅ COMPLETE

-   Challenge/Decision: Partially fulfilled requests not appearing in fulfillment queue and fulfill quantity showing wrong value (approved instead of remaining).
-   Solution: Updated SupplyFulfillmentController@index to include 'partially_fulfilled' status in WHERE IN clause; updated SupplyRequest@canBeFulfilled() to check for both 'approved' and 'partially_fulfilled'; updated fulfillment show view to calculate remaining = approved_quantity - fulfilled_quantity and display remaining quantity in Fulfill Qty field.
-   Key Learning: Partially fulfilled requests need to remain visible in fulfillment queue until completion. Fulfill quantity field must show remaining quantity (approved - fulfilled), not approved quantity, to prevent over-fulfillment.

### [MEM-025] Modal DataTable Supply Selection (2025-11-03) ✅ COMPLETE

-   Challenge/Decision: Regular select dropdown inefficient for 172+ inventory items; needed searchable, sortable interface for better UX when selecting supplies in request form.
-   Solution: Replaced select dropdown with Bootstrap modal containing DataTable. Added suppliesData() controller method with server-side processing, created modal with 25 items/page, search, sort capabilities. Fixed route order issue (supplies-data must come before supplies resource to avoid 404). Changed @push('scripts') to @push('js') for proper script loading.
-   Key Learning: Modal with DataTable provides efficient searchable interface for large item lists. Route ordering critical - specific routes (requests/supplies-data) must come before parameterized resource routes (supplies/{supply}) to prevent conflicts. Use @push('js') not @push('scripts') with Laravel stacks.

### [MEM-026] Supply Request UX Improvements (2025-11-03) ✅ COMPLETE

-   Challenge/Decision: Supply request forms needed confirmation dialog to prevent accidental submissions, and stock validation was too restrictive for planning purposes.
-   Solution: Added SweetAlert confirmation dialogs before submit (both create and edit forms). Removed `quantity-input.attr('max', stock)` validation - users can now request any quantity. Stock validation moved to fulfillment process where it belongs. Fixed edit.blade.php script loading directive.
-   Key Learning: Stock validation should occur at fulfillment/transaction time, not at request creation. Requests are planning documents; stock may change between request and fulfillment. SweetAlert confirms prevent accidental submissions and improve data quality.

### [MEM-027] Roles DataTable Index Column Fix (2025-11-07) ✅ COMPLETE

**Challenge**: Roles index view showed blank numbering because DataTables expected DT_RowIndex but server response omitted it and scripts loaded on wrong stack.
**Solution**: Switched Blade stack to `@push('js')`, added `addIndexColumn()` in `RoleController@index`, and updated DataTable config to consume `DT_RowIndex` with default ordering.
**Key Learning**: Yajra DataTables requires `addIndexColumn()` when the UI expects sequential numbering; Blade stack names must align with layout `@stack` directives to ensure scripts execute.

### [MEM-028] Meeting Room Reservation Workflow Launch (2025-11-06) ⚠️ PARTIAL

**Challenge**: Deliver a meeting room booking system with department approvals, GA allocation, and catering requests while reusing AdminLTE patterns.  
**Solution**: Implemented `MeetingRoomReservationController` with two-step approvals, room availability checks, and modal-driven actions; added `MeetingRoom`, `MeetingConsumptionRequest`, and supporting views with server-side DataTables.  
**Key Learning**: Centralising department scoping via `User::canViewAllDepartments()` simplifies controller logic, but notification dispatch and consumption fulfilment must be addressed next to complete the workflow.

### [MEM-030] ArkFleet Sync Queue & Permissions (2025-11-08) ✅ COMPLETE

-   Challenge/Decision: Needed to prevent long-running ArkFleet sync requests and surface controlled bulk sync actions.  
-   Solution: Introduced `ArkFleetSyncJob` to execute `ArkFleetSyncService` in the queue, updated `VehicleImportController` to dispatch jobs and parse manual unit lists, added vehicles index modal/buttons, and seeded new `import vehicles`/`sync vehicles` permissions.  
-   Key Learning: Offloading sync to the queue keeps UI responsive and simplifies future scheduling/retry strategies; guarding routes with dedicated permissions ensures only GA Admin/Admin can trigger large imports.

### [MEM-029] ArkFleet Integration Service Layer & Schema Update (2025-11-08) ✅ COMPLETE

-   Challenge/Decision: Needed reliable scaffolding to ingest ARKFleet vehicles without disrupting existing vehicle features.
-   Solution: Extended `vehicles` table (unit_no, plant_group, sync metadata, payload JSON), added ArkFleet service trio (API, import, sync) with shared mapping trait and status normalisation, stored raw payloads for audit, and deactivated units missing from the latest sync.
-   Key Learning: Centralizing mapping logic and persisting source payloads simplifies reconciliation and future UI work; marking missing units during sync prevents stale records but requires queue orchestration to avoid overlapping jobs.

### [MEM-031] Vehicle Index Unit No Column (2025-11-08) ✅ COMPLETE

-   Challenge/Decision: GA admins need to confirm ARKFleet unit numbers on the local vehicles grid before initiating sync/import troubleshooting.
-   Solution: Updated `VehicleController@index` JSON payload and vehicles index DataTable to surface `unit_no`, realigned filters with the new column order, and refreshed UI plan/docs to track remaining sync metadata work.
-   Key Learning: Keeping ARKFleet identifiers visible in core lists avoids context switching to the import screen; when adjusting DataTables column order, update filter indices and documentation together to prevent regressions.

### [MEM-032] Preserve ARKFleet License Plate Mapping (2025-11-08) ✅ COMPLETE

-   Challenge/Decision: Import routine was substituting `unit_no` for `nomor_polisi` when ARKFleet omitted the plate, leading to mismatched data between systems.
-   Solution: Adjusted `MapsArkFleetVehicles` to read both snake_case and camelCase payload keys and stop auto-generating fallback values so GENAF mirrors ARKFleet exactly.
-   Key Learning: External identifiers must remain authoritative—avoid fabricating stand-ins that can mask missing data and break reconciliation workflows.

### [MEM-033] Vehicle Index Current Project Column (2025-11-08) ✅ COMPLETE

-   Challenge/Decision: GA admins needed visibility into ARKFleet project assignments inside the core vehicles grid to validate sync decisions without leaving the page.
-   Solution: Extended the vehicles index API response and DataTable to include `current_project_code`, adjusted column order, and kept filter bindings accurate after the insert.
-   Key Learning: When enriching client-side DataTables, audit filter indices and documentation simultaneously to prevent subtle UI regressions as column counts change.

### [MEM-034] Vehicle Index Sync Indicators (2025-11-08) ✅ COMPLETE

-   Challenge/Decision: Support staff needed at-a-glance insight into each vehicle’s ARKFleet sync health without drilling into logs.
-   Solution: Exposed raw sync status/message fields through the index endpoint, rendered pill badges with severity colouring, and formatted `arkfleet_synced_at` with human-readable + absolute timestamps.
-   Key Learning: Pairing relative and absolute timestamps keeps operators informed about freshness while badge colours surface error states immediately; always pass both raw and display values to DataTables renderers for consistent sorting.

### [MEM-035] Vehicle Document Data Model Foundation (2025-11-08) ✅ COMPLETE

-   Challenge/Decision: Existing vehicle document storage lacked typed metadata, renewal history, and reminder-friendly fields.
-   Solution: Introduced `vehicle_document_types`, remodelled `vehicle_documents` with document number/dates/supplier/amount, added revision audit table, automatic revision snapshots, and seeded STNK/KIR defaults.
-   Key Learning: Capturing renewals as immutable snapshots (with user attribution) sets the stage for expiry monitoring and UI tooling—ensure migrations backfill legacy data and factories/tests cover the new relationships early.

### [MEM-036] Vehicle Documents UI & History Modal (2025-11-08) ✅ COMPLETE

-   Challenge/Decision: GA admins needed a single place inside the vehicle profile to view, add, and extend STNK/KIR records while seeing prior renewals.
-   Solution: Added Documents tab with status badges, modal-driven create/edit flow, downloadable attachments, and a revision history modal sourced from the new audit table; wired update endpoint + permissions and expanded feature tests.
-   Key Learning: Precomputing document metadata (status labels, formatted URLs, revisions) in the controller simplified Blade + JS logic; consolidating create/edit into one modal required hidden context inputs to restore form state after validation failures.

### [MEM-037] Local Login Seed & Session Fix (2025-11-08) ✅ COMPLETE

-   Challenge/Decision: Browser-based QA blocked because seeded credentials didn’t match the documented `superadmin / 20132013` account and the session cookie shipped with `secure` enabled, triggering repeated 419s over HTTP.
-   Solution: Updated `DatabaseSeeder` to create a `superadmin` user (and other fixtures) with password `20132013`, refreshed the vehicle sample data to mirror ArkFleet attributes, defaulted the session secure-cookie flag to `false` for local use, and added a dev-only `/dev-login` helper route for quick manual sign-in.
-   Key Learning: Aligning seed data with expected QA credentials eliminates wasted debugging cycles; local environments must disable secure-only cookies when testing over plain HTTP, and a guarded helper route accelerates smoke tests without touching production configs.
