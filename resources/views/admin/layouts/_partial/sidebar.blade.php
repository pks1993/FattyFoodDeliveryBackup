<aside class="main-sidebar sidebar-dark-light elevation-4" style="background-color: #000335">

    <!-- Sidebar -->
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">

          <img src="{{asset('logo/user_logo.png')}}" class="img-circle" alt="User Image">

        </div>
        <div class="info">
          <a href="#" class="d-block"><b>Fatty Admin</b><i class="fa fa-circle text-success btn-sm"></i> <span style="font-size: 10;">Online</span></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" style="color: white;">
        <li class="nav-item has-treeview">
                <a href="{{url('fatty/main/admin/dashboard')}}" class="nav-link">
                    <div class="row">
                        <div style="width: 15%;">
                            <i class="fa fa-dashboard" style="margin-right: 5px" aria-hidden="true"></i>
                        </div>
                        <div style="width: 50%;">
                            <p>Dashboard</p>
                        </div>
                    </div>
                </a>
            </li>
            <li class="nav-item has-treeview">
                <a href="{{url('fatty/main/admin/foods/orders/lists')}}" class="nav-link">
                    <div class="row">
                        <div style="width: 15%;">
                            <i class="fa fa-list" style="margin-right: 5px" aria-hidden="true"></i>
                        </div>
                        <div style="width: 50%;">
                            <p>Order Assign</p>
                        </div>
                    </div>
                </a>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <div class="row">
                        <div style="width: 15%;">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                        </div>
                        <div style="width: 50%;">
                            <p>Food Orders</p>
                        </div>
                        <div style="width: 35%;text-align: right;">
                            <i class="right fas fa-angle-left"></i>
                        </div>
                    </div>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/daily_food_orders')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Daily Food Orders</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/monthly_food_orders')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Monthly Food Orders</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/yearly_food_orders')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Yearly Food Orders</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/food_orders_chart')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Food Orders Chart</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <div class="row">
                        <div style="width: 15%;">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                        </div>
                        <div style="width: 50%;">
                            <p>Parcel Orders</p>
                        </div>
                        <div style="width: 35%;text-align: right;">
                            <i class="right fas fa-angle-left"></i>
                        </div>
                    </div>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/daily_parcel_orders')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Daily Parcel Orders</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/monthly_parcel_orders')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Monthly Parcel Orders</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/yearly_parcel_orders')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Yearly Parcel Orders</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/parcel_orders_chart')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Parcel Orders Chart</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/parcel_states')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Parcel States</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <div class="row">
                        <div style="width: 15%;">
                            <i class="fa fa-users" aria-hidden="true"></i>
                        </div>
                        <div style="width: 50%;">
                            <p>New Users</p>
                        </div>
                        <div style="width: 35%;text-align: right;">
                            <i class="right fas fa-angle-left"></i>
                        </div>
                    </div>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/customers')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p> User all List</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/daily_customers')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Daily New Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/monthly_customers')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Monthly New Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/yearly_customers')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Yearly New Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/customer_chart')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Users Chart</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <div class="row">
                        <div style="width: 15%;">
                            <i class="fa fa-users" aria-hidden="true"></i>
                        </div>
                        <div style="width: 50%;">
                            <p>Ordered Users</p>
                        </div>
                        <div style="width: 35%;text-align: right;">
                            <i class="right fas fa-angle-left"></i>
                        </div>
                    </div>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/daily_ordered_customers')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Daily Ordered Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/monthly_ordered_customers')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Monthly Ordered Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/yearly_ordered_customers')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Yearly Ordered Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/order_customer_chart')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Ordered Users Chart</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <div class="row">
                        <div style="width: 15%;">
                            <i class="fa fa-users" aria-hidden="true"></i>
                        </div>
                        <div style="width: 50%;">
                            <p>Active Users</p>
                        </div>
                        <div style="width: 35%;text-align: right;">
                            <i class="right fas fa-angle-left"></i>
                        </div>
                    </div>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/daily_active_customers')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Daily Active Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/monthly_active_customers')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Monthly Active Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/yearly_active_customers')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Yearly Active Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/active_customer_chart')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Active Users Chart</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <div class="row">
                        <div style="width: 15%;">
                            <i class="fas fa-biking" aria-hidden="true"></i>
                        </div>
                        <div style="width: 50%;">
                            <p>Riders</p>
                        </div>
                        <div style="width: 35%;text-align: right;">
                            <i class="right fas fa-angle-left"></i>
                        </div>
                    </div>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/riders')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Riders List</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/daily_100_riders')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Daily Top 100</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/monthly_100_riders')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Monthly Top 100</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/yearly_100_riders')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Yearly Top 100</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/rider_chart')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Riders Chart</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <div class="row">
                        <div style="width: 15%;">
                            <i class="fa fa-bars" aria-hidden="true"></i>
                        </div>
                        <div style="width: 50%;">
                            <p>Category</p>
                        </div>
                        <div style="width: 35%;text-align: right;">
                            <i class="right fas fa-angle-left"></i>
                        </div>
                    </div>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/restaurant/categories')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p> Category List</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/restaurant/categories/assign')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Category Assign List</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <div class="row">
                        <div style="width: 15%;text-align: left;">
                            <i class="fas fa-store-alt" style="font-size: 90%;" aria-hidden="true"></i>
                        </div>
                        <div style="width: 50%;">
                            <p>Restaurant</p>
                        </div>
                        <div style="width: 35%;text-align: right;">
                            <i class="right fas fa-angle-left"></i>
                        </div>
                    </div>                    
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url('fatty/main/admin/restaurants') }}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>All List</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('fatty/main/admin/100_restaurants') }}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Top 100 List</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('fatty/main/admin/recommend_restaurants') }}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Recommanded All list</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('fatty/main/admin/restaurant_chart') }}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Restaurant Chart</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <div class="row">
                        <div style="width: 15%;">
                            <i class="fa fa-cutlery" style="margin-left: 3px;" aria-hidden="true"></i>
                        </div>
                        <div style="width: 50%;">
                            <p>Food</p>
                        </div>
                        <div style="width: 35%;text-align: right;">
                            <i class="right fas fa-angle-left"></i>
                        </div>
                    </div>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url('fatty/main/admin/foods') }}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Food List</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/food_menu')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Food Menu List</p>
                        </a>
                    </li>
                </ul>
            </li>  
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <div class="row">
                        <div style="width: 15%;">
                            <i class="fa fa-bell" aria-hidden="true"></i>
                        </div>
                        <div style="width: 50%;">
                            <p>Notification</p>
                        </div>
                        <div style="width: 35%;text-align: right;">
                            <i class="right fas fa-angle-left"></i>
                        </div>
                    </div>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/notification_templates')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Send Notification</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <div class="row">
                        <div style="width: 15%;">
                            <i class="fa fa-lock" style="margin-left: 3px;font-size: 115%;" aria-hidden="true"></i>
                        </div>
                        <div style="width: 50%;">
                            <p>Permission</p>
                        </div>
                        <div style="width: 35%;text-align: right;">
                            <i class="right fas fa-angle-left"></i>
                        </div>
                    </div>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/user')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Admin</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/roles')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Role</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/zones')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Zone</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/rider_group')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>RiderGroup</p>
                        </a>
                    </li>
                </ul>
            </li>  
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <div class="row">
                        <div style="width: 15%;">
                            <i class="fa fa-gear" style="margin-left: 2px;font-size: 115%;" aria-hidden="true"></i>
                        </div>
                        <div style="width: 50%;">
                            <p>Settings</p>
                        </div>
                        <div style="width: 35%;text-align: right;">
                            <i class="right fas fa-angle-left"></i>
                        </div>
                    </div>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/support_center')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Support Center</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{url('fatty/main/admin/tutorials')}}" class="nav-link">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            <p>Tutorial Video</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <form id="frm-logout" action="{{ route('fatty.admin.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="{{route('fatty.admin.logout')}} " onclick="event.preventDefault(); document.getElementById('frm-logout').submit();" class="nav-link">
                            <i class="fa fa-sign-out" aria-hidden="true"></i>
                            <p>Logout</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
      </nav>
    </div>
</aside>
