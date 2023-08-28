<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    // hidden attributes
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // add appeneds
    protected $appends = [
        'membership_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * return the join date in the format d-M-Y
     */
    public function joinDate() : Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $attributes['created_at']->format('d-M-Y'),
        );
    }

    // a function to return the membership status
    public function membershipStatus() : Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $attributes['membership_due_date'] > now() ? 'Active' : 'Expired',
        );
    }

    /**
     * increase the membership duration by the given number of months
     */
    public function extendMembership(int $months)
    {
        $this->membership_due_date = $this->membership_due_date->addMonths($months);
        $this->save();
    }
}
