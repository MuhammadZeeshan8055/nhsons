@extends('layouts.master')

@section('top')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('content')
    <div class="box box-success">

        <div class="box-header">
            <h3 class="box-title">Choose Category</h3>
        </div>
        <div class="box-body">
            <div class="form-group row">
                {{-- <label for="category_id" class="col-sm-3 col-form-label">Choose Category</label> --}}
                <div class="col-sm-6">
                    {!! Form::select('category_id', $category, null, [
                        'class' => 'form-control',
                        'placeholder' => '-- Choose Category --',
                        'id' => 'category_id',
                        'required',
                    ]) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="box box-success">
        <div class="box-header">
            <h3 class="box-title">List of Products</h3>
            @if(auth()->user()->role === 'admin')
            <a onclick="addForm()" class="btn btn-success pull-right" style="margin-top: -8px;">
                <i class="fa fa-plus"></i> Add Products
            </a>
            @endif
        </div>
        <div class="box-header">
            <a href="{{ route('exportPDF.productAll') }}" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Export PDF</a>
            <a href="{{ route('exportExcel.productAll') }}" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Export Excel</a>
        </div>
        <div class="box-body">
            <table id="products-table" class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        @if(auth()->user()->role === 'admin')
                        <th>Price</th>
                        @endif
                        <th>Qty.</th>
                        <th>Category</th>
                        @if(auth()->user()->role === 'admin')
                        <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    @include('products.form')
@endsection

@section('bot')
    <!-- DataTables -->
    <script src="{{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

    <script type="text/javascript">
        var table = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api.products') }}",
                data: function(d) {
                    d.category_id = $('#category_id').val(); // Pass selected category ID
                }
            },
            columns: [{
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // Sequential numbering starting from 1
                    }
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                @if(auth()->user()->role === 'admin')
                {
                    data: 'harga',
                    name: 'harga'
                },
                @endif
                {
                    data: 'qty',
                    name: 'qty'
                },
                {
                    data: 'category_name',
                    name: 'category_name'
                }
                ,
                @if(auth()->user()->role === 'admin')
                {
                    data: 'action',
                    name: 'action'
                },
                @endif
                
            ]
        });

        // Reload table when category is changed
        $('#category_id').change(function() {
            table.ajax.reload();
        });

        function addForm() {
            save_method = "add";
            $('input[name=_method]').val('POST');
            $('#modal-form').modal('show');
            $('#modal-form form')[0].reset();
            $('.modal-title').text('Add Products');
        }

        // function editForm(id) {
        //     save_method = 'edit';
        //     $('input[name=_method]').val('PATCH');
        //     $('#modal-form form')[0].reset();
        //     $.ajax({
        //         url: "{{ url('products') }}" + '/' + id + "/edit",
        //         type: "GET",
        //         dataType: "JSON",
        //         success: function(data) {
        //             $('#modal-form').modal('show');
        //             $('.modal-title').text('Edit Products');
        //             $('#id').val(data.id);
        //             $('#nama').val(data.nama);
        //             $('#harga').val(data.harga);
        //             $('#qty').val(data.qty);
        //             $('#category_id').val(data.category_id);
        //             $('#category_id option[value="' + data.category_id + '"]').prop('selected', true);
        //         },
        //         error: function() {
        //             alert("Nothing Data");
        //         }
        //     });
        // }
        
        
        function editForm(id) {
            save_method = 'edit';
            $('input[name=_method]').val('PATCH');
            $('#modal-form form')[0].reset();
            
            $.ajax({
                url: "{{ url('products') }}" + '/' + id + "/edit",
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    $('#modal-form').modal('show');
                    $('.modal-title').text('Edit Products');
                    $('#id').val(data.id);
                    $('#nama').val(data.nama);
                    $('#harga').val(data.harga);
                    $('#qty').val(data.qty);
                    $('#category_id').val(data.category_id);
                    $('#category_id option[value="' + data.category_id + '"]').prop('selected', true);
                },
                error: function() {
                    alert("Nothing Data");
                }
            });
        }
        
        // Add form submission handler
        $('#form-item').on('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            var url, method;
            
            if (save_method == 'edit') {
                url = "{{ url('products') }}" + '/' + $('#id').val();
                method = 'POST'; // Laravel expects POST with _method=PATCH
                formData.append('_method', 'PATCH');
            } else {
                url = "{{ url('products') }}";
                method = 'POST';
            }
            
            // Add CSRF token
            formData.append('_token', '{{ csrf_token() }}');
            
            $.ajax({
                url: url,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#modal-form').modal('hide');
                    location.reload(); // or reload your datatable
                },
                error: function(xhr) {
                    if (xhr.status == 422) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = 'Validation errors:\n';
                        for (var field in errors) {
                            errorMessage += errors[field][0] + '\n';
                        }
                        alert(errorMessage);
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });

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
                    url: "{{ url('products') }}" + '/' + id,
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
                    },
                    error: function() {
                        swal({
                            title: 'Oops...',
                            text: data.message,
                            type: 'error',
                            timer: '1500'
                        });
                    }
                });
            });
        }
    </script>
@endsection
