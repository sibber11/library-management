<?php

namespace App\Services;

use App\Models\CheckOut;
use App\Models\Book;
use App\Models\Member;
use Carbon\Carbon;

class CheckOutService
{

    public function __construct()
    {
        
    }

    /**
     * @param array<string, mixed> $data
     */
    public function checkOut(Member $member, Book $book, Carbon $dueDate = null): void
    {
        if ($dueDate == null) {
            dump('here');
            $dueDate = now()->addMonth();
        }
        $checkout = new CheckOut([
            'book_id' => $book->id,
            'member_id' => $member->id,
            'check_out_date' => now(),
            'due_date' => $dueDate,
        ]);
        $checkout->save();
    }
}