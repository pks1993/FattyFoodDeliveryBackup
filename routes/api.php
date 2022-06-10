<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::post('fatty/202221/lashio/main/admin/customers/login','Api\Customer\CustomerApiController@store');
// Route::post('fatty/202221/lashio/main/admin/customers/v1/login','Api\Customer\CustomerApiController@login_version_one');
// Route::post('fatty/202221/lashio/main/admin/customers/login/v2','Api\Customer\CustomerApiController@login_version_two');
// Route::get('fatty/202221/lashio/main/admin/customers','Api\Customer\CustomerApiController@index');
// Route::post('fatty/202221/lashio/main/admin/customers/destroy','Api\Customer\CustomerApiController@destroy');
// Route::post('fatty/202221/lashio/main/admin/customers/one_device/login','Api\Customer\CustomerApiController@one_device_login');
// Route::post('fatty/202221/lashio/main/admin/customers/address/default','Api\Customer\CustomerAddressApiController@default');
// Route::post('fatty/202221/lashio/main/admin/customers/search','Api\Restaurant\RestaurantApiController@food_search');
// Route::post('fatty/202221/lashio/main/admin/wishlist/list','Api\Wishlist\WishlistApiController@index');
// Route::post('fatty/202221/lashio/main/admin/wishlist/delete','Api\Wishlist\WishlistApiController@destroy');
// Route::post('fatty/202221/lashio/main/admin/customers/orders/create','Api\Order\OrderApiController@store');
// Route::post('fatty/202221/lashio/main/admin/customers/orders/cancel','Api\Order\OrderApiController@cancle_order');
// Route::post('fatty/202221/lashio/main/admin/riders/send/noti/to/customers','Api\Order\OrderApiController@send_noti_to_customer');
// Route::post('auth_login','Api\Auth\AuthApiController@index');
// Route::post('fatty/202221/lashio/main/admin/parcels/states','Api\StateCity\StateCityApiController@parcel_state');
// Route::post('fatty/202221/lashio/main/admin/restaurants/orders/status','Api\Order\OrderApiController@restaurant_status');

Route::post('v1/fatty/202221/lashio/main/admin/customers/request_otp','Api\Customer\CustomerApiController@request_otp');
Route::post('v1/fatty/202221/lashio/main/admin/customers/verify_otp','Api\Customer\CustomerApiController@verify_otp');
Route::post('v1/fatty/202221/lashio/main/admin/customers/resend_request_otp','Api\Customer\CustomerApiController@resend_request_otp');
Route::post('v1/fatty/202221/lashio/main/admin/customers/resend_verify_otp','Api\Customer\CustomerApiController@resend_verify_otp');


// Route::middleware('one_device_login')->group(function(){
    //Customer Api
    //one device login

    Route::post('v1/fatty/202221/lashio/main/admin/customers/update','Api\Customer\CustomerApiController@update');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/location','Api\Customer\CustomerApiController@location');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/logout','Api\Customer\CustomerApiController@logout');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/notification/token/update','Api\Customer\CustomerApiController@fcm_token_update');

    //Customer Home Page Api
    Route::post('v1/fatty/202221/lashio/main/admin/home_page','Api\HomePage\HomePageApiController@home_page');
    Route::post('v1/fatty/202221/lashio/main/admin/category/list','Api\HomePage\HomePageApiController@category_list');
    Route::post('v1/fatty/202221/lashio/main/admin/recommend/restaurant/list','Api\HomePage\HomePageApiController@recommend_list');

    //Customer Notification
    Route::get('v1/fatty/202221/lashio/main/admin/customers/notifications','Api\Notification\NotificationApiController@index');
    Route::post('v1/fatty/202221/lashio/main/admin/click/category/data','Api\HomePage\HomePageApiController@click_category_data');
    Route::post('v1/fatty/202221/lashio/main/admin/click/restaurant/data','Api\HomePage\HomePageApiController@click_restaurant_data');
    Route::post('v1/fatty/202221/lashio/main/admin/click/menu/data','Api\HomePage\HomePageApiController@click_menu_data');

    //Customer Address
    Route::post('v1/fatty/202221/lashio/main/admin/customers/address/default','Api\Customer\CustomerAddressApiController@default_v1');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/address/list','Api\Customer\CustomerAddressApiController@index');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/address/create','Api\Customer\CustomerAddressApiController@store');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/address/update','Api\Customer\CustomerAddressApiController@update');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/address/delete','Api\Customer\CustomerAddressApiController@destroy');

    //Choose address parcel
    Route::post('v1/fatty/202221/lashio/main/admin/parcels/choose_address','Api\StateCity\StateCityApiController@parcel_choose_address');

    //Search
    //Food
    Route::post('v1/fatty/202221/lashio/main/admin/customers/search','Api\Restaurant\RestaurantApiController@food_search_v1');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/filter','Api\Restaurant\RestaurantApiController@food_filter');

    //Wishlist
    Route::post('v1/fatty/202221/lashio/main/admin/wishlist/list','Api\Wishlist\WishlistApiController@list_v1');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/wishlists','Api\Wishlist\WishlistApiController@store');

    //Customer About
    Route::get('v1/fatty/202221/lashio/main/admin/application/abouts','Api\About\AboutApiController@index');
    //Customer Support Center
    Route::get('v1/fatty/202221/lashio/main/admin/application/support_centers','Api\SupportCenter\SupportCenterApiController@index');
    //Tutorial
    Route::get('v1/fatty/202221/lashio/main/admin/application/tutorials','Api\Tutorial\TutorialApiController@index');
    //Privacy
    Route::get('v1/fatty/202221/lashio/main/admin/application/customers/privacy','Api\Setting\SettingApiController@customer_privacy');
    //Terms&Conditions
    Route::get('v1/fatty/202221/lashio/main/admin/application/termsandconditions','Api\Setting\SettingApiController@url_terms');
    Route::get('v1/fatty/202221/lashio/main/admin/application/customers/termsandconditions','Api\Setting\SettingApiController@customer_terms');

    //Customer Order
    Route::post('v1/fatty/202221/lashio/main/admin/customers/orders/lists','Api\Order\OrderApiController@customer_index');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/orders/create','Api\Order\OrderApiController@store_v1');
    Route::post('v1/fatty/202221/lashio/main/admin/customer/orders/click','Api\Order\OrderApiController@customer_order_click');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/orders/cancel','Api\Order\OrderApiController@cancle_order_v1');
    Route::get('v1/fatty/202221/lashio/main/admin/order/payment/lists','Api\Order\OrderApiController@payment_list');
    Route::get('v1/fatty/202221/lashio/main/admin/order/status/lists','Api\Order\OrderApiController@status_list');

    //kpay close api
    Route::post('v1/fatty/202221/lashio/main/admin/customers/ios/payment/list','Api\Order\OrderApiController@kpay_close');
    //order reviews
    Route::post('v1/fatty/202221/lashio/main/admin/customers/orders/reviews','Api\Order\ParcelOrderApiController@order_reviews');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/food/orders/delivery/fee','Api\Order\OrderApiController@deleivery_fee');
    //force update
    Route::post('v1/fatty/main/admin/android/version/check','Api\Notification\NotificationApiController@android_version_check');
    Route::get('v1/fatty/main/admin/ios/version/check','Api\Notification\NotificationApiController@ios_version_check');

    //parcel
    Route::post('v1/fatty/202221/lashio/main/admin/customers/parcels/type/store','Api\Order\ParcelOrderApiController@create');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/parcels/extra/store','Api\Order\ParcelOrderApiController@store');
    Route::get('v1/fatty/202221/lashio/main/admin/customers/parcels/type/list','Api\Order\ParcelOrderApiController@parcel_type_list');
    Route::get('v1/fatty/202221/lashio/main/admin/customers/parcels/extra/list','Api\Order\ParcelOrderApiController@parcel_extra_list');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/parcels/images/delete','Api\Order\ParcelOrderApiController@parcel_image_delete');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/parcels/orders/list','Api\Order\ParcelOrderApiController@index');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/parcels/orders/store','Api\Order\ParcelOrderApiController@order_store');
    Route::post('v1/fatty/202221/lashio/main/admin/customers/parcels/orders/total_estimate','Api\Order\ParcelOrderApiController@order_estimate_cost');
// });
//Login Api

//Rider Notification
Route::get('v1/fatty/202221/lashio/main/admin/riders/notifications','Api\Notification\NotificationApiController@rider');
//Restauant Notification
Route::get('v1/fatty/202221/lashio/main/admin/restaurants/notifications','Api\Notification\NotificationApiController@restaurant');


//Restaurant Api
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/users/register','Api\Restaurant\RestaurantApiController@user_register');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/register','Api\Restaurant\RestaurantApiController@restaurant_register');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/login','Api\Restaurant\RestaurantApiController@login');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/update','Api\Restaurant\RestaurantApiController@update');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/foods/create','Api\Restaurant\RestaurantApiController@food_store');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/foods/update','Api\Restaurant\RestaurantApiController@food_update');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/foods/menus','Api\Restaurant\RestaurantApiController@food_menus');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/foods/menus/create','Api\Restaurant\RestaurantApiController@food_menus_create');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/foods/menus/update','Api\Restaurant\RestaurantApiController@food_menus_update');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/foods/menus/delete','Api\Restaurant\RestaurantApiController@food_menus_delete');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/opening/list','Api\Restaurant\RestaurantApiController@opening_list');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/opening/create','Api\Restaurant\RestaurantApiController@opening_store');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/preparing_time/define','Api\Restaurant\RestaurantApiController@preparing_store');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/preparing_time/list','Api\Restaurant\RestaurantApiController@preparing_list');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/details','Api\Restaurant\RestaurantApiController@restaurant_details');

Route::post('v1/fatty/202221/lashio/main/admin/restaurants_token/update','Api\Restaurant\RestaurantApiController@restaurant_token_update');

//Food Menu Api
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/menus','Api\Restaurant\RestaurantApiController@restaurant_menu');

//All States
Route::post('v1/fatty/202221/lashio/main/admin/states','Api\StateCity\StateCityApiController@index');

//Parcel State
Route::post('v1/fatty/202221/lashio/main/admin/parcels/states','Api\StateCity\StateCityApiController@parcel_state_version1');
Route::post('v1/fatty/202221/lashio/main/admin/rider/parcels/choose_address','Api\StateCity\StateCityApiController@parcel_choose_address');

//Rider About
Route::get('v1/fatty/202221/lashio/main/admin/application/riders/abouts','Api\About\AboutApiController@rider_about');
//Restaurant About
Route::get('v1/fatty/202221/lashio/main/admin/application/restaurants/abouts','Api\About\AboutApiController@restaurant_about');
//Rider Support Center
Route::get('v1/fatty/202221/lashio/main/admin/application/riders/support_centers','Api\SupportCenter\SupportCenterApiController@rider_support_center');
//Restaurant Support Center
Route::get('v1/fatty/202221/lashio/main/admin/application/restaurants/support_centers','Api\SupportCenter\SupportCenterApiController@restaurant_support_center');

//Privacy riders
Route::get('v1/fatty/202221/lashio/main/admin/application/riders/privacy','Api\Setting\SettingApiController@rider_privacy');
//Privacy restaurants
Route::get('v1/fatty/202221/lashio/main/admin/application/restaurants/privacy','Api\Setting\SettingApiController@restaurant_privacy');
//Terms&conditions
Route::get('v1/fatty/202221/lashio/main/admin/application/riders/termsandconditions','Api\Setting\SettingApiController@rider_terms');
Route::get('v1/fatty/202221/lashio/main/admin/application/restaurants/termsandconditions','Api\Setting\SettingApiController@restaurant_terms');



//Restaurant Order
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/orders/count','Api\Order\OrderApiController@restaurant_order_count');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/orders/lists','Api\Order\OrderApiController@restaurant_index');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/orders/preparing','Api\Order\OrderApiController@restaurant_preparing');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/orders/click','Api\Order\OrderApiController@restaurant_order_click');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/orders/status','Api\Order\OrderApiController@restaurant_status_v1');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/cancle_orders/status','Api\Order\OrderApiController@restaurant_cancle_order');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/cancel_orders/status','Api\Order\OrderApiController@restaurant_cancel_order_v1');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/orders/details','Api\Restaurant\RestaurantApiController@restaurant_order_details');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/activenow','Api\Restaurant\RestaurantApiController@activenow');
Route::post('v1/fatty/202221/lashio/main/admin/foods/onoff','Api\Restaurant\RestaurantApiController@food_onoff');

//Rider
Route::post('v1/fatty/202221/lashio/main/admin/rider/login','Api\Rider\RiderApiController@login');
Route::post('v1/fatty/202221/lashio/main/admin/rider/update','Api\Rider\RiderApiController@update');
Route::post('v1/fatty/202221/lashio/main/admin/rider/home_page','Api\Rider\RiderApiController@home_page');
Route::post('v1/fatty/202221/lashio/main/admin/rider/location/update','Api\Rider\RiderApiController@rider_location');
Route::post('v1/fatty/202221/lashio/main/admin/rider/location','Api\Rider\RiderApiController@rider_location_get');
Route::post('v1/fatty/202221/lashio/main/admin/rider/orders/status','Api\Rider\RiderApiController@order_status');
Route::post('v1/fatty/202221/lashio/main/admin/rider/foods/orders/history','Api\Rider\RiderApiController@order_food_history');
Route::post('v1/fatty/202221/lashio/main/admin/rider/foods/orders/history/filter','Api\Rider\RiderApiController@order_food_history_filter');
Route::post('v1/fatty/202221/lashio/main/admin/rider/foods/orders/details','Api\Rider\RiderApiController@order_details');

Route::post('v1/fatty/202221/lashio/main/admin/rider/attendance','Api\Rider\RiderApiController@rider_attendance');
Route::post('v1/fatty/202221/lashio/main/admin/rider/activenow','Api\Rider\RiderApiController@rider_activenow');
Route::post('v1/fatty/202221/lashio/main/admin/rider/activenow/list','Api\Rider\RiderApiController@rider_activenow_list');
Route::post('v1/fatty/202221/lashio/main/admin/rider/office/location','Api\Rider\RiderApiController@rider_office_location');
Route::post('v1/fatty/202221/lashio/main/admin/rider/details','Api\Rider\RiderApiController@rider_details');
Route::post('v1/fatty/202221/lashio/main/admin/rider_token/update','Api\Rider\RiderApiController@rider_token_update');
Route::post('v1/fatty/202221/lashio/main/admin/rider/parcels/orders/total_estimate','Api\Order\ParcelOrderApiController@order_estimate_cost');


//insightRider
Route::post('v1/fatty/202221/lashio/main/admin/rider/insight','Api\Rider\RiderApiController@rider_insight');
Route::post('v1/fatty/202221/lashio/main/admin/rider/get_billing','Api\Rider\RiderApiController@rider_getBilling');

//insightRestaurant
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/insight','Api\Restaurant\RestaurantApiController@restaurant_insight');
Route::post('v1/fatty/202221/lashio/main/admin/restaurants/insight','Api\Restaurant\RestaurantApiController@restaurant_insight_v1');

//Testing
Route::post('rider/test/store','Api\Rider\RiderApiController@test_store');
Route::post('v1/fatty/202221/lashio/main/admin/testing','Api\Rider\RiderApiController@index');
Route::get('v1/fatty/202221/lashio/main/admin/customers/food/orders/delivery/Hello','Api\Order\OrderApiController@Test');
Route::get('restaurant/available/create/all','Api\Restaurant\RestaurantApiController@available_create');

//rider parcel
Route::post('v1/fatty/202221/lashio/main/admin/rider/orders/click','Api\Order\OrderApiController@customer_order_click');
Route::post('v1/fatty/202221/lashio/main/admin/rider/parcels/images/delete','Api\Order\ParcelOrderApiController@parcel_image_delete');
Route::get('v1/fatty/202221/lashio/main/admin/rider/parcels/extra/list','Api\Order\ParcelOrderApiController@parcel_extra_list');
Route::get('v1/fatty/202221/lashio/main/admin/rider/parcels/type/list','Api\Order\ParcelOrderApiController@parcel_type_list');
Route::post('v1/fatty/202221/lashio/main/admin/rider/parcels/orders/update','Api\Order\ParcelOrderApiController@rider_order_update');


//Notify url
Route::post('fatty/main/admin/kbz/pay/notify_url','Api\Notification\NotificationApiController@notify_url');

//force update
Route::get('v1/fatty/main/admin/restaurant/version/check','Api\Notification\NotificationApiController@restaurant_version_check');
Route::get('v1/fatty/main/admin/rider/version/check','Api\Notification\NotificationApiController@rider_version_check');

















