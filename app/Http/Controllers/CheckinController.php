<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Guest;
use App\Models\Checkin;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Inventory;
use Session;
use RealRashid\SweetAlert\Facades\Alert;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;
use App\Http\Requests\BookInventoryRequest;

class CheckinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(Session::get('staffid'));
        return view('checkin.index');
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

    public function checkStatus($id)
    {
        if(User::where('staffid', '=', $id)->exists()){
            if(Checkin::where('staffid', '=', $id)->exists()){
                $info = Checkin::where('staffid', $id)->latest('created_at')->pluck('status')->first();
                
                if($info == "Checkedin"){
                    DB::insert('insert into checkins (staffid,status,created_at) values (?,?,?)', [$id,'Checkedout',Carbon::now()->toDateTimeString()]);
                    return response()->json([
                        'message' => 'User found and updated.'], 200);
                }elseif($info == "Checkedout"){
                    DB::insert('insert into checkins (staffid,status,created_at) values (?,?,?)', [$id,'Checkedin',Carbon::now()->toDateTimeString()]);
                    return response()->json([
                        'message' => 'User found and updated.'], 200);
                }
            }else{
                DB::insert('insert into checkins (staffid,status,created_at) values (?,?,?)', [$id,'Checkedin',Carbon::now()->toDateTimeString()]);
                return response()->json([
                    'message' => 'User found and updated.'], 200);
            }
        }elseif(Guest::where('guestid', '=', $id)->exists()){
            if(Checkin::where('staffid', '=', $id)->exists()){
                $info2 = Checkin::where('staffid', $id)->latest('created_at')->pluck('status')->first();

                if($info2 == "Guestin"){
                    DB::insert('insert into checkins (staffid,status,created_at) values (?,?,?)', [$id,'Guestout',Carbon::now()->toDateTimeString()]);
                    return response()->json([
                        'message' => 'User found and updated.'], 200);
                }elseif($info2 == "Guestout"){
                    DB::insert('insert into checkins (staffid,status,created_at) values (?,?,?)', [$id,'Guestin',Carbon::now()->toDateTimeString()]);
                    return response()->json([
                        'message' => 'User found and updated.'], 200);
                }
            }else{
                DB::insert('insert into checkins (staffid,status,created_at) values (?,?,?)', [$id,'Guestin',Carbon::now()->toDateTimeString()]);
                return response()->json([
                    'message' => 'User found and updated.'], 200);
            }
        }else{
            return response()->json([
                'message' => 'User not found.'], 404);
        }
    }

    public function checkIn($staffid)
    {
        if(User::where('staffid', '=', $staffid)->exists()){
            DB::insert('insert into checkins (staffid,status,created_at) values (?,?,?)', [$staffid,'Checkedin',Carbon::now()->toDateTimeString()]);
            return response()->json([
                'message' => 'User found and updated.'], 200);
        }else{
            return response()->json([
                'message' => 'User not found.'], 404);
        }
    }

    public function checkOut($staffid)
    {
        if(User::where('staffid', '=', $staffid)->exists()){
            DB::insert('insert into checkins (staffid,status,created_at) values (?,?,?)', [$staffid,'Checkedout',Carbon::now()->toDateTimeString()]);
            return response()->json([
                'message' => 'User found and updated.'], 200);
        }else{
            return response()->json([
                'message' => 'User not found.'], 404);
        }
    }

    public function guestCheckIn($guestid)
    {
        if(Guest::where('guestid', '=', $guestid)->exists()){
            DB::insert('insert into checkins (staffid,status,created_at) values (?,?,?)', [$guestid,'Guestin',Carbon::now()->toDateTimeString()]);
            return response()->json([
                'message' => 'User found and updated.'], 200);
        }else{
            return response()->json([
                'message' => 'User not found.'], 404);
        }
    }

    public function guestCheckOut($guestid)
    {
        if(Guest::where('guestid', '=', $guestid)->exists()){
            DB::insert('insert into checkins (staffid,status,created_at) values (?,?,?)', [$guestid,'Guestout',Carbon::now()->toDateTimeString()]);
            return response()->json([
                'message' => 'User found and updated.'], 200);
        }else{
            return response()->json([
                'message' => 'User not found.'], 404);
        }
    }

    public function itemcheckin()
    {
        // $id = '54352452525';
        // $id = '7b8810ce-fe74-430b-9408-7aec739311f9';
        // dd(User::where('staffid', '=', $id)->exists());
        // dd(Inventory::where('serial', $id)->pluck('available')->first() == 'NO');
        // dd(Inventory::where('serial', $id)->first());

        return view('checkin.itemcheckin');
    }

    public function checkStatusItem($id)
    {

        if(Session::get('staffid') != ''){

            if(User::where('staffid', '=', $id)->exists() &&  Session::get('staffid') == $id){
                Session::forget('staffid');
                return response()->json([
                    'message' => 'Removed id from session.'], 200);
            }elseif(User::where('staffid', '=', $id)->exists() &&  Session::get('staffid') != $id){
                Session::forget('staffid');
                Session::put('staffid', $id);
                Session::save();
                return response()->json([
                    'message' => 'Updated id from session.'], 200);
            }else{                

                if(Inventory::where('serial', $id)->exists() && Inventory::where('serial', $id)->pluck('available')->first() == 'YES'){

                    $userid = Session::get('staffid');
                    $name = Inventory::where('serial', $id)->pluck('name')->first();
                    $model = Inventory::where('serial', $id)->pluck('model')->first();
                    $serial = Inventory::where('serial', $id)->pluck('serial')->first();
                    $borrow = User::where('staffid', $userid)->pluck('name')->first();
            
                    // $this->validated($name,$model,$serial,$borrow);
                    $validated['name'] = $name;
                    $validated['model'] = $model;
                    $validated['serial'] = $serial;
                    $validated['borrow'] = $borrow;
                    Borrow::create($validated);
            
                    $inventory = Inventory::where('serial', $id)->first();
                    $inventory->available = 'NO';
                    $inventory->save();
    
                    return response()->json([
                        'message' => 'Item Checkout.'], 200);

                }else{
                    return response()->json([
                        'message' => 'Item not available.'], 404);
                }
            } 

        }else{

            if(User::where('staffid', '=', $id)->exists()){
                Session::put('staffid', $id);
                Session::save();
                return response()->json([
                    'message' => 'Save id to session.'], 200);
            }if(Inventory::where('serial', $id)->exists() && Inventory::where('serial', $id)->pluck('available')->first() == 'NO'){



                Borrow::where('serial', $id)->latest('created_at')->delete();
        
                $inventory = Inventory::where('serial', $id)->first();
                $inventory->available = 'YES';
                $inventory->save();
                
                return response()->json([
                    'message' => 'Item returned.'], 200);

            }else{
                return response()->json([
                    'message' => 'User not found.'], 404);
            }

        }  

    }

    public function booking($id)
    {
        if(Session::get('staffid') == ''){
            return 'No staffid found';
        }else{
            $userid = Session::get('staffid');
            $name = Inventory::where('serial', $id)->pluck('name')->first();
            $model = Inventory::where('serial', $id)->pluck('model')->first();
            $serial = Inventory::where('serial', $id)->pluck('serial')->first();
            $borrow = User::where('staffid', $userid)->pluck('name')->first();
    
            // $this->validated($name,$model,$serial,$borrow);
            $validated['name'] = $name;
            $validated['model'] = $model;
            $validated['serial'] = $serial;
            $validated['borrow'] = $borrow;
            Borrow::create($validated);
    
            $inventory = Inventory::where('serial', $id)->first();
            $inventory->available = 'NO';
            $inventory->save();

            return redirect()->route('checkin.itemcheckin');
        }  
    }

    public function clearSession()
    {
        Session::forget('staffid');
    }
}
