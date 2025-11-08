# ARKFleet Vehicle Integration Plan

## Overview

Integrate ARKFleet equipment data into the GENAF Vehicle Administration module so that GA Admin users can browse ARKFleet vehicles, import selected units into the local database, and perform manual synchronisation to keep status and project information current.

## Requirements Recap

-   **Primary identifier**: `unit_no`
-   **Local schema remap**:
    -   `nomor_polisi` → `nomor_polisi`
    -   `unit_no` → `unit_no`
    -   `description` → parsed into `brand` + `model` (or stored directly in `model`)
    -   `plant_group` → `plant_group`
    -   `unitstatus` → local `status` (`RFU` → active, `RFM` → maintenance, `SCRAP`/`SOLD` → retired)
    -   `active_date` → derive `year`
    -   `remarks` → `remarks`
-   **Sync rules**:
    -   Vehicles missing from ARKFleet response are marked inactive locally.
    -   Sync updates only current project assignment and status.
    -   Manual sync trigger only.
-   **UI expectations**:
    -   Separate “Import from ARKFleet” page with filters and bulk import.
    -   Vehicles index shows sync status (last sync timestamp, errors).

## Architecture Concept

```
ARKFleet API (GET /api/equipments)
        ↓
Import UI (browse/search & bulk select)
        ↓
ArkFleetImportService → local vehicles table (with arkfleet metadata)
        ↓
Manual Sync (ArkFleetSyncService)
        ↓
Local Vehicles (manage fuel, maintenance, documents in GENAF)
```

## Recommendations

-   Recreate the local `vehicles` table to mirror ARKFleet fields and store sync metadata.
-   Store each vehicle’s last ARKFleet payload (JSON) for troubleshooting.
-   Use service classes (`ArkFleetApiService`, `ArkFleetImportService`, `ArkFleetSyncService`) to isolate integration logic.
-   Provide manual “Import” and “Sync” actions in the UI with clear status indicators.
-   Log sync errors per vehicle and allow retry.

## Implementation Plan

### Phase 1 – Schema & Model

**Status**: ✅ Completed 2025-11-08 – `vehicles` table extended (unit_no, plant_group, sync metadata, payload JSON) and `Vehicle` model updated with fillable/casts to support ArkFleet services.

1. Create migration to rebuild `vehicles` table with new columns:
    - `unit_no` (unique), `nomor_polisi`, `brand`, `model`, `plant_group`, `status`, `year`, `remarks`, `current_project_code`, `is_active`.
    - Sync metadata: `arkfleet_synced_at`, `arkfleet_sync_status`, `arkfleet_last_payload` (JSON).
2. Update `Vehicle` model (fillable, casts, helpers, scopes).

### Phase 2 – ARKFleet API Infrastructure

1. Configure `.env` (`ARKFLEET_API_URL`).
2. Implement `ArkFleetApiService` for list retrieval with filters and single `unit_no` lookups.

### Phase 3 – Import Workflow

**Status**: ⚠️ In Progress – Import page now pulls filtered ARKFleet data, renders DataTable with multi-select, and posts selected `unit_no` values through `VehicleImportController@store` into `ArkFleetImportService`; background job/offline handling still pending.

1. Add `VehicleImportController@index` for browsing ARKFleet data (DataTable with filters: project_code, status, plant_group).
2. Implement `VehicleImportController@store` for bulk import using `ArkFleetImportService`.
3. Map fields according to requirements and set `arkfleet_last_payload`.

### Phase 4 – Sync Workflow

**Status**: ⚠️ In Progress – Manual sync actions wired via `VehicleImportController@syncSelected` and `@syncAll`, guarded by `sync vehicles` permission and now enqueued through `ArkFleetSyncJob`; still need additional UX polish and completion notifications.

1. Add manual sync actions (e.g., `VehicleController@sync` and `VehicleController@syncSelected`).
2. Implement `ArkFleetSyncService`:
    - Fetch latest ARKFleet data per `unit_no`.
    - Update current project code & status; keep local-only fields (e.g., odometer).
    - Mark vehicles missing from ARKFleet as `is_active = false`.
    - Record `arkfleet_synced_at`, `arkfleet_sync_status`, and errors.

### Phase 5 – UI Enhancements

**Status**: ⚠️ In Progress – Vehicles index now displays `unit_no`, `current_project_code`, `arkfleet_sync_status`, and `arkfleet_synced_at` with indicator badges; polishing explanatory tooltips/messages still outstanding.

1. Vehicles index: add columns for `unit_no` ✅, `current_project_code` ✅, `arkfleet_sync_status` ✅, `arkfleet_synced_at` ✅.
2. Add buttons: “Import from ARKFleet”, “Sync All”, “Sync Selected” (vehicles index now exposes queue-backed sync actions with modal entry for unit numbers).
3. Display sync badges (e.g., success, warning, error) and tooltips for error messages.

### Phase 6 – Testing & Documentation

1. Manual testing: import flow, sync updates, inactive marking, error handling.
2. Add automated feature tests mocking ArkFleet API and queue assertions (✅ created in `tests/Feature/VehicleImportControllerTest.php`).
3. Update documentation: architecture, ADR, todo backlog, memory entries, and user guide if applicable.

## Outstanding Clarifications (closed)

-   Mapping rules, sync behavior, UI flow, and manual sync frequency confirmed by GA Admin stakeholders.
