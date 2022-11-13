<?php

namespace App\Http\Controllers\Admin\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Customer\Customer;
use App\Models\Zone\Zone;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Order\CustomerOrder;
class DashboardController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:dashboard-list', ['only' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->zone_id==0 && Auth::user()->zone == null){
            $today_total_order=CustomerOrder::whereRaw('Date(created_at) = CURDATE()')->count();
            $all_orders=CustomerOrder::count();
            $all_food_orders=CustomerOrder::where('order_type','food')->count();
            $all_parcel_orders=CustomerOrder::where('order_type','parcel')->count();
    
            $total_all_admin=User::count();
            $total_main_admin=User::where('is_main_admin','1')->count();
            $total_branch_admin=User::where('is_main_admin','0')->count();
            $total_customer=Customer::count();
            $total_zone=Zone::count();
            
        }else{
            if(Auth::user()->zone){
                $today_total_order=CustomerOrder::whereRaw('Date(created_at) = CURDATE()')->where('state_id',Auth::user()->zone->state_id)->where('city_id',Auth::user()->zone->city_id)->count();
                $all_orders=CustomerOrder::where('state_id',Auth::user()->zone->state_id)->where('city_id',Auth::user()->zone->city_id)->count();
                $all_food_orders=CustomerOrder::where('order_type','food')->where('state_id',Auth::user()->zone->state_id)->where('city_id',Auth::user()->zone->city_id)->count();
                $all_parcel_orders=CustomerOrder::where('order_type','parcel')->where('state_id',Auth::user()->zone->state_id)->where('city_id',Auth::user()->zone->city_id)->count();
        
                $total_all_admin=User::where('zone_id',Auth::user()->zone_id)->count();
                $total_main_admin=User::where('zone_id',Auth::user()->zone_id)->where('is_main_admin','1')->count();
                $total_branch_admin=User::where('zone_id',Auth::user()->zone_id)->where('is_main_admin','0')->count();
                $total_customer=Customer::where('state_id',Auth::user()->zone->state_id)->where('city_id',Auth::user()->zone->city_id)->count();
                $total_zone=Zone::where('zone_id',Auth::user()->zone_id)->count();
            }

        }
        return view('admin.dashboard.dashboard',compact('total_customer','total_all_admin','total_branch_admin','total_main_admin','total_zone','today_total_order','all_orders','all_food_orders','all_parcel_orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
