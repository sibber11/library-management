<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Book;
use Inertia\Inertia;
use App\Models\Member;
use App\Models\Reservation;
use App\Events\BookReserved;
use Illuminate\Http\Request;
use App\Services\CheckOutService;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservation::with(['book', 'member.user'])->paginate();
        return Inertia::render('Admin/Reservation/Index', [
            'reservations' => $reservations
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $members = Member::active()
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
                    'reservations' => $member->reservations->pluck('book_id')
                ];
            });
        $books = Book::unAvailable()->get();
        // throw new Exception('dd');
        return Inertia::render('Admin/CheckOut/Fields', compact('members', 'books'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request)
    {
        $member = Member::find($request->member_id);
        $book = Book::find($request->book_id);

        if ($member->alreadyCheckedOut($book)) {
            throw new \Exception('The book is already checked out by current member.');
        }

        $reservation = Reservation::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->where('status', '<>', 'completed')->first();

        if (empty($reservation)) {
            Reservation::create($request->validated());
        }

        return redirect()->route('reservations.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        if ($request->status == 'completed') {
            // $reservation->load(['member','book']);
            return to_route('check-outs.create', [
                'member' => $reservation->member_id,
                'book' => $reservation->book_id
            ]);
        }

        if ($reservation->status == 'completed') {
            return redirect()->route('reservations.index');
        }

        if ($reservation->status == 'pending' || $reservation->status == 'reserved') {
            $reservation->cancel();
        }

        return redirect()->route('reservations.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return redirect()->route('reservations.index');
    }
}
