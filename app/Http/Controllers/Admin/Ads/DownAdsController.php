<?php

namespace App\Http\Controllers\Admin\Ads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Ads\DownAds;
use App\Models\Restaurant\Restaurant;

class DownAdsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $down_ads=DownAds::orderBy('sort_id')->get();
        return view('admin.Ads.down.index',compact('down_ads'));
    }

    public function sort_update(Request $request)
    {
        $posts = DownAds::all();

        foreach ($posts as $post) {
            foreach ($request->order as $order) {
                if($order['id'] == $post->down_ads_id) {
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
        $restaurants=Restaurant::doesnthave('down_ads')->get();
        return view('admin.Ads.down.create',compact('restaurants'));
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
        $count=DownAds::count();

        $down_ads=new DownAds();
        if($image){
            foreach($image as $file){
                $name=$name+1;
                $image_name=$name.'.'.$file->getClientOriginalExtension();
                Storage::disk('Down_Ads')->put($image_name, File::get($file));
                $imgName[]=$image_name;
            }
            if(count($imgName)==3){
                $down_ads->image_mm=$imgName[0];
                $down_ads->image_en=$imgName[1];
                $down_ads->image_ch=$imgName[2];
            }elseif(count($imgName)==2){
                $down_ads->image_mm=$imgName[0];
                $down_ads->image_en=$imgName[1];
            }elseif(count($imgName)==1){
                $down_ads->image_mm=$imgName[0];
            }
        }
        $down_ads->restaurant_id=$restaurant_id;
        $down_ads->sort_id=$count+1;
        $down_ads->save();
        $request->session()->flash('alert-success', 'successfully create up ads!');
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
        $down_ads=DownAds::find($id);
        $restaurants=Restaurant::doesnthave('down_ads')->get();
        return view('admin.Ads.down.edit',compact('restaurants','down_ads'));
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
        $image_mm=$request->file('image_mm');
        $image_en=$request->file('image_en');
        $image_ch=$request->file('image_ch');
        $name=time();


        $down_ads=DownAds::where('down_ads_id',$id)->FirstOrFail();
        if($image_mm && $image_en && $image_ch){
            $array=[$image_mm,$image_en,$image_ch];
            foreach($array as $file){
                Storage::disk('Down_Ads')->delete($down_ads->image_mm);
                Storage::disk('Down_Ads')->delete($down_ads->image_en);
                Storage::disk('Down_Ads')->delete($down_ads->image_ch);
                $name=$name+1;
                $image_name=$name.'.'.$file->getClientOriginalExtension();
                Storage::disk('Down_Ads')->put($image_name, File::get($file));
                $imgName[]=$image_name;
            }
            $down_ads->image_mm=$imgName[0];
            $down_ads->image_en=$imgName[1];
            $down_ads->image_ch=$imgName[2];

        }elseif($image_mm && $image_en || $image_mm && $image_ch || $image_en && $image_ch ){
            if($image_mm && $image_en){
                $array=[$image_mm,$image_en];
                foreach($array as $file){
                    Storage::disk('Down_Ads')->delete($down_ads->image_mm);
                    Storage::disk('Down_Ads')->delete($down_ads->image_en);
                    $name=$name+1;
                    $image_name=$name.'.'.$file->getClientOriginalExtension();
                    Storage::disk('Down_Ads')->put($image_name, File::get($file));
                    $imgName[]=$image_name;
                }
                $down_ads->image_mm=$imgName[0];
                $down_ads->image_en=$imgName[1];
            }elseif($image_mm && $image_ch){
                $array=[$image_mm,$image_ch];
                foreach($array as $file){
                    Storage::disk('Down_Ads')->delete($down_ads->image_mm);
                    Storage::disk('Down_Ads')->delete($down_ads->image_ch);
                    $name=$name+1;
                    $image_name=$name.'.'.$file->getClientOriginalExtension();
                    Storage::disk('Down_Ads')->put($image_name, File::get($file));
                    $imgName[]=$image_name;
                }
                $down_ads->image_mm=$imgName[0];
                $down_ads->image_ch=$imgName[1];
            }else{
                $array=[$image_en,$image_ch];
                foreach($array as $file){
                    Storage::disk('Down_Ads')->delete($down_ads->image_en);
                    Storage::disk('Down_Ads')->delete($down_ads->image_ch);
                    $name=$name+1;
                    $image_name=$name.'.'.$file->getClientOriginalExtension();
                    Storage::disk('Down_Ads')->put($image_name, File::get($file));
                    $imgName[]=$image_name;
                }
                $down_ads->image_en=$imgName[0];
                $down_ads->image_ch=$imgName[1];
            }
        }else{
            if($image_mm){
                Storage::disk('Down_Ads')->delete($down_ads->image_mm);
                $image_name_mm=$name.'.'.$request->file('image_mm')->getClientOriginalExtension();
                $down_ads->image=$image_name_mm;
                $down_ads->image_mm=$image_name_mm;
                Storage::disk('Down_Ads')->put($image_name_mm, File::get($image_mm));
            }
            if($image_en){
                Storage::disk('Down_Ads')->delete($down_ads->image_en);
                $image_name_en=$name.'.'.$request->file('image_en')->getClientOriginalExtension();
                $down_ads->image_en=$image_name_en;
                Storage::disk('Down_Ads')->put($image_name_en, File::get($image_en));
            }
            if($image_ch){
                Storage::disk('Down_Ads')->delete($down_ads->image_ch);
                $image_name_ch=$name.'.'.$request->file('image_ch')->getClientOriginalExtension();
                $down_ads->image_ch=$image_name_ch;
                Storage::disk('Down_Ads')->put($image_name_ch, File::get($image_ch));
            }
        }
        $down_ads->restaurant_id=$restaurant_id;
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
    public function destroy(Request $request,$id)
    {
        $down_ads=DownAds::where('down_ads_id',$id)->first();
        if($down_ads){
            $sortId=DownAds::where('sort_id','>',$down_ads->sort_id)->get();
            foreach($sortId as $value){
                $sort_id=$value->sort_id-1;
                $down_ads_id=$value->down_ads_id;

                DownAds::where('down_ads_id',$down_ads_id)->update(['sort_id'=>$sort_id]);
            }

            Storage::disk('Down_Ads')->delete($down_ads->image_mm);
            Storage::disk('Down_Ads')->delete($down_ads->image_en);
            Storage::disk('Down_Ads')->delete($down_ads->image_ch);
            $down_ads->delete();

            $request->session()->flash('alert-danger', 'successfully delete up down ads!');
            return redirect('fatty/main/admin/ads/down_ads');
        }else{
            $request->session()->flash('alert-warning', 'up ads id is not define!');
            return redirect('fatty/main/admin/ads/down_ads');
        }
    }
}
