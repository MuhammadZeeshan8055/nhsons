<style>
    .remove-button-style {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .mt-3{
         margin-top: 3%;
    }
    .customer-ledger-details.mt-3 {
        margin-right: 15%;
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
                    <div class="customer-ledger-details mt-3">
                        <div class="form-group">
                            <label for="due">Total Due</label>
                            <input type="text" name="total_due" id="total_due" readonly>
                        </div>
                        <div class="form-group">
                            <label for="due">Total Paid</label>
                            <input type="text" name="total_paid" id="total_paid">
                        </div>
                        <div class="form-group">
                            <label for="due">Total Remaining</label>
                            <input type="text" name="total_remaining" id="total_remaining" readonly>
                        </div>
                    </div>
                    <br>
                    <br>
                    <hr>
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
<!-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const totalPaidInput = document.getElementById('total_paid');
        const totalDueInput = document.getElementById('total_due');
        const totalRemainingInput = document.getElementById('total_remaining');
        const grandTotalSpan = document.getElementById('grand-total');

        function calculateRemaining() {
            const totalDue = parseFloat(totalDueInput.value) || 0;
            const grandTotal = parseFloat(grandTotalSpan.textContent) || 0;
            const totalPaid = parseFloat(totalPaidInput.value) || 0;

            const totalRemaining = (grandTotal - totalPaid) + totalDue;

            totalRemainingInput.value = totalRemaining.toFixed(2);
        }

        // Trigger on input
        totalPaidInput.addEventListener('input', calculateRemaining);

        // Optional initial call
        calculateRemaining();
    });
</script> -->

<script>
    $(document).ready(function () {

        function updateTotalRemaining() {
            let grandTotal = parseFloat($('#grand-total').text().trim()) || 0;
            let totalPaid = parseFloat($('#total_paid').val().trim()) || 0;
            let totalDue = parseFloat($('#total_due').val().trim()) || 0;

            console.log('grandTotal:', grandTotal, 'totalPaid:', totalPaid, 'totalDue:', totalDue);

            let difference = grandTotal - totalPaid;
            let totalRemaining = difference + totalDue;

            console.log('difference:', difference, 'totalRemaining:', totalRemaining);

            $('#total_remaining').val(totalRemaining.toFixed(2));
        }

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

        $('#customer_id').change(function () {
            let customerId = $(this).val();
            let ledgerSelect = $('#ledger_id');

            if (customerId) {
                $.ajax({
                    url: `/ledgers-by-customer/${customerId}`,
                    type: 'GET',
                    success: function (response) {
                        
                        console.log('ledger',response);

                        // Show total due
                        $('#total_due').val(response.total_due);
                        updateTotalRemaining();
                    },
                    error: function () {
                        alert('Failed to fetch ledgers. Please try again.');
                    }
                });
            }
        });

         // When total_paid input changes, recalculate remaining
        $('#total_paid').on('input', function() {
            updateTotalRemaining();
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
    
    function updateGrandTotal() {
        let grandTotal = 0;
        $('#modal-form input[name="total[]"]').each(function () {
            grandTotal += parseFloat($(this).val()) || 0;
        });
        $('#grand-total').text(grandTotal.toFixed(2));

        // Update total remaining immediately after updating grand total
        let totalPaid = parseFloat($('#total_paid').val().trim()) || 0;
        let totalDue = parseFloat($('#total_due').val().trim()) || 0;

        console.log('grandTotal:', grandTotal, 'totalPaid:', totalPaid, 'totalDue:', totalDue);

        let difference = grandTotal - totalPaid;
        let totalRemaining = difference + totalDue;

        console.log('difference:', difference, 'totalRemaining:', totalRemaining);

        $('#total_remaining').val(totalRemaining.toFixed(2));
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
