<?php

namespace App\Http\Controllers;

use App\Models\CheckOut;
use App\Http\Requests\StoreCheckOutRequest;
use App\Http\Requests\UpdateCheckOutRequest;
use App\Models\Book;
use App\Models\Member;
use App\Services\CheckOutService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

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
                $query->where(function ($query) use ($search) {
                    $query->whereHas('member.user', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    })
                        ->orWhereHas('book', function ($query) use ($search) {
                            $query->where('title', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->orderBy('is_checked_in')
            ->paginate()
            ->withQueryString();
        return Inertia::render('Admin/CheckOut/Index', [
            'checkouts' => $checkouts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $members = Member::active()
            ->canCheckout()
            ->with([
                'user',
                'checkouts' => fn ($q) => $q->checkedout(),
                'reservations' => fn ($q) => $q->whereIn('status', ['pending', 'reserved'])
            ])
            ->limit(10)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->user->name,
                    'email' => $member->user->email,
                    'checkouts' => $member->checkouts->pluck('book_id'),
                    'reservations' => $member->reservations->pluck('book_id'),
                ];
            });
        $books = Book::get();
        $due_date = now()->addMonth()->format('Y-m-d');
        return Inertia::render('Admin/CheckOut/Fields', compact('members', 'books', 'due_date'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCheckOutRequest $request, CheckOutService $service)
    {
        $member = \App\Models\Member::find($request->member_id);
        $book = \App\Models\Book::find($request->book_id);

        if ($member->alreadyCheckedOut($book)) {
            throw new \Exception('The book is already checked out by current member.');
            // return back()->withErrors(['member_id' => 'The book is already checked out by current member.']);
        }

        if ($book->isReserved($member)) {
            throw new \Exception('The book is reserved by someone else.');
        }

        $service->checkOut($member, $book, $request->date('due_date'));
        return redirect()->route('check-outs.index');
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
        $checkOut->checkIn();
        $checkOut->delete();
        return redirect()->route('check-outs.index');
    }
}
