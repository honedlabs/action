<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Stubs;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->public_id = Str::uuid();
        });
    }

    protected $casts = [
        'status' => Status::class,
    ];
}
