<?php

namespace App\Http\Controllers\Admin\NotificationTemplate;

use App\Models\Notification\NotificationTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class NotificationTemplateController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:notification-list', ['only' => ['index']]);
         $this->middleware('permission:notification-create', ['only' => ['create','store']]);
         $this->middleware('permission:notification-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:notification-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notification_templates=NotificationTemplate::orderBy('notification_template_id','DESC')->get();
        return view('admin.notification_template.index',compact('notification_templates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $notification_templates=NotificationTemplate::all();
        return view('admin.notification_template.create',compact('notification_templates'));
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
            'notification_title' => 'required',
            'notification_body' => 'required',
        ]);
        $title = $request['notification_title'];
        $messages = $request['notification_body'];
        $image = $request['notification_image'];
        $imagename=time();

        $notification=new NotificationTemplate();
        $notification->notification_title=$title;
        $notification->notification_body=$messages;
        if(!empty($image)){
            $img_name=$imagename.'.'.$request->file('notification_image')->getClientOriginalExtension();
            $notification->notification_image=$img_name;
            Storage::disk('Notification')->put($img_name, File::get($request['notification_image']));
        }

        $notification->save();


        $message = strip_tags($messages);
        $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
        $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';

        $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');
        $notification = array('title' => $title, 'body' => $message);

        // if($pushType == "group"){
        //     $rider=Rider::orderBy('created_at','DESC')->get();
        //     $fcm_token=array();
        //     foreach($rider as rid){
        //         array_push($fcm_token, $rid->fcm_token);
        //     }
        //     $field=array('register_id'=>$fcm_token,'notification'=>$notification);
        // }else{
        //     $field=array('to'=>'/topics/customer_all','notification'=>$notification);
        // }
        $field=array('to'=>'/topics/customer_all','notification'=>$notification,'data'=>['title' => $title, 'body' => $message]);

        $playLoad = json_encode($field);
        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
        curl_setopt($curl_session, CURLOPT_POST, true);
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);

        $result = curl_exec($curl_session);
        curl_close($curl_session);

        $request->session()->flash('alert-success', 'successfully send notification for customers!');
        return redirect('fatty/main/admin/notification_templates');
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
        $notification_templates=NotificationTemplate::findOrFail($id);
        return view('admin.notification_template.edit',compact('notification_templates'));
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
            'notification_title' => 'required',
            'notification_body' => 'required'
        ]);
        $title = $request['notification_title'];
        $messages = $request['notification_body'];
        $image = $request['notification_image'];
        $imagename=time();

        $notification=NotificationTemplate::where('notification_template_id',$id)->first();
        $notification->notification_title=$title;
        $notification->notification_body=$messages;
        if(!empty($image)){
            $img_name=$imagename.'.'.$request->file('notification_image')->getClientOriginalExtension();
            $notification->notification_image=$img_name;
            Storage::disk('Notification')->put($img_name, File::get($request['notification_image']));
        }

        $notification->update();

        $message = strip_tags($messages);


        $message = strip_tags($messages);
        $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
        $server_key = 'AAAAHUFURUE:APA91bFYFV1jpHJmov1g3wwbAHghzW0vxRkMbYE6DVIuvPJsMV0zRz2guLdhvi4UPMKvAxSYjpuoWH9y_XVmn44ngtFvCYe1GIVsTY11CldVLsqVRp4cDLq9GXmuW63dvv8Fp0CuCt1s';

        $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');
        $notification = array('title' => $title, 'body' => $message);

        // if($pushType == "group"){
        //     $rider=Rider::orderBy('created_at','DESC')->get();
        //     $fcm_token=array();
        //     foreach($rider as rid){
        //         array_push($fcm_token, $rid->fcm_token);
        //     }
        //     $field=array('register_id'=>$fcm_token,'notification'=>$notification);
        // }else{
        //     $field=array('to'=>'/topics/customer_all','notification'=>$notification);
        // }
        $field=array('to'=>'/topics/customer_all','notification'=>$notification);

        $playLoad = json_encode($field);
        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
        curl_setopt($curl_session, CURLOPT_POST, true);
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);

        $result = curl_exec($curl_session);
        curl_close($curl_session);

        $request->session()->flash('alert-success', 'successfully update send notification for customers!');
        return redirect('fatty/main/admin/notification_templates');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
       $notification=NotificationTemplate::where('notification_template_id',$id)->FirstOrFail();
        Storage::disk('Notification')->delete($notification->notification_image);
        $notification->delete();
        
        $request->session()->flash('alert-danger', 'successfully delete notification!');
        return redirect('fatty/main/admin/notification_templates');
    }
}
