<?php

namespace App\Http\Controllers;

use App\Exports\ExportSuppliers;
use App\Imports\SuppliersImport;
use App\Ledger;
use App\Customer;
use Excel;
use Illuminate\Http\Request;
use PDF;
use Yajra\DataTables\DataTables;


class LedgerController extends Controller
{
    public function exportPDF(Request $request)
    {
        $customerId = $request->query('customer_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        
        if (!$customerId) {
            return redirect()->back()->with('error', 'Please select a customer first.');
        }
        
        // Get customer details
        $customer = Customer::findOrFail($customerId);
        
        // Build query with optional date filtering
        $query = Ledger::where('customer_id', $customerId);
        
        // Add date filtering - same logic as apiLedger method
        if (!empty($dateFrom) && !empty($dateTo)) {
            // If both dates are the same, search for specific date
            if ($dateFrom == $dateTo) {
                $query->whereDate('transaction_date', $dateFrom);
            } else {
                // Range search
                $query->whereDate('transaction_date', '>=', $dateFrom)
                      ->whereDate('transaction_date', '<=', $dateTo);
            }
        } elseif (!empty($dateFrom)) {
            $query->whereDate('transaction_date', '>=', $dateFrom);
        } elseif (!empty($dateTo)) {
            $query->whereDate('transaction_date', '<=', $dateTo);
        }
        
        $ledgerEntries = $query->orderBy('transaction_date', 'asc')->get();
        
        // Calculate running balance for each entry
        $runningBalance = 0;
        $ledgerEntriesWithBalance = $ledgerEntries->map(function ($entry) use (&$runningBalance) {
            $runningBalance += $entry->bill_amount - $entry->amount_paid;
            $entry->running_balance = $runningBalance;
            return $entry;
        });
        
        // Calculate totals
        $totalBillAmount = $ledgerEntries->sum('bill_amount');
        $totalPaidAmount = $ledgerEntries->sum('amount_paid');
        $totalDueAmount = $totalBillAmount - $totalPaidAmount;
        
        // Additional statistics
        $paidEntries = $ledgerEntries->where('bill_amount', '<=', 'amount_paid')->count();
        $unpaidEntries = $ledgerEntries->where('amount_paid', 0)->count();
        $partialEntries = $ledgerEntries->where('amount_paid', '>', 0)
                                      ->where('amount_paid', '<', 'bill_amount')->count();
        
        $data = [
            'customer' => $customer,
            'ledgerEntries' => $ledgerEntriesWithBalance,
            'totalBillAmount' => $totalBillAmount,
            'totalPaidAmount' => $totalPaidAmount,
            'totalDueAmount' => $totalDueAmount,
            'paidEntries' => $paidEntries,
            'unpaidEntries' => $unpaidEntries,
            'partialEntries' => $partialEntries,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'generatedDate' => now()->format('Y-m-d H:i:s'),
            'totalEntries' => $ledgerEntries->count()
        ];
        
        $pdf = PDF::loadView('ledger.pdf_export', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'ledger_' . str_replace(' ', '_', $customer->nama) . '_' . now()->format('Ymd_His') . '.pdf';
        
        // return $pdf->download($filename);
        return $pdf->stream($filename);
    }
    
    public function apiLedger(Request $request)
    {
        $customerId = $request->query('customer_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        
        // Build the query with optional filtering
        $query = Ledger::with('customer');
        
        if (!empty($customerId)) {
            $query->where('customer_id', $customerId);
        }
        
        // Add date filtering
        if (!empty($dateFrom) && !empty($dateTo)) {
            // If both dates are the same, search for specific date
            if ($dateFrom == $dateTo) {
                $query->whereDate('transaction_date', $dateFrom);
            } else {
                // Range search
                $query->whereDate('transaction_date', '>=', $dateFrom)
                      ->whereDate('transaction_date', '<=', $dateTo);
            }
        } elseif (!empty($dateFrom)) {
            $query->whereDate('transaction_date', '>=', $dateFrom);
        } elseif (!empty($dateTo)) {
            $query->whereDate('transaction_date', '<=', $dateTo);
        }
        
        // Order by transaction date for proper running balance calculation
        $ledgerEntries = $query->orderBy('transaction_date', 'asc')->get();
        
        // For running balance calculation, we need to consider:
        // 1. If customer is selected, calculate running balance for that customer only
        // 2. If no customer is selected, show individual transaction balance (bill_amount - amount_paid)
        
        if (!empty($customerId)) {
            // Calculate running balance for selected customer
            $runningBalance = 0;
            $ledgerEntriesWithBalance = $ledgerEntries->map(function ($entry) use (&$runningBalance) {
                $runningBalance += $entry->bill_amount - $entry->amount_paid;
                $entry->running_balance = $runningBalance;
                return $entry;
            });
        } else {
            // For general view (all customers), show individual transaction balance
            $ledgerEntriesWithBalance = $ledgerEntries->map(function ($entry) {
                $entry->running_balance = $entry->bill_amount - $entry->amount_paid;
                return $entry;
            });
        }
        
        return Datatables::of($ledgerEntriesWithBalance)
            ->addColumn('customer_name', function ($ledger) {
                return $ledger->customer ? $ledger->customer->nama : '-';
            })
            ->editColumn('bill_amount', function ($ledger) {
                return number_format($ledger->bill_amount, 2);
            })
            ->editColumn('amount_paid', function ($ledger) {
                return number_format($ledger->amount_paid, 2);
            })
            ->addColumn('balance', function ($ledger) {
                return '<span style="color: ' . ($ledger->running_balance > 0 ? '#dc3545' : '#28a745') . '">' . 
                       number_format($ledger->running_balance, 2) . '</span>';
            })
            ->addColumn('description', function ($ledger) {
                return $ledger->description ? $ledger->description : '-';
            })
            ->editColumn('transaction_date', function ($ledger) {
                return date('Y-m-d', strtotime($ledger->transaction_date));
            })
            ->addColumn('action', function ($ledger) {
                $actions = '';
                if (auth()->user()->role === 'admin') {
                    $actions .= '<a onclick="editForm(' . $ledger->id . ')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a> ';
                    $actions .= '<a onclick="deleteData(' . $ledger->id . ')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                }
                return $actions;
            })
            ->rawColumns(['action', 'balance'])
            ->make(true);
    }

    // Method to get customer summary for dashboard
    public function getCustomerSummary($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        $ledgerEntries = Ledger::where('customer_id', $customerId)->get();
        
        $summary = [
            'customer' => $customer,
            'total_bills' => $ledgerEntries->count(),
            'total_bill_amount' => $ledgerEntries->sum('bill_amount'),
            'total_paid_amount' => $ledgerEntries->sum('amount_paid'),
            'total_due_amount' => $ledgerEntries->sum('bill_amount') - $ledgerEntries->sum('amount_paid'),
            'last_transaction' => $ledgerEntries->sortByDesc('transaction_date')->first()
        ];
        
        return response()->json($summary);
    }
    
    public function index(Request $request)
    {
        $ledger = Ledger::all();

        $customers = Customer::orderBy('nama','ASC')
            ->get()
            ->pluck('nama','id');

        // Calculate total balance based on filters
        $totalBalance = $this->calculateTotalBalance($request);

        return view('ledger.index', compact('customers', 'totalBalance'));
    }

    // Add new method to calculate total balance
    private function calculateTotalBalance(Request $request)
    {
        $customerId = $request->query('customer_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        
        // Build query with optional filtering
        $query = Ledger::query();
        
        if (!empty($customerId)) {
            $query->where('customer_id', $customerId);
        }
        
        // Add date filtering
        if (!empty($dateFrom) && !empty($dateTo)) {
            if ($dateFrom == $dateTo) {
                $query->whereDate('transaction_date', $dateFrom);
            } else {
                $query->whereDate('transaction_date', '>=', $dateFrom)
                      ->whereDate('transaction_date', '<=', $dateTo);
            }
        } elseif (!empty($dateFrom)) {
            $query->whereDate('transaction_date', '>=', $dateFrom);
        } elseif (!empty($dateTo)) {
            $query->whereDate('transaction_date', '<=', $dateTo);
        }
        
        // Calculate total balance (Bill Amount - Amount Paid)
        $totalBillAmount = $query->sum('bill_amount');
        $totalPaidAmount = $query->sum('amount_paid');
        $totalBalance = $totalBillAmount - $totalPaidAmount;
        
        return $totalBalance;
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'required',
            'bill_amount' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'transaction_date' => 'required|date',
        ]);

        try {
            $ledger = Ledger::create($validatedData);
            return response()->json(['message' => 'Ledger created successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating ledger'], 500);
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        Ledger::destroy($id);

        return response()->json([
            'success' => true,
            'message' => 'Ledger Deleted',
        ]);
    }
}