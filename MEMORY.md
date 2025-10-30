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

### [MEM-013] Stock Opname Module with Gradual Counting (2025-01-25) ⚠️ PARTIAL

**Challenge**: Implement physical inventory count system with gradual counting, save progress, and complete later functionality
**Solution**: Created StockOpnameSession/Items models with status workflow (pending → counting → counted → verified), implemented saveDraft and finalizeCount methods, added photo evidence upload, variance calculation with reason codes, automatic stock adjustment after approval
**Key Learning**: Gradual counting with draft save enables realistic stock opname workflows - items can be in 'counting' status (work-in-progress) before finalization, concurrent counting supported, status-based workflow provides clear progress tracking

### [MEM-014] User Schema & Login Update (2025-10-30) ✅ COMPLETE

-   Challenge/Decision: Add `username` (nullable, unique) and `nik` (nullable, unique); change `department` to `department_id` FK; enable login via email or username; remove legacy `role` enum.
-   Solution: Updated base users migration; requests validation; controller and views; login detection in `LoginRequest`; updated seeder; guarded later migrations (drop role, add nik, add department_id) to be idempotent; deferred FK creation where needed.
-   Key Learning: When modifying base tables, later delta migrations must guard for presence/absence to support migrate:fresh; FK constraints must respect creation order or be added after both tables exist.

### [MEM-015] Ticket Reservations Module Implementation (2025-10-30) ✅ COMPLETE

**Challenge**: Build ticket reservation system with approval workflow, document management, and DataTables integration  
**Solution**: Created TicketReservation and ReservationDocument models, TicketReservationController with CRUD and workflow methods (approve, reject, markBooked, markCompleted), implemented file upload/delete, added 12 permissions, created DataTables index with advanced filtering, fixed FK relationship issue (`reservation_id` vs `ticket_reservation_id`)  
**Key Learning**: Eloquent relationship foreign keys must be explicitly specified when naming differs from convention (e.g., `reservation_id` not `ticket_reservation_id`). Removed eager loading of problematic relationships from DataTables queries to prevent FK errors.