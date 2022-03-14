<?php

namespace App\Http\Controllers\Admin\RiderGroup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiderGroup\RiderGroup;
use App\Models\Zone\Zone;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;
use App\User;

class RiderGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->is_main_admin=="1"){
            $rider_group=RiderGroup::orderBy('rider_group_id','DESC')->get();
        }else{
            $rider_group=RiderGroup::where('zone_id',Auth::user()->zone_id)->orderBy('created_at','DESC')->get();
        }
        return view('admin.riderGroup.index',compact('rider_group'));
    }

     /**
     *for branch list all 
    */
    public function user_list($id)
    {
        $user=User::where('zone_id',$id)->get();
            return response()->json($user);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->is_main_admin=="1"){
            $zones=Zone::all();
        }else{
            $zones=Zone::where('zone_id',Auth::user()->zone_id)->get();
        }
        return view('admin.riderGroup.create',compact('zones'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'rider_group_name' => 'required',
            'zone_id' => 'required',
            'user_id' => 'required'
        ]);
        RiderGroup::create($request->all());

        $request->session()->flash('alert-success', 'successfully store group!');
        return redirect('fatty/main/admin/rider_group');
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
        $rider_group=RiderGroup::findOrFail($id);
        $users=User::where('zone_id',$rider_group->zone_id)->get();

        // dd($users);
        if(Auth::user()->is_main_admin=="1"){
            $zones=Zone::where('zone_id','!=',$rider_group->zone_id)->get();
        }else{
            $zones=Zone::where('zone_id',Auth::user()->zone_id)->get();
        }
        return view('admin.riderGroup.edit',compact('rider_group','zones','users'));
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
        $this->validate($request,[
            'rider_group_name' => 'required',
            'zone_id' => 'required'
        ]);

        RiderGroup::find($id)->update($request->all());
        $request->session()->flash('alert-success', 'successfully update group!');
        return redirect('fatty/main/admin/rider_group');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        RiderGroup::destroy($id);
        $request->session()->flash('alert-success', 'successfully update group!');
        return redirect('fatty/main/admin/rider_group');
    }
}
