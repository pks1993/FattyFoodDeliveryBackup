<?php

namespace App\Http\Controllers\Admin\Tutorial;

use App\Models\Tutorial\Tutorial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TutorialController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:tutorials-list|tutorials-create|tutorials-edit|tutorials-delete', ['only' => ['index','store']]);
         $this->middleware('permission:tutorials-create', ['only' => ['create','store']]);
         $this->middleware('permission:tutorials-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:tutorials-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tutorials=Tutorial::orderBy('tutorial_id','DESC')->get();
        return view('admin.tutorial.index',compact('tutorials'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.tutorial.create');
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
            'video' => 'required',
            'photo' => 'required'
        ]);

        $name=$request['name'];
        $video=$request->file('video');
        $cover_photo=$request->file('photo');
        $video_name=time();

        $tutorials=new Tutorial();
        $tutorials->name=$name;

        $photo_name=$video_name.'.'.$request->file('photo')->getClientOriginalExtension();
        $tutorials->photo=$photo_name;
        Storage::disk('Tutorial_Cover')->put($photo_name, File::get($cover_photo));


        $video_name=$video_name.'.'.$request->file('video')->getClientOriginalExtension();
        $tutorials->video=$video_name;
        Storage::disk('Tutorial')->put($video_name, File::get($video));

        $tutorials->save();

        $request->session()->flash('alert-success', 'successfully upload video and image');
        return redirect('fatty/main/admin/tutorials');

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
        $tutorials=Tutorial::findOrFail($id);
        return view('admin.tutorial.edit',compact('tutorials'));
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
        ]);

        $video=$request->file('video');
        $cover_photo=$request->file('photo');
        $video_name=time();

        $tutorials=Tutorial::where('tutorial_id','=',$id)->FirstOrFail();
        if(!empty($video)){
            Storage::disk('Tutorial')->delete($tutorials->video);
            $video_name=$video_name.'.'.$request->file('video')->getClientOriginalExtension();
            $tutorials->video=$video_name;
            Storage::disk('Tutorial')->put($video_name, File::get($video));
        }
        if($cover_photo){
            Storage::disk('Tutorial_Cover')->delete($tutorials->photo);
            $photo_name=$video_name.'.'.$request->file('photo')->getClientOriginalExtension();
            $tutorials->photo=$photo_name;
            Storage::disk('Tutorial_Cover')->put($photo_name, File::get($cover_photo));
        }
        $tutorials->name=$request['name'];
        $tutorials->update();
        $request->session()->flash('alert-success', 'successfully update video and image!');
        return redirect('fatty/main/admin/tutorials');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $tutorials=Tutorial::where('tutorial_id','=',$id)->FirstOrFail();
        Storage::disk('Tutorial')->delete($tutorials->video);
        Storage::disk('Tutorial_Cover')->delete($tutorials->photo);
        $tutorials->delete();

        $request->session()->flash('alert-danger', 'successfully delete video and image');
        return redirect('fatty/main/admin/tutorials');
    }
}
