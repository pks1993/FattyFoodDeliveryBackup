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
    // function __construct()
    // {
    //     $this->middleware('permission:up_ads-list', ['only' => ['index']]);
    //     $this->middleware('permission:up_ads-create', ['only' => ['create','store']]);
    //     $this->middleware('permission:up_ads-edit', ['only' => ['edit','update']]);
    //     $this->middleware('permission:up_ads-delete', ['only' => ['destroy']]);
    // }
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
        // if($image){
        //     $image_name=$name.'.'.$request->file('image')->getClientOriginalExtension();
        //     $up_ads->image=$image_name;
        //     Storage::disk('Up_Ads')->put($image_name, File::get($image));
        // }
        if($image){
            foreach($image as $file){
                $name=$name+1;
                $image_name=$name.'.'.$file->getClientOriginalExtension();
                Storage::disk('Up_Ads')->put($image_name, File::get($file));
                $imgName[]=$image_name;
            }
            if(count($imgName)==3){
                $up_ads->image_mm=$imgName[0];
                $up_ads->image_en=$imgName[1];
                $up_ads->image_ch=$imgName[2];
            }elseif(count($imgName)==2){
                $up_ads->image_mm=$imgName[0];
                $up_ads->image_en=$imgName[1];
            }elseif(count($imgName)==1){
                $up_ads->image_mm=$imgName[0];
            }
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
        // $restaurant_id=$request['restaurant_id'];
        // $image=$request->file('image');
        // $name=time();

        // $up_ads=UpAds::where('up_ads_id',$id)->FirstOrFail();
        // if($image){
        //     Storage::disk('Up_Ads')->delete($up_ads->image);
        //     $image_name=$name.'.'.$request->file('image')->getClientOriginalExtension();
        //     $up_ads->image=$image_name;
        //     Storage::disk('Up_Ads')->put($image_name, File::get($image));
        // }
        // $up_ads->restaurant_id=$restaurant_id;
        // $up_ads->update();

        // $request->session()->flash('alert-success', 'successfully create up ads!');
        // return redirect('fatty/main/admin/ads/up_ads');

        $restaurant_id=$request['restaurant_id'];
        $image_mm=$request->file('image_mm');
        $image_en=$request->file('image_en');
        $image_ch=$request->file('image_ch');
        $name=time();


        $up_ads=UpAds::where('up_ads_id',$id)->FirstOrFail();
        if($image_mm && $image_en && $image_ch){
            $array=[$image_mm,$image_en,$image_ch];
            foreach($array as $file){
                Storage::disk('Up_Ads')->delete($up_ads->image_mm);
                Storage::disk('Up_Ads')->delete($up_ads->image_en);
                Storage::disk('Up_Ads')->delete($up_ads->image_ch);
                $name=$name+1;
                $image_name=$name.'.'.$file->getClientOriginalExtension();
                Storage::disk('Up_Ads')->put($image_name, File::get($file));
                $imgName[]=$image_name;
            }
            $up_ads->image_mm=$imgName[0];
            $up_ads->image_en=$imgName[1];
            $up_ads->image_ch=$imgName[2];

        }elseif($image_mm && $image_en || $image_mm && $image_ch || $image_en && $image_ch ){
            if($image_mm && $image_en){
                $array=[$image_mm,$image_en];
                foreach($array as $file){
                    Storage::disk('Up_Ads')->delete($up_ads->image_mm);
                    Storage::disk('Up_Ads')->delete($up_ads->image_en);
                    $name=$name+1;
                    $image_name=$name.'.'.$file->getClientOriginalExtension();
                    Storage::disk('Up_Ads')->put($image_name, File::get($file));
                    $imgName[]=$image_name;
                }
                $up_ads->image_mm=$imgName[0];
                $up_ads->image_en=$imgName[1];
            }elseif($image_mm && $image_ch){
                $array=[$image_mm,$image_ch];
                foreach($array as $file){
                    Storage::disk('Up_Ads')->delete($up_ads->image_mm);
                    Storage::disk('Up_Ads')->delete($up_ads->image_ch);
                    $name=$name+1;
                    $image_name=$name.'.'.$file->getClientOriginalExtension();
                    Storage::disk('Up_Ads')->put($image_name, File::get($file));
                    $imgName[]=$image_name;
                }
                $up_ads->image_mm=$imgName[0];
                $up_ads->image_ch=$imgName[1];
            }else{
                $array=[$image_en,$image_ch];
                foreach($array as $file){
                    Storage::disk('Up_Ads')->delete($up_ads->image_en);
                    Storage::disk('Up_Ads')->delete($up_ads->image_ch);
                    $name=$name+1;
                    $image_name=$name.'.'.$file->getClientOriginalExtension();
                    Storage::disk('Up_Ads')->put($image_name, File::get($file));
                    $imgName[]=$image_name;
                }
                $up_ads->image_en=$imgName[0];
                $up_ads->image_ch=$imgName[1];
            }
        }else{
            if($image_mm){
                Storage::disk('Up_Ads')->delete($up_ads->image_mm);
                $image_name_mm=$name.'.'.$request->file('image_mm')->getClientOriginalExtension();
                $up_ads->image=$image_name_mm;
                $up_ads->image_mm=$image_name_mm;
                Storage::disk('Up_Ads')->put($image_name_mm, File::get($image_mm));
            }
            if($image_en){
                Storage::disk('Up_Ads')->delete($up_ads->image_en);
                $image_name_en=$name.'.'.$request->file('image_en')->getClientOriginalExtension();
                $up_ads->image_en=$image_name_en;
                Storage::disk('Up_Ads')->put($image_name_en, File::get($image_en));
            }
            if($image_ch){
                Storage::disk('Up_Ads')->delete($up_ads->image_ch);
                $image_name_ch=$name.'.'.$request->file('image_ch')->getClientOriginalExtension();
                $up_ads->image_ch=$image_name_ch;
                Storage::disk('Up_Ads')->put($image_name_ch, File::get($image_ch));
            }
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

        $up_ads=UpAds::where('up_ads_id',$id)->first();
        if($up_ads){
            $sortId=UpAds::where('sort_id','>',$up_ads->sort_id)->get();
            foreach($sortId as $value){
                $sort_id=$value->sort_id-1;
                $up_ads_id=$value->up_ads_id;

                UpAds::where('up_ads_id',$up_ads_id)->update(['sort_id'=>$sort_id]);
            }

            Storage::disk('Up_Ads')->delete($up_ads->image_mm);
            Storage::disk('Up_Ads')->delete($up_ads->image_en);
            Storage::disk('Up_Ads')->delete($up_ads->image_ch);
            $up_ads->delete();

            $request->session()->flash('alert-danger', 'successfully delete up down ads!');
            return redirect('fatty/main/admin/ads/up_ads');
        }else{
            $request->session()->flash('alert-warning', 'up ads id is not define!');
            return redirect('fatty/main/admin/ads/up_ads');
        }

    }
}
