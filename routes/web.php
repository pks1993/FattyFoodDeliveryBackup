<?php
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('admin.layouts.login_master');
});

//Billing-restaurant
Route::get('fatty/main/admin/restaurant_billing/data_list/{restaurant_id}','Admin\Restaurant\RestaurantController@restaurant_billing_list_url');
Route::get('fatty/main/admin/restaurant_billing/data_history/{restaurant_id}','Admin\Restaurant\RestaurantController@restaurant_billing_history_url');
Route::get('fatty/main/admin/restaurant_billing/data_history/search/{restaurant_id}','Admin\Restaurant\RestaurantController@restaurant_billing_history_search')->name('restaurant_billing_history.search');
Route::get('fatty/main/admin/restaurant_billing/update/{id}','Admin\Restaurant\RestaurantController@restaurant_billing_update');
Route::get('fatty/main/admin/restaurant_billing/data_history/detail/{restaurant_payment_id}','Admin\Restaurant\RestaurantController@restaurant_billing_history_detail_url');

//Billing-rider
Route::get('fatty/main/admin/rider_billing/data_list/{rider_id}','Admin\Rider\RiderController@rider_billing_list_url');
Route::get('fatty/main/admin/rider_billing/data_history/{rider_id}','Admin\Rider\RiderController@rider_billing_history_url');
Route::get('fatty/main/admin/rider_billing/data_history/search/{rider_id}','Admin\Rider\RiderController@rider_billing_history_search')->name('rider_billing_history.search');
Route::get('fatty/main/admin/rider_billing/update/{id}','Admin\Rider\RiderController@rider_billing_update');
Route::get('fatty/main/admin/rider_billing/data_history/detail/{Rider_payment_id}','Admin\Rider\RiderController@rider_billing_history_detail_url');

//Today Billing-rider
Route::get('fatty/main/admin/today_rider_billing/data_list/{rider_id}','Admin\Rider\RiderController@today_rider_billing_list_url');
Route::get('fatty/main/admin/today_rider_billing/data_history/{rider_id}','Admin\Rider\RiderController@today_rider_billing_history_url');
Route::get('fatty/main/admin/today_rider_billing/data_history/search/{rider_id}','Admin\Rider\RiderController@today_rider_billing_history_search')->name('today_rider_billing_history.search');
Route::get('fatty/main/admin/today_rider_billing/update/{id}','Admin\Rider\RiderController@today_rider_billing_update');
Route::get('fatty/main/admin/today_rider_billing/data_history/detail/{Rider_payment_id}','Admin\Rider\RiderController@today_rider_billing_history_detail_url');

//Privacy and Term&Condition
Route::get('fatty/main/admin/privacy','Admin\Setting\SettingController@index');
Route::get('fatty/main/admin/term&condition','Admin\Setting\SettingController@term');

Route::get('application/contact_us','Admin\SupportCenter\SupportCenterController@contact_us');


Route::get('/','Admin\Login\LoginController@index')->name('login');

Route::post('fatty/post/login','Admin\Login\LoginController@login')->name('fatty.post.login');

//Payment
// Route::get('fatty/order/payment/url','Admin\Order\PaymentController@index');
// Route::post('fatty/order/payment/place_order','Admin\Order\PaymentController@create')->name('place_order.create');

Route::get('admin_parcel_orders/login','Admin\Parcel\ParcelStateController@login');
Route::post('admin_parcel_orders/login/check','Admin\Parcel\ParcelStateController@login_check')->name('login_check.create');
Route::get('admin_parcel_orders/logout_check','Admin\Parcel\ParcelStateController@logout_check')->name('logout_check');

//cookies
Route::get('/cookie/set','CookieController@setCookie');
Route::get('/cookie/get','CookieController@getCookie');

Route::get('admin_parcel_orders/copy/{order_id}','Admin\Parcel\ParcelStateController@admin_parcel_copy');
//parcel create
Route::get('admin_parcel_orders/list/{customer_admin_id}','Admin\Parcel\ParcelStateController@admin_parcel_list');
// Route::get('admin_parcel_orders/datatable/listajax/{customer_id}','Admin\Parcel\ParcelStateController@admin_parcel_list_ajax');
Route::get('admin_parcel_orders/filter','Admin\Parcel\ParcelStateController@admin_parcel_filter')->name('admin_parcel_orders.filter');

Route::get('admin_parcel_orders/create/{customer_admin_id}','Admin\Parcel\ParcelStateController@admin_parcel_create');
Route::post('admin_parcel_orders/store','Admin\Parcel\ParcelStateController@admin_parcel_store')->name('admin_parcel.store');

Route::get('admin_parcel_orders/edit/{order_id}/{customer_id}','Admin\Parcel\ParcelStateController@admin_parcel_edit');
Route::post('admin_parcel_orders/update/{order_id}','Admin\Parcel\ParcelStateController@admin_parcel_update')->name('admin_parcel.update');
Route::get('admin_parcel_orders/destroy/{order_id}/{customer_id}','Admin\Parcel\ParcelStateController@admin_parcel_destroy')->name('admin_parcel.destroy');

//calculate price
Route::get('admin_parcel_orders/calculate/price/{from_block_id}/{to_block_id}','Admin\Parcel\ParcelStateController@calculate_price');

//Rider Order Report admin
Route::get('admin_parcel_orders/report/{customer_admin_id}','Admin\Parcel\ParcelStateController@admin_rider_order_report');
Route::get('admin_parcel_orders/report/filter/{customer_admin_id}','Admin\Parcel\ParcelStateController@admin_parcel_report_filter')->name('admin_parcel_report.filter');
Route::get('admin_parcel_orders/report/date/filter/{customer_admin_id}/{current_date}','Admin\Parcel\ParcelStateController@admin_parcel_report_date_filter')->name('admin_parcel_report_date.filter');

//AllRider OrderReport admin
Route::get('admin_parcel_orders/all_report/{customer_admin_id}','Admin\Parcel\ParcelStateController@admin_rider_order_all_report');
Route::get('admin_parcel_orders/all_report/filter/{customer_admin_id}','Admin\Parcel\ParcelStateController@admin_parcel_all_report_filter')->name('admin_parcel_all_report.filter');
Route::get('admin_parcel_orders/all_report/date/filter/{customer_admin_id}/{current_date}','Admin\Parcel\ParcelStateController@admin_parcel_all_report_date_filter')->name('admin_parcel_all_report_date.filter');





// Auth::routes(['verify'=>true]);

Route::group(['prefix'=>'fatty/main/admin','as'=>'fatty.admin.','middleware'=>['auth']], function(){
    //version_update
    Route::get('version_list','Admin\Setting\SettingController@version_list');
    Route::post('version_update/{version_update_id}','Admin\Setting\SettingController@version_update')->name('version.update');
    Route::get('force_update/{version_update_id}','Admin\Setting\SettingController@force_update');
    Route::get('available_update/{version_update_id}','Admin\Setting\SettingController@available_update');

    //kpay_onoff
    Route::get('kpay_onoff_list','Admin\Setting\SettingController@kpay_onoff_list');
    Route::get('kpay_onoff_android/update/{kpay_onoff_id}','Admin\Setting\SettingController@kpay_onoff_update_android')->name('kpay_onoff_android.update');
    Route::post('kpay_update/{kpay_onoff_id}','Admin\Setting\SettingController@kpay_update')->name('kpay.update');

    Route::get('rider_location','Admin\Login\LoginController@location_check');

    //Dashboard
    Route::get('dashboard','Admin\Dashboard\DashboardController@index');

    //Logout
    Route::post('fatty/main/admin/logout','Admin\Login\LoginController@logout')->name('logout');

    //Export Excel
    Route::post('fatty/main/admin/backup/customers','Admin\Backup\BackupController@index')->name('backup.customers');
    Route::post('fatty/main/admin/backup/daily/parcel_orders','Admin\Backup\BackupController@daily_parcel_orders')->name('backup.daily_parcel_orders');
    Route::post('fatty/main/admin/backup/all/food_orders','Admin\Backup\BackupController@all_food_orders')->name('backup.all_food_orders');


    //User
    Route::get('user','Admin\User\UserController@index');
    Route::get('user/create','Admin\User\UserController@create')->name('user.create');
    Route::post('user/store','Admin\User\UserController@store')->name('user.store');
    Route::get('user/edit/{user_id}','Admin\User\UserController@edit')->name('user.edit');
    Route::post('user/update/{user_id}','Admin\User\UserController@update')->name('user.update');
    Route::delete('user/delete/{user_id}','Admin\User\UserController@destroy')->name('user.destroy');

     //Role
    Route::get('roles','Admin\RoleController@index')->name('roles.index');
    Route::get('roles/create','Admin\RoleController@create')->name('roles.create');
    Route::post('roles/store','Admin\RoleController@store')->name('roles.store');
    Route::get('roles/edit/{id}','Admin\RoleController@edit')->name('roles.edit');
    Route::post('roles/update/{id}','Admin\RoleController@update')->name('roles.update');
    Route::delete('roles/delete/{id}','Admin\RoleController@destroy')->name('roles.destroy');


    //Notification Template
    Route::get('notification_templates','Admin\NotificationTemplate\NotificationTemplateController@index');
    Route::get('notification_templates/create','Admin\NotificationTemplate\NotificationTemplateController@create')->name('notification_templates.create');
    Route::post('notification_templates/store','Admin\NotificationTemplate\NotificationTemplateController@store')->name('notification_templates.store');
    Route::get('notification_templates/edit/{notification_template_id}','Admin\NotificationTemplate\NotificationTemplateController@edit')->name('notification_templates.edit');
    Route::post('notification_templates/update/{notification_template_id}','Admin\NotificationTemplate\NotificationTemplateController@update')->name('notification_templates.update');
    Route::delete('notification_templates/delete/{notification_template_id}','Admin\NotificationTemplate\NotificationTemplateController@destroy')->name('notification_templates.destroy');

    //Customer
    Route::get('customers','Admin\Customer\CustomerController@index');
    Route::get('customers/datatable/ssd','Admin\Customer\CustomerController@ssd');
    Route::get('customers/view/{customer_id}','Admin\Customer\CustomerController@show')->name('customers.view');

    //customer chart
    Route::get('customer_chart','Admin\Customer\CustomerController@customerchart');

    Route::get('daily_customers','Admin\Customer\CustomerController@dailyindex');
    Route::get('customers/datatable/dailyajax','Admin\Customer\CustomerController@dailyajax');

    Route::get('monthly_customers','Admin\Customer\CustomerController@monthlyindex');
    Route::get('customers/datatable/monthlyajax','Admin\Customer\CustomerController@monthlyajax');
    // Route::get('monthly_customers/view/{customer_id}','Admin\Customer\CustomerController@monthlyshow')->name('customers.monthlyview');

    Route::get('yearly_customers','Admin\Customer\CustomerController@yearlyindex');
    Route::get('customers/datatable/yearlyajax','Admin\Customer\CustomerController@yearlyajax');
    // Route::get('yearly_customers/view/{customer_id}','Admin\Customer\CustomerController@yearlyshow')->name('customers.yearlyview');

    Route::get('daily_active_customers','Admin\Customer\CustomerController@dailyactiveindex');
    Route::get('customers/datatable/dailyactiveajax','Admin\Customer\CustomerController@dailyactiveajax');

    Route::get('monthly_active_customers','Admin\Customer\CustomerController@monthlyactiveindex');
    Route::get('customers/datatable/monthlyactiveajax','Admin\Customer\CustomerController@monthlyactiveajax');

    Route::get('yearly_active_customers','Admin\Customer\CustomerController@yearlyactiveindex');
    Route::get('customers/datatable/yearlyactiveajax','Admin\Customer\CustomerController@yearlyactiveajax');
    Route::get('active_customer_chart','Admin\Customer\CustomerController@activecustomerchart');


    //Riders
    Route::get('riders','Admin\Rider\RiderController@index');
    Route::get('riders/check/location/{rider_id}','Admin\Rider\RiderController@location')->name('riders.location');
    Route::get('riders/create','Admin\Rider\RiderController@create')->name('riders.create');
    Route::post('riders/store','Admin\Rider\RiderController@store')->name('riders.store');
    Route::get('riders/edit/{rider_id}','Admin\Rider\RiderController@edit')->name('riders.edit');
    Route::post('riders/update/{rider_id}','Admin\Rider\RiderController@update')->name('riders.update');
    Route::get('riders/activenow/{rider_id}','Admin\Rider\RiderController@activenow')->name('riders.activenow');
    Route::get('riders/ban/{rider_id}','Admin\Rider\RiderController@ban_rider')->name('riders.ban');
    Route::get('riders/admin/approved/update/{rider}','Admin\Rider\RiderController@admin_approved');
    Route::get('daily_100_riders/admin/approved/update/{rider}','Admin\Rider\RiderController@daily_admin_approved');
    Route::get('monthly_100_riders/admin/approved/update/{rider}','Admin\Rider\RiderController@monthly_admin_approved');
    Route::get('yearly_100_riders/admin/approved/update/{rider}','Admin\Rider\RiderController@yearly_admin_approved');

    //Rider Level
    Route::get('riders_level','Admin\Rider\RiderController@level_list');
    Route::post('riders_level/store','Admin\Rider\RiderController@level_store')->name('riders_level.store');
    Route::post('riders_level/update/{rider_level_id}','Admin\Rider\RiderController@level_update')->name('riders_level.update');
    Route::delete('riders_level/delete/{rider_level_id}','Admin\Rider\RiderController@level_destroy')->name('riders_level.destroy');


    //Riders Map
    Route::get('all_riders_location','Admin\Rider\RiderController@all_rider_location');
    Route::get('all_riders_location/hasOrder','Admin\Rider\RiderController@has_order');
    Route::get('all_riders_location/hasNotOrder','Admin\Rider\RiderController@has_not_order');
    Route::get('riders/detail/{rider_id}','Admin\Rider\RiderController@rider_map_detail');
    Route::get('riders/detail/search/{rider_id}','Admin\Rider\RiderController@rider_map_detail_search');
    Route::get('assign/order/list/{rider_id}','Admin\Rider\RiderController@assign_order_list');
    Route::get('orders/datatable/assign_order_list_ajax/{rider_id}','Admin\Rider\RiderController@assign_order_list_ajax');
    Route::get('orders/assign/{order_id}/{rider_id}','Admin\Rider\RiderController@assign_order_noti');


    //Rider System Admin
    Route::get('riders/parcel_order/report','Admin\Order\OrderController@rider_parcel_order_report');
    Route::get('riders/parcel_order/datatable/report/parcelorderajax','Admin\Order\OrderController@report_parcelorderajax');

    Route::get('riders/food_order/report','Admin\Order\OrderController@rider_food_order_report');
    Route::get('riders/food_order/datatable/report/foodorderajax','Admin\Order\OrderController@report_foodorderajax');
    Route::get('riders/rider_order/report','Admin\Order\OrderController@rider_order_report');
    Route::get('riders/rider_order/report_filter','Admin\Order\OrderController@rider_order_report_filter');

    Route::get('riders/datatable/riderajax','Admin\Rider\RiderController@riderajax');
    Route::get('rider_chart','Admin\Rider\RiderController@riderchart');

    Route::get('riders/view/{rider_id}','Admin\Rider\RiderController@show')->name('riders.view');
    Route::delete('riders/delete/{rider_id}','Admin\Rider\RiderController@destroy')->name('riders.destroy');

    Route::get('daily_100_riders','Admin\Rider\RiderController@hundredIndex');
    Route::get('riders/datatable/hundredriderajax','Admin\Rider\RiderController@hundredriderajax');

    Route::get('monthly_100_riders','Admin\Rider\RiderController@hundredMonthlyIndex');
    Route::get('riders/datatable/monthlyhundredriderajax','Admin\Rider\RiderController@monthlyhundredriderajax');

    Route::get('yearly_100_riders','Admin\Rider\RiderController@hundredYearlyIndex');
    Route::get('riders/datatable/yearlyhundredriderajax','Admin\Rider\RiderController@yearlyhundredriderajax');

    Route::get('daily_ordered_customers','Admin\Customer\CustomerController@dailyorderedindex');
    Route::get('customers/datatable/dailyorderedajax','Admin\Customer\CustomerController@dailyorderedajax');

    Route::get('monthly_ordered_customers','Admin\Customer\CustomerController@monthlyorderedindex');
    Route::get('customers/datatable/monthlyorderedajax','Admin\Customer\CustomerController@monthlyorderedajax');

    Route::get('yearly_ordered_customers','Admin\Customer\CustomerController@yearlyorderedindex');
    Route::get('customers/datatable/yearlyorderedajax','Admin\Customer\CustomerController@yearlyorderedajax');
    Route::get('order_customer_chart','Admin\Customer\CustomerController@ordercustomerchart');




    //test
    Route::get('rider_get_billing/print/all_page','Admin\Rider\RiderController@rider_print_all_page');



    //customer
    Route::get('customers/create','Admin\Customer\CustomerController@create')->name('customers.create');
    Route::get('customers/view/{customer_id}','Admin\Customer\CustomerController@show')->name('customers.view');
    Route::post('customers/store','Admin\Customer\CustomerController@store')->name('customers.store');
    Route::get('customers/edit/{customer_id}','Admin\Customer\CustomerController@edit')->name('customers.edit');
    Route::post('customers/update/{customer_id}','Admin\Customer\CustomerController@update')->name('customers.update');
    Route::delete('customers/delete/{customer_id}','Admin\Customer\CustomerController@destroy')->name('customers.destroy');
    Route::get('customers/restricted/{customer_id}','Admin\Customer\CustomerController@restricted')->name('customers.restricted');


    //Tutorial
    Route::get('tutorials','Admin\Tutorial\TutorialController@index');
    Route::get('tutorials/create','Admin\Tutorial\TutorialController@create')->name('tutorials.create');
    Route::post('tutorials/store','Admin\Tutorial\TutorialController@store')->name('tutorials.store');
    Route::get('tutorials/edit/{tutorial_id}','Admin\Tutorial\TutorialController@edit')->name('tutorials.edit');
    Route::post('tutorials/update/{tutorial_id}','Admin\Tutorial\TutorialController@update')->name('tutorials.update');
    Route::delete('tutorials/delete/{tutorial_id}','Admin\Tutorial\TutorialController@destroy')->name('tutorials.destroy');

    //Support Center
    Route::get('support_center','Admin\SupportCenter\SupportCenterController@index');
    Route::get('support_center/create','Admin\SupportCenter\SupportCenterController@create')->name('support_center.create');
    Route::post('support_center/store','Admin\SupportCenter\SupportCenterController@store')->name('support_center.store');
    Route::get('support_center/edit/{support_center_id}','Admin\SupportCenter\SupportCenterController@edit')->name('support_center.edit');
    Route::post('support_center/update/{support_center_id}','Admin\SupportCenter\SupportCenterController@update')->name('support_center.update');
    Route::delete('support_center/delete/{support_center_id}','Admin\SupportCenter\SupportCenterController@destroy')->name('support_center.destroy');

    //Zone
    Route::get('zones','Admin\Zone\ZoneController@index');
    Route::get('zones/create','Admin\Zone\ZoneController@create')->name('zones.create');
    Route::post('zones/store','Admin\Zone\ZoneController@store')->name('zones.store');
    Route::get('zones/edit/{zone_id}','Admin\Zone\ZoneController@edit')->name('zones.edit');
    Route::post('zones/update/{zone_id}','Admin\Zone\ZoneController@update')->name('zones.update');
    Route::delete('zones/delete/{zone_id}','Admin\Zone\ZoneController@destroy')->name('zones.destroy');
    Route::get('city/list/{id}','Admin\Zone\ZoneController@city_list');

    //Rider Group
    Route::get('rider_group','Admin\RiderGroup\RiderGroupController@index');
    Route::get('rider_group/create','Admin\RiderGroup\RiderGroupController@create')->name('rider_group.create');
    Route::post('rider_group/store','Admin\RiderGroup\RiderGroupController@store')->name('rider_group.store');
    Route::get('rider_group/edit/{rider_group_id}','Admin\RiderGroup\RiderGroupController@edit')->name('rider_group.edit');
    Route::post('rider_group/update/{rider_group_id}','Admin\RiderGroup\RiderGroupController@update')->name('rider_group.update');
    Route::delete('rider_group/delete/{rider_group_id}','Admin\RiderGroup\RiderGroupController@destroy')->name('rider_group.destroy');
    Route::get('all_user/list/{id}','Admin\RiderGroup\RiderGroupController@user_list');

    //Restaurant Start//
    //CRUD
    Route::get('restaurants','Admin\Restaurant\RestaurantController@index');
    Route::get('restaurants/datatable/restaurantajax','Admin\Restaurant\RestaurantController@restaurantajax');
    Route::get('restaurants/view/{restaurant_id}','Admin\Restaurant\RestaurantController@show')->name('restaurants.view');
    // Route::get('restaurants/create','Admin\Restaurant\RestaurantController@create')->name('restaurants.create');
    Route::post('restaurants/store','Admin\Restaurant\RestaurantController@store')->name('restaurants.store');
    Route::get('restaurants/edit/{restaurant_id}','Admin\Restaurant\RestaurantController@edit')->name('restaurants.edit');
    Route::post('restaurants/update/{restaurant_id}','Admin\Restaurant\RestaurantController@update')->name('restaurants.update');
    Route::delete('restaurants/delete/{restaurant_id}','Admin\Restaurant\RestaurantController@destroy')->name('restaurants.destroy');

    //openingtime
    Route::get('restaurants/openingtime/view/{restaurant_id}','Admin\Restaurant\RestaurantController@openingtime_view')->name('restaurants.openingtime.view');
    Route::post('restaurants/openingtime/update/{restaurant_id}','Admin\Restaurant\RestaurantController@openingtime_update')->name('restaurants_openingtime.update');
    //recommend_restaurant
    Route::get('restaurants/recommends/update/{restaurant_id}','Admin\Restaurant\RestaurantController@restaurant_recommend_update')->name('restaurants_recommend.update');

    //menu
    Route::get('restaurants/menu/list/{restaurant_id}','Admin\Restaurant\RestaurantController@menu_list')->name('restaurants_menu.list');
    Route::get('restaurants/menu/list/datatable/menuajax/{restaurant_id}','Admin\Restaurant\RestaurantController@menu_list_data');
    Route::post('restaurants/menu/store','Admin\Restaurant\RestaurantController@menu_store')->name('restaurants_menu.store');
    Route::get('restaurants/menu/edit/{menu_id}','Admin\Restaurant\RestaurantController@menu_edit');
    Route::post('restaurants/menu/update/{menu_id}','Admin\Restaurant\RestaurantController@menu_update')->name('restaurants_menu.update');
    Route::delete('restaurants/menu/delete/{menu_id}','Admin\Restaurant\RestaurantController@menu_destroy')->name('restaurants_menu.destroy');

    //restaurant_user
    Route::get('restaurants/user/create','Admin\Restaurant\RestaurantController@user_create')->name('restaurants_user.create');
    Route::post('restaurants/user/store','Admin\Restaurant\RestaurantController@user_store')->name('restaurants_user.store');

    //restaurant_food
    Route::get('restaurants/food/detail/view/{food_id}','Admin\Restaurant\RestaurantController@restaurant_food_view')->name('restaurants_food.view');
    Route::get('restaurants/food/list/{restaurant_id}','Admin\Restaurant\RestaurantController@restaurant_food_list')->name('restaurants_food.list');
    Route::get('restaurants/food/list/datatable/foodlistajax/{restaurant_id}','Admin\Restaurant\RestaurantController@foodlistajax');
    Route::get('restaurants/food/create/{restaurant_id}','Admin\Restaurant\RestaurantController@restauant_food_create')->name('restaurants_food.create');
    Route::post('restaurants/food/store','Admin\Restaurant\RestaurantController@restaurant_food_store')->name('restaurants_food.store');
    Route::get('restaurants/food/edit/{food_id}','Admin\Restaurant\RestaurantController@restaurant_food_edit');
    Route::post('restaurants/food/update/{food_id}','Admin\Restaurant\RestaurantController@restaurant_food_update')->name('restaurants_food.update');
    Route::delete('restaurants/food/delete/{food_id}','Admin\Restaurant\RestaurantController@restaurant_food_destroy')->name('restaurants_food.destroy');
    Route::get('restaurants/food/list/restaurants/food/recommend/update/{food_id}','Admin\Restaurant\RestaurantController@restaurant_food_recommend')->name('restaurants_food_recommend.update');
    Route::get('restaurants/food/list/restaurants/food/open/update/{food_id}','Admin\Restaurant\RestaurantController@restaurant_food_open')->name('restaurants_food_open.update');

    //restaurant billing
    Route::get('restaurants_billing/list','Admin\Restaurant\RestaurantController@restaurant_billing_list')->name('restaurants_billing.list');
    Route::get('restaurants_billing/store/{id}','Admin\Restaurant\RestaurantController@restaurant_billing_store')->name('restaurants_billing.store');

    Route::get('restaurants_billing/offered','Admin\Restaurant\RestaurantController@restaurant_billing_offered')->name('restaurants_billing.offered');
    // Route::get('restaurants_billing/update/{id}','Admin\Restaurant\RestaurantController@restaurant_billing_update')->name('restaurants_billing.update');

    //rider billing
    Route::get('v1/riders_billing/list','Admin\Rider\RiderController@rider_billing_list_v1')->name('v1_riders_billing.list');
    Route::get('riders_billing/list','Admin\Rider\RiderController@rider_billing_list')->name('riders_billing.list');
    Route::get('riders_billing/detail/{id}','Admin\Rider\RiderController@rider_billing_detail')->name('riders_billing.detail');
    Route::get('v1/riders_billing/detail/{id}','Admin\Rider\RiderController@rider_billing_detail_v1')->name('v1_riders_billing.detail');
    Route::get('riders_billing/offered','Admin\Rider\RiderController@rider_billing_offered')->name('riders_billing.offered');
    Route::get('riders_billing/history','Admin\Rider\RiderController@rider_billing_history')->name('riders_billing.history');
    Route::get('riders_billing/store/{id}','Admin\Rider\RiderController@rider_billing_store')->name('riders_billing.store');
    Route::get('riders_billing/offered','Admin\Rider\RiderController@rider_billing_offered')->name('riders_billing.offered');
    // Route::get('riders_billing/update/{id}','Admin\Rider\RiderController@rider_billing_update')->name('riders_billing.update');

    //Only day rider billing
    Route::get('today_riders_billing/list','Admin\Rider\RiderController@today_rider_billing_list')->name('today_riders_billing.list');
    Route::get('today_riders_billing/store/{id}','Admin\Rider\RiderController@today_rider_billing_store')->name('today_riders_billing.store');
    Route::get('today_riders_billing/offered','Admin\Rider\RiderController@today_rider_billing_offered')->name('today_riders_billing.offered');


    //chart
    Route::get('restaurant_chart','Admin\Restaurant\RestaurantController@restaurantchart');
    Route::get('restaurants/city/list/{id}','Admin\Restaurant\RestaurantController@city_list1');
    Route::get('restaurants/state/list/{id}','Admin\Restaurant\RestaurantController@state_list');
    //approved and opening
    Route::get('restaurants/approved/update/{restaurant_id}','Admin\Restaurant\RestaurantController@approved_update');
    Route::get('restaurants/opening/update/{restaurant_id}','Admin\Restaurant\RestaurantController@opening_update');
    //100 Restaurant
    Route::get('100_restaurants','Admin\Restaurant\RestaurantController@hundredIndex');
    Route::get('restaurants/datatable/hundredrestaurantajax','Admin\Restaurant\RestaurantController@hundredrestaurantajax');
    Route::get('100_restaurants/approved/update/{restaurant_id}','Admin\Restaurant\RestaurantController@approved_update_100');
    Route::get('100_restaurants/opening/update/{restaurant_id}','Admin\Restaurant\RestaurantController@opening_update_100');
    //Restaurant End//

    //Recommend Restaurant
    Route::get('recommend_restaurants','Admin\Restaurant\RecommendRestaurantController@index');
    Route::get('recommend_restaurants/create','Admin\Restaurant\RecommendRestaurantController@create')->name('recommend_restaurants.create');
    Route::post('recommend_restaurants/store','Admin\Restaurant\RecommendRestaurantController@store')->name('recommend_restaurants.store');
    Route::get('recommend_restaurants/edit/{recommend_restaurant_id}','Admin\Restaurant\RecommendRestaurantController@edit')->name('recommend_restaurants.edit');
    Route::post('recommend_restaurants/update/{recommend_restaurant_id}','Admin\Restaurant\RecommendRestaurantController@update')->name('recommend_restaurants.update');
    Route::delete('recommend_restaurants/delete/{recommend_restaurant_id}','Admin\Restaurant\RecommendRestaurantController@destroy')->name('recommend_restaurants.destroy');
    Route::get('restaurants/city/list/{id}','Admin\Restaurant\RecommendRestaurantController@city_list');
    Route::get('restaurants/list/{id}','Admin\Restaurant\RecommendRestaurantController@restaurant_list');
    Route::post('recommend_restaurants/sort/update','Admin\Restaurant\RecommendRestaurantController@sort_update');

    Route::post('restaurants/list','Admin\Restaurant\RecommendRestaurantController@restaurant_list')->name('recommend_restaurants.show');

    //Category
    Route::get('restaurant/categories','Admin\Restaurant\CategoryController@index');
    Route::post('restaurant/categories/store','Admin\Restaurant\CategoryController@store')->name('restaurant_categories.store');
    Route::post('restaurant/categories/update/{restaurant_category_id}','Admin\Restaurant\CategoryController@update')->name('restaurant_categories.update');
    Route::delete('restaurant/categories/delete/{restaurant_category_id}','Admin\Restaurant\CategoryController@destroy')->name('restaurant_categories.destroy');

    //Category assign
    Route::get('restaurant/categories/assign','Admin\Restaurant\CategoryController@assign_list');
    Route::get('restaurant/categories/assign/create/{restaurant_category_id}','Admin\Restaurant\CategoryController@assign_create')->name('assign_categories.create');
    Route::post('restaurant/categories/assign/store','Admin\Restaurant\CategoryController@assign_store')->name('assign_categories.store');
    Route::get('restaurant/categories/assign/edit/{category_assign_id}','Admin\Restaurant\CategoryController@assign_edit')->name('assign_categories.edit');
    // Route::get('restaurant/categories/assign/edit/{category_assign_id}','Admin\Restaurant\CategoryController@assign_edit')->name('assign_categories.edit');
    Route::post('restaurant/categories/assign/update/{category_assign_id}','Admin\Restaurant\CategoryController@assign_update')->name('assign_categories.update');
    Route::post('restaurant/categories/assign/sort/update','Admin\Restaurant\CategoryController@sort_update');
    Route::delete('restaurant/categories/assign/delete/{caegory_assign_id}','Admin\Restaurant\CategoryController@assign_destroy')->name('assign_categorises.destroy');

    //Ctegory assign Sort
    Route::get('restaurant/categories/assign_sort','Admin\Restaurant\CategoryController@assign_sort_list');
    Route::post('restaurant/categories/assign_type/sort/update','Admin\Restaurant\CategoryController@assign_type_sort_update');

    //Food
    Route::get('foods','Admin\Food\FoodController@index');
    Route::get('foods/create','Admin\Food\FoodController@create')->name('foods.create');
    Route::post('foods/store','Admin\Food\FoodController@store')->name('foods.store');
    Route::get('foods/edit/{food_id}','Admin\Food\FoodController@edit')->name('foods.edit');
    Route::post('foods/update/{food_id}','Admin\Food\FoodController@update')->name('foods.update');
    Route::delete('foods/delete/{food_id}','Admin\Food\FoodController@destroy')->name('foods.destroy');
    Route::get('foods/category/list/{id}','Admin\Food\FoodController@category_list');
    Route::get('foods/menu/list/{id}','Admin\Food\FoodController@menu_list');


    //Food Menu
    Route::get('food_menu','Admin\Food\FoodMenuController@index');
    Route::post('food_menu/store','Admin\Food\FoodMenuController@store')->name('food_menu.store');
    Route::get('food_menu/edit/{food_menu_id}','Admin\Food\FoodMenuController@edit')->name('food_menu.edit');
    Route::post('food_menu/update/{food_menu_id}','Admin\Food\FoodMenuController@update')->name('food_menu.update');
    Route::delete('food_menu/delete/{food_menu_id}','Admin\Food\FoodMenuController@destroy')->name('food_menu.destroy');

    //Food Sub Item
    Route::get('foods/sub_items/{food_id}','Admin\Food\FoodSubItemController@index');
    Route::get('foods/sub_items/create/{food_id}','Admin\Food\FoodSubItemController@create')->name('foods.sub_items.create');
    Route::post('foods/sub_items/store','Admin\Food\FoodSubItemController@store')->name('foods.sub_items.store');
    Route::get('foods/sub_items/edit/{food_sub_item_id}','Admin\Food\FoodSubItemController@edit')->name('foods.sub_items.edit');
    Route::post('foods/sub_items/update/{food_sub_item_id}','Admin\Food\FoodSubItemController@update')->name('foods.sub_items.update');
    Route::delete('foods/sub_items/delete/{food_sub_item_id}','Admin\Food\FoodSubItemController@destroy')->name('foods.sub_items.destroy');

    Route::get('foods/sub_items/section_data/edit/{food_sub_item_data_id}','Admin\Food\FoodSubItemController@item_edit')->name('foods.sub_items.data.edit');
    Route::post('foods/sub_items/section_data/update/{food_sub_item_data_id}','Admin\Food\FoodSubItemController@item_update')->name('foods.sub_items.data.update');

    Route::get('foods/sub_items/data/create/{food_sub_item_id}','Admin\Food\FoodSubItemController@item_create')->name('foods.sub_items.data.create');
    Route::post('foods/sub_items/data/store/{food_sub_item_id}','Admin\Food\FoodSubItemController@item_store')->name('foods.sub_items.data.update');
    Route::get('foods/sub_items/data/edit/{food_sub_item_data_id}','Admin\Food\FoodSubItemController@item_edit')->name('foods.sub_items.data.edit');
    Route::post('foods/sub_items/data/update/{food_sub_item_data_id}','Admin\Food\FoodSubItemController@item_update')->name('foods.sub_items.data.update');
    Route::delete('foods/sub_items/data/delete/{food_sub_item_data_id}','Admin\Food\FoodSubItemController@item_destroy')->name('foods.sub_items.data.destroy');


    //testing
    Route::get('golocation','Admin\About\AboutController@golocation');
    Route::get('riders/map-location','Admin\About\AboutController@all_riders');


    // Route::get('golocation','Admin\About\AboutController@index');

    //UpAds
    Route::get('ads/up_ads','Admin\Ads\UpAdsController@index');
    Route::get('ads/up_ads/create','Admin\Ads\UpAdsController@create')->name('up_ads.create');
    Route::post('ads/up_ads/store','Admin\Ads\UpAdsController@store')->name('up_ads.store');
    Route::get('ads/up_ads/edit/{up_ads_id}','Admin\Ads\UpAdsController@edit')->name('up_ads.edit');
    Route::post('ads/up_ads/update/{up_ads_id}','Admin\Ads\UpAdsController@update')->name('up_ads.update');
    Route::delete('ads/up_ads/delete/{up_ads_id}','Admin\Ads\UpAdsController@destroy')->name('up_ads.destroy');
    Route::post('ads/up_ads/sort_update','Admin\Ads\UpAdsController@sort_update');


    //DownAds
    Route::get('ads/down_ads','Admin\Ads\DownAdsController@index');
    Route::get('ads/down_ads/create','Admin\Ads\DownAdsController@create')->name('down_ads.create');
    Route::post('ads/down_ads/store','Admin\Ads\DownAdsController@store')->name('down_ads.store');
    Route::get('ads/down_ads/edit/{down_ads_id}','Admin\Ads\DownAdsController@edit')->name('down_ads.edit');
    Route::post('ads/down_ads/update/{down_ads_id}','Admin\Ads\DownAdsController@update')->name('down_ads.update');
    Route::delete('ads/down_ads/delete/{down_ads_id}','Admin\Ads\DownAdsController@destroy')->name('down_ads.destroy');
    Route::post('ads/down_ads/sort_update','Admin\Ads\DownAdsController@sort_update');

    //assign Order
    Route::get('foods/orders/lists','Admin\Order\OrderController@index');
    Route::get('orders/datatable/assginorderajax','Admin\Order\OrderController@assignorderajax');

    //assign order update
    Route::get('foods/orders/date_filter','Admin\Order\OrderController@assign_order_datefilter');
    Route::get('foods/orders/search','Admin\Order\OrderController@assign_order_search');

    Route::get('foods/orders/assign/{order_id}','Admin\Order\OrderController@assign')->name('food_orders.assign');
    Route::post('foods/orders/assign/notification/{rider_id}','Admin\Order\OrderController@assign_noti')->name('food_orders.assign.notification');

    //pending_assign
    Route::get('foods/orders/pending_assign/{order_id}','Admin\Order\OrderController@pending_assign');
    Route::post('foods/orders/pending_assign/notification/{rider_id}','Admin\Order\OrderController@pending_assign_noti')->name('food_orders.pending_assign.notification');

    //Food Order View
    Route::get('food_orders/view/{order_id}','Admin\Order\OrderController@show');
    Route::get('parcel_orders/view/{order_id}','Admin\Order\OrderController@parcel_show');

    //parcel edit
    Route::get('parcel_orders/edit/{order_id}','Admin\Parcel\ParcelStateController@parcel_edit')->name('parcel.edit');
    Route::post('parcel_orders/update/{order_id}','Admin\Parcel\ParcelStateController@parcel_update')->name('parcel_order.update');

    Route::get('parcel_orders/create','Admin\Parcel\ParcelStateController@parcel_create')->name('parcel.create');
    Route::post('parcel_orders/store','Admin\Parcel\ParcelStateController@parcel_store')->name('parcel_order.store');

    //food order
    Route::get('daily_food_orders','Admin\Order\OrderController@dailyfoodorderindex');
    Route::get('orders/datatable/dailyfoodorderajax','Admin\Order\OrderController@dailyfoodorderajax');
    Route::get('complete_order/update/{order_id}','Admin\Order\OrderController@completeorderupdate')->name('complete_order.update');

    Route::get('daily_food_orders/list','Admin\Order\OrderController@dailyfoodorderlist');
    Route::get('daily_food_orders/date_filter','Admin\Order\OrderController@dailyfoodorderdatefilter');
    Route::get('daily_food_orders/search','Admin\Order\OrderController@dailyfoodordersearch');

    Route::get('monthly_food_orders','Admin\Order\OrderController@monthlyfoodorderindex');
    Route::get('orders/datatable/monthlyfoodorderajax','Admin\Order\OrderController@monthlyfoodorderajax');

    Route::get('yearly_food_orders','Admin\Order\OrderController@yearlyfoodorderindex');
    Route::get('orders/datatable/yearlyfoodorderajax','Admin\Order\OrderController@yearlyfoodorderajax');

    Route::get('food_orders_chart','Admin\Order\OrderController@foodorderchart');

    //food_order_delivery_fee
    Route::get('food_order_delivery_fee','Admin\Order\OrderController@food_order_delivery_fee');
    Route::post('food_order_delivery_fee/create','Admin\Order\OrderController@food_order_delivery_fee_create')->name('food_order_delivery_fee.create');
    Route::post('food_order_delivery_fee/update/{food_order_deli_fee_id}','Admin\Order\OrderController@food_order_delivery_fee_update')->name('food_order_delivery_fee.update');

    //near_restaurant
    Route::get('near_restaurant_distance','Admin\Restaurant\RestaurantController@near_restaurant_distance');
    Route::post('near_restaurant_distance/update/{near_restaurant_distance_id}','Admin\Restaurant\RestaurantController@near_restaurant_distance_update')->name('near_restaurant_distance.update');


    //Pending Orders
    Route::get('pending/orders/lists','Admin\Order\OrderController@pending');
    Route::get('orders/datatable/pendingorderajax','Admin\Order\OrderController@pendingorderajax');
    Route::get('pending/orders/define/{order_id}','Admin\Order\OrderController@pendingorderdefine')->name('pending_order.update');


    //parcel order
    //daily parcel order list
    Route::get('daily_parcel_orders/list','Admin\Order\OrderController@dailyparcelorderlist');
    Route::get('daily_parcel_orders/date_filter','Admin\Order\OrderController@dailyparcelorderdatefilter');
    Route::get('daily_parcel_orders/search','Admin\Order\OrderController@dailyparcelordersearch');

    Route::get('daily_parcel_orders','Admin\Order\OrderController@dailyparcelorderindex');
    Route::get('orders/datatable/dailyparcelorderajax','Admin\Order\OrderController@dailyparcelorderajax');

    //monthly parcel order list
    Route::get('monthly_parcel_orders/list','Admin\Order\OrderController@monthlyparcelorderlist');
    Route::get('monthly_parcel_orders/date_filter','Admin\Order\OrderController@monthlyparcelorderdatefilter');
    Route::get('monthly_parcel_orders/search','Admin\Order\OrderController@monthlyparcelordersearch');

    Route::get('monthly_parcel_orders','Admin\Order\OrderController@monthlyparcelorderindex');
    Route::get('orders/datatable/monthlyparcelorderajax','Admin\Order\OrderController@monthlyparcelorderajax');

    //yearly parcel order list
    Route::get('yearly_parcel_orders/list','Admin\Order\OrderController@yearlyparcelorderlist');
    Route::get('yearly_parcel_orders/date_filter','Admin\Order\OrderController@yearlyparcelorderdatefilter');
    Route::get('yearly_parcel_orders/search','Admin\Order\OrderController@yearlyparcelordersearch');

    Route::get('yearly_parcel_orders','Admin\Order\OrderController@yearlyparcelorderindex');
    Route::get('orders/datatable/yearlyparcelorderajax','Admin\Order\OrderController@yearlyparcelorderajax');
    Route::get('parcel_orders_chart','Admin\Order\OrderController@parcelorderchart');

    //parcel_state
    Route::get('parcel_states','Admin\Parcel\ParcelStateController@index');
    Route::post('store/parcel_states','Admin\Parcel\ParcelStateController@store')->name('parcel_state.store');;
    Route::post('parcel_states/update/{parcel_state_id}','Admin\Parcel\ParcelStateController@update')->name('parcel_state.update');
    Route::delete('parcel_states/delete/{parcel_states_id}','Admin\Parcel\ParcelStateController@destroy')->name('parcel_state.destroy');

    //multi order limit
    Route::get('multi_order','Admin\Parcel\ParcelBlockController@multi_order_list');
    Route::post('multi_order/store','Admin\Parcel\ParcelBlockController@multi_order_store')->name('multi_order.store');;
    Route::post('multi_order/update/{multi_order_limit_id}','Admin\Parcel\ParcelBlockController@multi_order_update')->name('multi_order.update');
    
    //Benefit
    Route::get('rider_benefit','Admin\Order\BenefitController@index');
    Route::post('rider_benefit/store','Admin\Order\BenefitController@rider_benefit_store')->name('rider_benefit.store');;
    Route::post('rider_benefit/update/{rider_benefit_id}','Admin\Order\BenefitController@rider_benefit_update')->name('rider_benefit.update');
    Route::delete('rider_benefit/delete/{rider_benefit_id}','Admin\Order\BenefitController@rider_benefit_destroy')->name('rider_benefit.destroy');

    //benefit peak time
    Route::get('benefit_peak_time','Admin\Order\BenefitController@peak_index');
    Route::post('benefit_peak_time/store','Admin\Order\BenefitController@benefit_peak_time_store')->name('benefit_peak_time.store');;
    Route::post('benefit_peak_time/update/{benefit_peak_time_id}','Admin\Order\BenefitController@benefit_peak_time_update')->name('benefit_peak_time.update');
    Route::delete('benefit_peak_time/delete/{benefit_peak_time_id}','Admin\Order\BenefitController@benefit_peak_time_destroy')->name('benefit_peak_time.destroy');

    //order_block route
    Route::get('order_block_route','Admin\Parcel\ParcelBlockController@order_block_route_list');
    Route::post('order_block_route/store','Admin\Parcel\ParcelBlockController@order_block_route_store')->name('order_block_route.store');;
    Route::post('order_block_route/update/{order_route_block_id}','Admin\Parcel\ParcelBlockController@order_block_route_update')->name('order_block_route.update');
    Route::delete('order_block_route/delete/{order_route_block_id}','Admin\Parcel\ParcelBlockController@order_block_route_destroy')->name('order_block_route.destroy');
    
    
    //order_block
    Route::get('order_block','Admin\Parcel\ParcelBlockController@order_block_list');
    Route::post('order_block/store','Admin\Parcel\ParcelBlockController@order_block_store')->name('order_block.store');;
    Route::post('order_block/update/{order_block_id}','Admin\Parcel\ParcelBlockController@order_block_update')->name('order_block.update');
    Route::delete('order_block/delete/{order_block_id}','Admin\Parcel\ParcelBlockController@order_block_destroy')->name('order_block.destroy');
   


    //parcel_block
    Route::get('parcel_block','Admin\Parcel\ParcelBlockController@index');
    Route::post('store/parcel_block','Admin\Parcel\ParcelBlockController@store')->name('parcel_block.store');;
    Route::post('parcel_block/update/{parcel_block_id}','Admin\Parcel\ParcelBlockController@update')->name('parcel_block.update');
    Route::delete('parcel_block/delete/{parcel_block_id}','Admin\Parcel\ParcelBlockController@destroy')->name('parcel_block.destroy');

    //parcel_from_to_block
    Route::get('parcel_from_to_block','Admin\Parcel\ParcelFromToBlockController@index');
    Route::get('parcel_from_to_block/dataTables/ajaxparcelfromtoblock','Admin\Parcel\ParcelFromToBlockController@ajaxparcelfromtoblock');
    Route::post('store/parcel_from_to_block','Admin\Parcel\ParcelFromToBlockController@store')->name('parcel_from_to_block.store');;
    Route::delete('parcel_from_to_block/delete/{parcel_from_to_block_id}','Admin\Parcel\ParcelFromToBlockController@destroy')->name('parcel_from_to_block.destroy');

    Route::get('parcel_from_to_block/edit/{parcel_from_to_block_id}','Admin\Parcel\ParcelFromToBlockController@edit')->name('parcel_from_to_block.edit');
    Route::post('parcel_from_to_block/update/{parcel_from_to_block_id}','Admin\Parcel\ParcelFromToBlockController@update')->name('parcel_from_to_block.update');



});
