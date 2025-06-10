<?php

namespace App\Http\Controllers;

use App\Category;
use App\Customer;
use App\Ledger;
use App\User;
use App\Exports\ExportProdukKeluar;
use App\Product;
use App\Product_Keluar;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use PDF;


class ProductKeluarController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,staff');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $categories  = Category::orderBy('name', 'ASC')
        ->get()
        ->pluck('name', 'id');
        $products = Product::orderBy('nama','ASC')
            ->get()
            ->pluck('nama','id');

        $customers = Customer::orderBy('nama','ASC')
            ->get()
            ->pluck('nama','id');
            
        $users = User::orderBy('name','ASC')
            ->get()
            ->pluck('name','id');

        $invoice_data = Product_Keluar::all();
        return view('product_keluar.index', compact('categories', 'products','customers','users', 'invoice_data'));
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
        $this->validate($request, [
            'bill_number' => 'required|unique:product_keluar',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'qty' => 'required|array',
            'qty.*' => 'required|integer|min:1',
            'customer_id' => 'required|exists:customers,id',
            'user_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'price' => 'required|array',
            'price.*' => 'required|numeric',
            'total' => 'required|array',
            'total.*' => 'required|numeric',
            'total_paid' => 'required|numeric',
            'category_id' => 'required|array',
            'category_id.*' => 'required',
        ]);
    
        $billNumber = $request->bill_number;
        $customerId = $request->customer_id;
        $user_id = $request->user_id;
        $tanggal = $request->tanggal;
    
        $productIds = $request->product_id;
        $qtys = $request->qty;
        $prices = $request->price;
        $totals = $request->total;
        $categories = $request->category_id;
        $total_paid = $request->total_paid;
    
        $grandTotal = 0;
    
        // Check stock availability before processing
        $stockErrors = [];
        foreach ($productIds as $index => $productId) {
            $product = Product::findOrFail($productId);
            $requestedQty = $qtys[$index];
    
            if ($product->qty < $requestedQty) {
                if ($product->qty == 0) {
                    $stockErrors[] = "Stock of product '{$product->nama}' is 0, cannot deduct from it.<br>";
                } else {
                    $stockErrors[] = "Insufficient stock for product '{$product->nama}'. Available: {$product->qty}, Requested: {$requestedQty}.<br>";
                }
            }
        }
    
        if (!empty($stockErrors)) {
            return response()->json([
                'success' => false,
                'message' => 'Stock validation failed',
                'errors' => $stockErrors
            ], 400);
        }
    
        foreach ($productIds as $index => $productId) {
            Product_Keluar::create([
                'bill_number' => $billNumber,
                'product_id' => $productId,
                'customer_id' => $customerId,
                'user_id' => $user_id,
                'qty' => $qtys[$index],
                'price' => $prices[$index],
                'total' => $totals[$index],
                'category_id' => $categories[$index],
                'tanggal' => $tanggal,
            ]);
    
            $product = Product::findOrFail($productId);
            $product->qty -= $qtys[$index];
            $product->save();
    
            $grandTotal += $totals[$index];
        }
        
        $date = now();
        Ledger::create([
            'customer_id' => $customerId,
            'bill_number' => $billNumber,
            'bill_amount' => $grandTotal,
            'amount_paid' => $total_paid,
            'transaction_date' => $date
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Products Out Created',
        ]);
    }
    

    
    public function update(Request $request, $billNumber)
    {
         $this->validate($request, [
            'date' => 'required|date',
            'customer_name' => 'required',
            'user_name' => 'required',
            'items' => 'required|array',
            'items.*.category_id' => 'required',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'items.*.total' => 'required|numeric',
        ]);
        
        // First retrieve all existing items related to this bill
        $existingItems = Product_Keluar::where('bill_number', $billNumber)->get();
        
        // For each existing item, add the quantity back to the product
        foreach ($existingItems as $item) {
            $product = Product::findOrFail($item->product_id);
            $product->qty += $item->qty; // Add back the quantity
            $product->save();
        }
        
        // Delete all existing bill items
        Product_Keluar::where('bill_number', $billNumber)->delete();
        
        // Get customer ID from name
        $customer = Customer::where('nama', $request->customer_name)->firstOrFail();
        
        // Get customer ID from name
        $user = User::where('name', $request->user_name)->firstOrFail();
        
        // Create new records for each item in the request
        foreach ($request->items as $item) {
            Product_Keluar::create([
                'bill_number' => $billNumber,
                'product_id' => $item['product_id'],
                'customer_id' => $customer->id,
                'user_id' => $user->id,
                'qty' => $item['qty'],
                'price' => $item['price'],
                'total' => $item['total'],
                'category_id' => $item['category_id'],
                'tanggal' => $request->date,
            ]);
            
            // Update product quantity
            $product = Product::findOrFail($item['product_id']);
            $product->qty -= $item['qty'];
            $product->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Bill updated successfully',
        ]);
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

    public function getByCustomer($id)
    {
        $totalDue = Ledger::where('customer_id', $id)
                    ->selectRaw('SUM(bill_amount) - SUM(amount_paid) as total_due')
                    ->value('total_due');
    
        return response()->json([
            'customer_id' => $id,
            'total_due' => (int) ($totalDue ?? 0)
        ]);
    }    


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product_keluar = Product_Keluar::find($id);
        return $product_keluar;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $billNumber=$id;
        // Fetch all product_keluar records with the given bill number
        $productKeluars = Product_Keluar::where('bill_number', $billNumber)->get();
    
        if ($productKeluars->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No records found for the given bill number.',
            ], 404);
        }
    
        // Loop through each record and restore product quantity
        foreach ($productKeluars as $record) {
            $product = Product::find($record->product_id);
            if ($product) {
                $product->qty += $record->qty; // Add the quantity back
                $product->save();
            }
    
            // Delete the Product_Keluar record
            $record->delete();
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Records deleted and stock restored successfully.',
        ]);
    }

    // public function apiProductsOut(){
    //     // $product = Product_Keluar::all();

    //     // return Datatables::of($product)
    //     //     ->addColumn('products_name', function ($product){
    //     //         return $product->product->nama;
    //     //     })
    //     //     ->addColumn('customer_name', function ($product){
    //     //         return $product->customer->nama;
    //     //     })
    //     //     ->addColumn('action', function($product){
    //     //         return'<a onclick="editForm('. $product->id .')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a> ' .
    //     //             '<a onclick="deleteData('. $product->id .')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
    //     //     })
    //     //     ->rawColumns(['products_name','customer_name','action'])->make(true);
            
            
    //     // unique bill number and view bill option
        
    //     // for simple total
        
    //     // $product = Product_Keluar::all()->groupBy('bill_number')
    //     // ->map(function ($group) {
    //     //     // Return first item from each group
    //     //     return $group->first();
    //     // })->values();
    
    //     // for grand total
    //     $product = Product_Keluar::all()
    //             ->groupBy('bill_number')
    //             ->map(function ($group) {
    //                 $first = $group->first();
    //                 $first->total = $group->sum('total'); // Add grand total here
    //                 return $first;
    //             })
    //             ->sortByDesc('id')
    //             ->values();
    
    //     return Datatables::of($product)
    //         ->addColumn('products_name', function ($product){
    //             return $product->product->nama;
    //         })
    //         ->addColumn('customer_name', function ($product){
    //             return $product->customer->nama;
    //         })
    //         ->addColumn('action', function($product){
    //             return'<a onclick="viewBill('. $product->bill_number .')" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> View Bill</a> ' .
    //                 '<a onclick="editForm('. $product->id .')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a> ' .
    //                 '<a onclick="deleteData('. $product->id .')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
    //         })
    //         ->rawColumns(['products_name','customer_name','action'])->make(true);

    // }
    
    public function apiProductsOut(Request $request)
    {
        $query = Product_Keluar::query();
        
        // Handle date filtering
        if ($request->has('date') && !empty($request->date)) {
            // Predefined date ranges from dropdown
            $dates = explode('/', $request->date);
            $fromDate = $dates[0]; // Start date
            $toDate = $dates[1];   // End date
            
            $query->whereBetween('tanggal', [$fromDate, $toDate]);
        } 
        elseif ($request->has('from_date') && $request->has('to_date') && 
                !empty($request->from_date) && !empty($request->to_date)) {
            // Custom date range from date inputs
            $query->whereBetween('tanggal', [$request->from_date, $request->to_date]);
        }
        
        // Get filtered results and group by bill number
        $products = $query->get()
            ->groupBy('bill_number')
            ->map(function ($group) {
                $first = $group->first();
                $first->total = $group->sum('total'); // Add grand total here
                return $first;
            })
            ->sortByDesc('id')
            ->values();
            
        return DataTables::of($products)
            ->addColumn('products_name', function ($product) {
                return $product->product->nama;
            })
            ->addColumn('customer_name', function ($product) {
                return $product->customer->nama;
            })
            ->addColumn('user_name', function ($product) {
                return $product->user->name;
            })
            ->addColumn('action', function($product) {
                $viewBtn = '<a onclick="viewBill('. $product->bill_number .')" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> View Bill</a>';
            
                if (auth()->check() && auth()->user()->role === 'admin') {
                    $editBtn = '<a onclick="editForm('. $product->bill_number .')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                    $deleteBtn = '<a onclick="deleteData('. $product->bill_number .')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                    return $viewBtn . ' ' . $editBtn . ' ' . $deleteBtn;
                }
            
                return $viewBtn;
            })
            ->rawColumns(['products_name', 'customer_name', 'action'])
            ->make(true);
    }
    
    
    public function searchByDate(Request $request)
    {
        if (!$request->has('date') || !str_contains($request->date, '/')) {
            return response()->json(['error' => 'Invalid date format.'], 400);
        }
    
        [$date1, $date2] = explode('/', $request->date);
    
        // Filter records by date before fetching
        $products = Product_Keluar::whereBetween('tanggal', [$date1, $date2])
            ->get()
            ->groupBy('bill_number')
            ->map(function ($group) {
                $first = $group->first();
                $first->total = $group->sum('total');
                return $first;
            })
            ->sortByDesc('id')
            ->values();
    
        return datatables()->of($products)
            ->addColumn('products_name', fn($product) => optional($product->product)->nama)
            ->addColumn('customer_name', fn($product) => optional($product->customer)->nama)
            ->addColumn('action', function ($product) {
                return '<a onclick="viewBill('. $product->bill_number .')" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> View Bill</a> ' .
                       '<a onclick="editForm('. $product->id .')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a> ' .
                       '<a onclick="deleteData('. $product->id .')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
            })
            ->rawColumns(['products_name','customer_name','action'])
            ->make(true);
    }

    // public function getBillData($bill_number)
    // {
    //     $billItems = Product_Keluar::where('bill_number', $bill_number)->get();
    
    //     if ($billItems->isEmpty()) {
    //         return response()->json(['error' => 'Bill not found'], 404);
    //     }
    
    //     $customerName = $billItems->first()->customer->nama;
    //     $date = $billItems->first()->tanggal;
    
    //     $productInfo = $billItems->map(function($item) {
    //         return [
    //             'product_name' => $item->product->nama,
    //             'category_name' => $item->product->category->name ?? null
    //         ];
    //     });
    
    //     return response()->json([
    //         'date' => $date,
    //         'bill_number' => $bill_number,
    //         'customer_name' => $customerName,
    //         'products' => $productInfo,
    //         'items' => $billItems
    //     ]);
    // }
    
    public function getBillData($bill_number)
    {
        $billItems = Product_Keluar::where('bill_number', $bill_number)
            ->with(['product.category', 'customer'])
            ->get();
        
        if ($billItems->isEmpty()) {
            return response()->json(['error' => 'Bill not found'], 404);
        }
        
        $customerName = $billItems->first()->customer->nama;
        $userName = $billItems->first()->user->name;
        $date = $billItems->first()->tanggal;
        
        $itemsWithCategory = $billItems->map(function($item) {
            $itemArray = $item->toArray();
    
            $itemArray['category_name'] = $item->product->category->name ?? null;
            
            return $itemArray;
        });
        
        $productInfo = $billItems->map(function($item) {
            return [
                'product_name' => $item->product->nama,
                'category_name' => $item->product->category->name ?? null
            ];
        });
        
        return response()->json([
            'date' => $date,
            'bill_number' => $bill_number,
            'customer_name' => $customerName,
            'user_name' => $userName,
            'products' => $productInfo,
            'items' => $itemsWithCategory  
        ]);
    }

    public function getSaleReport(Request $request)
    {
        $filter = $request->input('date');
        
        $users = User::orderBy('name','ASC')
            ->get()
            ->pluck('name','id');
        
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        
        if(!empty($filter)){
            if (!$request->has('date') || !str_contains($filter, '/')) {
                return response()->json(['error' => 'Invalid date format.'], 400);
            }
        
            [$date1, $date2] = explode('/', $filter);
            
            $todaySale = Product_Keluar::whereBetween('tanggal', [$date1, $date2])->sum('total');
            $monthSale = Product_Keluar::whereMonth('tanggal', now()->month)
                            ->whereYear('tanggal', now()->year)
                            ->sum('total');
            
            // You can calculate total cost and profit if needed
            $totalCost = 0;
            $totalProfit = 0;
        }elseif(!empty($from_date && $to_date)){
        
            $todaySale = Product_Keluar::whereBetween('tanggal', [$from_date, $to_date])->sum('total');
            $monthSale = Product_Keluar::whereMonth('tanggal', now()->month)
                            ->whereYear('tanggal', now()->year)
                            ->sum('total');
            
            // You can calculate total cost and profit if needed
            $totalCost = 0;
            $totalProfit = 0;
            
        }else{
            $todaySale = Product_Keluar::whereDate('tanggal', today())->sum('total');
            $monthSale = Product_Keluar::whereMonth('tanggal', now()->month)
                            ->whereYear('tanggal', now()->year)
                            ->sum('total');
            
            // You can calculate total cost and profit if needed
            $totalCost = 0;
            $totalProfit = 0;
        }
        
        
    
        return view('sale_reports.index', compact('todaySale', 'monthSale', 'totalCost', 'totalProfit','users'));
    }


    public function exportProductKeluarAll()
    {
        $product_keluar = Product_Keluar::all();
        $pdf = PDF::loadView('product_keluar.productKeluarAllPDF',compact('product_keluar'));
        return $pdf->download('product_out.pdf');
    }

    public function exportProductKeluar($id)
    {
        $product_keluar = Product_Keluar::findOrFail($id);
        $pdf = PDF::loadView('product_keluar.productKeluarPDF', compact('product_keluar'));
        return $pdf->download($product_keluar->id.'_product_out.pdf');
    }

    public function exportExcel()
    {
        return (new ExportProdukKeluar)->download('product_keluar.xlsx');
    }

    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)
            ->orderBy('nama', 'ASC')
            ->pluck('nama', 'id');
    
        return response()->json($products);
    }

}
