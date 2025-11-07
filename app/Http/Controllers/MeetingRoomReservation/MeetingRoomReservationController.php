<?php

namespace App\Http\Controllers\MeetingRoomReservation;

use App\Http\Controllers\Controller;
use App\Models\MeetingRoom;
use App\Models\MeetingRoomReservation;
use App\Models\MeetingConsumptionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class MeetingRoomReservationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = MeetingRoomReservation::with([
                'requestor',
                'department',
                'requestedRoom',
                'allocatedRoom',
                'departmentHeadApprover',
                'gaAdminApprover'
            ])->select('meeting_room_reservations.*');

            // Filter by department for non-admin/ga-admin users
            if (!auth()->user()->canViewAllDepartments()) {
                $query->where('department_id', auth()->user()->department_id);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->where('meeting_date_start', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->where('meeting_date_start', '<=', $request->date_to);
            }

            // Limit to own reservations for employees
            if (Auth::user()->hasRole('employee') && !Auth::user()->hasRole('admin') && !Auth::user()->hasRole('ga admin')) {
                $query->where('requestor_id', Auth::id());
            }

            return DataTables::of($query)
                ->addColumn('index', function ($reservation) {
                    return '';
                })
                ->addColumn('form_number', function ($reservation) {
                    return $reservation->form_number ?? 'N/A';
                })
                ->addColumn('requestor_name', function ($reservation) {
                    return $reservation->requestor ? $reservation->requestor->name : 'N/A';
                })
                ->addColumn('department_name', function ($reservation) {
                    return $reservation->department ? $reservation->department->department_name : 'N/A';
                })
                ->addColumn('meeting_title', function ($reservation) {
                    return $reservation->meeting_title ?? 'N/A';
                })
                ->addColumn('requested_room', function ($reservation) {
                    return $reservation->requestedRoom ? $reservation->requestedRoom->name : 'N/A';
                })
                ->addColumn('allocated_room', function ($reservation) {
                    return $reservation->allocatedRoom ? $reservation->allocatedRoom->name : 'N/A';
                })
                ->addColumn('location', function ($reservation) {
                    return $reservation->location ?? 'N/A';
                })
                ->addColumn('meeting_dates', function ($reservation) {
                    $start = Carbon::parse($reservation->meeting_date_start)->format('d/m/Y');
                    if ($reservation->meeting_date_end && Carbon::parse($reservation->meeting_date_end)->notEqualTo(Carbon::parse($reservation->meeting_date_start))) {
                        $end = Carbon::parse($reservation->meeting_date_end)->format('d/m/Y');
                        return $start . ' - ' . $end;
                    }
                    return $start;
                })
                ->addColumn('meeting_time', function ($reservation) {
                    $start = Carbon::parse($reservation->meeting_time_start)->format('H:i');
                    $end = Carbon::parse($reservation->meeting_time_end)->format('H:i');
                    return $start . ' - ' . $end;
                })
                ->addColumn('participant_count', function ($reservation) {
                    return $reservation->participant_count ?? 'N/A';
                })
                ->addColumn('status_badge', function ($reservation) {
                    $badgeClass = match ($reservation->status) {
                        'pending_dept_head' => 'badge-warning',
                        'pending_ga_admin' => 'badge-info',
                        'approved' => 'badge-success',
                        'confirmed' => 'badge-primary',
                        'rejected' => 'badge-danger',
                        'cancelled' => 'badge-secondary',
                        default => 'badge-secondary'
                    };
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst(str_replace('_', ' ', $reservation->status)) . '</span>';
                })
                ->addColumn('actions', function ($reservation) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('meeting-rooms.reservations.show', $reservation) . '" class="btn btn-info btn-sm" title="View"><i class="fas fa-eye"></i></a>';

                    if (Auth::user()->can('edit meeting room reservations') && $reservation->canBeEdited()) {
                        $actions .= '<a href="' . route('meeting-rooms.reservations.edit', $reservation) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>';
                    }

                    if (Auth::user()->can('approve dept head meeting room reservations') && $reservation->canBeDeptHeadApproved()) {
                        $actions .= '<button type="button" class="btn btn-success btn-sm" onclick="approveDeptHead(' . $reservation->id . ')" title="Approve"><i class="fas fa-check"></i></button>';
                    }

                    if (Auth::user()->can('approve ga admin meeting room reservations') && $reservation->canBeGAAdminApproved()) {
                        $actions .= '<button type="button" class="btn btn-primary btn-sm" onclick="approveGAAdmin(' . $reservation->id . ')" title="GA Approve"><i class="fas fa-check-double"></i></button>';
                    }

                    if (Auth::user()->can('reject dept head meeting room reservations') && $reservation->canBeDeptHeadRejected()) {
                        $actions .= '<button type="button" class="btn btn-danger btn-sm" onclick="rejectDeptHead(' . $reservation->id . ')" title="Reject"><i class="fas fa-times"></i></button>';
                    }

                    if (Auth::user()->can('reject ga admin meeting room reservations') && $reservation->canBeGAAdminRejected()) {
                        $actions .= '<button type="button" class="btn btn-danger btn-sm" onclick="rejectGAAdmin(' . $reservation->id . ')" title="GA Reject"><i class="fas fa-times"></i></button>';
                    }

                    if (Auth::user()->can('allocate meeting room reservations') && $reservation->status === 'approved') {
                        $actions .= '<button type="button" class="btn btn-success btn-sm" onclick="allocateRoom(' . $reservation->id . ')" title="Allocate Room"><i class="fas fa-door-open"></i></button>';
                    }

                    if (Auth::user()->can('delete meeting room reservations') && $reservation->canBeCancelled()) {
                        $actions .= '<button type="button" class="btn btn-danger btn-sm" onclick="cancelReservation(' . $reservation->id . ')" title="Cancel"><i class="fas fa-trash"></i></button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }

        return view('meeting-room-reservation.reservations.index');
    }

    public function create()
    {
        $meetingRooms = MeetingRoom::where('is_active', true)->orderBy('name')->get();
        return view('meeting-room-reservation.reservations.create', compact('meetingRooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'requested_room_id' => 'nullable|exists:meeting_rooms,id',
            'location' => 'required|string|max:255',
            'meeting_title' => 'required|string|max:255',
            'meeting_date_start' => 'required|date',
            'meeting_date_end' => 'nullable|date|after_or_equal:meeting_date_start',
            'meeting_time_start' => 'required|date_format:H:i',
            'meeting_time_end' => 'required|date_format:H:i|after:meeting_time_start',
            'participant_count' => 'required|integer|min:1',
            'required_facilities' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'consumption' => 'nullable|array',
            'consumption.*.date' => 'required|date',
            'consumption.*.coffee_break_morning' => 'nullable|boolean',
            'consumption.*.coffee_break_afternoon' => 'nullable|boolean',
            'consumption.*.lunch' => 'nullable|boolean',
            'consumption.*.dinner' => 'nullable|boolean',
            'consumption.*.coffee_break_morning_desc' => 'nullable|string|max:500',
            'consumption.*.coffee_break_afternoon_desc' => 'nullable|string|max:500',
            'consumption.*.lunch_desc' => 'nullable|string|max:500',
            'consumption.*.dinner_desc' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $reservation = MeetingRoomReservation::create([
                'requestor_id' => Auth::id(),
                'department_id' => Auth::user()->department_id,
                'requested_room_id' => $validated['requested_room_id'] ?? null,
                'location' => $validated['location'],
                'meeting_title' => $validated['meeting_title'],
                'meeting_date_start' => $validated['meeting_date_start'],
                'meeting_date_end' => $validated['meeting_date_end'] ?? $validated['meeting_date_start'],
                'meeting_time_start' => $validated['meeting_time_start'],
                'meeting_time_end' => $validated['meeting_time_end'],
                'participant_count' => $validated['participant_count'],
                'required_facilities' => $validated['required_facilities'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending_dept_head',
            ]);

            // Create consumption requests for each day
            if (isset($validated['consumption'])) {
                $dates = $reservation->getMeetingDates();
                foreach ($dates as $date) {
                    if (isset($validated['consumption'][$date])) {
                        $dayConsumption = $validated['consumption'][$date];
                        $types = ['coffee_break_morning', 'coffee_break_afternoon', 'lunch', 'dinner'];
                        
                        foreach ($types as $type) {
                            $requested = $dayConsumption[$type] ?? false;
                            if ($requested) {
                                MeetingConsumptionRequest::create([
                                    'reservation_id' => $reservation->id,
                                    'consumption_date' => $date,
                                    'consumption_type' => $type,
                                    'requested' => true,
                                    'description' => $dayConsumption[$type . '_desc'] ?? null,
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('meeting-rooms.reservations.index')
                ->with('success', 'Meeting room reservation request created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create reservation: ' . $e->getMessage());
        }
    }

    public function show(MeetingRoomReservation $reservation)
    {
        $reservation->load([
            'requestor',
            'department',
            'requestedRoom',
            'allocatedRoom',
            'departmentHeadApprover',
            'gaAdminApprover',
            'consumptionRequests'
        ]);

        return view('meeting-room-reservation.reservations.show', compact('reservation'));
    }

    public function edit(MeetingRoomReservation $reservation)
    {
        if (!$reservation->canBeEdited()) {
            return redirect()->route('meeting-rooms.reservations.index')
                ->with('error', 'Only pending requests can be edited.');
        }

        $meetingRooms = MeetingRoom::where('is_active', true)->orderBy('name')->get();
        $reservation->load('consumptionRequests');
        
        return view('meeting-room-reservation.reservations.edit', compact('reservation', 'meetingRooms'));
    }

    public function update(Request $request, MeetingRoomReservation $reservation)
    {
        if (!$reservation->canBeEdited()) {
            return redirect()->route('meeting-rooms.reservations.index')
                ->with('error', 'Only pending requests can be updated.');
        }

        $validated = $request->validate([
            'requested_room_id' => 'nullable|exists:meeting_rooms,id',
            'location' => 'required|string|max:255',
            'meeting_title' => 'required|string|max:255',
            'meeting_date_start' => 'required|date',
            'meeting_date_end' => 'nullable|date|after_or_equal:meeting_date_start',
            'meeting_time_start' => 'required|date_format:H:i',
            'meeting_time_end' => 'required|date_format:H:i|after:meeting_time_start',
            'participant_count' => 'required|integer|min:1',
            'required_facilities' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'consumption' => 'nullable|array',
            'consumption.*.date' => 'required|date',
            'consumption.*.coffee_break_morning' => 'nullable|boolean',
            'consumption.*.coffee_break_afternoon' => 'nullable|boolean',
            'consumption.*.lunch' => 'nullable|boolean',
            'consumption.*.dinner' => 'nullable|boolean',
            'consumption.*.coffee_break_morning_desc' => 'nullable|string|max:500',
            'consumption.*.coffee_break_afternoon_desc' => 'nullable|string|max:500',
            'consumption.*.lunch_desc' => 'nullable|string|max:500',
            'consumption.*.dinner_desc' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $reservation->update([
                'requested_room_id' => $validated['requested_room_id'] ?? null,
                'location' => $validated['location'],
                'meeting_title' => $validated['meeting_title'],
                'meeting_date_start' => $validated['meeting_date_start'],
                'meeting_date_end' => $validated['meeting_date_end'] ?? $validated['meeting_date_start'],
                'meeting_time_start' => $validated['meeting_time_start'],
                'meeting_time_end' => $validated['meeting_time_end'],
                'participant_count' => $validated['participant_count'],
                'required_facilities' => $validated['required_facilities'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Delete existing consumption requests and recreate
            $reservation->consumptionRequests()->delete();

            if (isset($validated['consumption'])) {
                $dates = $reservation->getMeetingDates();
                foreach ($dates as $date) {
                    if (isset($validated['consumption'][$date])) {
                        $dayConsumption = $validated['consumption'][$date];
                        $types = ['coffee_break_morning', 'coffee_break_afternoon', 'lunch', 'dinner'];
                        
                        foreach ($types as $type) {
                            $requested = $dayConsumption[$type] ?? false;
                            if ($requested) {
                                MeetingConsumptionRequest::create([
                                    'reservation_id' => $reservation->id,
                                    'consumption_date' => $date,
                                    'consumption_type' => $type,
                                    'requested' => true,
                                    'description' => $dayConsumption[$type . '_desc'] ?? null,
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('meeting-rooms.reservations.index')
                ->with('success', 'Reservation updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update reservation: ' . $e->getMessage());
        }
    }

    public function destroy(MeetingRoomReservation $reservation)
    {
        if (!$reservation->canBeCancelled()) {
            return redirect()->route('meeting-rooms.reservations.index')
                ->with('error', 'Only pending requests can be cancelled.');
        }

        $reservation->consumptionRequests()->delete();
        $reservation->delete();

        return redirect()->route('meeting-rooms.reservations.index')
            ->with('success', 'Reservation cancelled successfully.');
    }

    public function approveDeptHead(Request $request, MeetingRoomReservation $reservation)
    {
        if (!$reservation->canBeDeptHeadApproved()) {
            return response()->json(['success' => false, 'message' => 'Request cannot be approved.'], 400);
        }

        // Security check: Department Head can only approve requests from their department
        if (!auth()->user()->canViewAllDepartments() && $reservation->department_id !== auth()->user()->department_id) {
            return response()->json(['success' => false, 'message' => 'You can only approve requests from your department.'], 403);
        }

        $reservation->update([
            'status' => 'pending_ga_admin',
            'department_head_approved_by' => Auth::id(),
            'department_head_approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request approved by Department Head successfully.'
        ]);
    }

    public function rejectDeptHead(Request $request, MeetingRoomReservation $reservation)
    {
        if (!$reservation->canBeDeptHeadRejected()) {
            return response()->json(['success' => false, 'message' => 'Request cannot be rejected.'], 400);
        }

        // Security check
        if (!auth()->user()->canViewAllDepartments() && $reservation->department_id !== auth()->user()->department_id) {
            return response()->json(['success' => false, 'message' => 'You can only reject requests from your department.'], 403);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $reservation->update([
            'status' => 'rejected',
            'department_head_approved_by' => Auth::id(),
            'department_head_approved_at' => now(),
            'department_head_rejection_reason' => $validated['rejection_reason'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request rejected successfully.'
        ]);
    }

    public function approveGAAdmin(Request $request, MeetingRoomReservation $reservation)
    {
        if (!$reservation->canBeGAAdminApproved()) {
            return response()->json(['success' => false, 'message' => 'Request cannot be approved.'], 400);
        }

        $reservation->update([
            'status' => 'approved',
            'ga_admin_approved_by' => Auth::id(),
            'ga_admin_approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request approved by GA Admin successfully.'
        ]);
    }

    public function rejectGAAdmin(Request $request, MeetingRoomReservation $reservation)
    {
        if (!$reservation->canBeGAAdminRejected()) {
            return response()->json(['success' => false, 'message' => 'Request cannot be rejected.'], 400);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $reservation->update([
            'status' => 'rejected',
            'ga_admin_approved_by' => Auth::id(),
            'ga_admin_approved_at' => now(),
            'ga_admin_rejection_reason' => $validated['rejection_reason'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request rejected successfully.'
        ]);
    }

    public function allocateRoom(Request $request, MeetingRoomReservation $reservation)
    {
        if ($reservation->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Only approved requests can have rooms allocated.'], 400);
        }

        $validated = $request->validate([
            'allocated_room_id' => 'required|exists:meeting_rooms,id',
        ]);

        $room = MeetingRoom::findOrFail($validated['allocated_room_id']);

        // Check room availability
        if (!$room->isAvailableForMeeting(
            $reservation->meeting_date_start->format('Y-m-d'),
            $reservation->meeting_date_end ? $reservation->meeting_date_end->format('Y-m-d') : $reservation->meeting_date_start->format('Y-m-d'),
            $reservation->meeting_time_start,
            $reservation->meeting_time_end,
            $reservation->id
        )) {
            return response()->json(['success' => false, 'message' => 'Room is not available for the selected dates and time.'], 400);
        }

        // Check capacity
        if ($room->capacity < $reservation->participant_count) {
            return response()->json(['success' => false, 'message' => 'Room capacity (' . $room->capacity . ') is less than participant count (' . $reservation->participant_count . ').'], 400);
        }

        $reservation->update([
            'allocated_room_id' => $validated['allocated_room_id'],
            'room_allocated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Room allocated successfully.'
        ]);
    }

    public function sendResponse(Request $request, MeetingRoomReservation $reservation)
    {
        if (!$reservation->allocated_room_id) {
            return response()->json(['success' => false, 'message' => 'Room must be allocated before sending response.'], 400);
        }

        $validated = $request->validate([
            'response_notes' => 'nullable|string|max:1000',
        ]);

        $reservation->update([
            'status' => 'confirmed',
            'response_sent_at' => now(),
            'response_notes' => $validated['response_notes'] ?? null,
        ]);

        // TODO: Send notification (email and in-app)

        return response()->json([
            'success' => true,
            'message' => 'Response sent to requestor successfully.'
        ]);
    }

    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:meeting_rooms,id',
            'date_start' => 'required|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_start',
            'exclude_reservation_id' => 'nullable|exists:meeting_room_reservations,id',
        ]);

        $room = MeetingRoom::findOrFail($validated['room_id']);
        $dateEnd = $validated['date_end'] ?? $validated['date_start'];

        $isAvailable = $room->isAvailableForMeeting(
            $validated['date_start'],
            $dateEnd,
            $validated['time_start'],
            $validated['time_end'],
            $validated['exclude_reservation_id'] ?? null
        );

        return response()->json(['available' => $isAvailable]);
    }

    public function allocationDiagram(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $meetingRooms = MeetingRoom::where('is_active', true)->orderBy('name')->get();
        
        $reservations = MeetingRoomReservation::where('allocated_room_id', '!=', null)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'rejected')
            ->whereDate('meeting_date_start', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('meeting_date_end')
                    ->orWhereDate('meeting_date_end', '>=', $date);
            })
            ->with(['allocatedRoom', 'requestor'])
            ->get();

        return view('meeting-room-reservation.allocation-diagram', compact('meetingRooms', 'reservations', 'date'));
    }
}
