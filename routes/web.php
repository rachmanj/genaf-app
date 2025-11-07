<?php

use App\Http\Controllers\Common\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->middleware('auth');



// User Management Routes
Route::middleware('auth')->group(function () {
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::patch('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
});

// PMS Routes
Route::prefix('pms')->middleware('auth')->name('pms.')->group(function () {
    // Dashboard
    Route::get('dashboard', [\App\Http\Controllers\PropertyManagement\PmsDashboardController::class, 'index'])->name('dashboard.index');

    Route::resource('buildings', \App\Http\Controllers\PropertyManagement\BuildingController::class);
    Route::get('buildings/search', [\App\Http\Controllers\PropertyManagement\BuildingController::class, 'search'])->name('buildings.search');
    Route::resource('rooms', \App\Http\Controllers\PropertyManagement\RoomController::class);
    // Dependent select endpoint
    Route::get('buildings/{building}/rooms', [\App\Http\Controllers\PropertyManagement\RoomController::class, 'byBuilding'])->name('buildings.rooms');

    // Calendar
    Route::get('calendar', [\App\Http\Controllers\PropertyManagement\RoomReservationController::class, 'calendar'])->name('calendar.index');
    Route::get('calendar/events', [\App\Http\Controllers\PropertyManagement\RoomReservationController::class, 'events'])->name('calendar.events');

    // Reservations
    Route::resource('reservations', \App\Http\Controllers\PropertyManagement\RoomReservationController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('reservations/check-availability', [\App\Http\Controllers\PropertyManagement\RoomReservationController::class, 'checkAvailability'])->name('reservations.check-availability');
    Route::get('reservations/unavailable-dates', [\App\Http\Controllers\PropertyManagement\RoomReservationController::class, 'getUnavailableDates'])->name('reservations.unavailable-dates');
    Route::get('reservations/guest-info', [\App\Http\Controllers\PropertyManagement\RoomReservationController::class, 'getGuestInfo'])->name('reservations.guest-info');
    Route::post('reservations/{reservation}/approve', [\App\Http\Controllers\PropertyManagement\RoomReservationController::class, 'approve'])->name('reservations.approve');
    Route::post('reservations/{reservation}/check-in', [\App\Http\Controllers\PropertyManagement\RoomReservationController::class, 'checkIn'])->name('reservations.check-in');
    Route::post('reservations/{reservation}/check-out', [\App\Http\Controllers\PropertyManagement\RoomReservationController::class, 'checkOut'])->name('reservations.check-out');
    Route::post('reservations/{reservation}/cancel', [\App\Http\Controllers\PropertyManagement\RoomReservationController::class, 'cancel'])->name('reservations.cancel');

    // Room Maintenances
    Route::resource('maintenances', \App\Http\Controllers\PropertyManagement\RoomMaintenanceController::class);
    Route::post('maintenances/{maintenance}/complete', [\App\Http\Controllers\PropertyManagement\RoomMaintenanceController::class, 'markComplete'])->name('maintenances.complete');
});

// Vehicle Administration Routes
Route::middleware('auth')->group(function () {
    // Vehicle registry
    Route::resource('vehicles', \App\Http\Controllers\Vehicle\VehicleController::class);

    // Fuel Records - global listing and management
    Route::resource('fuel-records', \App\Http\Controllers\Vehicle\FuelRecordController::class)->only([
        'index',
        'create',
        'store',
        'edit',
        'update',
        'destroy',
    ]);

    // Vehicle Maintenance - global listing and management
    Route::resource('vehicle-maintenance', \App\Http\Controllers\Vehicle\VehicleMaintenanceController::class)->only([
        'index',
        'create',
        'store',
        'edit',
        'update',
        'destroy',
    ]);

    // Vehicle Documents - managed via upload/delete and secure download
    Route::resource('vehicle-documents', \App\Http\Controllers\Vehicle\VehicleDocumentController::class)->only([
        'store',
        'destroy',
    ]);
    Route::get('vehicle-documents/{document}/download', [\App\Http\Controllers\Vehicle\VehicleDocumentController::class, 'download'])
        ->name('vehicle-documents.download');
});

// Admin Routes
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->names([
        'index' => 'admin.roles.index',
        'create' => 'admin.roles.create',
        'store' => 'admin.roles.store',
        'show' => 'admin.roles.show',
        'edit' => 'admin.roles.edit',
        'update' => 'admin.roles.update',
        'destroy' => 'admin.roles.destroy',
    ]);

    Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class)->names([
        'index' => 'admin.permissions.index',
        'create' => 'admin.permissions.create',
        'store' => 'admin.permissions.store',
        'show' => 'admin.permissions.show',
        'edit' => 'admin.permissions.edit',
        'update' => 'admin.permissions.update',
        'destroy' => 'admin.permissions.destroy',
    ]);

    Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class)->names([
        'index' => 'admin.projects.index',
        'create' => 'admin.projects.create',
        'store' => 'admin.projects.store',
        'show' => 'admin.projects.show',
        'edit' => 'admin.projects.edit',
        'update' => 'admin.projects.update',
        'destroy' => 'admin.projects.destroy',
    ]);
});

// Supplies Management Routes (accessible to managers and employees)
Route::middleware('auth')->group(function () {
    // Supply Requests Routes (nested under supplies) - must come before supplies/{supply}
    Route::prefix('supplies')->group(function () {
        // Supplies data for modal selection (must be first to avoid route conflict)
        Route::get('requests/supplies-data', [\App\Http\Controllers\OfficeSupplies\SupplyRequestController::class, 'suppliesData'])
            ->name('supplies.requests.supplies-data');

        Route::resource('requests', \App\Http\Controllers\OfficeSupplies\SupplyRequestController::class)->names([
            'index' => 'supplies.requests.index',
            'create' => 'supplies.requests.create',
            'store' => 'supplies.requests.store',
            'show' => 'supplies.requests.show',
            'edit' => 'supplies.requests.edit',
            'update' => 'supplies.requests.update',
            'destroy' => 'supplies.requests.destroy',
        ]);
        // Two-level approval routes
        Route::post('requests/{supplyRequest}/approve-dept-head', [\App\Http\Controllers\OfficeSupplies\SupplyRequestController::class, 'approveDeptHead'])->name('supplies.requests.approve-dept-head');
        Route::post('requests/{supplyRequest}/reject-dept-head', [\App\Http\Controllers\OfficeSupplies\SupplyRequestController::class, 'rejectDeptHead'])->name('supplies.requests.reject-dept-head');
        Route::post('requests/{supplyRequest}/approve-ga-admin', [\App\Http\Controllers\OfficeSupplies\SupplyRequestController::class, 'approveGAAdmin'])->name('supplies.requests.approve-ga-admin');
        Route::post('requests/{supplyRequest}/reject-ga-admin', [\App\Http\Controllers\OfficeSupplies\SupplyRequestController::class, 'rejectGAAdmin'])->name('supplies.requests.reject-ga-admin');

        // Supply Fulfillment Routes (specific routes must come before dynamic routes)
        Route::get('fulfillment', [\App\Http\Controllers\OfficeSupplies\SupplyFulfillmentController::class, 'index'])->name('supplies.fulfillment.index');
        Route::get('fulfillment/history', [\App\Http\Controllers\OfficeSupplies\SupplyFulfillmentController::class, 'history'])->name('supplies.fulfillment.history');

        // Verification Routes (must come before fulfillment/{request} to avoid route conflict)
        Route::get('fulfillment/verification', [\App\Http\Controllers\OfficeSupplies\SupplyFulfillmentController::class, 'pendingVerification'])->name('supplies.fulfillment.pending-verification');
        Route::get('fulfillment/verification/{distribution}', [\App\Http\Controllers\OfficeSupplies\SupplyFulfillmentController::class, 'verifyShow'])->name('supplies.fulfillment.verify-show');
        Route::post('fulfillment/verification/{distribution}/verify', [\App\Http\Controllers\OfficeSupplies\SupplyFulfillmentController::class, 'verify'])->name('supplies.fulfillment.verify');
        Route::post('fulfillment/verification/{distribution}/reject', [\App\Http\Controllers\OfficeSupplies\SupplyFulfillmentController::class, 'rejectVerification'])->name('supplies.fulfillment.reject');

        // Rejected Distributions Routes (for GA Admin)
        Route::get('fulfillment/rejected', [\App\Http\Controllers\OfficeSupplies\SupplyFulfillmentController::class, 'rejectedDistributions'])->name('supplies.fulfillment.rejected-distributions');
        Route::get('fulfillment/rejected/{distribution}', [\App\Http\Controllers\OfficeSupplies\SupplyFulfillmentController::class, 'rejectedShow'])->name('supplies.fulfillment.rejected-show');

        // Dynamic fulfillment routes (must come last)
        Route::get('fulfillment/{request}', [\App\Http\Controllers\OfficeSupplies\SupplyFulfillmentController::class, 'show'])->name('supplies.fulfillment.show');
        Route::post('fulfillment/{request}/fulfill', [\App\Http\Controllers\OfficeSupplies\SupplyFulfillmentController::class, 'fulfill'])->name('supplies.fulfillment.fulfill');

        // Department Stock Routes
        Route::get('department-stock', [\App\Http\Controllers\OfficeSupplies\DepartmentStockController::class, 'index'])->name('supplies.department-stock.index');
        Route::get('department-stock/{department}', [\App\Http\Controllers\OfficeSupplies\DepartmentStockController::class, 'show'])->name('supplies.department-stock.show');
        Route::get('department-stock/{department}/report', [\App\Http\Controllers\OfficeSupplies\DepartmentStockController::class, 'report'])->name('supplies.department-stock.report');

        // Stock Opname Routes
        Route::prefix('stock-opname')->name('stock-opname.')->group(function () {
            Route::get('/', [\App\Http\Controllers\OfficeSupplies\StockOpnameController::class, 'index'])->name('index');
            Route::get('create', [\App\Http\Controllers\OfficeSupplies\StockOpnameController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\OfficeSupplies\StockOpnameController::class, 'store'])->name('store');
            Route::get('{session}', [\App\Http\Controllers\OfficeSupplies\StockOpnameController::class, 'show'])->name('show');
            Route::get('{session}/edit', [\App\Http\Controllers\OfficeSupplies\StockOpnameController::class, 'edit'])->name('edit');
            Route::put('{session}', [\App\Http\Controllers\OfficeSupplies\StockOpnameController::class, 'update'])->name('update');
            Route::delete('{session}', [\App\Http\Controllers\OfficeSupplies\StockOpnameController::class, 'destroy'])->name('destroy');
            Route::post('{session}/start', [\App\Http\Controllers\OfficeSupplies\StockOpnameController::class, 'start'])->name('start');
            Route::post('{session}/complete', [\App\Http\Controllers\OfficeSupplies\StockOpnameController::class, 'complete'])->name('complete');
            Route::post('{session}/approve', [\App\Http\Controllers\OfficeSupplies\StockOpnameController::class, 'approve'])->name('approve');
            Route::post('{session}/cancel', [\App\Http\Controllers\OfficeSupplies\StockOpnameController::class, 'cancel'])->name('cancel');
            Route::get('{session}/export', [\App\Http\Controllers\OfficeSupplies\StockOpnameController::class, 'export'])->name('export');

            // Items
            Route::get('{session}/items', [\App\Http\Controllers\OfficeSupplies\StockOpnameItemController::class, 'index'])->name('items.index');
            Route::put('{session}/items/{item}', [\App\Http\Controllers\OfficeSupplies\StockOpnameItemController::class, 'update'])->name('items.update');
            Route::post('{session}/items/bulk-update', [\App\Http\Controllers\OfficeSupplies\StockOpnameItemController::class, 'bulkUpdate'])->name('items.bulk-update');
            Route::post('{session}/items/{item}/photo', [\App\Http\Controllers\OfficeSupplies\StockOpnameItemController::class, 'uploadPhoto'])->name('items.photo');
            Route::post('{session}/items/{item}/verify', [\App\Http\Controllers\OfficeSupplies\StockOpnameItemController::class, 'verify'])->name('items.verify');
            Route::post('{session}/items/{item}/save-draft', [\App\Http\Controllers\OfficeSupplies\StockOpnameItemController::class, 'saveDraft'])->name('items.save-draft');
            Route::post('{session}/items/{item}/finalize', [\App\Http\Controllers\OfficeSupplies\StockOpnameItemController::class, 'finalizeCount'])->name('items.finalize');

            // Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('variance', [\App\Http\Controllers\OfficeSupplies\StockOpnameReportController::class, 'variance'])->name('variance');
                Route::get('accuracy', [\App\Http\Controllers\OfficeSupplies\StockOpnameReportController::class, 'accuracy'])->name('accuracy');
                Route::get('trends', [\App\Http\Controllers\OfficeSupplies\StockOpnameReportController::class, 'trends'])->name('trends');
                Route::get('history', [\App\Http\Controllers\OfficeSupplies\StockOpnameReportController::class, 'history'])->name('history');
            });
        });

        // Supply Transactions Routes (nested under supplies)
        Route::resource('transactions', \App\Http\Controllers\OfficeSupplies\SupplyTransactionController::class)->names([
            'index' => 'supplies.transactions.index',
            'create' => 'supplies.transactions.create',
            'store' => 'supplies.transactions.store',
            'show' => 'supplies.transactions.show',
            'destroy' => 'supplies.transactions.destroy',
        ]);
    });

    // Office Supplies Dashboard
    Route::get('supplies/dashboard', [\App\Http\Controllers\OfficeSupplies\SupplyDashboardController::class, 'index'])
        ->name('supplies.dashboard');

    // Main supplies resource routes (must come after nested routes)
    Route::resource('supplies', \App\Http\Controllers\OfficeSupplies\SupplyController::class)->names([
        'index' => 'supplies.index',
        'create' => 'supplies.create',
        'store' => 'supplies.store',
        'show' => 'supplies.show',
        'edit' => 'supplies.edit',
        'update' => 'supplies.update',
        'destroy' => 'supplies.destroy',
    ]);
});

// Departments Routes
Route::middleware('auth')->group(function () {
    Route::resource('departments', \App\Http\Controllers\Admin\DepartmentController::class);
    Route::post('departments/{department}/toggle-status', [\App\Http\Controllers\Admin\DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
});

// Ticket Reservations Routes
Route::middleware('auth')->group(function () {
    Route::resource('ticket-reservations', \App\Http\Controllers\TicketReservation\TicketReservationController::class);
    Route::post('ticket-reservations/{ticketReservation}/approve', [\App\Http\Controllers\TicketReservation\TicketReservationController::class, 'approve'])->name('ticket-reservations.approve');
    Route::post('ticket-reservations/{ticketReservation}/reject', [\App\Http\Controllers\TicketReservation\TicketReservationController::class, 'reject'])->name('ticket-reservations.reject');
    Route::post('ticket-reservations/{ticketReservation}/mark-booked', [\App\Http\Controllers\TicketReservation\TicketReservationController::class, 'markBooked'])->name('ticket-reservations.mark-booked');
    Route::post('ticket-reservations/{ticketReservation}/mark-completed', [\App\Http\Controllers\TicketReservation\TicketReservationController::class, 'markCompleted'])->name('ticket-reservations.mark-completed');
    Route::post('ticket-reservations/{ticketReservation}/upload-document', [\App\Http\Controllers\TicketReservation\TicketReservationController::class, 'uploadDocument'])->name('ticket-reservations.upload-document');
    Route::delete('ticket-reservations/{ticketReservation}/delete-document/{document}', [\App\Http\Controllers\TicketReservation\TicketReservationController::class, 'deleteDocument'])->name('ticket-reservations.delete-document');
});

// Meeting Room Reservations Routes
Route::prefix('meeting-rooms')->middleware('auth')->name('meeting-rooms.')->group(function () {
    Route::resource('reservations', \App\Http\Controllers\MeetingRoomReservation\MeetingRoomReservationController::class);
    Route::post('reservations/{reservation}/approve-dept-head', [\App\Http\Controllers\MeetingRoomReservation\MeetingRoomReservationController::class, 'approveDeptHead'])->name('reservations.approve-dept-head');
    Route::post('reservations/{reservation}/reject-dept-head', [\App\Http\Controllers\MeetingRoomReservation\MeetingRoomReservationController::class, 'rejectDeptHead'])->name('reservations.reject-dept-head');
    Route::post('reservations/{reservation}/approve-ga-admin', [\App\Http\Controllers\MeetingRoomReservation\MeetingRoomReservationController::class, 'approveGAAdmin'])->name('reservations.approve-ga-admin');
    Route::post('reservations/{reservation}/reject-ga-admin', [\App\Http\Controllers\MeetingRoomReservation\MeetingRoomReservationController::class, 'rejectGAAdmin'])->name('reservations.reject-ga-admin');
    Route::post('reservations/{reservation}/allocate-room', [\App\Http\Controllers\MeetingRoomReservation\MeetingRoomReservationController::class, 'allocateRoom'])->name('reservations.allocate-room');
    Route::post('reservations/{reservation}/send-response', [\App\Http\Controllers\MeetingRoomReservation\MeetingRoomReservationController::class, 'sendResponse'])->name('reservations.send-response');
    Route::post('reservations/check-availability', [\App\Http\Controllers\MeetingRoomReservation\MeetingRoomReservationController::class, 'checkAvailability'])->name('reservations.check-availability');
    Route::get('allocation-diagram', [\App\Http\Controllers\MeetingRoomReservation\MeetingRoomReservationController::class, 'allocationDiagram'])->name('allocation-diagram');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::patch('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
});

require __DIR__ . '/auth.php';
