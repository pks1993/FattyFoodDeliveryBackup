<?php

namespace App\Http\Controllers\Admin\Ads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Ads\DownAds;

class DownAdsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $down_ads=DownAds::orderBy('created_at','DESC')->paginate(10);
        return view('admin.Ads.down.index',compact('down_ads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.Ads.down.create');
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

        $down_ads=new DownAds();
        if($image){
            $image_name=$name.'.'.$request->file('image')->getClientOriginalExtension();
            $down_ads->image=$image_name;
            Storage::disk('Down_Ads')->put($image_name, File::get($image));
        }
        $down_ads->save();
        $request->session()->flash('alert-success', 'successfully create down ads!');
        return redirect('fatty/main/admin/ads/down_ads');
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
        $down_ads=DownAds::where('down_ads_id',$id)->first();
        return view('admin.Ads.down.edit',compact('down_ads'));
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
            // 'image' => 'required',

        ]);
        $image=$request->file('image');
        $name=time();

        $down_ads=DownAds::where('down_ads_id',$id)->first();
        // if($image){
        //     Storage::disk('Down_Ads')->delete($down_ads->image);
        //     $image_name=$name.'.'.$request->file('image')->getClientOriginalExtension();
            // $down_ads->image=$image_name;
        //     Storage::disk('Down_Ads')->put($image_name, File::get($image));
        // }
        $down_ads->update();
        $request->session()->flash('alert-success', 'successfully create up ads!');
        return redirect('fatty/main/admin/ads/down_ads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $rrequest,$id)
    {
        $down_ads=DownAds::where('down_ads_id',$id)->FirstOrFail();
        Storage::disk('Down_Ads')->delete($down_ads->image);
        $down_ads->delete();

        $request->session()->flash('alert-danger', 'successfully delete up ads!');
        return redirect('fatty/main/admin/ads/down_ads');
    }
}
