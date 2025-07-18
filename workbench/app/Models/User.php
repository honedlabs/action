<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Honed\Action\Attributes\UseBatch;
use Honed\Action\Concerns\HasBatch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Workbench\App\Batches\UserBatch;
use Workbench\Database\Factories\UserFactory;

#[UseBatch(UserBatch::class)]
class User extends Authenticatable
{
    /**
     * @use \Honed\Action\Concerns\HasBatch<\Workbench\App\Batches\UserBatch>
     */
    use HasBatch;

    /**
     * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Workbench\Database\Factories\UserFactory>
     */
    use HasFactory;

    use Notifiable;

    /**
     * The factory for the model.
     *
     * @return class-string<\Illuminate\Database\Eloquent\Factories\Factory>
     */
    protected static $factory = UserFactory::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the products for the user.
     *
     * @return HasMany<Product>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the products that the user has.
     *
     * @return BelongsToMany<Product, $this>
     */
    public function purchasedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
