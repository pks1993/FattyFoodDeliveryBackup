<?php

namespace App\Http\Controllers\Admin\Ads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Ads\UpAds;

class UpAdsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $up_ads=UpAds::orderBy('created_at','DESC')->paginate(10);
        return view('admin.Ads.up.index',compact('up_ads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.Ads.up.create');
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
            'image' => 'required',

        ]);
        $image=$request->file('image');
        $name=time();

        $up_ads=new UpAds();
        if($image){
            $image_name=$name.'.'.$request->file('image')->getClientOriginalExtension();
            $up_ads->image=$image_name;
            Storage::disk('Up_Ads')->put($image_name, File::get($image));
        }
        $up_ads->save();
        $request->session()->flash('alert-success', 'successfully create up ads!');
        return redirect('fatty/main/admin/ads/up_ads');

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
        $this->validate($request, [
            'image' => 'required',

        ]);
        $image=$request->file('image');
        $name=time();

        $up_ads=UpAds::where('up_ads_id',$id)->FirstOrFail();
        if($image){
            Storage::disk('Up_Ads')->delete($up_ads->image);
            $image_name=$name.'.'.$request->file('image')->getClientOriginalExtension();
            $up_ads->image=$image_name;
            Storage::disk('Up_Ads')->put($image_name, File::get($image));
        }
        $up_ads->update();
        $request->session()->flash('alert-success', 'successfully create up ads!');
        return redirect('fatty/main/admin/ads/up_ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $up_ads=UpAds::where('up_ads_id',$id)->FirstOrFail();
        Storage::disk('Up_Ads')->delete($up_ads->image);
        $up_ads->delete();

        $request->session()->flash('alert-danger', 'successfully delete up ads!');
        return redirect('fatty/main/admin/ads/up_ads');
    }
}
