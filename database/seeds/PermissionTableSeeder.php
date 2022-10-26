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

           'about-list',
           'about-create',
           'about-edit',
           'about-delete',

           'down_ads-list',
           'down_ads-create',
           'down_ads-edit',
           'down_ads-delete',
           
           'up_ads-list',
           'up_ads-create',
           'up_ads-edit',
           'up_ads-delete',

           'dashboard-list',

           'customers-list',
           'customers-create',
           'customers-edit',
           'customers-delete',

           'daily_customers-list',
           'monthly_customers-list',
           'yearly_customers-list',
           
           'daily_ordered_customers-list',
           'monthly_ordered_customers-list',
           'yearly_ordered_customers-list',
           
           'daily_active_customers-list',
           'monthly_active_customers-list',
           'yearly_active_customers-list',

           'customerchart-list',
           'order_customer_chart-list',
           'active_customer_chart-list',

           'order_assign-list',
           'rider_order_assign-list',
           'rider_order_assign-noti',
           'rider_pending_assign-list',
           'rider_pending_assign-noti',

           'food_orders-detail',

           'pending-list',

           'daily_food_orders-list',
           'monthly_food_orders-list',
           'yearly_food_orders-list',

           'food_order_delivery_fee-list',
           'food_orders_chart-list',

           'daily_parcel_orders-list',
           'monthly_parcel_orders-list',
           'yearly_parcel_orders-list',
           'parcel_orders_chart-list',

           'rider_order_report-list',
           'rider_parcel_order_report-list',
           'riders-list',
           'riders-create',
           'riders-edit',
           'riders-delete',
           'riders-view',
           'riders_chart-list',
           'riders_activenow-update',
           'riders_location-list',

           'riders_admin_approved-update',
           'riders_daily_admin_approved-update',
           'riders_monthly_admin_approved-update',
           'riders_yearly_admin_approved-update',
           'riders_ban-update',

           'riders_level-list',
           'riders_level-store',
           'riders_level-update',
           'riders_level-delete',

           'user-list',
           'user-create',
           'user-edit',
           'user-delete',

           'notification-list',
           'notification-create',
           'notification-edit',
           'notification-delete',

           'tutorials-list',
           'tutorials-create',
           'tutorials-edit',
           'tutorials-delete',

           'feedback-list',
           'feedback-delete',

           'wishlist-list'
        ];


        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}
