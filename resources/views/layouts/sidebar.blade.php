    <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

            <!-- Sidebar user panel (optional) -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{{ asset('user-profile.png') }} " class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p>{{ \Auth::user()->name  }}</p>
                    <!-- Status -->
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <!-- Log on to codeastro.com for more projects! -->
            <!-- search form (Optional) -->
            <!-- <form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search...">
                    <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
                </span>
                </div>
            </form> -->
            <!-- /.search form -->

            <!-- Sidebar Menu -->
            <!--<ul class="sidebar-menu" data-widget="tree">-->
                <!-- <li class="header">Functions</li> -->
                <!-- Optionally, you can add icons to the links -->
            <!--    <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>-->
            <!--    <li><a href="{{ route('categories.index') }}"><i class="fa fa-list"></i> <span>Category</span></a></li>-->
            <!--    <li><a href="{{ route('products.index') }}"><i class="fa fa-cubes"></i> <span>Stock</span></a></li>-->
                
            <!--    {{-- {{ dd(auth()->user()->role) }} --}}-->
            <!--    @if(auth()->user()->role === 'admin')-->
            <!--    <li><a href="{{ route('customers.index') }}"><i class="fa fa-users"></i> <span>Customer</span></a></li>-->
                <!-- <li><a href="{{ route('sales.index') }}"><i class="fa fa-cart-plus"></i> <span>Penjualan</span></a></li> -->
            <!--    <li><a href="{{ route('suppliers.index') }}"><i class="fa fa-truck"></i> <span>Supplier</span></a></li>-->
            <!--    <li><a href="{{ url('/sale_report') }}"><i class="fa fa-minus"></i> <span>Sale Report</span></a></li>-->
            <!--    @endif-->
            <!--    <li><a href="{{ route('productsOut.index') }}"><i class="fa fa-minus"></i> <span>Sale Order</span></a></li>-->
                
            <!--    <li><a href="{{ route('productsIn.index') }}"><i class="fa fa-cart-plus"></i> <span>Purchase Products</span></a></li>-->
            <!--    @if(auth()->user()->role === 'admin')-->
            <!--    <li><a href="{{ route('user.index') }}"><i class="fa fa-user-secret"></i> <span>System Users</span></a></li>-->
            <!--    @endif-->

            <!--</ul>-->
            
            <ul class="sidebar-menu" data-widget="tree">
                @if(auth()->user()->role === 'admin')
                    <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
                    <li><a href="{{ route('categories.index') }}"><i class="fa fa-list"></i> <span>Category</span></a></li>
                    <li><a href="{{ route('products.index') }}"><i class="fa fa-cubes"></i> <span>Stock</span></a></li>
                    <li><a href="{{ route('customers.index') }}"><i class="fa fa-users"></i> <span>Customer</span></a></li>
                    <li><a href="{{ route('ledger.index') }}"><i class="fa fa-users"></i> <span>Ledger</span></a></li>
                    <li><a href="{{ route('suppliers.index') }}"><i class="fa fa-truck"></i> <span>Supplier</span></a></li>
                    <li><a href="{{ url('/sale_report') }}"><i class="fa fa-minus"></i> <span>Sale Report</span></a></li>
                    <li><a href="{{ route('productsOut.index') }}"><i class="fa fa-minus"></i> <span>Sale Order</span></a></li>
                    @if(auth()->check() && auth()->user()->email == 'usman.shani@nhsons.com')
                        <li>
                            <a href="{{ route('productsIn.index') }}">
                                <i class="fa fa-cart-plus"></i> <span>Purchase Products</span>
                            </a>
                        </li>
                    @endif
                    <li><a href="{{ route('user.index') }}"><i class="fa fa-user-secret"></i> <span>System Users</span></a></li>
                @elseif(auth()->user()->role === 'staff' || auth()->user()->role === 'hafiz')
                    <li><a href="{{ route('products.index') }}"><i class="fa fa-cubes"></i> <span>Stock</span></a></li>
                @endif
            </ul>


            <!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
    </aside>
