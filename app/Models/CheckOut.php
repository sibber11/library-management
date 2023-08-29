<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckOut extends Model
{
    use HasFactory;

    // fillable properties
    protected $fillable = [
        'book_id',
        'member_id',
        'check_out_date',
        'due_date',
        'check_in_date',
        'is_checked_in',
    ];

    public function checkIn(): void
    {
        $this->check_in_date = now();
        $this->is_checked_in = true;
        $this->save();
    }

    public function extendDueDate(Carbon $date)
    {
        $this->due_date = $date;
        $this->save();
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeCheckedIn($query)
    {
        return $query->where('is_checked_in', true);
    }

    public function scopeCheckedOut($query)
    {
        return $query->where('is_checked_in', false);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now());
    }

    public function scopeDueToday($query)
    {
        return $query->where('due_date', now());
    }


}
