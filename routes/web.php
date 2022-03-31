<?php


Route::get('/', function () {
    return view('admin.layouts.login_master');
});

Route::get('fatty/main/admin/privacy','Admin\Setting\SettingController@index');
Route::get('fatty/main/admin/term&condition','Admin\Setting\SettingController@term');

Route::get('/','Admin\Login\LoginController@index')->name('login');

Route::post('fatty/post/login','Admin\Login\LoginController@login')->name('fatty.post.login');

//Payment
Route::get('fatty/order/payment/url','Admin\Order\PaymentController@index');
Route::post('fatty/order/payment/place_order','Admin\Order\PaymentController@create')->name('place_order.create');


// Auth::routes(['verify'=>true]);

Route::group(['prefix'=>'fatty/main/admin','as'=>'fatty.admin.','middleware'=>['auth']], function(){

    //Dashboard
    Route::get('dashboard','Admin\Dashboard\DashboardController@index');

    //Logout
    Route::post('fatty/main/admin/logout','Admin\Login\LoginController@logout')->name('logout');

    //Export Excel
    Route::post('fatty/main/admin/backup/customers','Admin\Backup\BackupController@index')->name('backup.customers');


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

    Route::get('customers/create','Admin\Customer\CustomerController@create')->name('customers.create');
    Route::get('customers/view/{customer_id}','Admin\Customer\CustomerController@show')->name('customers.view');
    Route::post('customers/store','Admin\Customer\CustomerController@store')->name('customers.store');
    Route::get('customers/edit/{customer_id}','Admin\Customer\CustomerController@edit')->name('customers.edit');
    Route::post('customers/update/{customer_id}','Admin\Customer\CustomerController@update')->name('customers.update');
    Route::delete('customers/delete/{customer_id}','Admin\Customer\CustomerController@destroy')->name('customers.destroy');

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

    //Restaurant
    Route::get('restaurants','Admin\Restaurant\RestaurantController@index');
    Route::get('restaurants/datatable/restaurantajax','Admin\Restaurant\RestaurantController@restaurantajax');
    Route::get('restaurant_chart','Admin\Restaurant\RestaurantController@restaurantchart');

    Route::get('restaurants/create','Admin\Restaurant\RestaurantController@create')->name('restaurants.create');
    Route::post('restaurants/store','Admin\Restaurant\RestaurantController@store')->name('restaurants.store');
    Route::get('restaurants/edit/{restaurant_id}','Admin\Restaurant\RestaurantController@edit')->name('restaurants.edit');
    Route::post('restaurants/update/{restaurant_id}','Admin\Restaurant\RestaurantController@update')->name('restaurants.update');
    Route::delete('restaurants/delete/{restaurant_id}','Admin\Restaurant\RestaurantController@destroy')->name('restaurants.destroy');
    Route::get('restaurants/city/list/{id}','Admin\Restaurant\RestaurantController@city_list1');
    Route::get('restaurants/state/list/{id}','Admin\Restaurant\RestaurantController@state_list');

    //100 Restaurant
    Route::get('100_restaurants','Admin\Restaurant\RestaurantController@hundredIndex');
    Route::get('restaurants/datatable/hundredrestaurantajax','Admin\Restaurant\RestaurantController@hundredrestaurantajax');

    //Recommend Restaurant
    Route::get('recommend_restaurants','Admin\Restaurant\RecommendRestaurantController@index');
    Route::get('recommend_restaurants/create','Admin\Restaurant\RecommendRestaurantController@create')->name('recommend_restaurants.create');
    Route::post('recommend_restaurants/store','Admin\Restaurant\RecommendRestaurantController@store')->name('recommend_restaurants.store');
    Route::get('recommend_restaurants/edit/{recommend_restaurant_id}','Admin\Restaurant\RecommendRestaurantController@edit')->name('recommend_restaurants.edit');
    Route::post('recommend_restaurants/update/{recommend_restaurant_id}','Admin\Restaurant\RecommendRestaurantController@update')->name('recommend_restaurants.update');
    Route::delete('recommend_restaurants/delete/{recommend_restaurant_id}','Admin\Restaurant\RecommendRestaurantController@destroy')->name('recommend_restaurants.destroy');
    Route::get('restaurants/city/list/{id}','Admin\Restaurant\RecommendRestaurantController@city_list');
    Route::get('restaurants/list/{id}','Admin\Restaurant\RecommendRestaurantController@restaurant_list');

    Route::post('restaurants/list','Admin\Restaurant\RecommendRestaurantController@restaurant_list')->name('recommend_restaurants.show');

    //Category
    Route::get('restaurant/categories','Admin\Restaurant\CategoryController@index');
    Route::post('restaurant/categories/store','Admin\Restaurant\CategoryController@store')->name('restaurant_categories.store');
    Route::post('restaurant/categories/update/{restaurant_category_id}','Admin\Restaurant\CategoryController@update')->name('restaurant_categories.update');
    Route::delete('restaurant/categories/delete/{restaurant_category_id}','Admin\Restaurant\CategoryController@destroy')->name('restaurant_categories.destroy');

    //Category assign
    Route::get('restaurant/categories/assign','Admin\Restaurant\CategoryController@assign_list');
    Route::post('restaurant/categories/assign/store','Admin\Restaurant\CategoryController@assign_store')->name('assign_categories.store');
    Route::get('restaurant/categories/assign/edit/{category_assign_id}','Admin\Restaurant\CategoryController@assign_edit')->name('assign_categories.edit');
    Route::post('restaurant/categories/assign/update/{category_assign_id}','Admin\Restaurant\CategoryController@assign_update')->name('assign_categories.update');

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

    Route::get('foods/sub_items/data/create/{food_sub_item_id}','Admin\Food\FoodSubItemController@item_create')->name('foods.sub_items.data.create');
    Route::post('foods/sub_items/data/store/{food_sub_item_id}','Admin\Food\FoodSubItemController@item_store')->name('foods.sub_items.data.update');

    // Route::get('foods/sub_items/data/create/{food_sub_item_id}','Admin\Food\FoodSubItemController@item_create')->name('foods.sub_items.data.create');
    // Route::post('foods/sub_items/data/store/{food_sub_item_id}','Admin\Food\FoodSubItemController@item_store')->name('foods.sub_items.data.update');
    // Route::delete('foods/sub_items/delete/{food_sub_item_id}','Admin\Food\FoodSubItemController@destroy')->name('foods.sub_items.destroy');

    //testing
    Route::get('golocation','Admin\About\AboutController@golocation');

    //UpAds
    Route::get('ads/up_ads','Admin\Ads\UpAdsController@index');
    Route::get('ads/up_ads/create','Admin\Ads\UpAdsController@create')->name('up_ads.create');
    Route::post('ads/up_ads/store','Admin\Ads\UpAdsController@store')->name('up_ads.store');
    Route::get('ads/up_ads/edit/{up_ads_id}','Admin\Ads\UpAdsController@edit')->name('up_ads.edit');
    Route::post('ads/up_ads/update/{up_ads_id}','Admin\Ads\UpAdsController@update')->name('up_ads.update');
    Route::delete('ads/up_ads/delete/{up_ads_id}','Admin\Ads\UpAdsController@destroy')->name('up_ads.destroy');

    //DownAds
    Route::get('ads/down_ads','Admin\Ads\DownAdsController@index');
    Route::get('ads/down_ads/create','Admin\Ads\DownAdsController@create')->name('down_ads.create');
    Route::post('ads/down_ads/store','Admin\Ads\DownAdsController@store')->name('down_ads.store');
    Route::get('ads/down_ads/edit/{down_ads_id}','Admin\Ads\DownAdsController@edit')->name('down_ads.edit');
    Route::post('ads/down_ads/update/{down_ads_id}','Admin\Ads\DownAdsController@update')->name('down_ads.update');

    //Order
    Route::get('foods/orders/lists','Admin\Order\OrderController@index');
    Route::get('foods/orders/assign/{order_id}','Admin\Order\OrderController@assign')->name('food_orders.assign');
    Route::post('foods/orders/assign/notification/{rider_id}','Admin\Order\OrderController@assign_noti')->name('food_orders.assign.notification');

    //Food Order View
    Route::get('food_orders/view/{order_id}','Admin\Order\OrderController@show');
    Route::get('parcel_orders/view/{order_id}','Admin\Order\OrderController@parcel_show');

    //food order
    Route::get('daily_food_orders','Admin\Order\OrderController@dailyfoodorderindex');
    Route::get('orders/datatable/dailyfoodorderajax','Admin\Order\OrderController@dailyfoodorderajax');

    Route::get('monthly_food_orders','Admin\Order\OrderController@monthlyfoodorderindex');
    Route::get('orders/datatable/monthlyfoodorderajax','Admin\Order\OrderController@monthlyfoodorderajax');

    Route::get('yearly_food_orders','Admin\Order\OrderController@yearlyfoodorderindex');
    Route::get('orders/datatable/yearlyfoodorderajax','Admin\Order\OrderController@yearlyfoodorderajax');

    Route::get('food_orders_chart','Admin\Order\OrderController@foodorderchart');

    //parcel order
    Route::get('daily_parcel_orders','Admin\Order\OrderController@dailyparcelorderindex');
    Route::get('orders/datatable/dailyparcelorderajax','Admin\Order\OrderController@dailyparcelorderajax');

    Route::get('monthly_parcel_orders','Admin\Order\OrderController@monthlyparcelorderindex');
    Route::get('orders/datatable/monthlyparcelorderajax','Admin\Order\OrderController@monthlyparcelorderajax');

    Route::get('yearly_parcel_orders','Admin\Order\OrderController@yearlyparcelorderindex');
    Route::get('orders/datatable/yearlyparcelorderajax','Admin\Order\OrderController@yearlyparcelorderajax');
    Route::get('parcel_orders_chart','Admin\Order\OrderController@parcelorderchart');
});
