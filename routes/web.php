<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductMasukController;
use App\Http\Controllers\ProductKeluarController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
	return view('auth.login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/sale_report', 'ProductKeluarController@getSaleReport')->name('sale_report');

Route::get('dashboard', function () {
	return view('layouts.master');
});

Route::group(['middleware' => 'auth'], function () {
	Route::resource('categories', 'CategoryController');
	Route::get('/apiCategories', 'CategoryController@apiCategories')->name('api.categories');
	Route::get('/exportCategoriesAll', 'CategoryController@exportCategoriesAll')->name('exportPDF.categoriesAll');
	Route::get('/exportCategoriesAllExcel', 'CategoryController@exportExcel')->name('exportExcel.categoriesAll');
	Route::get('categories/{id}/products', [CategoryController::class, 'getProducts'])->name('categories.products');


	Route::resource('customers', 'CustomerController');
	Route::get('/apiCustomers', 'CustomerController@apiCustomers')->name('api.customers');
	Route::post('/importCustomers', 'CustomerController@ImportExcel')->name('import.customers');
	Route::get('/exportCustomersAll', 'CustomerController@exportCustomersAll')->name('exportPDF.customersAll');
	Route::get('/exportCustomersAllExcel', 'CustomerController@exportExcel')->name('exportExcel.customersAll');

	Route::resource('sales', 'SaleController');
	Route::get('/apiSales', 'SaleController@apiSales')->name('api.sales');
	Route::post('/importSales', 'SaleController@ImportExcel')->name('import.sales');
	Route::get('/exportSalesAll', 'SaleController@exportSalesAll')->name('exportPDF.salesAll');
	Route::get('/exportSalesAllExcel', 'SaleController@exportExcel')->name('exportExcel.salesAll');

	Route::resource('suppliers', 'SupplierController');
	Route::get('/apiSuppliers', 'SupplierController@apiSuppliers')->name('api.suppliers');
	Route::post('/importSuppliers', 'SupplierController@ImportExcel')->name('import.suppliers');
	Route::get('/exportSupplierssAll', 'SupplierController@exportSuppliersAll')->name('exportPDF.suppliersAll');
	Route::get('/exportSuppliersAllExcel', 'SupplierController@exportExcel')->name('exportExcel.suppliersAll');

	Route::resource('products', 'ProductController')->middleware('role');;
	Route::get('/apiProducts', 'ProductController@apiProducts')->name('api.products');

	Route::resource('productsOut', 'ProductKeluarController');
	Route::get('/apiProductsOut', 'ProductKeluarController@apiProductsOut')->name('api.productsOut');
	Route::get('/exportProductKeluarAll', 'ProductKeluarController@exportProductKeluarAll')->name('exportPDF.productKeluarAll');
	Route::get('/exportProductKeluarAllExcel', 'ProductKeluarController@exportExcel')->name('exportExcel.productKeluarAll');
	Route::get('/exportProductKeluar/{id}', 'ProductKeluarController@exportProductKeluar')->name('exportPDF.productKeluar');
	Route::get('/products-by-category/{categoryId}', [ProductKeluarController::class, 'getProductsByCategory']);


	Route::resource('productsIn', 'ProductMasukController');
	Route::get('/apiProductsIn', 'ProductMasukController@apiProductsIn')->name('api.productsIn');
	Route::get('/exportProductMasukAll', 'ProductMasukController@exportProductMasukAll')->name('exportPDF.productMasukAll');
	Route::get('/exportProductMasukAllExcel', 'ProductMasukController@exportExcel')->name('exportExcel.productMasukAll');
	Route::get('/exportProductMasuk/{id}', 'ProductMasukController@exportProductMasuk')->name('exportPDF.productMasuk');
	Route::get('products-by-category/{categoryId}', [ProductMasukController::class, 'getProductsByCategory'])->name('products.byCategory');


	Route::resource('user', 'UserController');
	Route::get('/apiUser', 'UserController@apiUsers')->name('api.users');
	Route::get('/bill-data/{bill_number}', [ProductKeluarController::class, 'getBillData']);
    Route::put('productsOut/{bill_number}', 'ProductKeluarController@update')->name('productsOut.update');
    
    Route::get('/ledgers-by-customer/{id}', [ProductKeluarController::class, 'getByCustomer']);
    
    Route::resource('ledger', 'LedgerController');
	Route::get('/apiLedger', 'LedgerController@apiLedger')->name('api.ledger');
    
    //exportproduct
	Route::get('/exportProductAll', 'ProductController@exportProductAll')->name('exportPDF.productAll');
	Route::get('/exportProductAllExcel', 'ProductController@exportExcel')->name('exportExcel.productAll');
    Route::resource('ledger', 'LedgerController');
    Route::get('api/ledger', 'LedgerController@apiLedger')->name('api.ledger');
    
    // New PDF export route
    Route::get('ledger/export/pdf', 'LedgerController@exportPDF')->name('ledger.exportPDF');

});
