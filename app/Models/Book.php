<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    public $fillable = [
        'title',
        'author',
        'price',
        'available',
        'description',
        'published_at',
    ];

    // hidden fields
    public $hidden = [
        'created_at',
        'updated_at',
        'price',
    ];

    public $casts = [
        'published_at' => 'datetime:Y-m-d',
    ];

     /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['is_new', 'publish_year'];

    /**
     * Determine if the book is new.
     */
    protected function isNew(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['created_at'] > now()->subMonth(6),
        );
    }

    protected function publishYear(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => Carbon::make($attributes['published_at'])->format('Y'),
        );
    }

    public function isAvailable(): bool
    {
        return $this->available > 0;
    }

    public function isReserved(Member $member): bool
    {
        $reservations = Reservation::reserved()->whereBookId($this->id)->get();
        $reservedByMember =  $reservations->contains(function ($item) use ($member) {
            return $item->member_id == $member->id;
        });
        if ($reservations->count() > 0 && $reservedByMember) {
            return false;
        }
        if ($reservations->count() == 0) {
            return false;
        }
        return true;
    }

    public function scopeAvailable($query)
    {
        return $query->where('available', '>', 0);
    }

    public function scopeUnAvailable($query)
    {
        return $query->where('available', '=', 0);
    }

    public function checkouts()
    {
        return $this->hasMany(CheckOut::class);
    }

    // public function genre()
    // {
    //     return $this->belongsTo(Genre::class);
    // }

    /**
     * Get the book's Fine.
     */
    // public function fines()
    // {
    //     return $this->hasMany(Fine::class);
    // }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
