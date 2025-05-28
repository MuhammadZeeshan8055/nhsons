@extends('layouts.master')

@section('top')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

<!-- daterange picker -->
<link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('content')
<div class="box box-success">

    <div class="box-header">
        <h3 class="box-title">Purchase Products List</h3>
    </div>

    @if (auth()->user()->role === 'admin')
    <div class="box-header">
        <button onclick="addForm()" class="btn btn-success"><i class="fa fa-plus"></i> Add New Purchase</button>
        <a href="{{ route('exportPDF.productMasukAll') }}" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Export PDF</a>
        <a href="{{ route('exportExcel.productMasukAll') }}" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Export Excel</a>
    </div>
    @endif

    <!-- Table wrapper for responsiveness -->
    <div class="box-body table-responsive">
        <table id="products-in-table" class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Products</th>
                    <th>Supplier</th>
                    <th>Qty.</th>
                    @if (auth()->user()->role === 'admin')
                    <th>Price of one Packet</th>
                    <th>Total Price</th>
                    @endif
                    <th>In Date</th>
                    @if (auth()->user()->role === 'admin')
                    <th>Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@include('product_masuk.form')

@endsection

@section('bot')
<!-- DataTables -->
<script src="{{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

<!-- InputMask -->
<script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.js') }}"></script>
<script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
<script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
<!-- date-range-picker -->
<script src="{{ asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<!-- bootstrap datepicker -->
<script src="{{ asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<!-- bootstrap color picker -->
<script src="{{ asset('assets/bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
<!-- bootstrap time picker -->
<script src="{{ asset('assets/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
<script src="{{ asset('assets/validator/validator.min.js') }}"></script>

<script type="text/javascript">
    var table = $('#products-in-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('api.productsIn') }}",
        order: [[6, 'desc']],  // Order by the 'tanggal' column (index 6) in descending order
        columns: [
            {
                data: null,
                name: 'id',
                render: function(data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                data: 'products_name',
                name: 'products_name'
            },
            {
                data: 'supplier_name',
                name: 'supplier_name'
            },
            {
                data: 'qty',
                name: 'qty'
            },
            @if (auth()->user()->role === 'admin')
            {
                data: 'price',
                name: 'price'
            },
            {
                data: 'total_price',
                name: 'total_price',
                render: function(data, type, row) {
                    var totalPrice = row.qty * row.price;
                    return totalPrice.toFixed(2);
                }
            },
            @endif
            {
                data: 'tanggal',
                name: 'tanggal'
            },
            @if (auth()->user()->role === 'admin')
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
            @endif
        ]
    });

    // Responsive modal adjustments
    $(document).on('shown.bs.modal', function () {
        $('body').addClass('modal-open');
    });

    function addForm() {
        save_method = "add";
        $('input[name=_method]').val('POST');
        $('#modal-form').modal('show');
        $('#modal-form form')[0].reset();
        $('.modal-title').text('Add New Purchase');
    }

    function editForm(id) {
        save_method = 'edit';
        $('input[name=_method]').val('PATCH');
        $('#modal-form form')[0].reset();
        $.ajax({
            url: "{{ url('productsIn') }}" + '/' + id + "/edit",
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#modal-form').modal('show');
                $('.modal-title').text('Edit Products In');
                $('#id').val(data.id);
                $('#product_id').val(data.product_id);
                $('#supplier_id').val(data.supplier_id);
                $('#qty').val(data.qty);
                $('#tanggal').val(data.tanggal);
            },
            error: function() {
                alert("No data found");
            }
        });
    }

    function deleteData(id) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#d33',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then(function() {
            $.ajax({
                url: "{{ url('productsIn') }}" + '/' + id,
                type: "POST",
                data: {
                    '_method': 'DELETE',
                    '_token': csrf_token
                },
                success: function(data) {
                    table.ajax.reload();
                    swal({
                        title: 'Success!',
                        text: data.message,
                        type: 'success',
                        timer: '1500'
                    })
                },
                error: function() {
                    swal({
                        title: 'Oops...',
                        text: data.message,
                        type: 'error',
                        timer: '1500'
                    })
                }
            });
        });
    }
</script>
@endsection
