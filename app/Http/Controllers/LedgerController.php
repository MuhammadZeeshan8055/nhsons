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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ledger = Ledger::all();

        $customers = Customer::orderBy('nama','ASC')
            ->get()
            ->pluck('nama','id');

        return view('ledger.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function apiLedger(Request $request)
    {
        // Get the customer_id from query parameters
        $customerId = $request->query('customer_id');

        // Build the query with optional filtering
        $query = Ledger::with('customer');

        if (!empty($customerId)) {
            $query->where('customer_id', $customerId);
        }

        $ledger = $query->get();

        // Calculate total due for footer
        $totalDue = $ledger->sum(function ($item) {
            return $item->bill_amount - $item->amount_paid;
        });

        return Datatables::of($ledger)
            ->addColumn('customer_name', function ($ledger) {
                return $ledger->customer ? $ledger->customer->nama : '-';
            })
            ->addColumn('action', function ($ledger) {
                return 
                    '<a onclick="editForm(' . $ledger->id . ')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a> ' .
                    '<a onclick="deleteData(' . $ledger->id . ')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
            })
            ->rawColumns(['action'])
            ->with('totalDue', number_format($totalDue, 2))
            ->make(true);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
