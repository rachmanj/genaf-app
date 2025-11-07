<?php

namespace App\Http\Controllers\PropertyManagement;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Room;
use App\Models\RoomMaintenance;
use App\Models\RoomReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class PmsDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('view pms dashboard');
        $selectedBuildingId = $request->integer('building_id');
        $dateRange = $request->input('date_range', 'month'); // month, week, year, custom
        $startDate = $this->getStartDate($dateRange, $request);
        $endDate = $this->getEndDate($dateRange, $request);

        $buildings = Building::orderBy('name')->get();

        // Overall statistics
        $overallStats = $this->getOverallStats($selectedBuildingId, $startDate, $endDate);

        // Building-specific statistics
        $buildingStats = $this->getBuildingStats($selectedBuildingId, $startDate, $endDate);

        // Recent reservations
        $recentReservations = RoomReservation::with(['room.building'])
            ->when($selectedBuildingId, fn ($q) => $q->whereHas('room', fn ($rq) => $rq->where('building_id', $selectedBuildingId)))
            ->latest('created_at')
            ->limit(10)
            ->get();

        // Upcoming maintenance
        $upcomingMaintenance = RoomMaintenance::with(['room.building'])
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->when($selectedBuildingId, fn ($q) => $q->whereHas('room', fn ($rq) => $rq->where('building_id', $selectedBuildingId)))
            ->where('scheduled_date', '>=', now()->toDateString())
            ->orderBy('scheduled_date')
            ->limit(10)
            ->get();

        // Reservation trends (last 12 months)
        $reservationTrends = $this->getReservationTrends($selectedBuildingId);

        // Status distribution
        $statusDistribution = $this->getStatusDistribution($selectedBuildingId);

        return view('property-management.dashboard.index', [
            'buildings' => $buildings,
            'selectedBuildingId' => $selectedBuildingId,
            'dateRange' => $dateRange,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'overallStats' => $overallStats,
            'buildingStats' => $buildingStats,
            'recentReservations' => $recentReservations,
            'upcomingMaintenance' => $upcomingMaintenance,
            'reservationTrends' => $reservationTrends,
            'statusDistribution' => $statusDistribution,
        ]);
    }

    private function getOverallStats(?int $buildingId, Carbon $startDate, Carbon $endDate): array
    {
        $roomQuery = Room::query();
        $reservationQuery = RoomReservation::query();
        $maintenanceQuery = RoomMaintenance::query();

        if ($buildingId) {
            $roomQuery->where('building_id', $buildingId);
            $reservationQuery->whereHas('room', fn ($q) => $q->where('building_id', $buildingId));
            $maintenanceQuery->whereHas('room', fn ($q) => $q->where('building_id', $buildingId));
        }

        $totalRooms = $roomQuery->where('is_active', true)->count();
        $activeReservations = $reservationQuery
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in', '<=', $endDate)
            ->where('check_out', '>=', $startDate)
            ->count();

        $totalReservations = $reservationQuery
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $completedReservations = $reservationQuery
            ->where('status', 'checked_out')
            ->whereBetween('checked_out_at', [$startDate, $endDate])
            ->count();

        $totalMaintenanceCost = $maintenanceQuery
            ->where('status', 'completed')
            ->whereBetween('completed_date', [$startDate, $endDate])
            ->sum('cost');

        $pendingMaintenance = $maintenanceQuery
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->count();

        $occupancyRate = $totalRooms > 0 
            ? round(($activeReservations / $totalRooms) * 100, 2) 
            : 0;

        return [
            'total_rooms' => $totalRooms,
            'active_reservations' => $activeReservations,
            'total_reservations' => $totalReservations,
            'completed_reservations' => $completedReservations,
            'total_maintenance_cost' => $totalMaintenanceCost,
            'pending_maintenance' => $pendingMaintenance,
            'occupancy_rate' => $occupancyRate,
        ];
    }

    private function getBuildingStats(?int $buildingId, Carbon $startDate, Carbon $endDate): array
    {
        if (!$buildingId) {
            return [];
        }

        $building = Building::with('rooms')->find($buildingId);
        if (!$building) {
            return [];
        }

        $totalRooms = $building->rooms()->where('is_active', true)->count();
        
        $activeReservations = RoomReservation::whereHas('room', fn ($q) => $q->where('building_id', $buildingId))
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in', '<=', $endDate)
            ->where('check_out', '>=', $startDate)
            ->count();

        $totalReservations = RoomReservation::whereHas('room', fn ($q) => $q->where('building_id', $buildingId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $maintenanceCost = RoomMaintenance::whereHas('room', fn ($q) => $q->where('building_id', $buildingId))
            ->where('status', 'completed')
            ->whereBetween('completed_date', [$startDate, $endDate])
            ->sum('cost');

        $occupancyRate = $totalRooms > 0 
            ? round(($activeReservations / $totalRooms) * 100, 2) 
            : 0;

        return [
            'building' => $building,
            'total_rooms' => $totalRooms,
            'active_reservations' => $activeReservations,
            'total_reservations' => $totalReservations,
            'maintenance_cost' => $maintenanceCost,
            'occupancy_rate' => $occupancyRate,
        ];
    }

    private function getReservationTrends(?int $buildingId): array
    {
        $trends = [];
        $start = now()->subMonths(11)->startOfMonth();

        for ($i = 0; $i < 12; $i++) {
            $monthStart = $start->copy()->addMonths($i)->startOfMonth();
            $monthEnd = $start->copy()->addMonths($i)->endOfMonth();

            $count = RoomReservation::when($buildingId, fn ($q) => $q->whereHas('room', fn ($rq) => $rq->where('building_id', $buildingId)))
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $trends[] = [
                'month' => $monthStart->format('M Y'),
                'count' => $count,
            ];
        }

        return $trends;
    }

    private function getStatusDistribution(?int $buildingId): array
    {
        $distribution = RoomReservation::when($buildingId, fn ($q) => $q->whereHas('room', fn ($rq) => $rq->where('building_id', $buildingId)))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'pending' => $distribution['pending'] ?? 0,
            'confirmed' => $distribution['confirmed'] ?? 0,
            'checked_in' => $distribution['checked_in'] ?? 0,
            'checked_out' => $distribution['checked_out'] ?? 0,
            'cancelled' => $distribution['cancelled'] ?? 0,
        ];
    }

    private function getStartDate(string $dateRange, Request $request): Carbon
    {
        if ($dateRange === 'custom' && $request->filled('start_date')) {
            return Carbon::parse($request->input('start_date'));
        }

        return match ($dateRange) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };
    }

    private function getEndDate(string $dateRange, Request $request): Carbon
    {
        if ($dateRange === 'custom' && $request->filled('end_date')) {
            return Carbon::parse($request->input('end_date'));
        }

        return match ($dateRange) {
            'week' => now()->endOfWeek(),
            'month' => now()->endOfMonth(),
            'year' => now()->endOfYear(),
            default => now()->endOfMonth(),
        };
    }
}

