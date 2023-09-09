<?php

namespace App\Models;

use Carbon\Carbon;
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

    protected $casts = [
        'membership_due_date' => 'datetime:Y-m-d'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkouts()
    {
        return $this->hasMany(CheckOut::class);
    }

    /**
     * return the join date in the format d-M-Y
     */
    public function joinDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $attributes['created_at']->format('d-M-Y'),
        );
    }

    // a function to return the membership status
    public function membershipStatus(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $attributes['membership_due_date'] > now(),
        );
    }

    /**
     * increase the membership duration by the given number of months
     */
    public function extendMembership(int $months)
    {
        if ($this->membership_due_date < now()) {
            $this->membership_due_date = now();
        }
        $this->membership_due_date = $this->membership_due_date->addMonths($months);
        $this->save();
    }

    /**
     * scope to return only active members
     */
    public function scopeActive($query)
    {
        $query->where('membership_due_date', '>', now());
    }

    /**
     * scope to return only expired members
     */
    public function scopeExpired($query)
    {
        $query->where('membership_due_date', '<', now());
    }

    /**
     * scope to return only members who can checkout
     * warning: this scope will not work with count
     */
    public function scopeCanCheckout($query)
    {
        $query->withCount(['checkouts' => function ($query) {
            $query->where('is_checked_in', false);
        }])
            ->whereRaw('"checkouts_count" < "members"."type"');
    }

    public function scopeCantCheckout($query)
    {
        $query->withCount('checkouts')
            ->whereRaw('checkouts_count = members.type');
    }

    public function canCheckout()
    {
        return $this->checkouts()->checkedout()->count() < $this->type;
    }

    public function alreadyCheckedOut(Book $book)
    {
        return $this->checkouts()->checkedout()->whereBookId($book->id)->count() > 0;
    }
}
