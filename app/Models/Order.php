<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'user_id',
        'total_amount',
        'status',
        'address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'address' => 'array',
    ];

    /**
     * The possible order statuses.
     */
    const STATUS_PENDING = 'PENDING';
    const STATUS_PAID = 'PAID';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_COMPLETED = 'COMPLETED';

    /**
     * Get the user that owns the order.
     */
    public function user()
{
    return $this->belongsTo(User::class);
}

    /**
     * Get the items for the order.
     */
    public function items()
{
    return $this->hasMany(OrderItem::class);
}

    /**
     * Generate a unique order code.
     */
    public static function generateCode(): string
    {
        return 'ORD' . now()->format('YmdHis') . rand(1000, 9999);
    }
    
}