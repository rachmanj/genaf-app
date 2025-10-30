<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketReservation;
use App\Models\ReservationDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TicketReservationController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('view ticket reservations')) {
            abort(403, 'You do not have permission to view ticket reservations.');
        }

        if ($request->ajax()) {
            try {
                $query = TicketReservation::with(['employee', 'approver']);

                // Filter by status
                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }

                // Filter by ticket type
                if ($request->filled('ticket_type')) {
                    $query->where('ticket_type', $request->ticket_type);
                }

                // Filter by employee
                if ($request->filled('employee_id')) {
                    $query->where('employee_id', $request->employee_id);
                }

                // Filter by date range
                if ($request->filled('date_from')) {
                    $query->where('departure_date', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $query->where('departure_date', '<=', $request->date_to);
                }

                // Limit to own reservations for employees
                if (Auth::user()->hasRole('employee') && !Auth::user()->hasRole('admin')) {
                    $query->where('employee_id', Auth::id());
                }

            return DataTables::of($query)
                ->addColumn('employee_name', function ($reservation) {
                    return $reservation->employee ? $reservation->employee->name : 'N/A';
                })
                ->addColumn('ticket_type_badge', function ($reservation) {
                    $iconMap = [
                        'flight' => 'fas fa-plane',
                        'train' => 'fas fa-train',
                        'bus' => 'fas fa-bus',
                        'hotel' => 'fas fa-bed',
                    ];
                    $icon = $iconMap[$reservation->ticket_type] ?? 'fas fa-ticket-alt';
                    return '<i class="' . $icon . '"></i> ' . ucfirst($reservation->ticket_type);
                })
                ->addColumn('departure_date_formatted', function ($reservation) {
                    return $reservation->departure_date ? $reservation->departure_date->format('d/m/Y') : 'N/A';
                })
                ->addColumn('return_date_formatted', function ($reservation) {
                    return $reservation->return_date ? $reservation->return_date->format('d/m/Y') : 'N/A';
                })
                ->addColumn('cost_formatted', function ($reservation) {
                    return 'Rp ' . number_format($reservation->cost, 0, ',', '.');
                })
                ->addColumn('status_badge', function ($reservation) {
                    $badgeClass = match ($reservation->status) {
                        'pending' => 'badge-warning',
                        'approved' => 'badge-success',
                        'rejected' => 'badge-danger',
                        'booked' => 'badge-info',
                        'completed' => 'badge-secondary',
                        default => 'badge-secondary'
                    };
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($reservation->status) . '</span>';
                })
                ->addColumn('approver_name', function ($reservation) {
                    return $reservation->approver ? $reservation->approver->name : 'N/A';
                })
                ->addColumn('approved_at_formatted', function ($reservation) {
                    return $reservation->approved_at ? $reservation->approved_at->format('d/m/Y H:i') : 'N/A';
                })
                ->addColumn('actions', function ($reservation) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('ticket-reservations.show', $reservation) . '" class="btn btn-info btn-sm" title="View"><i class="fas fa-eye"></i></a>';

                    if (Auth::user()->can('edit ticket reservations') && $reservation->status === 'pending') {
                        $actions .= '<a href="' . route('ticket-reservations.edit', $reservation) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>';
                    }

                    if (Auth::user()->can('approve ticket reservations') && $reservation->canBeApproved()) {
                        $actions .= '<button type="button" class="btn btn-success btn-sm" onclick="approveReservation(' . $reservation->id . ')" title="Approve"><i class="fas fa-check"></i></button>';
                    }

                    if (Auth::user()->can('approve ticket reservations') && $reservation->canBeRejected()) {
                        $actions .= '<button type="button" class="btn btn-danger btn-sm" onclick="rejectReservation(' . $reservation->id . ')" title="Reject"><i class="fas fa-times"></i></button>';
                    }

                    if (Auth::user()->can('delete ticket reservations') && $reservation->status === 'pending') {
                        $actions .= '<button type="button" class="btn btn-danger btn-sm" onclick="deleteReservation(' . $reservation->id . ')" title="Delete"><i class="fas fa-trash"></i></button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['ticket_type_badge', 'status_badge', 'actions'])
                ->make(true);
            } catch (\Exception $e) {
                \Log::error('DataTables Error: ' . $e->getMessage());
                return response()->json([
                    'draw' => $request->input('draw'),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        $employees = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['employee', 'department head', 'manager']);
        })->orderBy('name')->get();

        return view('admin.ticket-reservations.index', compact('employees'));
    }

    public function create()
    {
        if (!Auth::user()->can('create ticket reservations')) {
            abort(403, 'You do not have permission to create ticket reservations.');
        }

        return view('admin.ticket-reservations.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('create ticket reservations')) {
            abort(403, 'You do not have permission to create ticket reservations.');
        }

        $validated = $request->validate([
            'ticket_type' => 'required|in:flight,train,bus,hotel',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date|after_or_equal:today',
            'return_date' => 'nullable|date|after:departure_date',
            'cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $reservation = TicketReservation::create([
            'employee_id' => Auth::id(),
            'ticket_type' => $validated['ticket_type'],
            'destination' => $validated['destination'],
            'departure_date' => $validated['departure_date'],
            'return_date' => $validated['return_date'] ?? null,
            'cost' => $validated['cost'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('ticket-reservations.index')
            ->with('success', 'Ticket reservation created successfully.');
    }

    public function show(TicketReservation $ticketReservation)
    {
        if (!Auth::user()->can('view ticket reservations')) {
            abort(403, 'You do not have permission to view ticket reservations.');
        }

        // Check if employee can view this reservation
        if (Auth::user()->hasRole('employee') && !Auth::user()->hasRole('admin') && $ticketReservation->employee_id !== Auth::id()) {
            abort(403, 'You can only view your own reservations.');
        }

        $ticketReservation->load(['employee', 'approver', 'documents']);

        return view('admin.ticket-reservations.show', compact('ticketReservation'));
    }

    public function edit(TicketReservation $ticketReservation)
    {
        if (!Auth::user()->can('edit ticket reservations')) {
            abort(403, 'You do not have permission to edit ticket reservations.');
        }

        // Check if reservation can be edited
        if ($ticketReservation->status !== 'pending') {
            return redirect()->route('ticket-reservations.index')
                ->with('error', 'Only pending reservations can be edited.');
        }

        // Check if employee can edit this reservation
        if (Auth::user()->hasRole('employee') && !Auth::user()->hasRole('admin') && $ticketReservation->employee_id !== Auth::id()) {
            abort(403, 'You can only edit your own reservations.');
        }

        return view('admin.ticket-reservations.edit', compact('ticketReservation'));
    }

    public function update(Request $request, TicketReservation $ticketReservation)
    {
        if (!Auth::user()->can('edit ticket reservations')) {
            abort(403, 'You do not have permission to edit ticket reservations.');
        }

        // Check if reservation can be edited
        if ($ticketReservation->status !== 'pending') {
            return redirect()->route('ticket-reservations.index')
                ->with('error', 'Only pending reservations can be edited.');
        }

        $validated = $request->validate([
            'ticket_type' => 'required|in:flight,train,bus,hotel',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date|after_or_equal:today',
            'return_date' => 'nullable|date|after:departure_date',
            'cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $ticketReservation->update($validated);

        return redirect()->route('ticket-reservations.index')
            ->with('success', 'Ticket reservation updated successfully.');
    }

    public function destroy(TicketReservation $ticketReservation)
    {
        if (!Auth::user()->can('delete ticket reservations')) {
            abort(403, 'You do not have permission to delete ticket reservations.');
        }

        // Check if reservation can be deleted
        if ($ticketReservation->status !== 'pending') {
            return redirect()->route('ticket-reservations.index')
                ->with('error', 'Only pending reservations can be deleted.');
        }

        // Delete associated documents
        foreach ($ticketReservation->documents as $document) {
            Storage::disk('public')->delete($document->file_path);
            $document->delete();
        }

        $ticketReservation->delete();

        return redirect()->route('ticket-reservations.index')
            ->with('success', 'Ticket reservation deleted successfully.');
    }

    public function approve(Request $request, TicketReservation $ticketReservation)
    {
        if (!Auth::user()->can('approve ticket reservations')) {
            abort(403, 'You do not have permission to approve ticket reservations.');
        }

        if (!$ticketReservation->canBeApproved()) {
            return redirect()->route('ticket-reservations.index')
                ->with('error', 'This reservation cannot be approved.');
        }

        $ticketReservation->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('ticket-reservations.index')
            ->with('success', 'Ticket reservation approved successfully.');
    }

    public function reject(Request $request, TicketReservation $ticketReservation)
    {
        if (!Auth::user()->can('approve ticket reservations')) {
            abort(403, 'You do not have permission to reject ticket reservations.');
        }

        if (!$ticketReservation->canBeRejected()) {
            return redirect()->route('ticket-reservations.index')
                ->with('error', 'This reservation cannot be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $ticketReservation->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->route('ticket-reservations.index')
            ->with('success', 'Ticket reservation rejected.');
    }

    public function markBooked(Request $request, TicketReservation $ticketReservation)
    {
        if (!Auth::user()->can('approve ticket reservations')) {
            abort(403, 'You do not have permission to mark reservations as booked.');
        }

        if ($ticketReservation->status !== 'approved') {
            return redirect()->route('ticket-reservations.index')
                ->with('error', 'Only approved reservations can be marked as booked.');
        }

        $validated = $request->validate([
            'booking_reference' => 'required|string|max:100',
        ]);

        $ticketReservation->update([
            'status' => 'booked',
            'booking_reference' => $validated['booking_reference'],
        ]);

        return redirect()->route('ticket-reservations.index')
            ->with('success', 'Reservation marked as booked.');
    }

    public function markCompleted(TicketReservation $ticketReservation)
    {
        if (!Auth::user()->can('approve ticket reservations')) {
            abort(403, 'You do not have permission to mark reservations as completed.');
        }

        if ($ticketReservation->status !== 'booked') {
            return redirect()->route('ticket-reservations.index')
                ->with('error', 'Only booked reservations can be marked as completed.');
        }

        $ticketReservation->update([
            'status' => 'completed',
        ]);

        return redirect()->route('ticket-reservations.index')
            ->with('success', 'Reservation marked as completed.');
    }

    public function uploadDocument(Request $request, TicketReservation $ticketReservation)
    {
        if (!Auth::user()->can('edit ticket reservations')) {
            abort(403, 'You do not have permission to upload documents.');
        }

        $validated = $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        $file = $validated['document'];
        $path = $file->store('ticket-reservations', 'public');

        ReservationDocument::create([
            'reservation_id' => $ticketReservation->id,
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()->route('ticket-reservations.show', $ticketReservation)
            ->with('success', 'Document uploaded successfully.');
    }

    public function deleteDocument(TicketReservation $ticketReservation, ReservationDocument $document)
    {
        if (!Auth::user()->can('edit ticket reservations')) {
            abort(403, 'You do not have permission to delete documents.');
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('ticket-reservations.show', $ticketReservation)
            ->with('success', 'Document deleted successfully.');
    }
}
