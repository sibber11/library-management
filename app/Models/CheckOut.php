<?php

namespace App\Models;

use App\Events\BookCheckedIn;
use App\Exceptions\BookNotAvailableException;
use Carbon\Carbon;
use Exception;
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

    protected $casts = [
        'due_date' => 'datetime:Y-m-d'
    ];

    // boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($checkout) {
            if (!$checkout->book->isAvailable()) {
                throw new BookNotAvailableException();
            }
            if(!$checkout->member->canCheckout()){
                throw new Exception('Max checkout reached!');
            }
            if (!$checkout->is_checked_in) {
                $checkout->book->decrement('available');
            }
        });
    }

    public function checkIn(): void
    {
        if ($this->is_checked_in) {
            return;
        }
        $this->check_in_date = now();
        $this->is_checked_in = true;
        $this->book->increment('available');
        $this->save();
        BookCheckedIn::dispatch($this->book);
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
