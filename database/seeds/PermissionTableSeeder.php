<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
         $permissions = [

           'role-list',
           'role-create',
           'role-edit',
           'role-delete',

           'user-list',
           'user-create',
           'user-edit',
           'user-delete',

           'customers-list',
           'customers-create',
           'customers-edit',
           'customers-delete',

           'zone-list',
           'zone-create',
           'zone-edit',
           'zone-delete',

           'riders-list',
           'riders-create',
           'riders-edit',
           'riders-delete',

           'riders_level-list',
           'riders_level-store',
           'riders_level-update',
           'riders_level-delete',

           'restaurants-list',
           'restaurants-create',
           'restaurants-edit',
           'restaurants-delete',

           'down_ads-list',
           'down_ads-create',
           'down_ads-edit',
           'down_ads-delete',
           
           'up_ads-list',
           'up_ads-create',
           'up_ads-edit',
           'up_ads-delete',

           'daily_customers-list',
           'monthly_customers-list',
           'yearly_customers-list',
           'customerchart-list',
           
           'daily_ordered_customers-list',
           'daily_active_customers-list',
           'yearly_active_customers-list',
           'order_customer_chart-list',
           
           
           'food_orders_chart-list',
           'active_customer_chart-list',
           'order_assign-list',
           'pending-list',
           
           'rider_order_assign-list',
           'rider_order_assign-noti',
           'rider_pending_assign-list',
           'rider_pending_assign-noti',
           
           'food_orders-detail',
           'daily_food_orders-list',
           'monthly_food_orders-list',
           'yearly_food_orders-list',
           
           
           'daily_parcel_orders-list',
           'monthly_parcel_orders-list',
           'yearly_parcel_orders-list',
           'parcel_orders_chart-list',
           
           'rider_order_report-list',
           'rider_parcel_order_report-list',
           'riders_chart-list',
           'riders_location-list',
           
           'riders-view',
           'riders_activenow-update',
           'riders_ban-update',
           
           
           'categories-list',
           'categories-store',
           'categories-update',
           'categories-delete',
           
           'monthly_ordered_customers-list',
           'yearly_ordered_customers-list',
           'monthly_active_customers-list',
           'food_order_delivery_fee-list',

           
           'restaurant_category_type-list',
           'restaurants_user_create-list',
           'restaurants_user_store-list',
           'restaurants_opening-update',
           
           'restaurants_approved-update',
           'restaurants_recommend-update',
           '100_restaurants_approved-update',
           '100_restaurants_recommend-update',
           
           
           'restaurants_menu-list',
           'restaurants_menu-store',
           'restaurants_menu-edit',
           'restaurants_menu-delete',
           
           'recommend_restaurants-list',
           'recommend_restaurants-create',
           'recommend_restaurants-edit',
           'recommend_restaurants-delete',
           
           'restaurant_food-list',
           'restaurant_food-view',
           'restaurant_food-create',
           'restaurant_food-eidt',
           'restaurant_food-delete',
           
           'parcel_block-list',
           'parcel_block-create',
           'parcel_block-eidt',
           'parcel_block-delete',
           'parcel_from_to_block-list',
           'parcel_from_to_block-create',
           'parcel_from_to_block-eidt',
           'parcel_from_to_block-delete',

           'restaurants_openingtime-view',
           'restaurants_chart-list',
           'rider_billing-store',
           'rider_billing-detail',
           
           'restaurant_food_recommend-update',
           'restaurant_food_open-update',
           'restaurant_billing-list',
           'restaurant_billing-store',
           
           'rider_billing-list',
           'rider_billing_offered-list',
           'rider_billing_history-list',
           'today_rider_billing-list',

           'foods_sub_items-list',
           'foods_sub_items-create',
           'kpay_onoff-list',
           'kpay_onoff-update',
           
           'notification-list',
           'notification-create',
           'notification-edit',
           'notification-delete',
           
           'support_center-list',
           'support_center-create',
           'support_center-edit',
           'support_center-delete',
           
           'version-list',
           'version-update',
           'force_update',
           'available_update',
           
           'tutorials-list',
           'tutorials-create',
           'tutorials-edit',
           'tutorials-delete',
           
           'dashboard-list',
           'wishlist-list',
           'feedback-list',
           'feedback-delete',
           
           'about-list',
           'about-create',
           'about-edit',
           'about-delete',
           
           'riders_admin_approved-update',
           'riders_daily_admin_approved-update',
           'riders_monthly_admin_approved-update',
           'riders_yearly_admin_approved-update',

           'restaurant_category_assign-list',
           'restaurant_category_assign-store',
           'restaurant_category_assign-update',
           'restaurant_category_assign-delete',
           
           'restaurant_category_assign_edit-edit',
           'restaurant_category_assign_edit-update',
           'restaurant_category_assign_sort-update',

        ];


        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}
