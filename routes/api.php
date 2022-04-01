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


//Login Api
// Route::post('auth_login','Api\Auth\AuthApiController@index');


//Customer Api
Route::get('fatty/202221/lashio/main/admin/customers','Api\Customer\CustomerApiController@index');
Route::post('fatty/202221/lashio/main/admin/customers/login','Api\Customer\CustomerApiController@store');
Route::post('fatty/202221/lashio/main/admin/customers/v1/login','Api\Customer\CustomerApiController@login_version_one');
Route::post('fatty/202221/lashio/main/admin/customers/otp_send','Api\Customer\CustomerApiController@otp_send');
Route::post('fatty/202221/lashio/main/admin/customers/otp_check','Api\Customer\CustomerApiController@otp_check');
Route::post('fatty/202221/lashio/main/admin/customers/update','Api\Customer\CustomerApiController@update');
Route::post('fatty/202221/lashio/main/admin/customers/location','Api\Customer\CustomerApiController@location');
Route::post('fatty/202221/lashio/main/admin/customers/destroy','Api\Customer\CustomerApiController@destroy');

//Customer Notification
Route::get('fatty/202221/lashio/main/admin/customers/notifications','Api\Notification\NotificationApiController@index');

//Customer Home Page Api
Route::post('fatty/202221/lashio/main/admin/home_page','Api\HomePage\HomePageApiController@home_page');
Route::post('fatty/202221/lashio/main/admin/category/list','Api\HomePage\HomePageApiController@category_list');
Route::post('fatty/202221/lashio/main/admin/recommend/restaurant/list','Api\HomePage\HomePageApiController@recommend_list');

Route::post('fatty/202221/lashio/main/admin/click/category/data','Api\HomePage\HomePageApiController@click_category_data');
Route::post('fatty/202221/lashio/main/admin/click/restaurant/data','Api\HomePage\HomePageApiController@click_restaurant_data');
Route::post('fatty/202221/lashio/main/admin/click/menu/data','Api\HomePage\HomePageApiController@click_menu_data');

//Customer Address
Route::post('fatty/202221/lashio/main/admin/customers/address/default','Api\Customer\CustomerAddressApiController@default');
Route::post('fatty/202221/lashio/main/admin/customers/address/v1/default','Api\Customer\CustomerAddressApiController@default_v1');
Route::post('fatty/202221/lashio/main/admin/customers/address/list','Api\Customer\CustomerAddressApiController@index');
Route::post('fatty/202221/lashio/main/admin/customers/address/create','Api\Customer\CustomerAddressApiController@store');
Route::post('fatty/202221/lashio/main/admin/customers/address/update','Api\Customer\CustomerAddressApiController@update');
Route::post('fatty/202221/lashio/main/admin/customers/address/delete','Api\Customer\CustomerAddressApiController@destroy');

//Restaurant Api
Route::post('fatty/202221/lashio/main/admin/restaurants/users/register','Api\Restaurant\RestaurantApiController@user_register');
Route::post('fatty/202221/lashio/main/admin/restaurants/register','Api\Restaurant\RestaurantApiController@restaurant_register');
Route::post('fatty/202221/lashio/main/admin/restaurants/login','Api\Restaurant\RestaurantApiController@login');
Route::post('fatty/202221/lashio/main/admin/restaurants/update','Api\Restaurant\RestaurantApiController@update');
Route::post('fatty/202221/lashio/main/admin/restaurants/foods/create','Api\Restaurant\RestaurantApiController@food_store');
Route::post('fatty/202221/lashio/main/admin/restaurants/foods/update','Api\Restaurant\RestaurantApiController@food_update');
Route::post('fatty/202221/lashio/main/admin/restaurants/foods/menus','Api\Restaurant\RestaurantApiController@food_menus');
Route::post('fatty/202221/lashio/main/admin/restaurants/foods/menus/create','Api\Restaurant\RestaurantApiController@food_menus_create');
Route::post('fatty/202221/lashio/main/admin/restaurants/foods/menus/update','Api\Restaurant\RestaurantApiController@food_menus_update');
Route::post('fatty/202221/lashio/main/admin/restaurants/foods/menus/delete','Api\Restaurant\RestaurantApiController@food_menus_delete');
Route::post('fatty/202221/lashio/main/admin/restaurants/opening/list','Api\Restaurant\RestaurantApiController@opening_list');
Route::post('fatty/202221/lashio/main/admin/restaurants/opening/create','Api\Restaurant\RestaurantApiController@opening_store');
Route::post('fatty/202221/lashio/main/admin/restaurants/preparing_time/define','Api\Restaurant\RestaurantApiController@preparing_store');
Route::post('fatty/202221/lashio/main/admin/restaurants/preparing_time/list','Api\Restaurant\RestaurantApiController@preparing_list');
Route::post('fatty/202221/lashio/main/admin/restaurants/details','Api\Restaurant\RestaurantApiController@restaurant_details');


//Food Menu Api
Route::post('fatty/202221/lashio/main/admin/restaurants/menus','Api\Restaurant\RestaurantApiController@restaurant_menu');

//All States
Route::post('fatty/202221/lashio/main/admin/states','Api\StateCity\StateCityApiController@index');

//Parcel State
Route::post('fatty/202221/lashio/main/admin/parcels/states','Api\StateCity\StateCityApiController@parcel_state');
Route::post('fatty/202221/lashio/main/admin/parcels/states/v1','Api\StateCity\StateCityApiController@parcel_state_version1');

//Search
//Food
Route::post('fatty/202221/lashio/main/admin/customers/search','Api\Restaurant\RestaurantApiController@food_search');
Route::post('fatty/202221/lashio/main/admin/customers/filter','Api\Restaurant\RestaurantApiController@food_filter');

//Wishlist
Route::post('fatty/202221/lashio/main/admin/wishlist/list','Api\Wishlist\WishlistApiController@index');
Route::post('fatty/202221/lashio/main/admin/wishlist/list/v1','Api\Wishlist\WishlistApiController@list_v1');
Route::post('fatty/202221/lashio/main/admin/customers/wishlists','Api\Wishlist\WishlistApiController@store');
Route::post('fatty/202221/lashio/main/admin/wishlist/delete','Api\Wishlist\WishlistApiController@destroy');
//About
Route::get('fatty/202221/lashio/main/admin/application/abouts','Api\About\AboutApiController@index');
Route::get('fatty/202221/lashio/main/admin/application/riders/abouts','Api\About\AboutApiController@rider_about');
Route::get('fatty/202221/lashio/main/admin/application/restaurants/abouts','Api\About\AboutApiController@restaurant_about');

//Support Center
Route::get('fatty/202221/lashio/main/admin/application/support_centers','Api\SupportCenter\SupportCenterApiController@index');
Route::get('fatty/202221/lashio/main/admin/application/riders/support_centers','Api\SupportCenter\SupportCenterApiController@rider_support_center');
Route::get('fatty/202221/lashio/main/admin/application/restaurants/support_centers','Api\SupportCenter\SupportCenterApiController@restaurant_support_center');

//Tutorial
Route::get('fatty/202221/lashio/main/admin/application/tutorials','Api\Tutorial\TutorialApiController@index');
//Privacy
Route::get('fatty/202221/lashio/main/admin/application/customers/privacy','Api\Setting\SettingApiController@customer_privacy');
Route::get('fatty/202221/lashio/main/admin/application/riders/privacy','Api\Setting\SettingApiController@rider_privacy');
Route::get('fatty/202221/lashio/main/admin/application/restaurants/privacy','Api\Setting\SettingApiController@restaurant_privacy');

//Terms&Conditions
Route::get('fatty/202221/lashio/main/admin/application/customers/termsandconditions','Api\Setting\SettingApiController@customer_terms');
Route::get('fatty/202221/lashio/main/admin/application/riders/termsandconditions','Api\Setting\SettingApiController@rider_terms');
Route::get('fatty/202221/lashio/main/admin/application/restaurants/termsandconditions','Api\Setting\SettingApiController@restaurant_terms');

Route::get('fatty/202221/lashio/main/admin/application/termsandconditions','Api\Setting\SettingApiController@url_terms');

//Customer Order
Route::post('fatty/202221/lashio/main/admin/customers/orders/lists','Api\Order\OrderApiController@customer_index');
Route::post('fatty/202221/lashio/main/admin/customers/orders/create','Api\Order\OrderApiController@store');
Route::post('fatty/202221/lashio/main/admin/customers/orders/create/v1','Api\Order\OrderApiController@store_v1');
Route::post('fatty/202221/lashio/main/admin/customer/orders/click','Api\Order\OrderApiController@customer_order_click');
Route::get('fatty/202221/lashio/main/admin/order/payment/lists','Api\Order\OrderApiController@payment_list');
Route::get('fatty/202221/lashio/main/admin/order/status/lists','Api\Order\OrderApiController@status_list');
Route::post('fatty/202221/lashio/main/admin/customers/orders/cancel','Api\Order\OrderApiController@cancle_order');
Route::post('fatty/202221/lashio/main/admin/customers/orders/v1/cancel','Api\Order\OrderApiController@cancle_order_v1');
Route::post('fatty/202221/lashio/main/admin/riders/send/noti/to/customers','Api\Order\OrderApiController@send_noti_to_customer');



//Restaurant Order
Route::post('fatty/202221/lashio/main/admin/restaurants/orders/count','Api\Order\OrderApiController@restaurant_order_count');
Route::post('fatty/202221/lashio/main/admin/restaurants/orders/lists','Api\Order\OrderApiController@restaurant_index');
Route::post('fatty/202221/lashio/main/admin/restaurants/orders/preparing','Api\Order\OrderApiController@restaurant_preparing');
Route::post('fatty/202221/lashio/main/admin/restaurants/orders/click','Api\Order\OrderApiController@restaurant_order_click');
Route::post('fatty/202221/lashio/main/admin/restaurants/orders/status','Api\Order\OrderApiController@restaurant_status');
Route::post('fatty/202221/lashio/main/admin/restaurants/orders/v1/status','Api\Order\OrderApiController@restaurant_status_v1');
Route::post('fatty/202221/lashio/main/admin/restaurants/cancle_orders/status','Api\Order\OrderApiController@restaurant_cancle_order');
Route::post('fatty/202221/lashio/main/admin/restaurants/cancel_orders/v1/status','Api\Order\OrderApiController@restaurant_cancel_order_v1');
Route::post('fatty/202221/lashio/main/admin/restaurants/orders/details','Api\Restaurant\RestaurantApiController@restaurant_order_details');
Route::post('fatty/202221/lashio/main/admin/restaurants/activenow','Api\Restaurant\RestaurantApiController@activenow');
Route::post('fatty/202221/lashio/main/admin/foods/onoff','Api\Restaurant\RestaurantApiController@food_onoff');

//Rider
Route::post('fatty/202221/lashio/main/admin/rider/login','Api\Rider\RiderApiController@login');
Route::post('fatty/202221/lashio/main/admin/rider/update','Api\Rider\RiderApiController@update');
Route::post('fatty/202221/lashio/main/admin/rider/home_page','Api\Rider\RiderApiController@home_page');
Route::post('fatty/202221/lashio/main/admin/rider/location/update','Api\Rider\RiderApiController@rider_location');
Route::post('fatty/202221/lashio/main/admin/rider/location','Api\Rider\RiderApiController@rider_location_get');
Route::post('fatty/202221/lashio/main/admin/rider/orders/status','Api\Rider\RiderApiController@order_status');
Route::post('fatty/202221/lashio/main/admin/rider/foods/orders/history','Api\Rider\RiderApiController@order_food_history');
Route::post('fatty/202221/lashio/main/admin/rider/foods/orders/history/filter','Api\Rider\RiderApiController@order_food_history_filter');
Route::post('fatty/202221/lashio/main/admin/rider/foods/orders/details','Api\Rider\RiderApiController@order_details');

Route::post('fatty/202221/lashio/main/admin/rider/attendance','Api\Rider\RiderApiController@rider_attendance');
Route::post('fatty/202221/lashio/main/admin/rider/activenow','Api\Rider\RiderApiController@rider_activenow');
Route::post('fatty/202221/lashio/main/admin/rider/activenow/list','Api\Rider\RiderApiController@rider_activenow_list');
Route::post('fatty/202221/lashio/main/admin/rider/office/location','Api\Rider\RiderApiController@rider_office_location');
Route::post('fatty/202221/lashio/main/admin/rider/details','Api\Rider\RiderApiController@rider_details');

//insightRider
Route::post('fatty/202221/lashio/main/admin/rider/insight','Api\Rider\RiderApiController@rider_insight');
//insightRestaurant
Route::post('fatty/202221/lashio/main/admin/restaurants/insight','Api\Restaurant\RestaurantApiController@restaurant_insight');

//Testing
Route::post('rider/test/store','Api\Rider\RiderApiController@test_store');
Route::post('fatty/202221/lashio/main/admin/testing','Api\Rider\RiderApiController@index');
Route::get('fatty/202221/lashio/main/admin/customers/food/orders/delivery/Hello','Api\Order\OrderApiController@Test');

//parcel
Route::post('fatty/202221/lashio/main/admin/customers/parcels/type/store','Api\Order\ParcelOrderApiController@create');
Route::post('fatty/202221/lashio/main/admin/customers/parcels/extra/store','Api\Order\ParcelOrderApiController@store');

Route::get('fatty/202221/lashio/main/admin/customers/parcels/type/list','Api\Order\ParcelOrderApiController@parcel_type_list');
Route::get('fatty/202221/lashio/main/admin/customers/parcels/extra/list','Api\Order\ParcelOrderApiController@parcel_extra_list');
Route::post('fatty/202221/lashio/main/admin/customers/parcels/images/delete','Api\Order\ParcelOrderApiController@parcel_image_delete');

Route::post('fatty/202221/lashio/main/admin/customers/parcels/orders/list','Api\Order\ParcelOrderApiController@index');
Route::post('fatty/202221/lashio/main/admin/customers/parcels/orders/store','Api\Order\ParcelOrderApiController@order_store');
Route::post('fatty/202221/lashio/main/admin/customers/parcels/orders/riders/update','Api\Order\ParcelOrderApiController@rider_order_update');
Route::post('fatty/202221/lashio/main/admin/customers/parcels/orders/total_estimate','Api\Order\ParcelOrderApiController@order_estimate_cost');


//order reviews
Route::post('fatty/202221/lashio/main/admin/customers/orders/reviews','Api\Order\ParcelOrderApiController@order_reviews');
Route::post('fatty/202221/lashio/main/admin/customers/food/orders/delivery/fee','Api\Order\OrderApiController@deleivery_fee');

//Notify url
Route::post('fatty/main/admin/kbz/pay/notify_url','Api\Notification\NotificationApiController@notify_url');


















