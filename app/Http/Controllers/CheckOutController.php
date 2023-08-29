<?php

namespace App\Http\Controllers;

use App\Models\CheckOut;
use App\Http\Requests\StoreCheckOutRequest;
use App\Http\Requests\UpdateCheckOutRequest;
use App\Services\CheckOutService;
use Inertia\Inertia;

class CheckOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Admin/CheckOut/Index', [
            'checkOuts' => CheckOut::paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/CheckOut/Fields', [
            'books' => \App\Models\Book::all(),
            'members' => \App\Models\Member::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCheckOutRequest $request, CheckOutService $service)
    {
        $member = \App\Models\Member::find($request->member_id);
        $book = \App\Models\Book::find($request->book_id);
        $service->checkOut($member,$book,$request->validated('due_date'));
        return redirect()->route('check-outs.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(CheckOut $checkOut)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CheckOut $checkOut)
    {
        return Inertia::render('Admin/CheckOut/Fields', [
            'checkOut' => $checkOut
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCheckOutRequest $request, CheckOut $checkOut)
    {
        // dd($request->validated());
        if ($request->has('is_checked_in')) {
            $checkOut->checkIn();
        } else {
            $checkOut->update($request->validated());
        }
        return redirect()->route('check-outs.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CheckOut $checkOut)
    {
        $checkOut->delete();
        return redirect()->route('check-outs.index');
    }
}
