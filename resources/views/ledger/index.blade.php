@extends('layouts.master')

@section('top')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('content')
    <div class="box box-success">
        <div class="row">
            <div class="col-lg-4 col-xs-6"></div>
            <div class="col-lg-4 col-xs-6">
                <div class="small-box bg-maroon">
                    <div class="inner">
                        <h3 id="total-balance">
                            {{ number_format($totalBalance, 2) }}
                        </h3>
                        <p>
                            @if(request('customer_id'))
                                @php
                                    $selectedCustomer = App\Customer::find(request('customer_id'));
                                @endphp
                                {{ $selectedCustomer ? $selectedCustomer->nama : 'Selected Customer' }} Balance
                            @else
                                Total Cash Running In Market
                            @endif
                        </p>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-xs-6"></div>
        </div>
        <div class="box-header">
            <h3 class="box-title">List of Ledger</h3>
        </div>

        <form method="GET" action="{{ url()->current() }}">
            <div class="row">
                <div class="col-md-4">
                    <div class="box-header">
                        <label class="control-label">Customer</label>
                        <select name="customer_id" class="form-control select" id="customer_id" onchange="this.form.submit()">
                            <option value="">-- Choose Customer --</option>
                            @foreach($customers as $id => $name)
                                <option value="{{ $id }}" {{ request('customer_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <span class="help-block with-errors"></span>
                    </div>
                </div>
                
                @if(request('customer_id'))
                <div class="col-md-3">
                    <div class="box-header">
                        <label class="control-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" id="date_from" value="{{ request('date_from') }}">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="box-header">
                        <label class="control-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" id="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="box-header">
                        <label class="control-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ url()->current() }}?customer_id={{ request('customer_id') }}" class="btn btn-default">Clear</a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </form>

        @if(auth()->user()->role === 'admin')
        <div class="box-header">
            <a onclick="addForm()" class="btn btn-success"><i class="fa fa-plus"></i> Add Ledger</a>
            
            <!-- PDF Export Button - Only show when customer is selected -->
            @if(request('customer_id'))
                <a href="{{ route('ledger.exportPDF', ['customer_id' => request('customer_id'), 'date_from' => request('date_from'), 'date_to' => request('date_to')]) }}" 
                   class="btn btn-danger" target="_blank">
                    <i class="fa fa-file-pdf-o"></i> Export Customer PDF
                </a>
            @endif
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
                        <th>Balance</th>
                        <th>Description</th>
                        <th>Transaction Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
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

    @include('ledger.form')
@endsection

@section('bot')
    <!-- DataTables -->
    <script src=" {{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
    <script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }} "></script>

    {{-- Validator --}}
    <script src="{{ asset('assets/validator/validator.min.js') }}"></script>

    <script type="text/javascript">
        var table = $('#sales-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api.ledger') }}",
                data: function (d) {
                    d.customer_id = $('#customer_id').val();
                    d.date_from = "{{ request('date_from') }}";
                    d.date_to = "{{ request('date_to') }}";
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
                { data: 'balance', name: 'balance' },
                { data: 'description', name: 'description' },
                { data: 'transaction_date', name: 'transaction_date' },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            // Optional: Add footer callback to show totals in table footer
            // footerCallback: function (row, data, start, end, display) {
            //     var api = this.api();

            //     // Calculate totals for current page
            //     var totalBill = 0;
            //     var totalPaid = 0;
            //     var totalBalance = 0;
                
            //     api.rows({ page: 'current' }).data().each(function (rowData) {
            //         totalBill += parseFloat(rowData.bill_amount.replace(/,/g, '')) || 0;
            //         totalPaid += parseFloat(rowData.amount_paid.replace(/,/g, '')) || 0;
            //     });
                
            //     totalBalance = totalBill - totalPaid;

            //     // Update footer
            //     $(api.column(3).footer()).html('Total: ' + totalBill.toFixed(2));
            //     $(api.column(4).footer()).html('Total: ' + totalPaid.toFixed(2));
            //     $(api.column(5).footer()).html('Total: ' + totalBalance.toFixed(2));
            // }
        });

        $('#customer_id').on('change', function () {
            table.ajax.reload();
            // Reload page to show/hide PDF button and date filters and update balance
            if ($(this).val()) {
                window.location.href = "{{ url()->current() }}?customer_id=" + $(this).val();
            } else {
                window.location.href = "{{ url()->current() }}";
            }
        });

        function addForm() {
            save_method = "add";
            $('input[name=_method]').val('POST');
            $('#modal-form').modal('show');
            $('#modal-form form')[0].reset();
            $('.modal-title').text('Add Ledger');
        }

        function editForm(id) {
            save_method = 'edit';
            $('input[name=_method]').val('PATCH');
            $('#modal-form form')[0].reset();
            $.ajax({
                url: "{{ url('ledger') }}" + '/' + id + "/edit",
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    $('#modal-form').modal('show');
                    $('.modal-title').text('Edit ledger');

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
                    url: "{{ url('ledger') }}" + '/' + id,
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
                        });
                        // Reload page to update the balance widget
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
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
                    if (save_method == 'add') url = "{{ url('ledger') }}";
                    else url = "{{ url('ledger') . '/' }}" + id;

                    $.ajax({
                        url: url,
                        type: "POST",
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
                            });
                            // Reload page to update the balance widget
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
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