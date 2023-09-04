<?php

namespace App\Http\Controllers;

use App\Models\CheckOut;
use App\Http\Requests\StoreCheckOutRequest;
use App\Http\Requests\UpdateCheckOutRequest;
use App\Models\Member;
use App\Services\CheckOutService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $checkouts = CheckOut::with(['member.user', 'book'])
            ->when($request->has('filter'), function ($query) use ($request) {
                switch ($request->filter) {
                    case 'checked_in':
                        $query->checkedIn();
                        break;
                    case 'checked_out':
                        $query->checkedOut();
                        break;
                }
            })
            /**
             * Lesson: when using whereHas with orWhereHas, you need to wrap the
             * whereHas in a closure to avoid the query builder from adding
             * an additional where clause.
             */
            ->when($request->search, function ($query, $search) {
                $query->where(function($query) use($search){
                    $query->whereHas('member.user', function($query) use($search){
                        $query->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('book', function($query) use($search){
                        $query->where('title', 'LIKE', "%{$search}%");
                    });
                });
            })
            ->paginate()
            ->withQueryString();
    
        return Inertia::render('Admin/CheckOut/Index', [
            'checkouts' => $checkouts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = Member::active()->with('user')->limit(10)->get()->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->user->name,
            ];
        });
        return Inertia::render('Admin/CheckOut/Fields', [
            'books' => \App\Models\Book::available()->get(),
            'members' => $members,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCheckOutRequest $request, CheckOutService $service)
    {
        $member = \App\Models\Member::find($request->member_id);
        $book = \App\Models\Book::find($request->book_id);
        $service->checkOut($member, $book, $request->date('due_date'));
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
