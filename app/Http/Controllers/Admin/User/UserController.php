<?php

namespace App\Http\Controllers\Admin\User;

use App\User;
use App\Models\Zone\Zone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Hash;
use Auth;   


class UserController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:user-list', ['only' => ['index']]);
         $this->middleware('permission:user-create', ['only' => ['create','store']]);
         $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles=Role::all();
        if(Auth::user()->is_main_admin=="1"){
            $admins=User::orderBy('zone_id','ASC')->paginate('10');
        }else{
            $admins=User::where('zone_id',Auth::user()->zone_id)->orderBy('user_id','DESC')->paginate('10');
        }
        return view('admin.user.index',compact('admins','roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->is_main_admin=="1"){
            $roles=Role::orderBy('zone_id')->get();
            $zones=Zone::all();
        }else{
            $roles=Role::where('zone_id',Auth::user()->zone_id)->orderBy('zone_id')->get();
            $zones=Zone::where('zone_id',Auth::user()->zone_id)->get();
        }
        return view('admin.user.create',compact('roles','zones'));
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
            'name' => 'required',
            'password' => 'required|same:password_confirmation',
            'roles' => 'required'
        ]);
        $password = Hash::make($request['password']);
        $photoname=time();

        $user = new User();

        if(!empty($request['image'])){
            $img_name=$photoname.'.'.$request->file('image')->getClientOriginalExtension();
            $user->image=$img_name;
            Storage::disk('UsersImages')->put($img_name, File::get($request['image']));
        }
        $user->name=$request['name'];
        $user->is_main_admin=$request['is_main_admin'];
        if($request['is_main_admin']=="1"){
            $user->zone_id="0";
        }else{
            $user->zone_id=$request['zone_id'];
        }
        $user->phone=$request['phone'];
        $user->email=$request['email'];
        $user->password=$password;
        // $user->role_id=$request['roles'];
        $user->save();
        $user->assignRole($request['roles']);

        $request->session()->flash('alert-success', 'successfully create admin!');
        return redirect('fatty/main/admin/user');
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
        $user = User::find($id);
        $role_model=DB::table('model_has_roles')->where('model_id',$user->user_id)->first();
        $zones=Zone::where('zone_id','!=',$user->zone_id)->get();

        if(Auth::user()->is_main_admin=="1"){
            $role_one=Role::where('id',$role_model->role_id)->first();
            $roles=Role::where('id','!=',$role_model->role_id)->orderBy('zone_id','ASC')->get();
        }else{
            $role_one=Role::where('id',$role_model->role_id)->first();
            $roles=Role::where('id','!=',$role_model->role_id)->where('zone_id',Auth::user()->zone_id)->orderBy('zone_id','ASC')->get();
        }
        return view('admin.user.edit',compact('user','roles','role_one','zones'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $this->validate($request, [
            'name' => 'required',
            'password' => 'required|same:password_confirmation',
            'roles' => 'required'
        ]);
        $photoname=time();

        $user = User::find($id);

        if(!empty($request['password'])){
            $password = Hash::make($request['password']);
            $user->password=$password;
        }

        if(!empty($request['image'])){
            Storage::disk('UsersImages')->delete($user->image);
            $img_name=$photoname.'.'.$request->file('image')->getClientOriginalExtension();
            $user->image=$img_name;
            Storage::disk('UsersImages')->put($img_name, File::get($request['image']));
        }
        $user->name=$request['name'];
        $user->is_main_admin=$request['is_main_admin'];
        if($request['is_main_admin']=="1"){
            $user->zone_id="0";
        }else{
            $user->zone_id=$request['zone_id'];
        }
        $user->phone=$request['phone'];
        $user->email=$request['email'];
        // $user->role_id=$request['roles'];
        $user->update();

        DB::table('model_has_roles')->where('model_id',$id)->delete();
        $user->assignRole($request['roles']);

        $request->session()->flash('alert-success', 'successfully update admin!');
        return redirect('fatty/main/admin/user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $users=User::where('user_id','=',$id)->FirstOrFail();
        Storage::disk('UsersImages')->delete($users->image);
        $users->delete();
        $request->session()->flash('alert-danger', 'successfully delete admin!');
        return redirect('fatty/main/admin/user');
    }
    

}
