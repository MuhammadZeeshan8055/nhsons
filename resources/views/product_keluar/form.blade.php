<style>
    .remove-button-style {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .mt-3{
         margin-top: 3%;
    }
    @media (min-width: 992px) { /* Bootstrap's "large" breakpoint starts at 992px */
        .modal-content {
            width: 150%;
            margin-left: -25%;
        }
    }

</style>
<div class="modal fade" id="modal-form" tabindex="1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-item" method="post" class="form-horizontal" data-toggle="validator" enctype="multipart/form-data">
                {{ csrf_field() }} {{ method_field('POST') }}

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title"></h3>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    
                    <div class="row">
                        <div class="col-md-4">
                            <label class="control-label">Date</label>
                            <input data-date-format='yyyy-mm-dd' type="text" class="form-control" id="tanggal" name="tanggal" autocomplete="off" required>
                            <span class="help-block with-errors"></span>
                        </div>
                        <div class="col-md-4">
                            
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">Bill Number</label>
                            <input type="text" class="form-control" id="bill_number" name="bill_number" autocomplete="off" required>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">Customer</label>
                            {!! Form::select('customer_id', $customers, null, ['class' => 'form-control select', 'placeholder' => '-- Choose Customer --', 'id' => 'customer_id', 'required']) !!}
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">Sales Person</label>
                            {!! Form::select('user_id', $users, null, ['class' => 'form-control select', 'placeholder' => '-- Choose Sales Person --', 'id' => 'user_id', 'required']) !!}
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <!--<div class="row">-->
                    <!--    <div class="col-md-6">-->
                    <!--        <label class="control-label">Customer Address</label>-->
                    <!--        <input type="text" name="" placeholder="Customer Address" class="form-control">-->
                    <!--    </div>-->
                    <!--</div>-->
                
                    <div class="row">
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
                
                    <div id="product-wrapper">
                        <div class="row product-row">
                            <div class="col-md-2">
                                <select name="category_id[]" class="form-control select category-select" required>
                                    <option value="">Category</option>
                                    @foreach($categories as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="product_id[]" class="form-control select product-select" required>
                                    <option value="">-- Choose Product --</option>
                                    @foreach($products as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="qty[]" placeholder="qty" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="price[]" placeholder="price" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="total[]" placeholder="total" class="form-control" required>
                            </div>
                            <div class="col-md-1 remove-button-style">
                                <span class="btn text-danger remove-row" style="font-size: 20px; line-height: 1;">&times;</span>
                            </div>
                        </div>
                    </div>
                
                    <div class="col-md-12">
                        <button type="button" id="add-product" class="btn btn-danger btn-sm btn-outline-primary" style="margin-top:5%;margin-bottom:5%">+ Add Product</button>
                    </div>
                </div>

                <div class="modal-footer">
                    <div>
                        <strong>Grand Total: PKR <span id="grand-total">0.00</span></strong>
                    </div>
                    <button type="button" class="btn btn-danger pull-left mt-3" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success mt-3">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="view-bill-form" tabindex="1" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">View Bill</h3>
                </div>

                <div class="modal-body" id="view-bill-body">
                    
                </div>

                <div class="modal-footer" id="view-bill-footer">
                    
                </div>
            
        </div>
    </div>
</div>

<div class="modal fade" id="edit-bill-form" tabindex="1" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Edit Bill</h3>
                </div>

                <div class="modal-body" id="edit-bill-body">
                    
                </div>

                <div class="modal-footer" id="edit-bill-footer">
                    
                </div>
            
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#category_id').change(function () {
            let categoryId = $(this).val();
            let productSelect = $('#product_id');
            
            productSelect.empty().append('<option value="">-- Choose Product --</option>');
            
            if (categoryId) {
                $.ajax({
                    url: `/products-by-category/${categoryId}`,
                    type: 'GET',
                    success: function (data) {
                        $.each(data, function (id, nama) {
                            productSelect.append(`<option value="${id}">${nama}</option>`);
                        });
                    },
                    error: function () {
                        alert('Failed to fetch products. Please try again.');
                    }
                });
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        // Function to add a new product row
        $('#add-product').click(function () {
            var newRow = `
                <div class="row product-row">
                    <div class="col-md-2 mt-3">
                        
                        <select name="category_id[]" class="form-control select category-select" required>
                            <option value="">Category</option>
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mt-3">
                        
                        <select name="product_id[]" class="form-control select product-select" required>
                            <option value="">-- Choose Product --</option>
                            @foreach($products as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div> 
                    <div class="col-md-2 mt-3">
                        
                        <input type="number" name="qty[]" placeholder="qty" class="form-control" required>
                    </div>
                    
                    <div class="col-md-2 mt-3">
                        <input type="number" name="price[]" placeholder="price" class="form-control" required>
                    </div>
                    
                    <div class="col-md-2 mt-3">
                        <input type="number" name="total[]" placeholder="total" class="form-control" required>
                    </div>
                    
                    <div class="col-md-1 mt-3 remove-button-style">
                        <span class="btn text-danger remove-row" style="font-size: 20px; line-height: 1;">&times;</span>
                    </div>
                </div>
            `;
            $('#product-wrapper').append(newRow);
        });


        // Function to remove a product row
        $(document).on('click', '.remove-row', function () {
            $(this).closest('.product-row').remove();
        });

        // Function to update products based on selected category
        $(document).on('change', '.category-select', function () {
            var categoryId = $(this).val();
            var productSelect = $(this).closest('.product-row').find('.product-select');

            productSelect.empty().append('<option value="">-- Choose Product --</option>');

            if (categoryId) {
                $.ajax({
                    url: `/products-by-category/${categoryId}`,
                    type: 'GET',
                    success: function (data) {
                        $.each(data, function (id, name) {
                            productSelect.append(`<option value="${id}">${name}</option>`);
                        });
                    },
                    error: function () {
                        alert('Failed to fetch products. Please try again.');
                    }
                });
            }
        });
    });
    
    // function updateGrandTotal() {
    //     let grandTotal = 0;
    //     $('input[name="total[]"]').each(function () {
    //         grandTotal += parseFloat($(this).val()) || 0;
    //     });
    //     $('#grand-total').text(grandTotal.toFixed(2));
    // }
    
    // // Trigger grand total update when qty or price changes
    // $(document).on('input', 'input[name="qty[]"], input[name="price[]"]', function () {
    //     var row = $(this).closest('.product-row');
    //     var qty = parseFloat(row.find('input[name="qty[]"]').val()) || 0;
    //     var price = parseFloat(row.find('input[name="price[]"]').val()) || 0;
    //     var total = qty * price;
    //     row.find('input[name="total[]"]').val(total.toFixed(2));
    
    //     updateGrandTotal();
    // });
    
    // // Also recalculate grand total when a row is added or removed
    // $('#add-product').click(function () {
    //     setTimeout(updateGrandTotal, 100); // slight delay to wait for DOM append
    // });
    
    // $(document).on('click', '.remove-row', function () {
    //     $(this).closest('.product-row').remove();
    //     updateGrandTotal();
    // });
    
    function updateGrandTotal() {
        let grandTotal = 0;
        $('#modal-form input[name="total[]"]').each(function () {
            grandTotal += parseFloat($(this).val()) || 0;
        });
        $('#grand-total').text(grandTotal.toFixed(2));
    }// Setup event listeners when document is ready
    $(document).ready(function() {
        // Trigger grand total update when qty or price changes in add form
        $(document).on('input', '#modal-form input[name="qty[]"], #modal-form input[name="price[]"]', function () {
            var row = $(this).closest('.product-row');
            var qty = parseFloat(row.find('input[name="qty[]"]').val()) || 0;
            var price = parseFloat(row.find('input[name="price[]"]').val()) || 0;
            var total = qty * price;
            row.find('input[name="total[]"]').val(total.toFixed(2));
        
            updateGrandTotal();
        });
        
        // Update grand total when a row is added in add form
        $('#add-product').click(function () {
            setTimeout(updateGrandTotal, 100); // slight delay to wait for DOM append
        });
        
        // Update grand total when a row is removed in add form
        $(document).on('click', '#modal-form .remove-row', function () {
            $(this).closest('.product-row').remove();
            updateGrandTotal();
        });
        
        // Handle modal cleanup when modal is hidden
        $('#view-bill-form').on('hidden.bs.modal', function () {
            // Clear the view bill content to prevent interference
            $('#view-bill-body').html('');
            $('#view-bill-footer').html('');
        });
        
        // Clean up edit form when modal is hidden
        $('#edit-bill-form').on('hidden.bs.modal', function() {
            $('#edit-bill-body').html('');
            $('#edit-bill-footer').html('');
        });
        
        // Also clean up when add form is hidden
        $('#modal-form').on('hidden.bs.modal', function () {
            // Keep only the first product row for next time
            $('#product-wrapper .product-row').not(':first').remove();
            $('#product-wrapper .product-row:first').find('select, input').val('');
            $('#grand-total').text('0.00');
        });
    });
</script>
