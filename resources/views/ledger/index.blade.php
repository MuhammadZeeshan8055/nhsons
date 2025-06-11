@extends('layouts.master')


@section('top')
    <!-- DataTables --><!-- Log on to codeastro.com for more projects! -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('content')
    <div class="box box-success">

        <div class="box-header">
            <h3 class="box-title">List of Ledger</h3>
        </div>

        <form method="GET" action="{{ url()->current() }}">
            <div class="box-header" style="width:50%">
                <label class="control-label">Customer</label>
                <select name="customer_id" class="form-control select" id="customer_id" onchange="this.form.submit()">
                    <option value="">-- Choose Customer --</option>
                    @foreach($customers as $id => $name)
                        <option value="{{ $id }}" {{ request('customer_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <span class="help-block with-errors"></span>
            </div>
        </form>

        @if(auth()->user()->role === 'admin')

        <div class="box-header">
            <a onclick="addForm()" class="btn btn-success"><i class="fa fa-plus"></i> Add Ledger</a>
            <!-- <a href="{{ route('exportPDF.suppliersAll') }}" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Export
                PDF</a>
            <a href="{{ route('exportExcel.suppliersAll') }}" class="btn btn-primary"><i class="fa fa-file-excel-o"></i>
                Export Excel</a> -->
        </div>
        @endif


        <!-- /.box-header -->
        <div class="box-body">
            <table id="sales-table" class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Bill Number</th>
                        <th>Bill Amount</th>
                        <th>Amount Paid</th>
                        <!-- <th>Total Due</th> -->
                        <th>Transaction Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>

    {{-- @include('suppliers.form_import') --}}

    @include('suppliers.form')
@endsection

@section('bot')
    <!-- DataTables -->
    <script src=" {{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
    <script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }} "></script>

    {{-- Validator --}}
    <script src="{{ asset('assets/validator/validator.min.js') }}"></script>

    {{-- <script> --}}
    {{-- $(function () { --}}
    {{-- $('#items-table').DataTable() --}}
    {{-- $('#example2').DataTable({ --}}
    {{-- 'paging'      : true, --}}
    {{-- 'lengthChange': false, --}}
    {{-- 'searching'   : false, --}}
    {{-- 'ordering'    : true, --}}
    {{-- 'info'        : true, --}}
    {{-- 'autoWidth'   : false --}}
    {{-- }) --}}
    {{-- }) --}}
    {{-- </script> --}}

    <script type="text/javascript">
        var table = $('#sales-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api.ledger') }}",
                data: function (d) {
                    d.customer_id = $('#customer_id').val(); 
                }
            },
            columns: [
                {
                    data: null,
                    name: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'bill_number', name: 'bill_number' },
                { data: 'bill_amount', name: 'bill_amount' },
                { data: 'amount_paid', name: 'amount_paid' },
                { data: 'transaction_date', name: 'transaction_date' },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();

                var totalBill = 0;
                api.rows({ page: 'current' }).data().each(function (rowData) {
                    totalBill += parseFloat(rowData.bill_amount) || 0;
                });

                var totalPaid = 0;
                api.rows({ page: 'current' }).data().each(function (rowData) {
                    totalPaid += parseFloat(rowData.amount_paid) || 0;
                });

                var totalDue = totalBill - totalPaid;

                $(api.column(3).footer()).html('Total Bill Amount: ' + totalBill.toFixed(2));
                $(api.column(4).footer()).html('Total Paid Amount: ' + totalPaid.toFixed(2));

                $('#sales-table tfoot tr.total-due-row').remove();
                var totalDueRow = '<tr class="total-due-row" style="background-color: #f8f9fa; font-weight: bold;">' +
                    '<td></td><td></td><td></td>' +
                    '<td>Total Due Amount:</td>' +
                    '<td style="color: #dc3545;">' + totalDue.toFixed(2) + '</td>' +
                    '<td></td><td></td>' +
                    '</tr>';

                $('#sales-table tfoot').append(totalDueRow);
            }
        });
        $('#customer_id').on('change', function () {
            table.ajax.reload(); // 🔁 Reload table with new filter
        });


        function addForm() {
            save_method = "add";
            $('input[name=_method]').val('POST');
            $('#modal-form').modal('show');
            $('#modal-form form')[0].reset();
            $('.modal-title').text('Add Suppliers');
        }

        function editForm(id) {
            save_method = 'edit';
            $('input[name=_method]').val('PATCH');
            $('#modal-form form')[0].reset();
            $.ajax({
                url: "{{ url('suppliers') }}" + '/' + id + "/edit",
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    $('#modal-form').modal('show');
                    $('.modal-title').text('Edit Suppliers');

                    $('#id').val(data.id);
                    $('#nama').val(data.nama);
                    $('#alamat').val(data.alamat);
                    $('#email').val(data.email);
                    $('#telepon').val(data.telepon);
                },
                error: function() {
                    alert("Nothing Data");
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
                    url: "{{ url('suppliers') }}" + '/' + id,
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

        $(function() {
            $('#modal-form form').validator().on('submit', function(e) {
                if (!e.isDefaultPrevented()) {
                    var id = $('#id').val();
                    if (save_method == 'add') url = "{{ url('suppliers') }}";
                    else url = "{{ url('suppliers') . '/' }}" + id;

                    $.ajax({
                        url: url,
                        type: "POST",
                        //hanya untuk input data tanpa dokumen
                        //                      data : $('#modal-form form').serialize(),
                        data: new FormData($("#modal-form form")[0]),
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            $('#modal-form').modal('hide');
                            table.ajax.reload();
                            swal({
                                title: 'Success!',
                                text: data.message,
                                type: 'success',
                                timer: '1500'
                            })
                        },
                        error: function(data) {
                            swal({
                                title: 'Oops...',
                                text: data.message,
                                type: 'error',
                                timer: '1500'
                            })
                        }
                    });
                    return false;
                }
            });
        });
    </script>
@endsection
