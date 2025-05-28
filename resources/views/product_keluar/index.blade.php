@extends('layouts.master')
<style>
    .mt-3{
         margin-top: 3%;
    }
    span#grand-total,
    span#view-bill-grand-total,
    span#edit-bill-grand-total{
        margin-right: 15%;
    }
    .date_inputs {
        display: flex;
        gap: 20px;
        align-items: center;
    }
</style>
<?php
    $today_date 		= date('Y-m-d').'/'.date('Y-m-d'); 
    $yesterday_date 	= date('Y-m-d', strtotime(date('Y-m-d')." -1 day")).'/'.date('Y-m-d', strtotime(date('Y-m-d')." -1 day"));
    
    $last7days_date 	= date('Y-m-d', strtotime(date('Y-m-d')." -6 day")).'/'.date('Y-m-d');
    $thismonth_date 	= date('Y-m-d', strtotime(date('Y-m-')."1")).'/'.date('Y-m-t', strtotime(date('Y-m-d')));
    $lastmonth_date 	= date('Y-m-d', strtotime(date('Y-m-')."1 -1 month")).'/'.date('Y-m-t', strtotime(date('Y-m-d')."-1 month"));
    $thisyear_date 		= date('Y-01-01').'/'.date('Y-12-31');
    $lastyear_date 		= date('Y-01-01', strtotime(date('Y-01-01')."-1 year")).'/'.date('Y-12-31', strtotime(date('Y-12-31')."-1 year"));
    $alltime_date 		= '1969-12-31/'.date('Y-12-31');
?>
@section('top')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <style>
        @media (max-width: 768px) {
            .btn {
                font-size: 12px;
                padding: 6px 8px;
            }

            .box-title {
                font-size: 16px;
            }

            table thead th,
            table tbody td {
                font-size: 12px;
                white-space: nowrap;
            }
        }
    </style>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header">
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="search-filter">
                        <form id="date-select-form">
                            <div class="form-group">
                                <label for="date-select">Search</label>
                                <select class="form-control" id="date-select" name="date">
                                    <option value="">Select</option>
                                    <option value="{{ $today_date }}">Today</option>
                                    <option value="{{ $yesterday_date }}">Yesterday</option>
                                    <option value="{{ $last7days_date }}">Last 7 Days</option>
                                    <option value="{{ $thismonth_date }}">This Month</option>
                                    <option value="{{ $lastmonth_date }}">Last Month</option>
                                    <option value="{{ $thisyear_date }}">This Year</option>
                                    <option value="{{ $lastyear_date }}">Last Year</option>
                                    <option value="{{ $alltime_date }}">All Time</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="search-filter">
                        <form id="custom-date-form">
                            <div class="form-group">
                                <label for="from-date">Search By Date</label>
                                <div class="date_inputs">
                                    <input type="date" id="from-date" name="from_date" class="form-control">
                                    <input type="date" id="to-date" name="to_date" class="form-control">
                                    <button type="button" id="apply-date-filter" class="btn btn-primary">Apply</button>
                                </div>
                            </div>
                        </form> 
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="search-filter mt-3">
                        <button type="button" id="reset-filters" class="btn btn-default mt-3">Reset Filters</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-header">
            <h3 class="box-title">Sale Order List</h3>
        </div>

        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'hafiz')
            <div class="box-header">
                <div class="d-none d-md-block">
                    <a onclick="addForm()" class="btn btn-success"><i class="fa fa-plus"></i> Add New Sale Order</a>
        @endif
        @if (auth()->user()->role === 'admin')
                    <a href="{{ route('exportPDF.productKeluarAll') }}" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Export PDF</a>
                    <a href="{{ route('exportExcel.productKeluarAll') }}" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Export Excel</a>
                </div>
            </div>
        @endif

        <div class="box-body">
            <div class="table-responsive">
                <table id="products-out-table" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Bill Number</th>
                            <!--<th>Products</th>-->
                            <th>Customer</th>
                            <th>Sales Person</th>
                            <!--<th>Qty.</th>-->
                            <!--<th>Price.</th>-->
                            <th>Grand Total</th>
                            <th>Date</th>
                            <th>Actions</th> <!-- New Actions column -->
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    @include('product_keluar.form')
@endsection

@section('bot')
    <!-- DataTables -->
    <script src="{{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- InputMask -->
    <script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- Validator -->
    <script src="{{ asset('assets/validator/validator.min.js') }}"></script>

    <script>
        // $(function() {
        //     $('#tanggal').datepicker({ autoclose: true });

        //     var table = $('#products-out-table').DataTable({
        //         processing: true,
        //         serverSide: true,
        //         ajax: "{{ route('api.productsOut') }}",
        //         order: [[5, 'desc']],
        //         columns: [
        //             {
        //                 data: null,
        //                 name: 'id',
        //                 render: function(data, type, row, meta) {
        //                     return meta.row + 1;
        //                 }
        //             },
        //             { data: 'bill_number', name: 'bill_number' },
        //             // { data: 'products_name', name: 'products_name' },
        //             { data: 'customer_name', name: 'customer_name' },
        //             // { data: 'qty', name: 'qty' },
        //             // { data: 'price', name: 'price' },
        //             { data: 'total', name: 'total' },
        //             { data: 'tanggal', name: 'tanggal' },
        //             { 
        //                 data: 'action', 
        //                 name: 'action',
        //                 orderable: false,
        //                 searchable: false 
        //             }
        //         ],
        //         responsive: true
        //     });

        //     $('#modal-form form').validator().on('submit', function(e) {
        //         if (!e.isDefaultPrevented()) {
        //             var id = $('#id').val();
        //             var url = save_method === 'add' 
        //                 ? "{{ url('productsOut') }}" 
        //                 : "{{ url('productsOut') }}/" + id;

        //             $.ajax({
        //                 url: url,
        //                 type: "POST",
        //                 data: new FormData($("#modal-form form")[0]),
        //                 contentType: false,
        //                 processData: false,
        //                 success: function(data) {
        //                     $('#modal-form').modal('hide');
        //                     // Reset the form fields
        //                     $('#modal-form form')[0].reset();
                        
        //                     // Remove all dynamically added product rows except the first one (if any)
        //                     $('#product-wrapper .product-row').not(':first').remove();
                        
        //                     // Also reset the first row fields
        //                     $('#product-wrapper .product-row:first').find('select, input').val('');
        //                     $('#grand-total').text('0.00');
        //                     table.ajax.reload();
        //                     swal('Success!', data.message, 'success');
        //                 },
        //                 error: function(data) {
        //                     swal('Oops...', data.message, 'error');
        //                 }
        //             });
        //             return false;
        //         }
        //     });
        // });
        
        
        // before stock validation error
        // $(function() {
        //     // Initialize datepicker
        //     $('#tanggal').datepicker({ autoclose: true });
        
        //     // Initialize DataTable
        //     var table = $('#products-out-table').DataTable({
        //         processing: true,
        //         serverSide: true,
        //         ajax: {
        //             url: "{{ route('api.productsOut') }}",
        //             data: function(d) {
        //                 var dateSelect = $('#date-select').val();
        //                 var fromDate = $('#from-date').val();
        //                 var toDate = $('#to-date').val();
        
        //                 if (dateSelect) {
        //                     d.date = dateSelect;
        //                 } else if (fromDate && toDate) {
        //                     d.from_date = fromDate;
        //                     d.to_date = toDate;
        //                 }
        //                 // If no filters, don't send any
        //             }
        //         },
        //         order: [[6, 'desc']], // Assuming column 4 is the 'tanggal' (date) column
        //         columns: [
        //             {
        //                 data: null,
        //                 name: 'id',
        //                 render: function(data, type, row, meta) {
        //                     return meta.row + 1;
        //                 }
        //             },
        //             { data: 'bill_number', name: 'bill_number' },
        //             { data: 'customer_name', name: 'customer_name' },
        //             { data: 'user_name', name: 'user_name' },
        //             { data: 'total', name: 'total' },
        //             { data: 'tanggal', name: 'tanggal' },
        //             { 
        //                 data: 'action', 
        //                 name: 'action',
        //                 orderable: false,
        //                 searchable: false 
        //             }
        //         ],
        //         responsive: true
        //     });
        
        //     // Date filter dropdown change
        //     $('#date-select').on('change', function() {
        //         if ($(this).val()) {
        //             $('#from-date').val('');
        //             $('#to-date').val('');
        //         }
        //         table.ajax.reload();
        //     });
        
        //     // Custom date filter apply
        //     $('#apply-date-filter').on('click', function() {
        //         var fromDate = $('#from-date').val();
        //         var toDate = $('#to-date').val();
        
        //         if (!fromDate || !toDate) {
        //             alert('Please select both From and To dates');
        //             return;
        //         }
        
        //         $('#date-select').val('');
        //         table.ajax.reload();
        //     });
        
        //     // Reset filters
        //     $('#reset-filters').on('click', function() {
        //         $('#date-select').val('');
        //         $('#from-date').val('');
        //         $('#to-date').val('');
        //         table.ajax.reload();
        //     });
        
        //     // Form submit handling
        //     $('#modal-form form').validator().on('submit', function(e) {
        //         if (!e.isDefaultPrevented()) {
        //             var id = $('#id').val();
        //             var url = save_method === 'add' 
        //                 ? "{{ url('productsOut') }}" 
        //                 : "{{ url('productsOut') }}/" + id;
        
        //             $.ajax({
        //                 url: url,
        //                 type: "POST",
        //                 data: new FormData($("#modal-form form")[0]),
        //                 contentType: false,
        //                 processData: false,
        //                 success: function(data) {
        //                     $('#modal-form').modal('hide');
        //                     $('#modal-form form')[0].reset();
        //                     $('#product-wrapper .product-row').not(':first').remove();
        //                     $('#product-wrapper .product-row:first').find('select, input').val('');
        //                     $('#grand-total').text('0.00');
        
        //                     // Clear filters to make sure new data is shown
        //                     $('#date-select').val('');
        //                     $('#from-date').val('');
        //                     $('#to-date').val('');
        
        //                     table.ajax.reload();
        //                     swal('Success!', data.message, 'success');
        //                 },
        //                 error: function(data) {
        //                     swal('Oops...', data.message || 'Something went wrong!', 'error');
        //                 }
        //             });
        //             return false;
        //         }
        //     });
        // });
        
        $(function() {
            // Initialize datepicker
            $('#tanggal').datepicker({ autoclose: true });
        
            // Initialize DataTable
            var table = $('#products-out-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('api.productsOut') }}",
                    data: function(d) {
                        var dateSelect = $('#date-select').val();
                        var fromDate = $('#from-date').val();
                        var toDate = $('#to-date').val();
        
                        if (dateSelect) {
                            d.date = dateSelect;
                        } else if (fromDate && toDate) {
                            d.from_date = fromDate;
                            d.to_date = toDate;
                        }
                        // If no filters, don't send any
                    }
                },
                order: [[6, 'desc']], // Assuming column 4 is the 'tanggal' (date) column
                columns: [
                    {
                        data: null,
                        name: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    { data: 'bill_number', name: 'bill_number' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'user_name', name: 'user_name' },
                    { data: 'total', name: 'total' },
                    { data: 'tanggal', name: 'tanggal' },
                    { 
                        data: 'action', 
                        name: 'action',
                        orderable: false,
                        searchable: false 
                    }
                ],
                responsive: true
            });
        
            // Date filter dropdown change
            $('#date-select').on('change', function() {
                if ($(this).val()) {
                    $('#from-date').val('');
                    $('#to-date').val('');
                }
                table.ajax.reload();
            });
        
            // Custom date filter apply
            $('#apply-date-filter').on('click', function() {
                var fromDate = $('#from-date').val();
                var toDate = $('#to-date').val();
        
                if (!fromDate || !toDate) {
                    alert('Please select both From and To dates');
                    return;
                }
        
                $('#date-select').val('');
                table.ajax.reload();
            });
        
            // Reset filters
            $('#reset-filters').on('click', function() {
                $('#date-select').val('');
                $('#from-date').val('');
                $('#to-date').val('');
                table.ajax.reload();
            });
        
            // Form submit handling
            $('#modal-form form').validator().on('submit', function(e) {
                if (!e.isDefaultPrevented()) {
                    
                    // Disable submit button to prevent duplicate submissions
                    const $submitBtn = $('#modal-form button[type="submit"]');
                    $submitBtn.prop('disabled', true).text('Submitting...');
                    
                    var id = $('#id').val();
                    var url = save_method === 'add' 
                        ? "{{ url('productsOut') }}" 
                        : "{{ url('productsOut') }}/" + id;
        
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: new FormData($("#modal-form form")[0]),
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            $('#modal-form').modal('hide');
                            $('#modal-form form')[0].reset();
                            $('#product-wrapper .product-row').not(':first').remove();
                            $('#product-wrapper .product-row:first').find('select, input').val('');
                            $('#grand-total').text('0.00');
        
                            // Clear filters to make sure new data is shown
                            $('#date-select').val('');
                            $('#from-date').val('');
                            $('#to-date').val('');
        
                            table.ajax.reload();
                            swal('Success!', data.message, 'success');
                            
                            // Re-enable submit button
                            $submitBtn.prop('disabled', false).text('Submit');
                        },
                        error: function(xhr, status, error) {
                            console.log('Error response:', xhr.responseJSON);
                            
                            var errorMessage = 'Something went wrong!';
                            
                            if (xhr.responseJSON) {
                                // Check if it's a stock validation error
                                if (xhr.responseJSON.errors && Array.isArray(xhr.responseJSON.errors)) {
                                    // Join all error messages with line breaks
                                    errorMessage = xhr.responseJSON.errors.join('\n');
                                } else if (xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                            }
                            
                            swal('Stock Error!', errorMessage, 'error');
                            
                            // Re-enable submit button on error
                            $submitBtn.prop('disabled', false).text('Submit');
                             
                        }
                    });
                    return false;
                }
            });
        });


        // function addForm() {
        //     save_method = "add";
        //     $('input[name=_method]').val('POST');
        //     $('#modal-form').modal('show');
        //     $('#modal-form form')[0].reset();
        //     $('.modal-title').text('Add New Sale Order');
        // }
        
        function addForm() {
            save_method = "add";
            $('input[name=_method]').val('POST');
            
            // Reset the form completely
            $('#modal-form form')[0].reset();
            
            // Keep only the first product row and clear its fields
            $('#product-wrapper .product-row').not(':first').remove();
            $('#product-wrapper .product-row:first').find('select, input').val('');
            
            // Reset the grand total in the add form
            $('#grand-total').text('0.00');
            
            // Show the modal
            $('#modal-form').modal('show');
            $('.modal-title').text('Add New Sale Order');
        }
        
        // function viewBill(bill_number) {
            // save_method = "add";
            // $('input[name=_method]').val('POST');
            // $('#view-bill-form').modal('show');
            // $('.modal-title').text('View Sale Order');
            // $('#view_bill_number').val(bill_number);
        // }
        
        // function viewBill(bill_number) {
        //     save_method = "add";
        //     $('input[name=_method]').val('POST');
        //     $('#view-bill-form').modal('show');
        //     $('.modal-title').text('View Sale Order');
            
        //     $.ajax({
        //         url: "{{ url('/bill-data') }}/" + bill_number,
        //         type: 'GET',
        //         dataType: 'json',
        //         success: function(data) {
        //             console.log('Bill loaded:', data);
                    
        //             // Create HTML content for modal body
        //             var html = `
        //                 <div class="row">
        //                     <div class="col-md-4">
        //                         <label class="control-label">Date</label>
        //                         <input data-date-format='yyyy-mm-dd' type="text" id="date" class="form-control" value="${data.date}" id="tanggal" name="tanggal" autocomplete="off" readonly>
        //                         <span class="help-block with-errors"></span>
        //                     </div>
        //                     <div class="col-md-4">
                                
        //                     </div>
        //                     <div class="col-md-4">
        //                         <label class="control-label">Bill Number</label>
        //                         <input type="text" class="form-control" id="view_bill_number" value="${bill_number}" name="bill_number" autocomplete="off" readonly>
        //                         <span class="help-block with-errors"></span>
        //                     </div>
        //                 </div>
                    
        //                 <div class="row">
        //                     <div class="col-md-6">
        //                         <label class="control-label">Customer</label>
        //                         <input type="text" class="form-control" id="customer_name" value="${data.customer_name}" name="customer_name" autocomplete="off" readonly>
        //                     </div>
        //                 </div>
                        
        //                 <div class="row mt-3">
        //                     <div class="col-md-2">
        //                         <label class="control-label">Category</label>
        //                     </div>
        //                     <div class="col-md-3">
        //                         <label class="control-label">Product</label>
        //                     </div>
        //                     <div class="col-md-2">
        //                         <label class="control-label">Qty</label>
        //                     </div>
        //                     <div class="col-md-2">
        //                         <label class="control-label">Price</label>
        //                     </div>
        //                     <div class="col-md-2">
        //                         <label class="control-label">Total</label>
        //                     </div>
        //                     <div class="col-md-1">
        //                         <!-- Empty for remove button column -->
        //                     </div>
        //                 </div>
                    
        //                 <hr>
                        
        //                 <div id="product-wrapper">`;
                    
        //             // Loop through each item in data.items array
        //             if (data.items && data.items.length > 0) {
        //                 for (let i = 0; i < data.items.length; i++) {
        //                     const item = data.items[i];
                            
        //                     html += `
        //                     <div class="row product-row">
        //                         <div class="col-md-2">
        //                             <select name="category_id[]" class="form-control select category-select" readonly>
        //                                 <option value="${item.category_name}">${item.category_name}</option>
        //                             </select>
        //                         </div>
        //                         <div class="col-md-3">
        //                             <select name="product_id[]" class="form-control select product-select" readonly>
        //                                 <option value="${item.product_id}">${item.product.nama}</option>
        //                             </select>
        //                         </div>
        //                         <div class="col-md-2">
        //                             <input type="number" name="qty[]" value="${item.qty}" placeholder="qty" class="form-control" readonly>
        //                         </div>
        //                         <div class="col-md-2">
        //                             <input type="number" name="price[]" value="${item.price}" placeholder="price" class="form-control" readonly>
        //                         </div>
        //                         <div class="col-md-2">
        //                             <input type="number" name="total[]" value="${item.total}" placeholder="total" class="form-control" readonly>
        //                         </div>
        //                     </div>
        //                     <br>
        //                     `;
        //                 }
        //             } else {
        //                 // If no items, display an empty row
        //                 html += `
        //                     <div class="row product-row">
        //                         <div class="col-md-2">
        //                             <select name="category_id[]" class="form-control select category-select" required>
        //                                 <option value="">Category</option>
        //                             </select>
        //                         </div>
        //                         <div class="col-md-3">
        //                             <select name="product_id[]" class="form-control select product-select" required>
        //                                 <option value="">-- Choose Product --</option>
        //                             </select>
        //                         </div>
        //                         <div class="col-md-2">
        //                             <input type="number" name="qty[]" placeholder="qty" class="form-control" required>
        //                         </div>
        //                         <div class="col-md-2">
        //                             <input type="number" name="price[]" placeholder="price" class="form-control" required>
        //                         </div>
        //                         <div class="col-md-2">
        //                             <input type="number" name="total[]" placeholder="total" class="form-control" required>
        //                         </div>
        //                     </div>`;
        //             }
                    
        //             html += `
        //                 </div>`;
                    
        //             // Replace the modal-body content with our new HTML
        //             $('#view-bill-body').html(html);
                    
        //             // Calculate grand total
        //             let grandTotal = 0;
        //             if (data.items && data.items.length > 0) {
        //                 // Sum up all item totals
        //                 data.items.forEach(item => {
        //                     grandTotal += parseFloat(item.total);
        //                 });
        //             }
                    
        //             // Format grand total to 2 decimal places
        //             const formattedGrandTotal = grandTotal.toFixed(2);
                    
        //             // Create footer HTML with grand total
        //             const footerHtml = `
        //                 <div>
        //                     <strong>Grand Total: PKR <span id="grand-total">${formattedGrandTotal}</span></strong>
        //                 </div>
        //             `;
                    
        //             // Replace the modal-footer content
        //             $('#view-bill-footer').html(footerHtml);
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Error fetching bill:', status, error);
        //             console.error('Response:', xhr.responseText);
        //             alert('Could not load bill data.');
        //         }
        //     });
        // }

        function viewBill(bill_number) {
            save_method = "add";
            $('input[name=_method]').val('POST');
            $('#view-bill-form').modal('show');
            $('#view-bill-form .modal-title').text('View Sale Order');
            
            $.ajax({
                url: "{{ url('/bill-data') }}/" + bill_number,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // console.log('Bill loaded:', data);
                    
                    // Create HTML content for modal body
                    var html = `
                        <div class="row">
                            <div class="col-md-4">
                                <label class="control-label">Date</label>
                                <input data-date-format='yyyy-mm-dd' type="text" id="view_date" class="form-control" value="${data.date}" name="view_tanggal" autocomplete="off" readonly>
                                <span class="help-block with-errors"></span>
                            </div>
                            <div class="col-md-4">
                                
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">Bill Number</label>
                                <input type="text" class="form-control" id="view_bill_number" value="${bill_number}" name="view_bill_number" autocomplete="off" readonly>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">Customer</label>
                                <input type="text" class="form-control" id="view_customer_name" value="${data.customer_name}" name="view_customer_name" autocomplete="off" readonly>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">Sales Person</label>
                                <input type="text" class="form-control" id="view_customer_name" value="${data.user_name}" name="view_user_name" autocomplete="off" readonly>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-2">
                                <label class="control-label">Category</label>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label">Product</label>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">Qty</label>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">Price</label>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">Total</label>
                            </div>
                            <div class="col-md-1">
                                <!-- Empty for remove button column -->
                            </div>
                        </div>
                    
                        <hr>
                        
                        <div id="view-product-wrapper">`;
                    
                    // Loop through each item in data.items array
                    if (data.items && data.items.length > 0) {
                        for (let i = 0; i < data.items.length; i++) {
                            const item = data.items[i];
                            
                            html += `
                            <div class="row view-product-row">
                                <div class="col-md-2">
                                    <select name="view_category_id[]" class="form-control select" readonly>
                                        <option value="${item.category_name}">${item.category_name}</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="view_product_id[]" class="form-control select" readonly>
                                        <option value="${item.product_id}">${item.product.nama}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="view_qty[]" value="${item.qty}" placeholder="qty" class="form-control" readonly>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="view_price[]" value="${item.price}" placeholder="price" class="form-control" readonly>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="view_total[]" value="${item.total}" placeholder="total" class="form-control" readonly>
                                </div>
                            </div>
                            <br>
                            `;
                        }
                    } else {
                        // If no items, display an empty row
                        html += `
                            <div class="row">
                                <div class="col-md-12">
                                    <p>No product items found for this bill.</p>
                                </div>
                            </div>`;
                    }
                    
                    html += `
                        </div>`;
                    
                    // Replace the modal-body content with our new HTML
                    $('#view-bill-body').html(html);
                    
                    // Calculate grand total
                    let grandTotal = 0;
                    if (data.items && data.items.length > 0) {
                        // Sum up all item totals
                        data.items.forEach(item => {
                            grandTotal += parseFloat(item.total);
                        });
                    }
                    
                    // Format grand total to 2 decimal places
                    const formattedGrandTotal = grandTotal.toFixed(2);
                    
                    // Create footer HTML with grand total
                    const footerHtml = `
                        <div>
                            <strong>Grand Total: PKR <span id="view-bill-grand-total">${formattedGrandTotal}</span></strong>
                        </div>
                        <button type="button" class="btn btn-secondary mt-3" data-dismiss="modal">Close</button>
                    `;
                    
                    // Replace the modal-footer content
                    $('#view-bill-footer').html(footerHtml);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching bill:', status, error);
                    console.error('Response:', xhr.responseText);
                    alert('Could not load bill data.');
                }
            });
        }
        
        // function editForm(bill_number) {
        //     save_method = "add";
        //     $('input[name=_method]').val('POST');
        //     $('#edit-bill-form').modal('show');
        //     $('.modal-title').text('Edit Sale Order');
        //     $('#edit_bill_number').val(bill_number);
            
        //     $.ajax({
        //         url: "{{ url('/bill-data') }}/" + bill_number,
        //         type: 'GET',
        //         dataType: 'json',
        //         success: function(data) {
        //             // console.log('Bill loaded:', data);
                    
        //             // Create HTML content for modal body
        //             var html = `
        //                 <div class="row">
        //                     <div class="col-md-4">
        //                         <label class="control-label">Date</label>
        //                         <input data-date-format='yyyy-mm-dd' type="text" id="view_date" class="form-control" value="${data.date}" name="view_tanggal" autocomplete="off">
        //                         <span class="help-block with-errors"></span>
        //                     </div>
        //                     <div class="col-md-4">
                                
        //                     </div>
        //                     <div class="col-md-4">
        //                         <label class="control-label">Bill Number</label>
        //                         <input type="text" class="form-control" id="view_bill_number" value="${bill_number}" name="view_bill_number" autocomplete="off">
        //                         <span class="help-block with-errors"></span>
        //                     </div>
        //                 </div>
                    
        //                 <div class="row">
        //                     <div class="col-md-6">
        //                         <label class="control-label">Customer</label>
        //                         <input type="text" class="form-control" id="view_customer_name" value="${data.customer_name}" name="view_customer_name" autocomplete="off">
        //                     </div>
        //                 </div>
                        
        //                 <div class="row mt-3">
        //                     <div class="col-md-2">
        //                         <label class="control-label">Category</label>
        //                     </div>
        //                     <div class="col-md-3">
        //                         <label class="control-label">Product</label>
        //                     </div>
        //                     <div class="col-md-2">
        //                         <label class="control-label">Qty</label>
        //                     </div>
        //                     <div class="col-md-2">
        //                         <label class="control-label">Price</label>
        //                     </div>
        //                     <div class="col-md-2">
        //                         <label class="control-label">Total</label>
        //                     </div>
        //                     <div class="col-md-1">
        //                         <!-- Empty for remove button column -->
        //                     </div>
        //                 </div>
                    
        //                 <hr>
                        
        //                 <div id="view-product-wrapper">`;
                    
        //             // Loop through each item in data.items array
        //             if (data.items && data.items.length > 0) {
        //                 for (let i = 0; i < data.items.length; i++) {
        //                     const item = data.items[i];
                            
        //                     html += `
        //                     <div class="row view-product-row">
        //                         <div class="col-md-2">
        //                             <select name="view_category_id[]" class="form-control select">
        //                                 <option value="${item.category_name}">${item.category_name}</option>
        //                             </select>
        //                         </div>
        //                         <div class="col-md-3">
        //                             <select name="view_product_id[]" class="form-control select">
        //                                 <option value="${item.product_id}">${item.product.nama}</option>
        //                             </select>
        //                         </div>
        //                         <div class="col-md-2">
        //                             <input type="number" name="view_qty[]" value="${item.qty}" placeholder="qty" class="form-control">
        //                         </div>
        //                         <div class="col-md-2">
        //                             <input type="number" name="view_price[]" value="${item.price}" placeholder="price" class="form-control">
        //                         </div>
        //                         <div class="col-md-2">
        //                             <input type="number" name="view_total[]" value="${item.total}" placeholder="total" class="form-control">
        //                         </div>
        //                         <div class="col-md-1 remove-button-style">
        //                         <span class="btn text-danger remove-row" style="font-size: 20px; line-height: 1;">&times;</span>
        //                     </div>
        //                     </div>
        //                     <br>
        //                     `;
        //                 }
        //             } else {
        //                 // If no items, display an empty row
        //                 html += `
        //                     <div class="row">
        //                         <div class="col-md-12">
        //                             <p>No product items found for this bill.</p>
        //                         </div>
        //                     </div>`;
        //             }
                    
        //             html += `
                            
        //                 </div>
                        
        //                 <div class="col-md-12">
        //                     <button type="button" id="add-product" class="btn btn-danger btn-sm btn-outline-primary" style="margin-top:5%;margin-bottom:5%">+ Add Product</button>
        //                 </div>
                        
                        
        //                 `;
                    
        //             // Replace the modal-body content with our new HTML
        //             $('#edit-bill-body').html(html);
                    
        //             // Calculate grand total
        //             let grandTotal = 0;
        //             if (data.items && data.items.length > 0) {
        //                 // Sum up all item totals
        //                 data.items.forEach(item => {
        //                     grandTotal += parseFloat(item.total);
        //                 });
        //             }
                    
        //             // Format grand total to 2 decimal places
        //             const formattedGrandTotal = grandTotal.toFixed(2);
                    
        //             // Create footer HTML with grand total
        //             const footerHtml = `
        //                 <div>
        //                     <strong>Grand Total: PKR <span id="edit-bill-grand-total">${formattedGrandTotal}</span></strong>
        //                 </div>
        //                 <button type="button" class="btn btn-secondary mt-3" data-dismiss="modal">Close</button>
        //             `;
                    
        //             // Replace the modal-footer content
        //             $('#edit-bill-footer').html(footerHtml);
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Error fetching bill:', status, error);
        //             console.error('Response:', xhr.responseText);
        //             alert('Could not load bill data.');
        //         }
        //     });
        // }
        
        // Modified editForm function
        function editForm(bill_number) {
            save_method = "edit"; // Changed from "add" to "edit" for clarity
            $('input[name=_method]').val('POST');
            $('#edit-bill-form').modal('show');
            $('.modal-title').text('Edit Sale Order');
            
            $.ajax({
                url: "{{ url('/bill-data') }}/" + bill_number,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Create HTML content for modal body
                    var html = `
                        <div class="row">
                            <div class="col-md-4">
                                <label class="control-label">Date</label>
                                <input data-date-format='yyyy-mm-dd' type="text" id="edit_date" class="form-control" value="${data.date}" name="edit_tanggal" autocomplete="off">
                                <span class="help-block with-errors"></span>
                            </div>
                            <div class="col-md-4">
                                
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">Bill Number</label>
                                <input type="text" class="form-control" id="edit_bill_number" value="${bill_number}" name="edit_bill_number" autocomplete="off" readonly>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">Customer</label>
                                <select name="edit_customer_name" id="edit_customer_name" class="form-control select customer-select" required>
                                    <option value="${data.customer_name}">${data.customer_name}</option>
                                    @foreach($customers as $id => $name)
                                        <option value="{{ $name }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">Sales Person</label>
                                <select name="edit_user_name" id="edit_user_name" class="form-control select customer-select" required>
                                    <option value="${data.user_name}">${data.user_name}</option>
                                    @foreach($users as $id => $name)
                                        <option value="{{ $name }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-2">
                                <label class="control-label">Category</label>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label">Product</label>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">Qty</label>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">Price</label>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">Total</label>
                            </div>
                            <div class="col-md-1">
                                <!-- Empty for remove button column -->
                            </div>
                        </div>
                    
                        <hr>
                        
                        <div id="edit-product-wrapper">`;
                    
                    // Loop through each item in data.items array
                    if (data.items && data.items.length > 0) {
                        for (let i = 0; i < data.items.length; i++) {
                            const item = data.items[i];
                            
                            html += `
                            <div class="row edit-product-row">
                                <div class="col-md-2">
                                    <select name="edit_category_id[]" class="form-control select edit-category-select">
                                        <option value="${item.category_id}">${item.category_name}</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="edit_product_id[]" class="form-control select edit-product-select">
                                        <option value="${item.product_id}">${item.product.nama}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="edit_qty[]" value="${item.qty}" placeholder="qty" class="form-control edit-qty">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="edit_price[]" value="${item.price}" placeholder="price" class="form-control edit-price">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="edit_total[]" value="${item.total}" placeholder="total" class="form-control edit-total">
                                </div>
                                <div class="col-md-1 remove-button-style">
                                    <span class="btn text-danger edit-remove-row" style="font-size: 20px; line-height: 1;">&times;</span>
                                </div>
                            </div>
                            <br>
                            `;
                        }
                    } else {
                        // If no items, display an empty row
                        html += `
                            <div class="row">
                                <div class="col-md-12">
                                    <p>No product items found for this bill.</p>
                                </div>
                            </div>`;
                    }
                    
                    html += `
                        </div>
                        
                        <div class="col-md-12">
                            <button type="button" id="edit-add-product" class="btn btn-danger btn-sm btn-outline-primary" style="margin-top:5%;margin-bottom:5%">+ Add Product</button>
                        </div>
                    `;
                    
                    // Replace the modal-body content with our new HTML
                    $('#edit-bill-body').html(html);
                    
                    // Calculate grand total
                    updateEditGrandTotal();
                    
                    // Create footer HTML with grand total and save button
                    const footerHtml = `
                        <div>
                            <strong>Grand Total: PKR <span id="edit-bill-grand-total">0.00</span></strong>
                        </div>
                        <button type="button" class="btn btn-danger pull-left mt-3" data-dismiss="modal">Cancel</button>
                        <button type="button" id="edit-save-button" class="btn btn-success mt-3">Save Changes</button>
                    `;
                    
                    // Replace the modal-footer content
                    $('#edit-bill-footer').html(footerHtml);
                    
                    // Initialize edit form datepicker if needed
                    $('#edit_date').datepicker({ autoclose: true });
                    
                    // Attach event handlers for the edit form
                    attachEditFormEventHandlers();
                    
                    // Update the grand total initially
                    updateEditGrandTotal();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching bill:', status, error);
                    console.error('Response:', xhr.responseText);
                    alert('Could not load bill data.');
                }
            });
        }
        
        // Function to update grand total in the edit form
        function updateEditGrandTotal() {
            let grandTotal = 0;
            $('#edit-bill-form input[name="edit_total[]"]').each(function () {
                grandTotal += parseFloat($(this).val()) || 0;
            });
            $('#edit-bill-grand-total').text(grandTotal.toFixed(2));
        }
        
        // Function to attach all event handlers for the edit form
        function attachEditFormEventHandlers() {
            // Handle Add Product button click in edit form
            $('#edit-add-product').off('click').on('click', function() {
                var newRow = `
                    <div class="row edit-product-row">
                        <div class="col-md-2 mt-3">
                            <select name="edit_category_id[]" class="form-control select edit-category-select">
                                <option value="">Category</option>
                                @foreach($categories as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mt-3">
                            <select name="edit_product_id[]" class="form-control select edit-product-select">
                                <option value="">-- Choose Product --</option>
                                @foreach($products as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mt-3">
                            <input type="number" name="edit_qty[]" placeholder="qty" class="form-control edit-qty">
                        </div>
                        <div class="col-md-2 mt-3">
                            <input type="number" name="edit_price[]" placeholder="price" class="form-control edit-price">
                        </div>
                        <div class="col-md-2 mt-3">
                            <input type="number" name="edit_total[]" placeholder="total" class="form-control edit-total">
                        </div>
                        <div class="col-md-1 mt-3 remove-button-style">
                            <span class="btn text-danger edit-remove-row" style="font-size: 20px; line-height: 1;">&times;</span>
                        </div>
                    </div>
                `;
                $('#edit-product-wrapper').append(newRow);
                
                // Re-attach category change event handlers for the new row
                attachCategoryChangeHandlers();
                
                // Re-attach calculation handlers for the new row
                attachEditCalculationHandlers();
            });
            
            // Handle Remove Row button click in edit form using event delegation
            $('#edit-bill-form').off('click', '.edit-remove-row').on('click', '.edit-remove-row', function() {
                $(this).closest('.edit-product-row').remove();
                updateEditGrandTotal();
            });
            
            // Attach category change event handlers for all rows
            attachCategoryChangeHandlers();
            
            // Attach calculation handlers for all rows
            attachEditCalculationHandlers();
            
            // Handle Save Changes button click
            $('#edit-save-button').off('click').on('click', function() {
                saveEditFormData();
            });
        }
        
        // Function to attach category change handlers to all category selects in edit form
        function attachCategoryChangeHandlers() {
            $('.edit-category-select').off('change').on('change', function() {
                var categoryId = $(this).val();
                var productSelect = $(this).closest('.edit-product-row').find('.edit-product-select');
                
                productSelect.empty().append('<option value="">-- Choose Product --</option>');
                
                if (categoryId) {
                    $.ajax({
                        url: `/products-by-category/${categoryId}`,
                        type: 'GET',
                        success: function(data) {
                            $.each(data, function(id, name) {
                                productSelect.append(`<option value="${id}">${name}</option>`);
                            });
                        },
                        error: function() {
                            alert('Failed to fetch products. Please try again.');
                        }
                    });
                }
            });
        }
        
        // Function to attach calculation handlers to qty and price inputs in edit form
        function attachEditCalculationHandlers() {
            $('.edit-qty, .edit-price').off('input').on('input', function() {
                var row = $(this).closest('.edit-product-row');
                var qty = parseFloat(row.find('.edit-qty').val()) || 0;
                var price = parseFloat(row.find('.edit-price').val()) || 0;
                var total = qty * price;
                row.find('.edit-total').val(total.toFixed(2));
                
                updateEditGrandTotal();
            });
        }
        
        // Function to save the edit form data
        function saveEditFormData() {
            // Collect all the form data
            var billNumber = $('#edit_bill_number').val();
            var date = $('#edit_date').val();
            var customerName = $('#edit_customer_name').val();
            var userName = $('#edit_user_name').val();
            
            // Collect all the products
            var items = [];
            $('.edit-product-row').each(function() {
                var row = $(this);
                var item = {
                    category_id: row.find('select[name="edit_category_id[]"]').val(),
                    product_id: row.find('select[name="edit_product_id[]"]').val(),
                    qty: row.find('input[name="edit_qty[]"]').val(),
                    price: row.find('input[name="edit_price[]"]').val(),
                    total: row.find('input[name="edit_total[]"]').val()
                };
                items.push(item);
            });
            
            // Create the data object to send
            var formData = {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                bill_number: billNumber,
                date: date,
                customer_name: customerName,
                user_name: userName,
                items: items
            };
            
            // Send the AJAX request
            $.ajax({
                url: "{{ url('productsOut') }}/" + billNumber,
                type: "POST",
                data: JSON.stringify(formData),
                contentType: "application/json",
                dataType: "json",
                success: function(data) {
                    $('#edit-bill-form').modal('hide');
                    $('#products-out-table').DataTable().ajax.reload();
                    swal('Success!', 'Bill updated successfully', 'success');
                },
                error: function(xhr, status, error) {
                    console.error('Error updating bill:', status, error);
                    console.error('Response:', xhr.responseText);
                    swal('Oops...', 'Something went wrong when updating the bill!', 'error');
                }
            });
        }
        
        
        function deleteData(bill_number) {
            swal({
                title: 'Are you sure?',
                text: 'You will not be able to recover this record!',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ url('productsOut') }}" + '/' + bill_number,
                        type: "POST",
                        data: {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            $('#products-out-table').DataTable().ajax.reload();
                            swal('Deleted!', data.message, 'success');
                        },
                        error: function() {
                            swal('Oops...', 'Something went wrong!', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
