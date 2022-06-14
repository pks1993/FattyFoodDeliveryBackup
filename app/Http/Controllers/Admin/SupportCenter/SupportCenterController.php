<?php

namespace App\Http\Controllers\Admin\SupportCenter;

use App\Models\SupportCenter\SupportCenter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SupportCenterController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:supportCenter-list|supportCenter-create|supportCenter-edit|supportCenter-delete', ['only' => ['index','store']]);
         $this->middleware('permission:supportCenter-create', ['only' => ['create','store']]);
         $this->middleware('permission:supportCenter-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:supportCenter-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $support_center=SupportCenter::orderBy('support_center_id','DESC')->get();
        return view('admin.support_center.index',compact('support_center'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.support_center.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'phone_mm' => 'required',
            'phone_en' => 'required',
            'phone_ch' => 'required',
            'support_center_type'=>'required',
        ]);
        SupportCenter::create($request->all());
        $request->session()->flash('alert-success', 'successfully support center create');
        return redirect('fatty/main/admin/support_center');
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
        $support_center=SupportCenter::findOrFail($id);
        return view('admin.support_center.edit',compact('support_center'));
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
        $this->validate($request, [
            'type' => 'required',
            'phone_mm' => 'required',
            'phone_en' => 'required',
            'phone_ch' => 'required',
            'support_center_type'=>'required',
        ]);
        SupportCenter::find($id)->update($request->all());
        $request->session()->flash('alert-success', 'successfully support center update');
        return redirect('fatty/main/admin/support_center');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        SupportCenter::destroy($id);
        $request->session()->flash('alert-danger', 'successfully support center delete!');
        return redirect('fatty/main/admin/support_center');
    }
}
