<?php

namespace App\Models;

use App\Services\CheckOutService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'book_id',
        'status'
    ];

    public function complete()
    {
        $this->update(['status' => 'completed']);    
    }

    public function cancel()
    {
        $this->update(['status' => 'canceled']);
    }

    public function reserve($book = null)
    {
        $this->update(['status' => 'reserved']);
    }

    // public function scopeCompleted($query)
    // {
    //     $query->whereStatus('complete');
    // }

    // public function scopeCanceled($query)
    // {
    //     $query->whereStatus('canceled');
    // }

    public function scopeReserved($query)
    {
        $query->whereStatus('reserved');
    }

    public function scopePending($query)
    {
        $query->whereStatus('pending');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
