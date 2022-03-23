<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Http\Requests\BookInventoryRequest;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Guest;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $quantity = DB::select( DB::raw("SELECT *,sum(available = 'YES') AS available,COUNT(*) AS quantity FROM `inventories` group by category") );      

        return view('inventory.index', compact('quantity'));
    }

    public function create()
    {
        abort_if(Gate::denies('task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('inventory.create');
    }

    public function store(StoreInventoryRequest $request)
    {
        $request->validated();
        $validated['name'] = $request->input('name');
        $validated['model'] = $request->input('model');
        $validated['serial'] = $request->input('serial');
        $validated['incharge'] = Auth::user()->name;
        $validated['category'] = $request->input('category');
        $validated['available'] = 'YES';
        Inventory::create($validated);

        return redirect()->route('inventory.index');
    }

    public function show(Inventory $inventory)
    {
        abort_if(Gate::denies('task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('inventory.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        abort_if(Gate::denies('task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('inventory.edit', compact('inventory'));
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        $request->validated();
        $validated['name'] = $request->input('name');
        $validated['model'] = $request->input('model');
        $validated['serial'] = $request->input('serial');
        $validated['incharge'] = Auth::user()->name;
        $validated['available'] = $request->input('status');
        $inventory->update($validated);

        return redirect()->route('inventory.index');
    }

    public function destroy(Inventory $inventory)
    {
        abort_if(Gate::denies('task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inventory->delete();

        return redirect()->route('inventory.index');
    }

    public function alllistpage($id)
    {        
        $category = json_decode( json_encode(DB::table('inventories')->select('category')->where('id', $id)->first()), true);

        $inventories = Inventory::where('category', $category)->get();

        return view('inventory.alllistpage',compact('inventories'));
    }


    public function borrow($id)
    {       
        $inventories = Inventory::where('id', $id)->get();
        $users = User::all();
        $guests = Guest::all();

        return view('inventory.borrow',compact('inventories','users','guests'));
    }

    public function book(BookInventoryRequest $request, $id)
    {    
        $request->validated();
        $validated['name'] = $request->input('name');
        $validated['model'] = $request->input('model');
        $validated['serial'] = $request->input('serial');
        $validated['borrow'] = $request->input('borrow');
        $validated['approval'] = 'pending';
        Borrow::create($validated);

        $inventory = Inventory::find($id);
        $inventory->available = 'NO';
        $inventory->save();

        return redirect()->route('inventory.index');
    }

    public function return($id)
    {    
        $serial = Inventory::where('id', $id)->pluck('serial')->first();
        Borrow::where('serial', $serial)->latest('created_at')->delete();

        $inventory = Inventory::find($id);
        $inventory->available = 'YES';
        $inventory->save();

        return redirect()->route('inventory.index');
    }


    public function approve($id)
    {    
        $accept = Borrow::find($id);
        $accept->approval = 'accepted';
        $accept->save();

        return redirect()->route('dashboard');
    }

    public function decline($id)
    {    
        $serial = Borrow::where('id', $id)->pluck('serial')->first();
        // $iddecline = Borrow::where('serial', $serial)->latest('created_at')->pluck('id')->first();
        $decline = Borrow::find($id);
        $decline->approval = 'decline';
        $decline->save();

        Borrow::where('serial', $serial)->latest('created_at')->delete();

        $serialinv = Inventory::where('serial', $serial)->pluck('id')->first();

        $inventory = Inventory::find($serialinv);
        $inventory->available = 'YES';
        $inventory->save();

        return redirect()->route('dashboard');
    }
}
