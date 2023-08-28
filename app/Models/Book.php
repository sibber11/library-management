<?php

namespace App\Models;

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
        'published_at' => 'datetime:d-M-Y',
    ];

     /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['is_new'];

    /**
     * Determine if the book is new.
     */
    protected function isNew(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $attributes['created_at'] > now()->subMonth(6),
        );
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
}
