<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supply;
use App\Models\SupplyTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SupplyTransactionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = SupplyTransaction::with(['supply', 'user', 'department'])
                ->select('supply_transactions.*');

            return DataTables::of($query)
                ->addColumn('index', function ($transaction) {
                    return '';
                })
                ->addColumn('supply_name', function ($transaction) {
                    return $transaction->supply ? $transaction->supply->name : 'N/A';
                })
                ->addColumn('supply_code', function ($transaction) {
                    return $transaction->supply ? $transaction->supply->code : 'N/A';
                })
                ->addColumn('type_badge', function ($transaction) {
                    $badgeClass = $transaction->type === 'in' ? 'badge-success' : 'badge-danger';
                    $icon = $transaction->type === 'in' ? 'fa-arrow-up' : 'fa-arrow-down';
                    return '<span class="badge ' . $badgeClass . '"><i class="fas ' . $icon . '"></i> ' . strtoupper($transaction->type) . '</span>';
                })
                ->addColumn('source_badge', function ($transaction) {
                    $badgeClass = $transaction->source === 'SAP' ? 'badge-primary' : 'badge-secondary';
                    return '<span class="badge ' . $badgeClass . '">' . strtoupper($transaction->source) . '</span>';
                })
                ->addColumn('supplier_name', function ($transaction) {
                    return $transaction->supplier_name ?? 'N/A';
                })
                ->addColumn('purchase_order_no', function ($transaction) {
                    return $transaction->purchase_order_no ?? 'N/A';
                })
                ->addColumn('department_name', function ($transaction) {
                    return $transaction->department ? $transaction->department->department_name : 'N/A';
                })
                ->addColumn('quantity_formatted', function ($transaction) {
                    $sign = $transaction->type === 'in' ? '+' : '-';
                    return $sign . number_format($transaction->quantity);
                })
                ->addColumn('transaction_date_formatted', function ($transaction) {
                    return $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : 'N/A';
                })
                ->addColumn('user_name', function ($transaction) {
                    return $transaction->user ? $transaction->user->name : 'N/A';
                })
                ->addColumn('actions', function ($transaction) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('supplies.transactions.show', $transaction) . '" class="btn btn-info btn-sm" title="View"><i class="fas fa-eye"></i></a>';

                    /** @var \App\Models\User|null $user */
                    $user = Auth::user();
                    if ($user && $user->can('delete supply transactions')) {
                        $actions .= '<button type="button" class="btn btn-danger btn-sm" onclick="deleteTransaction(' . $transaction->id . ')" title="Delete"><i class="fas fa-trash"></i></button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['type_badge', 'source_badge', 'actions'])
                ->make(true);
        }

        return view('admin.supply-transactions.index');
    }

    public function create()
    {
        $supplies = Supply::orderBy('name')->get();
        return view('admin.supply-transactions.create', compact('supplies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supply_id' => 'required|exists:supplies,id',
            'type' => 'required|in:in,out',
            'source' => 'required|in:SAP,manual',
            'supplier_name' => 'nullable|string|max:255',
            'purchase_order_no' => 'nullable|string|max:100',
            'department_id' => 'nullable|exists:departments,id',
            'quantity' => 'required|integer|min:1',
            'reference_no' => 'nullable|string|max:100',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check stock availability for 'out' transactions
        if ($request->type === 'out') {
            $supply = Supply::find($request->supply_id);
            if ($supply->current_stock < $request->quantity) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Insufficient stock. Available: ' . $supply->current_stock . ' ' . $supply->unit);
            }
        }

        $transaction = SupplyTransaction::create([
            'supply_id' => $request->supply_id,
            'type' => $request->type,
            'source' => $request->source,
            'supplier_name' => $request->supplier_name,
            'purchase_order_no' => $request->purchase_order_no,
            'department_id' => $request->department_id,
            'quantity' => $request->quantity,
            'reference_no' => $request->reference_no,
            'transaction_date' => $request->transaction_date,
            'notes' => $request->notes,
            'user_id' => Auth::id(),
        ]);

        // Update supply stock
        $supply = Supply::find($request->supply_id);
        if ($request->type === 'in') {
            $supply->increment('current_stock', $request->quantity);
        } else {
            $supply->decrement('current_stock', $request->quantity);
        }

        return redirect()->route('supplies.transactions.index')
            ->with('success', 'Stock transaction created successfully.');
    }

    public function show(SupplyTransaction $supplyTransaction)
    {
        $supplyTransaction->load(['supply', 'user', 'department']);
        return view('admin.supply-transactions.show', compact('supplyTransaction'));
    }

    public function destroy(SupplyTransaction $supplyTransaction)
    {
        // Reverse the stock transaction
        $supply = $supplyTransaction->supply;
        if ($supplyTransaction->type === 'in') {
            $supply->decrement('current_stock', $supplyTransaction->quantity);
        } else {
            $supply->increment('current_stock', $supplyTransaction->quantity);
        }

        $supplyTransaction->delete();

        return response()->json(['message' => 'Stock transaction deleted successfully.']);
    }
}
