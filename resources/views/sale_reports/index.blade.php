@extends('layouts.master')

@section('top')
@endsection

@section('content')
<style>
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
    
    if (isset($_GET['date'])) {
        if($_GET['date']==$today_date){
        	$selectOption = "Today";
        }	
       
        if ($_GET['date'] == $yesterday_date) {
            $selectOption = "Yesterday";
        } elseif ($_GET['date'] == $last7days_date) {
            $selectOption = "Last 7 Days";
        } elseif ($_GET['date'] == $thismonth_date) {
            $selectOption = "This Month";
        } elseif ($_GET['date'] == $lastmonth_date) {
            $selectOption = "Last Month";
        } elseif ($_GET['date'] == $thisyear_date) {
            $selectOption = "This Year";
        } elseif ($_GET['date'] == $lastyear_date) {
            $selectOption = "Last Year";
        } elseif ($_GET['date'] == $alltime_date) {
            $selectOption = "All Time";
        } else {
            $selectOption = ''; // or some default
        }
    } else {
        $selectOption = ''; // or some default
    }

?>

<!-- Small boxes (Stat box) -->
<div class="row">
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>
                    PKR {{ number_format($todaySale) }}
                </h3>
                <p>
                <?= 
                    !empty($_GET['from_date']) 
                        ? 'Search Results Sale' 
                        : (!empty($_GET['date']) 
                            ? $selectOption . ' Sale' 
                            : "Today's Sale") 
                ?>
            </p>

            </div>
            <a href="{{ route('products.index') }}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>
                    PKR {{ number_format($monthSale) }}
                </h3>
                <p>This Month Sale</p>
            </div>
            <a href="{{ route('categories.index') }}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-maroon">
            <div class="inner">
                <h3>PKR {{ number_format($totalCost) }}</h3>
                <p>Total Cost</p>
            </div>
            <a href="{{ route('productsIn.index') }}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>PKR {{ number_format($totalProfit) }}</h3>
                <p>Total Profit</p>
            </div>
            <a href="{{ route('productsOut.index') }}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Log on to codeastro.com for more projects! -->


<div class="row">
    
  
    <!-- ./col -->
    @if(auth()->user()->role === 'admin' ) 
   
    @endif

    @if(auth()->user()->role === 'admin')
   
    @endif

    <!-- ./col -->
    <div id="container" class=" col-xs-6"></div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="search-filter">
            <form method="GET">
                <div class="form-group">
                    <label for="date-select">Search</label>
                    <select class="form-control" id="date-select" name="date" onchange="this.form.submit()" style="width: 50%;">
                        <?php
                            if(!empty($_GET['date'])){
                                ?>
                                    <option value="<?= isset($_GET['date']) ? htmlspecialchars($_GET['date'], ENT_QUOTES) : '' ?>"><?=$selectOption?></option>
                                <?php
                            }else{
                                ?>
                                    <option value="">Select</option>
                                <?php
                            }
                        ?>
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
    
    <div class="col-md-6">
        <div class="search-filter">
            <form method="GET">
                <div class="form-group">
                    <label for="from-date">Search By Date</label>
                    <div class="date_inputs">
                        <input type="date" id="from-date" name="from_date" value="<?= isset($_GET['from_date']) ? htmlspecialchars($_GET['from_date'], ENT_QUOTES) : '' ?>" class="form-control">
                        <input type="date" id="to-date" name="to_date" value="<?= isset($_GET['to_date']) ? htmlspecialchars($_GET['to_date'], ENT_QUOTES) : '' ?>" class="form-control">
                        <button type="submit" id="apply-date-filter" class="btn btn-primary">Apply</button>
                    </div>
                </div>
            </form> 
        </div>
    </div>
</div>

<!--<div class="row">-->
<!--    <form method="GET">-->
<!--        <div class="col-md-6">-->
<!--            <div class="search-filter">-->
                
<!--                    <div class="form-group">-->
<!--                        <label class="control-label">Sales Person</label>-->
<!--                        <select name="salesperson" id="salesperson" class="form-control select customer-select" required style="width: 50%;">-->
<!--                            <option value="">Select Sales Person</option>-->
<!--                            @foreach($users as $id => $name)-->
<!--                                <option value="{{ $id }}" {{ request('salesperson') == $id ? 'selected' : '' }}>-->
<!--                                    {{ $name }}-->
<!--                                </option>-->
<!--                            @endforeach-->
<!--                        </select>-->
<!--                    </div>-->
                    
<!--            </div>-->
<!--        </div>-->
<!--        <div class="col-md-6">-->
<!--            <div class="form-group">-->
<!--                <label for="from-date">Search By Date</label>-->
<!--                <div class="date_inputs">-->
<!--                    <input type="date" id="from-date" name="from_date" value="<?= isset($_GET['from_date']) ? htmlspecialchars($_GET['from_date'], ENT_QUOTES) : '' ?>" class="form-control">-->
<!--                    <input type="date" id="to-date" name="to_date" value="<?= isset($_GET['to_date']) ? htmlspecialchars($_GET['to_date'], ENT_QUOTES) : '' ?>" class="form-control">-->
<!--                    <button type="submit" id="apply-date-filter" class="btn btn-primary">Apply</button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </form>-->
<!--</div>-->


<!--<div class="box box-success mt-3">-->
<!--    <div class="box-body">-->
<!--        <div class="table-responsive">-->
<!--            <table id="products-out-table" class="table table-bordered table-hover table-striped">-->
<!--                <thead>-->
<!--                    <tr>-->
<!--                        <th>ID</th>-->
<!--                        <th>Bill Number</th>-->
<!--                        <th>Customer</th>-->
<!--                        <th>Sales Person</th>-->
<!--                        <th>Grand Total</th>-->
<!--                        <th>Date</th>-->
<!--                    </tr>-->
<!--                </thead>-->
<!--                <tbody>-->
<!--                    <tr>-->
<!--                        <td>ID</td>-->
<!--                        <td>Bill Number</td>-->
<!--                        <td>Customer</td>-->
<!--                        <td>Sales Person</td>-->
<!--                        <td>Grand Total</td>-->
<!--                        <td>Date</td>-->
<!--                    </tr>-->
<!--                </tbody>-->
<!--            </table>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
           

@endsection

@section('top')
@endsection
