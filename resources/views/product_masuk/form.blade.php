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
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="control-label">Supplier</label>
                            {!! Form::select(
                                'supplier_id',
                                $suppliers,
                                null,
                                ['class' => 'form-control select', 'placeholder' => '-- Choose Supplier --', 'id' => 'supplier_id', 'required']
                            ) !!}
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

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
                            <label class="control-label">Price (1 Packet)</label>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label">Total Price</label>
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
                                    <option value="">-- Choose Category --</option>
                                    @foreach($categories as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block with-errors"></span>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control select product-select" name="product_id[]" required>
                                    <option value="">-- Choose Product --</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control qty-input" name="qty[]" min="0" step="1" required>
                                <span class="help-block with-errors"></span>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control price-input" name="price[]" min="0" step="0.01" required>
                                <span class="help-block with-errors"></span>
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control total-price-input" name="total_price[]" readonly>
                                <span class="help-block with-errors"></span>
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
                    <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
    $(document).ready(function () {
        
        $('#tanggal').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });

        // Calculate total price function
        function calculateTotalPrice(row) {
            const quantity = parseFloat(row.find('.qty-input').val()) || 0;
            const price = parseFloat(row.find('.price-input').val()) || 0;
            row.find('.total-price-input').val((quantity * price).toFixed(2));
        }

        // Event delegation for quantity and price inputs
        $(document).on('input', '.qty-input, .price-input', function() {
            const row = $(this).closest('.product-row');
            calculateTotalPrice(row);
        });

        // Event delegation for category change
        $(document).on('change', '.category-select', function () {
            const categoryId = $(this).val();
            const row = $(this).closest('.product-row');
            const productSelect = row.find('.product-select');

            if (categoryId) {
                $.ajax({
                    url: `/products-by-category/${categoryId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        productSelect.html('<option value="">-- Choose Product --</option>');
                        $.each(data, function (id, name) {
                            productSelect.append(`<option value="${id}">${name}</option>`);
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching products:', error);
                    }
                });
            } else {
                productSelect.html('<option value="">-- Choose Product --</option>');
            }
        });

        // Function to add a new product row
        $('#add-product').click(function () {
            var newRow = `
                <div class="row product-row">
                    <div class="col-md-2 mt-3">
                        <select name="category_id[]" class="form-control select category-select" required>
                            <option value="">-- Choose Category --</option>
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mt-3">
                        <select class="form-control select product-select" name="product_id[]" required>
                            <option value="">-- Choose Product --</option>
                        </select>
                    </div> 
                    <div class="col-md-2 mt-3">
                        <input type="number" class="form-control qty-input" name="qty[]" min="0" step="1" required>
                    </div>
                    
                    <div class="col-md-2 mt-3">
                        <input type="number" class="form-control price-input" name="price[]" min="0" step="0.01" required>
                    </div>
                    
                    <div class="col-md-2 mt-3">
                        <input type="text" class="form-control total-price-input" name="total_price[]" readonly>
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
        
        // Also clean up when add form is hidden
        $('#modal-form').on('hidden.bs.modal', function () {
            // Keep only the first product row for next time
            $('#product-wrapper .product-row').not(':first').remove();
            $('#product-wrapper .product-row:first').find('select, input').val('');
            $('#grand-total').text('0.00');
        });
        
    });
</script>