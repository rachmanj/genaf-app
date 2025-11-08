**Purpose**: Future features and improvements prioritized by value
**Last Updated**: [Auto-updated by AI]

# Feature Backlog

## Next Sprint (High Priority)

### ArkFleet Import & Sync Notifications

- **Description**: Extend queued ArkFleet sync jobs with user-facing progress (notifications/dashboard widget) and audit trail, plus integrate live API smoke testing results.
- **User Value**: GA Admins receive confirmation when long-running sync jobs finish and can review outcomes without checking logs.
- **Effort**: Medium.
- **Dependencies**: Queue worker monitoring, finalised notification channels.
- **Files Affected**: `app/Jobs/ArkFleetSyncJob.php`, `app/Notifications/*`, `resources/views/vehicle/*`, `docs/vehicle-arkfleet-integration-plan.md`.

### Meeting Reservation Notifications

- **Description**: Dispatch email/in-app notifications on approval, rejection, allocation, and response.
- **User Value**: Requestors receive timely updates, reducing manual follow-up.
- **Effort**: Medium.
- **Dependencies**: Decide on notification channels; GA Admin copy.
- **Files Affected**: `MeetingRoomReservationController`, new `Notifications/*` classes, queue configuration.

### Consumption Fulfillment Console

- **Description**: GA Ops UI to mark catering requests as fulfilled with notes/evidence.
- **User Value**: Tracks delivery of coffee breaks/lunch; supports reconciliation.
- **Effort**: Medium.
- **Dependencies**: Notification work (above) for escalations.
- **Files Affected**: `meeting-room-reservation/reservations/show.blade.php`, new controller methods, routes.

## Upcoming Features (Medium Priority)

### PMS Dashboard Enhancements

- **Description**: Visual occupancy trendlines, maintenance cost breakdowns, and per-building widgets.
- **Effort**: Medium.
- **Value**: GA leadership gains clarity on room utilisation.

### Vehicle Preventive Maintenance Scheduler

- **Description**: Auto-calculate next service date/odometer and alert GA Admin dashboard.
- **Effort**: Medium.
- **Value**: Reduces missed maintenance events and downtime.

### User Self-Service Profile Requests

- **Description**: Allow employees to request department changes or account deactivation with approval workflow.
- **Effort**: Medium.
- **Value**: Offloads GA Admin for simple account updates.

## Ideas & Future Considerations (Low Priority)

### Slack/Teams Notification Bridge

- **Concept**: Send meeting/vehicle alerts to corporate chat channels.
- **Potential Value**: Real-time visibility for GA and department heads.
- **Complexity**: Medium (webhook integration + message templates).

### Analytics Warehouse Export

- **Concept**: Nightly export of reservations, supplies, and vehicles data to a BI warehouse.
- **Potential Value**: Enables long-term trend analysis beyond in-app dashboards.
- **Complexity**: Large (ETL, scheduling, infra).

## Technical Improvements

### Performance & Code Quality

- Replace vehicle/fuel/maintenance AJAX endpoints with server-side DataTables + pagination - Impact: Medium
- Extract shared approval helpers into traits/service classes - Effort: Medium

### Infrastructure

- Introduce queue workers for notifications and ARKFleet sync retries
- Add Playwright regression suite for meeting reservations and PMS dashboard
