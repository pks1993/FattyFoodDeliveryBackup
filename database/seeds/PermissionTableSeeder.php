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

           'currency-list',
           'currency-create',
           'currency-edit',
           'currency-delete',

           'user-list',
           'user-create',
           'user-edit',
           'user-delete',

           'customers-list',
           'customers-create',
           'customers-edit',
           'customers-delete',

           'notification-list',
           'notification-create',
           'notification-edit',
           'notification-delete',
           
           'taxes-list',
           'taxes-edit',
           'taxes-delete',



           'brand-list',
           'brand-create',
           'brand-edit',
           'brand-delete',

           'carriers-list',
           'carriers-create',
           'carriers-edit',
           'carriers-delete',

           'category-list',
           'category-create',
           'category-edit',
           'category-delete',

           'deliveryfee-list',
           'deliveryfee-create',
           'deliveryfee-edit',
           'deliveryfee-delete',


           'order_status-list',
           'order_status-create',
           'order_status-edit',
           'order_status-delete',

           'products-list',
           'products-create',
           'products-edit',
           'products-delete',

           'promotions-list',
           'promotions-create',
           'promotions-edit',
           'promotions-delete',

           'supportCenter-list',
           'supportCenter-create',
           'supportCenter-edit',
           'supportCenter-delete',


           'timeslot-list',
           'timeslot-create',
           'timeslot-edit',
           'timeslot-delete',

           'tutorials-list',
           'tutorials-create',
           'tutorials-edit',
           'tutorials-delete',

           'noti-bar-list',
           'noti-bar-edit',
           'noti-bar-delete',

           'order-list',
           'order-view',
           'order-delete',

           'feedback-list',
           'feedback-delete',

           'invoices-list',
           'invoices-view',

           'dashboard-list',

           'wishlist-list'
        ];


        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}
