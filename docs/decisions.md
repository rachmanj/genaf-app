# Architectural Decision Records (ADR)

## Purpose

This document records important architectural decisions made during the development of the GENAF Enterprise Management System. Each decision includes the context, alternatives considered, and the rationale for the chosen approach.

## Decision Template

Each decision follows this structure:

-   **Date**: When the decision was made
-   **Status**: Proposed, Accepted, Deprecated, Superseded
-   **Context**: The situation that led to this decision
-   **Decision**: What was decided
-   **Consequences**: Positive and negative outcomes
-   **Review Date**: When to revisit this decision

---

## ADR-001: UI Framework Selection - AdminLTE 3.x

**Date**: January 2025  
**Status**: Accepted  
**Context**: Need to choose a UI framework for the enterprise management system that provides professional appearance, comprehensive components, and easy integration with Laravel.

**Decision**: Use AdminLTE 3.x as the primary UI framework instead of Tailwind CSS + DaisyUI/Flowbite.

**Alternatives Considered**:

1. **Tailwind CSS + DaisyUI/Flowbite**: Modern utility-first CSS framework
2. **Bootstrap 5**: Popular CSS framework
3. **AdminLTE 3.x**: Admin dashboard template with Bootstrap 4

**Rationale**:

-   AdminLTE provides a complete admin dashboard solution out of the box
-   Professional appearance suitable for enterprise applications
-   Comprehensive component library (cards, tables, forms, modals)
-   Built-in responsive design
-   Easy integration with Laravel Blade templates
-   Extensive documentation and community support
-   Already had AdminLTE assets installed in the project

**Consequences**:

-   ✅ **Positive**: Faster development with pre-built components
-   ✅ **Positive**: Consistent professional appearance
-   ✅ **Positive**: Comprehensive feature set (DataTables, charts, etc.)
-   ✅ **Positive**: Mobile-responsive design
-   ⚠️ **Negative**: Bootstrap 4 instead of Bootstrap 5 (older version)
-   ⚠️ **Negative**: Less customization flexibility compared to utility-first CSS

**Review Date**: March 2025

---

## ADR-002: DataTables Integration for List Pages

**Date**: January 2025  
**Status**: Accepted  
**Context**: Need efficient data display for large datasets with search, sort, and pagination capabilities.

**Decision**: Use DataTables with server-side processing for all list pages.

**Alternatives Considered**:

1. **Client-side pagination**: Load all data and paginate in browser
2. **Laravel pagination**: Standard Laravel pagination
3. **DataTables server-side**: AJAX-based server-side processing

**Rationale**:

-   Server-side processing handles large datasets efficiently
-   Built-in search, sort, and pagination functionality
-   Professional appearance with AdminLTE integration
-   Reduces server load and improves performance
-   Consistent user experience across all list pages
-   Easy to implement with Laravel controllers

**Consequences**:

-   ✅ **Positive**: Better performance with large datasets
-   ✅ **Positive**: Professional table interface
-   ✅ **Positive**: Built-in search and filtering
-   ✅ **Positive**: Consistent user experience
-   ⚠️ **Negative**: Additional JavaScript dependency
-   ⚠️ **Negative**: More complex controller logic

**Review Date**: March 2025

---

## ADR-003: Role-Based Access Control Implementation

**Date**: January 2025  
**Status**: Accepted  
**Context**: Need comprehensive permission system for enterprise application with granular access control.

**Decision**: Use Spatie Laravel Permission package for role-based access control.

**Alternatives Considered**:

1. **Custom RBAC implementation**: Build permission system from scratch
2. **Laravel Gates and Policies**: Native Laravel authorization
3. **Spatie Laravel Permission**: Third-party package

**Rationale**:

-   Spatie package provides comprehensive RBAC functionality
-   Well-maintained and widely used in Laravel community
-   Supports roles, permissions, and model-specific permissions
-   Easy integration with Blade templates using `@can` directives
-   Database-driven permission system
-   Built-in caching for performance

**Consequences**:

-   ✅ **Positive**: Comprehensive permission system
-   ✅ **Positive**: Easy Blade template integration
-   ✅ **Positive**: Database-driven and cacheable
-   ✅ **Positive**: Well-documented and maintained
-   ⚠️ **Negative**: Additional package dependency
-   ⚠️ **Negative**: Learning curve for team members

**Review Date**: March 2025

---

## ADR-004: Notification System Implementation

**Date**: January 2025  
**Status**: Accepted  
**Context**: Need user-friendly notification system for success/error messages and confirmations.

**Decision**: Use Toastr for notifications and SweetAlert2 for confirmations.

**Alternatives Considered**:

1. **Laravel flash messages**: Simple session-based messages
2. **Bootstrap alerts**: Basic alert components
3. **Toastr + SweetAlert2**: Professional notification libraries

**Rationale**:

-   Toastr provides elegant toast notifications
-   SweetAlert2 offers beautiful confirmation dialogs
-   Both integrate well with AdminLTE
-   Better user experience than basic alerts
-   Consistent styling with the admin theme
-   Easy to implement and customize

**Consequences**:

-   ✅ **Positive**: Professional notification appearance
-   ✅ **Positive**: Better user experience
-   ✅ **Positive**: Easy to implement
-   ✅ **Positive**: Consistent with AdminLTE theme
-   ⚠️ **Negative**: Additional JavaScript dependencies
-   ⚠️ **Negative**: More complex than basic alerts

**Review Date**: March 2025

---

## ADR-005: Form Enhancement with Select2

**Date**: January 2025  
**Status**: Accepted  
**Context**: Need enhanced dropdown functionality for better user experience in forms.

**Decision**: Use Select2 for enhanced dropdowns throughout the application.

**Alternatives Considered**:

1. **Standard HTML select**: Basic dropdown functionality
2. **Bootstrap select**: Bootstrap-styled dropdowns
3. **Select2**: Enhanced dropdown with search and AJAX support

**Rationale**:

-   Select2 provides search functionality in dropdowns
-   Supports AJAX loading for large datasets
-   Consistent styling with AdminLTE
-   Better user experience for large option lists
-   Easy to implement and customize
-   Widely used and well-documented

**Consequences**:

-   ✅ **Positive**: Better user experience for large lists
-   ✅ **Positive**: Search functionality in dropdowns
-   ✅ **Positive**: AJAX support for dynamic loading
-   ✅ **Positive**: Consistent styling
-   ⚠️ **Negative**: Additional JavaScript dependency
-   ⚠️ **Negative**: Slightly more complex implementation

**Review Date**: March 2025

---

## ADR-006: JavaScript Stack Selection

**Date**: January 2025  
**Status**: Accepted  
**Context**: Need JavaScript libraries for enhanced functionality while maintaining compatibility with AdminLTE.

**Decision**: Use jQuery-based libraries (DataTables, SweetAlert2, Toastr, Select2) instead of modern JavaScript frameworks.

**Alternatives Considered**:

1. **Modern JavaScript frameworks**: Vue.js, React, Alpine.js
2. **jQuery-based libraries**: Traditional jQuery ecosystem
3. **Vanilla JavaScript**: Pure JavaScript implementation

**Rationale**:

-   AdminLTE is built on jQuery and Bootstrap
-   jQuery-based libraries integrate seamlessly
-   Faster development with existing AdminLTE components
-   No need for complex build processes
-   Easier to maintain and debug
-   Team familiarity with jQuery

**Consequences**:

-   ✅ **Positive**: Seamless integration with AdminLTE
-   ✅ **Positive**: Faster development
-   ✅ **Positive**: No complex build processes
-   ✅ **Positive**: Easy to maintain
-   ⚠️ **Negative**: Not using modern JavaScript features
-   ⚠️ **Negative**: Larger bundle size compared to modern alternatives

**Review Date**: March 2025

---

## ADR-007: Database Schema Design for Permissions

**Date**: January 2025  
**Status**: Accepted  
**Context**: Need comprehensive permission system covering all modules and future extensibility.

**Decision**: Implement 58 comprehensive permissions covering all current and planned modules.

**Alternatives Considered**:

1. **Minimal permissions**: Only basic CRUD permissions
2. **Module-specific permissions**: Permissions only for implemented modules
3. **Comprehensive permissions**: All permissions for current and planned modules

**Rationale**:

-   Comprehensive permission system provides future-proofing
-   Easier to implement new modules with existing permissions
-   Better security with granular access control
-   Consistent permission naming convention
-   Easier to understand and maintain
-   Supports role hierarchy (Admin > Manager > Employee)

**Consequences**:

-   ✅ **Positive**: Future-proof permission system
-   ✅ **Positive**: Granular access control
-   ✅ **Positive**: Consistent naming convention
-   ✅ **Positive**: Easy to extend for new modules
-   ⚠️ **Negative**: More complex initial setup
-   ⚠️ **Negative**: More permissions to manage

**Review Date**: March 2025

---

## ADR-008: Controller Architecture Pattern

**Date**: January 2025  
**Status**: Accepted  
**Context**: Need consistent controller structure for maintainable and scalable code.

**Decision**: Use Laravel Resource Controllers with permission checks and validation.

**Alternatives Considered**:

1. **Custom controller methods**: Non-standard controller structure
2. **API Resource Controllers**: RESTful API controllers
3. **Resource Controllers**: Standard Laravel resource controllers

**Rationale**:

-   Resource controllers follow Laravel conventions
-   Consistent CRUD operations across all modules
-   Easy to understand and maintain
-   Built-in route model binding
-   Standard HTTP methods and status codes
-   Easy to add API endpoints later

**Consequences**:

-   ✅ **Positive**: Consistent code structure
-   ✅ **Positive**: Follows Laravel conventions
-   ✅ **Positive**: Easy to maintain and extend
-   ✅ **Positive**: Built-in route model binding
-   ✅ **Positive**: Standard HTTP methods
-   ⚠️ **Negative**: Less flexibility for custom operations

**Review Date**: March 2025

---

## ADR-009: View Architecture Pattern

**Date**: January 2025  
**Status**: Accepted  
**Context**: Need consistent view structure for maintainable and reusable templates.

**Decision**: Use Blade templates with AdminLTE components and standard patterns.

**Alternatives Considered**:

1. **Component-based views**: Vue.js or React components
2. **Template inheritance**: Blade template inheritance
3. **Include-based views**: Blade includes and partials

**Rationale**:

-   Blade templates integrate well with Laravel
-   AdminLTE provides consistent components
-   Template inheritance reduces code duplication
-   Easy to maintain and debug
-   No additional build processes required
-   Team familiarity with Blade syntax

**Consequences**:

-   ✅ **Positive**: Consistent component usage
-   ✅ **Positive**: Reduced code duplication
-   ✅ **Positive**: Easy to maintain
-   ✅ **Positive**: No build processes
-   ✅ **Positive**: Team familiarity
-   ⚠️ **Negative**: Less dynamic than component frameworks

**Review Date**: March 2025

---

## ADR-010: Testing Strategy

**Date**: January 2025  
**Status**: Accepted  
**Context**: Need comprehensive testing approach for enterprise application.

**Decision**: Use browser automation testing with Playwright for end-to-end testing.

**Alternatives Considered**:

1. **Unit testing**: PHPUnit for backend testing only
2. **Feature testing**: Laravel feature tests
3. **Browser automation**: Playwright for E2E testing

**Rationale**:

-   Browser automation tests real user interactions
-   Catches integration issues between frontend and backend
-   Tests complete user workflows
-   Validates UI functionality and responsiveness
-   Easy to maintain and extend
-   Provides confidence in production deployment

**Consequences**:

-   ✅ **Positive**: Tests real user interactions
-   ✅ **Positive**: Catches integration issues
-   ✅ **Positive**: Validates complete workflows
-   ✅ **Positive**: Tests UI functionality
-   ⚠️ **Negative**: Slower than unit tests
-   ⚠️ **Negative**: More complex to set up

**Review Date**: March 2025

---

## ADR-011: Office Supplies Route Hierarchy

**Date**: January 2025  
**Status**: Accepted  
**Context**: Need to determine proper route hierarchy for supplies-related functionality.

**Decision**: Use `/supplies` as base route with nested routes for related functionality (`/supplies/requests`, `/supplies/transactions`, etc.).

**Alternatives Considered**:

1. **Separate top-level routes**: `/supplies`, `/requests`, `/transactions` as separate routes
2. **Admin-prefixed routes**: `/admin/supplies/requests` under admin prefix
3. **Nested under supplies**: `/supplies/requests` with logical hierarchy

**Rationale**:

-   Supplies management is not exclusively admin functionality - managers and employees also access it
-   Nested routes create logical hierarchy and organization
-   Clear separation of concerns (requests, transactions, fulfillment)
-   Easy to extend with additional nested functionality
-   Better URL structure for user understanding

**Consequences**:

-   ✅ **Positive**: Logical route hierarchy
-   ✅ **Positive**: Clear organization of functionality
-   ✅ **Positive**: Better URL structure
-   ✅ **Positive**: Scalable for future additions
-   ⚠️ **Negative**: Must manage route ordering carefully to prevent conflicts

**Review Date**: March 2025

---

## ADR-012: Two-Level Approval Workflow for Supply Requests

**Date**: January 2025  
**Status**: Accepted  
**Context**: Need realistic approval workflow for supply requests that reflects enterprise organizational structure.

**Decision**: Implement two-level approval workflow: Department Head → GA Admin.

**Alternatives Considered**:

1. **Single approval**: GA Admin only
2. **Three-level approval**: Employee → Dept Head → Manager → GA Admin
3. **Two-level approval**: Dept Head → GA Admin

**Rationale**:

-   Reflects realistic organizational structure
-   Department Head approves requests from their department
-   GA Admin has final approval and fulfillment authority
-   Balanced between approval rigor and process efficiency
-   Supports department-based accountability

**Consequences**:

-   ✅ **Positive**: Realistic workflow matching organizational structure
-   ✅ **Positive**: Department-based accountability
-   ✅ **Positive**: Clear approval chain
-   ✅ **Positive**: Flexible status tracking (pending_dept_head, pending_ga_admin)
-   ⚠️ **Negative**: More complex status management

**Review Date**: March 2025

---

## ADR-013: Partial Fulfillment Support

**Date**: January 2025  
**Status**: Accepted  
**Context**: Need to support realistic scenarios where stock may be insufficient for full request fulfillment.

**Decision**: Implement partial fulfillment with tracking of approved_quantity, fulfilled_quantity, and fulfillment_status per item.

**Alternatives Considered**:

1. **All-or-nothing**: Either fulfill complete request or reject
2. **Automatic partial**: Automatically fulfill available quantity
3. **Manual partial**: GA Admin manually decides fulfillment quantities

**Rationale**:

-   Realistic inventory scenarios (stock shortages, partial shipments)
-   Better customer service (fulfill available items instead of rejecting entire request)
-   Flexible request management
-   Clear tracking of fulfillment status per item
-   Supports department needs even with stock constraints

**Consequences**:

-   ✅ **Positive**: Realistic inventory management
-   ✅ **Positive**: Better customer service
-   ✅ **Positive**: Flexible fulfillment options
-   ✅ **Positive**: Clear item-level tracking
-   ⚠️ **Negative**: More complex fulfillment logic
-   ⚠️ **Negative**: Status management complexity (partially_fulfilled status)

**Review Date**: March 2025

---

## ADR-014: Stock Opname Gradual Counting Support

**Date**: January 2025  
**Status**: Accepted  
**Context**: Need to support realistic stock opname workflow where users count items gradually and save progress.

**Decision**: Implement gradual counting with status workflow: pending → counting → counted → verified.

**Alternatives Considered**:

1. **All-at-once**: Must count all items in single session
2. **Batch counting**: Count in fixed batches
3. **Gradual counting**: Count items individually, save progress, complete later

**Rationale**:

-   Realistic warehouse/inventory workflows
-   Supports large-scale stock opname sessions
-   Allows concurrent counting by multiple users
-   'Counting' status represents work-in-progress state
-   Draft save functionality enables progress preservation
-   Clear workflow progression

**Consequences**:

-   ✅ **Positive**: Realistic counting workflow
-   ✅ **Positive**: Supports large-scale inventory counts
-   ✅ **Positive**: Concurrent counting support
-   ✅ **Positive**: Progress preservation with draft save
-   ⚠️ **Negative**: More complex status management
-   ⚠️ **Negative**: Additional data persistence requirements

**Review Date**: March 2025

---

## ADR-015: User Table Enhancements (username, NIK, department_id)

- **Date**: 2025-10-30  
- **Status**: Accepted  
- **Context**: Need unique employee identifier and flexible login; align department to normalized schema.  
- **Decision**: Add nullable unique `username` and `nik`; replace `department` string with nullable `department_id` FK to `departments`. Remove legacy `role` enum in favor of Spatie roles.  
- **Alternatives**:
  1. Keep string `department` and legacy `role` enum
  2. Add only `nik` without `username`
  3. Full normalization with FK and optional `username` (chosen)
- **Consequences**:
  - ✅ Normalized relation to `departments`
  - ✅ Support enterprise identifiers via `nik`
  - ✅ Future-proof login via `username`
  - ⚠️ Requires migration order care (FKs after table creation)
- **Review Date**: 2025-12-15

---

## ADR-016: Login with Email or Username

- **Date**: 2025-10-30  
- **Status**: Accepted  
- **Context**: Users may prefer username or email for authentication.  
- **Decision**: Accept a single login field and detect if it is an email (contains '@'); if yes, authenticate with `email`, otherwise with `username`. Updated validation and UI placeholder accordingly.  
- **Consequences**:
  - ✅ Improved UX with flexible credentials
  - ✅ Minimal code changes scoped to `LoginRequest`
  - ⚠️ Must ensure unique indexes on both fields
- **Review Date**: 2025-12-15

---

## Summary

These architectural decisions form the foundation of the GENAF Enterprise Management System. They prioritize:

1. **Professional UI**: AdminLTE for enterprise-grade appearance
2. **Performance**: Server-side processing for large datasets
3. **Security**: Comprehensive RBAC with Spatie Laravel Permission
4. **User Experience**: Professional notifications and enhanced forms
5. **Maintainability**: Consistent patterns and Laravel conventions
6. **Quality**: Comprehensive testing with browser automation

All decisions are scheduled for review in March 2025 to ensure they continue to meet the project's needs as it evolves.

---

## ADR-017: Vehicle Administration Architecture

**Date**: 2025-10-30  
**Status**: Accepted  
**Context**: Introduce Vehicle Administration to manage vehicles, fuel, documents, and maintenance with alerts.  
**Decision**: Implement resource controllers (`Vehicle`, `FuelRecord`, `VehicleDocument`, `VehicleMaintenance`), global list routes for fuel and maintenance, secure document storage under `public` disk, and dashboard widgets for expiring documents (90 days) and upcoming services (30 days).  
**Alternatives Considered**:
1. Fully nested routes only under `/vehicles/{vehicle}`  
2. Single monolithic VehicleController handling all sub-resources  
3. Split controllers per sub-resource with global list routes (chosen)

**Rationale**:
- Clear separation of concerns and simpler list pages with global filters  
- Easier navigation via sidebar to fuel/maintenance without selecting a vehicle  
- Secure download via storage disk and permission `download vehicle documents`

**Consequences**:
- ✅ Simpler UX with direct access lists  
- ✅ Maintainable structure aligning with other modules  
- ⚠️ Additional route surface area  
- ⚠️ Requires consistent permission seeding and checks

**Review Date**: 2025-12-15

---

## ADR-018: Align Vehicle Pages with Supplies Layout and Client-side DataTables (Interim)

**Date**: 2025-10-30  
**Status**: Accepted  
**Context**: Vehicle pages initially had minimal markup and inconsistent stacks. Needed to match established AdminLTE pattern used by Supplies and provide filterable lists.  
**Decision**: Align Vehicles, Fuel Records, and Maintenance index views to supplies layout (breadcrumbs, card outline, icons, @push('js')). Implement client-side DataTables with an index column and responsive settings. Vehicles index includes Type/Status filters.  
**Alternatives Considered**:
1. Full server-side DataTables immediately  
2. Keep minimal tables without filters  
3. Client-side DataTables now, server-side later (chosen)

**Rationale**:
- Fast alignment with consistent UX across modules  
- Lower controller complexity while endpoints stabilize  
- Filters deliver immediate value for Vehicles index

**Consequences**:
- ✅ Consistent UI and script stacks  
- ✅ Usable filterable lists today  
- ⚠️ Client-side DataTables may not scale for very large datasets  
- ⚠️ Will require server-side endpoint changes when scaling

**Review Date**: 2025-12-15