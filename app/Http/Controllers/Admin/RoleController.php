<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\User;
use Auth;
use App\Models\Zone\Zone;




class RoleController extends Controller
{   

    function __construct()
    {
         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
         $this->middleware('permission:role-create', ['only' => ['create','store']]);
         $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admins=User::all();
        if(Auth::user()->is_main_admin=="1"){
            $main_roles=Role::orderBy('zone_id')->get();
        }else{
            $main_roles=Role::where('zone_id',Auth::user()->zone_id)->orderBy('created_at','DESC')->get();
        }
        $zones=Zone::all();
        return view('admin.role.index', compact('main_roles','admins','zones'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission_admin = Permission::whereBetween('id',[0,24])->get();
        $permission_tax=Permission::whereBetween('id',[25,27])->get();
        $permission_other=Permission::whereBetween('id',[28,67])->get();
        $order=Permission::whereBetween('id',[68,70])->get();
        $invoice=Permission::whereBetween('id',[73,74])->get();
        $feedback=Permission::whereBetween('id',[71,72])->get();
        $dashboard=Permission::where('id',75)->get();
        $wishlist=Permission::where('id',76)->get();
        if(Auth::user()->is_main_admin=="1"){
            $zones=Zone::all();
        }else{
            $zones=Zone::where('zone_id',Auth::user()->zone_id)->get();
        }
        return view('admin.role.create', compact('permission_admin','permission_tax','permission_other','order','invoice','feedback','dashboard','wishlist','zones'));

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
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
            'zone_id' => 'required',
        ]);
        $user=User::find($request['user_id']);
        $zone=Zone::find($request['zone_id']);
        if($zone){
            $zone_name=$zone->zone_name;
        }else{
            $zone_name="Super Admin";
        }

        $role = Role::create([
            'name' => $request->input('name'),
            'zone_id' => $request->input('zone_id'),
            'user_id' => Auth::user()->user_id,
            'zone_name' => $zone_name,
        ]);
        $role->syncPermissions($request->input('permission'));

        $request->session()->flash('alert-success', 'successfully create role!');
        return redirect("fatty/main/admin/roles");
                        

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
        $roles = Role::find($id);
        // $permission = Permission::get();
        $permission_admin = Permission::whereBetween('id',[0,24])->get();
        $permission_tax=Permission::whereBetween('id',[25,27])->get();
        $permission_other=Permission::whereBetween('id',[28,67])->get();
        $order=Permission::whereBetween('id',[68,70])->get();
        $invoice=Permission::whereBetween('id',[73,74])->get();
        $feedback=Permission::whereBetween('id',[71,72])->get();
        $dashboard=Permission::where('id',78)->get();
        $wishlist=Permission::where('id',76)->get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        // $admin=User::where('user_id',$roles->user_id)->first();
        // $admins=User::where('user_id','!=',$roles->user_id)->get();
        // $zones=Zone::all();
        if(Auth::user()->is_main_admin=="1"){
            $zones=Zone::all();
        }else{
            $zones=Zone::where('zone_id',Auth::user()->zone_id)->get();
        }
        $zone_one=Zone::where('zone_id','!=',$roles->zone_id)->get();
        return view('admin.role.edit',compact('permission_admin','permission_tax','permission_other','order','invoice','feedback','dashboard','wishlist','roles','rolePermissions','zones','zone_one'));
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
            'name' => 'required',
            'permission' => 'required',
            'zone_id'=>'required',
        ]);

        $zone=Zone::find($request['zone_id']);
        if($zone){
            $zone_name=$zone->zone_name;
        }else{
            $zone_name="Super Admin";
        }

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->zone_id=$request->input('zone_id');
        // $role->zone_name=$zone_name;
        $role->user_id=Auth::user()->user_id;
        $role->update();

        $role->syncPermissions($request->input('permission'));
        $request->session()->flash('alert-success', 'successfully update role!');
        return redirect("fatty/main/admin/roles");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        DB::table("roles")->where('id',$id)->delete();
        $request->session()->flash('alert-danger', 'successfully delete role!');
        return redirect("fatty/main/admin/roles");
    }
}
