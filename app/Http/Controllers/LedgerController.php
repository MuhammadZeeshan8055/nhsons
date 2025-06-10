<?php

namespace App\Http\Controllers;

use App\Exports\ExportSuppliers;
use App\Imports\SuppliersImport;
use App\Ledger;
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
		return view('ledger.index');
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

    public function apiLedger()
    {
        $ledger = Ledger::with('customer')->get(); // eager load customer

        return Datatables::of($ledger)
            ->addColumn('customer_name', function ($ledger) {
                return $ledger->customer ? $ledger->customer->nama : '-';
            })
            ->addColumn('total_due', function ($ledger) {
                $totalDue = Ledger::where('customer_id', $ledger->customer_id)
                    ->selectRaw('SUM(bill_amount) - SUM(amount_paid) as total_due')
                    ->value('total_due');

                return number_format($totalDue, 2); // format as currency
            })
            ->addColumn('action', function ($ledger) {
                return 
                    '<a onclick="editForm(' . $ledger->id . ')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a> ' .
                    '<a onclick="deleteData(' . $ledger->id . ')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
            })
            ->rawColumns(['action'])
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
