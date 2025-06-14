<div class="modal fade" id="modal-form" tabindex="1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="form-item" method="post" class="form-horizontal" data-toggle="validator" enctype="multipart/form-data" >
                {{ csrf_field() }} {{ method_field('POST') }}

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title"></h3>
                </div>


                <div class="modal-body">
                    <input type="hidden" id="id" name="id">


                    <div class="box-body">
                        <div class="form-group">
                            <label >Customers</label>
                            <select name="customer_id" class="form-control select" id="customer_id">
                                <option value="">-- Choose Customer --</option>
                                @foreach($customers as $id => $name)
                                    <option value="{{ $id }}" {{ request('customer_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>

                        <div class="form-group">
                            <label >Bill Number</label>
                            <input type="text" class="form-control" id="bill_number" name="bill_number">
                            <span class="help-block with-errors"></span>
                        </div>

                        <div class="form-group">
                            <label >Bill Amount</label>
                            <input type="text" class="form-control" id="bill_amount" name="bill_amount">
                            <span class="help-block with-errors"></span>
                        </div>

                        <div class="form-group">
                            <label >Amount Paid</label>
                            <input type="text" class="form-control" id="amount_paid" name="amount_paid">
                            <span class="help-block with-errors"></span>
                        </div>
                        <div class="form-group">
                            <input type="hidden" class="form-control" value="<?php echo $date = now();?>" id="transaction_date" name="transaction_date">
                        </div>


                    </div>
                    <!-- /.box-body -->

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>

            </form><!-- Log on to codeastro.com for more projects! -->
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div><!-- Log on to codeastro.com for more projects! -->
<!-- /.modal -->
