<?php

namespace App\Http\Controllers\Admin\Backup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Facades\FromView;

use App\Exports\DataExport;
use App\Exports\ParcelOrderExport;
use App\Exports\AllFoodOrderReport;
use App\Models\Customer\Customer;

class BackupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->input('exportexcel') != null ){
            return Excel::download(new DataExport, 'users.xlsx');
        }

        if ($request->input('exportcsv') != null ){
            return Excel::download(new DataExport, 'backup.csv');
        }

        return redirect()->back();
    }

    public function daily_parcel_orders(Request $request)
    {
        // $current_date=date('Ymd');
        // if ($request->input('all_parcel_exportexcel') != null ){
        //     return Excel::download(new ParcelOrderExport, 'all_parcel_order_'.$current_date.'.xlsx');
        // }

        // if ($request->input('daily_parcel_exportexcel') != null ){
        //     return Excel::download(new ParcelOrderExport, 'daily_parcel_'.$current_date.'.xlsx');
        // }

        // $from_date=$request->from_date;
        // $to_date = $request->to_date;
        // dd($from_date);
        if($request['from_date']){
            $from_date=date('Y-m-d 00:00:00',strtotime($request['from_date']));
        }else{
            $from_date=date('Y-m-d 00:00:00');
        }
        if($request['to_date']){
            $to_date=date('Y-m-d 23:59:59',strtotime($request['to_date']));
        }else{
            $to_date=date('Y-m-d 23:59:59');
        }
        $current_date=date('Ymd');
        if ($request->input('all_parcel_exportexcel') != null ){
            return Excel::download(new ParcelOrderExport($from_date,$to_date), 'all_parcel_order_'.$current_date.'.xlsx');
        }

        return redirect()->back();
    }

    public function all_food_orders(Request $request)
    {
        if($request['from_date']){
            $from_date=date('Y-m-d 00:00:00',strtotime($request['from_date']));
        }else{
            $from_date=date('Y-m-d 00:00:00');
        }
        if($request['to_date']){
            $to_date=date('Y-m-d 23:59:59',strtotime($request['to_date']));
        }else{
            $to_date=date('Y-m-d 23:59:59');
        }
        $current_date=date('Ymd');
        if ($request->input('all_food_order_exportexcel') != null ){
            return Excel::download(new AllFoodOrderReport($from_date,$to_date), 'all_food_order_'.$current_date.'.xlsx');
        }
        return redirect()->back();
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
