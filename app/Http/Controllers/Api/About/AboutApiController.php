<?php

namespace App\Http\Controllers\Api\About;

use App\Models\About\About;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class AboutApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data=About::where('about_id','1')->first();
        return response()->json(['success'=>true,'message'=>'this is about text','data'=>$data]);
    }

    public function rider_about()
    {
        $data=About::where('about_id','2')->first();
        return response()->json(['success'=>true,'message'=>'this is about text','data'=>$data]);
    }

    public function restaurant_about()
    {
        $data=About::where('about_id','3')->first();
        return response()->json(['success'=>true,'message'=>'this is about text','data'=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function pautganan()
    {
        $pat_tee="[3,8,4]";
        $pat_tee=json_decode($pat_tee);
        $pat_tee_win_no="[8]";
        $pat_tee_win_no=json_decode($pat_tee_win_no);
        $pat_tee_data=['win_status'=>1,'pat_tee'=>$pat_tee,'win_no'=>$pat_tee_win_no];

        $one_change="[7,2]";
        $one_change=json_decode($one_change);
        $one_change_win_no="[]";
        $one_change_win_no=json_decode($one_change_win_no);
        $one_change_data=['win_status'=>1,'one_change'=>$one_change,'win_no'=>$one_change_win_no];

        $kway_all="[72,74]";
        $kway_all=json_decode($kway_all);
        $kway_tin="[73,78,42,34]";
        $kway_tin=json_decode($kway_tin);
        $kway_yan="[84,32,82,83]";
        $kway_yan=json_decode($kway_yan);
        $kway_win_no="[84]";
        $kway_win_no=json_decode($kway_win_no);
        $kway_data=['win_status'=>1,'kway_all'=>$kway_all,'kway_yan'=>$kway_yan,'kway_tin'=>$kway_tin,'win_no'=>$kway_win_no];

        $noti_bar_data=['status'=>0,'about'=>"bar ngar sa ma ka lar"];
        return response()->json(['success'=>true,'message'=>'user home page data','data'=>['pat_tee'=>$pat_tee_data,'one_change'=>$one_change_data,'kway'=>$kway_data,'noti_bar'=>$noti_bar_data]]);
    }
    public function set_value_history()
    {
        $set_1200="1,618.77";$val_1200="32,722.06";$set_430="1,622.95";$val_430="66,204.09";$result_1200=72;$result_430=54;$internet_930=23;$modern_930=98;$internet_200=34;$modern_200=21;$date="20-May-2022 Monday";$date1="21-May-2022 Tuesday";$date2="20-May-2022 Wednesday";

        $data=['set_1200'=>$set_1200,'val_1200'=>$val_1200,'set_430'=>$set_430,'val_430'=>$val_430,'result_1200'=>$result_1200,'result_430'=>$result_430,'internet_930'=>$internet_930,'modern_930'=>$modern_930,'internet_200'=>$internet_200,'modern_200'=>$modern_200,'date'=>$date];
        $data1=['set_1200'=>$set_1200,'val_1200'=>$val_1200,'set_430'=>$set_430,'val_430'=>$val_430,'result_1200'=>$result_1200,'result_430'=>$result_430,'internet_930'=>$internet_930,'modern_930'=>$modern_930,'internet_200'=>$internet_200,'modern_200'=>$modern_200,'date'=>$date1];
        $data2=['set_1200'=>$set_1200,'val_1200'=>$val_1200,'set_430'=>$set_430,'val_430'=>$val_430,'result_1200'=>$result_1200,'result_430'=>$result_430,'internet_930'=>$internet_930,'modern_930'=>$modern_930,'internet_200'=>$internet_200,'modern_200'=>$modern_200,'date'=>$date2];
        return response()->json(['success'=>true,'message'=>'user home page data','data'=>[$data,$data1,$data2]]);
    }

    public function paut_count()
    {
        // $test=Carbon::now()->subDays(9)->toDateTimeLocalString();
        // return response()->json($test);
        $now = Carbon::now();
        $weekStartDate = $now->startOfWeek()->isoFormat('D-MMMM-Y dddd');
        $weekEndDate = $now->endOfWeek(5)->isoFormat('D-MMMM-Y dddd');
        return response()->json(['succss'=>true,'message'=>'pauntganan paut tee count data','start_date'=>$weekStartDate,'end_date'=>$weekEndDate,'data'=>['zero'=>0,'one'=>0,'two'=>2,'three'=>0,'four'=>1,'five'=>3,'six'=>0,'seven'=>0,'eight'=>1,'nine'=>0]]);
    }
}
