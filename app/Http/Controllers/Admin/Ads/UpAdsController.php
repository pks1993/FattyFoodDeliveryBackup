<?php

namespace App\Http\Controllers\Admin\Ads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Ads\UpAds;
use App\Models\Restaurant\Restaurant;

class UpAdsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $up_ads=UpAds::orderBy('sort_id')->get();
        return view('admin.Ads.up.index',compact('up_ads'));
    }

    public function sort_update(Request $request)
    {
        $posts = UpAds::all();

        foreach ($posts as $post) {
            foreach ($request->order as $order) {
                if($order['id'] == $post->up_ads_id) {
                    $post->update(['sort_id'=>$order['position']]);
                }
            }
        }
        $request->session()->flash('alert-success', 'successfully change sort number!');
        return response()->json(['status'=>'success']);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $restaurants=Restaurant::doesnthave('up_ads')->get();
        return view('admin.Ads.up.create',compact('restaurants'));
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
            'restaurant_id' => 'required',
            'image' => 'required',

        ]);
        $restaurant_id=$request['restaurant_id'];
        $image=$request->file('image');
        $name=time();
        $count=UpAds::count();

        $up_ads=new UpAds();
        if($image){
            $image_name=$name.'.'.$request->file('image')->getClientOriginalExtension();
            $up_ads->image=$image_name;
            Storage::disk('Up_Ads')->put($image_name, File::get($image));
        }
        $up_ads->restaurant_id=$restaurant_id;
        $up_ads->sort_id=$count+1;
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
        $up_ads=UpAds::find($id);
        $restaurants=Restaurant::doesnthave('up_ads')->get();
        return view('admin.Ads.up.edit',compact('restaurants','up_ads'));
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
        $restaurant_id=$request['restaurant_id'];
        $image=$request->file('image');
        $name=time();

        $up_ads=UpAds::where('up_ads_id',$id)->FirstOrFail();
        if($image){
            Storage::disk('Up_Ads')->delete($up_ads->image);
            $image_name=$name.'.'.$request->file('image')->getClientOriginalExtension();
            $up_ads->image=$image_name;
            Storage::disk('Up_Ads')->put($image_name, File::get($image));
        }
        $up_ads->restaurant_id=$restaurant_id;
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
    public function destroy(Request $request,$id)
    {
        $up_ads=UpAds::where('up_ads_id',$id)->first();
        if($up_ads){
            $sortId=UpAds::where('sort_id','>',$up_ads->sort_id)->get();
            foreach($sortId as $value){
                $sort_id=$value->sort_id-1;
                $up_ads_id=$value->up_ads_id;

                UpAds::where('up_ads_id',$up_ads_id)->update(['sort_id'=>$sort_id]);
            }

            Storage::disk('Up_Ads')->delete($up_ads->image);
            $up_ads->delete();

            $request->session()->flash('alert-danger', 'successfully delete up ads!');
            return redirect('fatty/main/admin/ads/up_ads');
        }else{
            $request->session()->flash('alert-warning', 'up ads id is not define!');
            return redirect('fatty/main/admin/ads/up_ads');
        }

    }
}
