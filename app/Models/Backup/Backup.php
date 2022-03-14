<?php

namespace App\Models\Backup;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer\Customer;

class Backup extends Model
{
    // Fetch all users
    public static function getCustomers(){

    // $records = DB::table('customers')->select('customer_id','name','phone','image')->orderBy('customer_id', 'asc')->get()->toArray();
    $records=Customer::orderBy('created_at','DESC')->get()->toArray();
    return $records;
   }
}
