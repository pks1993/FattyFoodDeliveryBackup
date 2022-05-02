<?php

namespace App\Http\Controllers\Admin\Parcel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order\ParcelState;
use App\Models\State\State;

class ParcelStateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parcel_states=ParcelState::all();
        $states=State::all();
        return view('admin.parcel_state.index',compact('parcel_states','states')); 
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
        ParcelState::create($request->all());
        $request->session()->flash('alert-success', 'successfully store parcel state!');
        return redirect('fatty/main/admin/parcel_states');
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
        ParcelState::find($id)->update($request->all());
        $request->session()->flash('alert-success', 'successfully update parcel state!');
        return redirect('fatty/main/admin/parcel_states');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        ParcelState::destroy($id);
        $request->session()->flash('alert-success', 'successfully delete parcel state!');
        return redirect('fatty/main/admin/parcel_states');
    }
}
