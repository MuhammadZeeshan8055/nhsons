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

                    <!-- Category -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Category</label>
                        <div class="col-md-9">
                            {!! Form::select(
                                'category_id',
                                $categories,
                                null,
                                ['class' => 'form-control select', 'placeholder' => '-- Choose Category --', 'id' => 'category_id', 'required']
                            ) !!}
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    <!-- Products -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Products</label>
                        <div class="col-md-9">
                            <select class="form-control select" id="product_id" name="product_id" required>
                                <option value="">-- Choose Product --</option>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    <!-- Supplier -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Supplier</label>
                        <div class="col-md-9">
                            {!! Form::select(
                                'supplier_id',
                                $suppliers,
                                null,
                                ['class' => 'form-control select', 'placeholder' => '-- Choose Supplier --', 'id' => 'supplier_id', 'required']
                            ) !!}
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Quantity</label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" id="qty" name="qty" min="0" step="1" required>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Price (1 Packet)</label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    <!-- Total Price -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Total Price</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="total_price" name="total_price" readonly>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Date</label>
                        <div class="col-md-9">
                            <input data-date-format='yyyy-mm-dd' type="text" class="form-control" id="tanggal" name="tanggal" autocomplete="off" required>
                            <span class="help-block with-errors"></span>
                        </div>
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
        
        
        const categorySelect = $('#category_id');
        const productSelect = $('#product_id');
        const qtyInput = $('#qty');
        const priceInput = $('#price');
        const totalPriceInput = $('#total_price');

        // Calculate total price
        function calculateTotalPrice() {
            const quantity = parseFloat(qtyInput.val()) || 0;
            const price = parseFloat(priceInput.val()) || 0;
            totalPriceInput.val((quantity * price).toFixed(2));
        }

        qtyInput.on('input', calculateTotalPrice);
        priceInput.on('input', calculateTotalPrice);

        // Fetch products based on selected category
        categorySelect.on('change', function () {
            const categoryId = $(this).val();

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
    });
</script>

