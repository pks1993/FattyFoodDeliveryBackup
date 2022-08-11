<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Privacy;
use App\Models\Setting\TermsConditions;
use App\Models\Setting\VersionUpdate;
use App\Models\Order\PaymentMethod;
use App\Models\Order\PaymentMethodClose;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $privacy=Privacy::orderBy('privacy_id','DESC')->get();
        return view('admin.setting.privacy.index',compact('privacy'));
    }

    public function term()
    {
        $data=TermsConditions::where('terms_conditions_id','1')->first();
        return view('admin.setting.term.index',compact('data'));
    }

    public function version_list()
    {
        $version_data=VersionUpdate::get();
        return view('admin.setting.version.index',compact('version_data'));
    }

    public function version_update(Request $request,$version_update_id)
    {
        VersionUpdate::where('version_update_id',$version_update_id)->update([
            "link"=>$request['link'],
            "current_version"=>$request['current_version'],
            "is_force_update"=>$request['is_force_update'],
            "is_available"=>$request['is_available'],
        ]);
        $request->session()->flash('alert-success', 'successfully update customer!');
        return redirect()->back();
    }

    public function force_update(Request $request,$version_update_id)
    {
        $check=VersionUpdate::where('version_update_id',$version_update_id)->first();
        if($check->is_force_update==1){
            $check->is_force_update=0;
            $check->update();
            $request->session()->flash('alert-success', 'successfully close force_update!');
        }else{
            $check->is_force_update=1;
            $check->update();
            $request->session()->flash('alert-success', 'successfully open force_update!');
        }
        return redirect()->back();
    }
    public function available_update(Request $request,$version_update_id)
    {
        $check=VersionUpdate::where('version_update_id',$version_update_id)->first();
        if($check->is_available==1){
            $check->is_available=0;
            $check->update();
            $request->session()->flash('alert-success', 'successfully close available!');
        }else{
            $check->is_available=1;
            $check->update();
            $request->session()->flash('alert-success', 'successfully open available!');
        }
        return redirect()->back();
    }
    

    public function kpay_onoff_list()
    {
        $payment_data=PaymentMethod::get();
        $close_payment_data=PaymentMethodClose::get();
        return view('admin.setting.kpay_onoff.index',compact('payment_data','close_payment_data'));
    }

    public function kpay_onoff_update_android(Request $request,$payment_method_id)
    {
        $check=PaymentMethod::where('payment_method_id',$payment_method_id)->first();
        if($check->on_off_status==1){
            $check->on_off_status=0;
            $check->update();
            $request->session()->flash('alert-success', 'successfully close payment!');
        }else{
            $check->on_off_status=1;
            $check->update();
            $request->session()->flash('alert-success', 'successfully open payment!');
        }
        return redirect()->back();
    }

    public function kpay_update(Request $request,$payment_method_close_id)
    {
        PaymentMethodClose::where('payment_method_close_id',$payment_method_close_id)->update([
            "version"=>$request['version'],
        ]);
        $request->session()->flash('alert-success', 'successfully pamyment method!');
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
