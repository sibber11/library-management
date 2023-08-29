<?php

namespace App\Models;

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

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
