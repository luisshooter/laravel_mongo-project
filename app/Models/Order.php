<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $fillable = [
        'user_id',
        'mesa',
        'items',
        'total_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'mesa' => 'integer',
        'items' => 'array',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_PROCESSING => 'Em Processamento',
            self::STATUS_COMPLETED => 'Concluído',
            self::STATUS_CANCELLED => 'Cancelado',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? 'Desconhecido';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'secondary',
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($order) {
            if (is_array($order->items)) {
                $total = 0;
                foreach ($order->items as $item) {
                    $total += ($item['unit_price'] ?? 0) * ($item['quantity'] ?? 0);
                }
                $order->total_price = round($total, 2);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i') : '-';
    }

    /** Retorna todos os itens do cardápio (pratos + drinks + refrigerantes) */
    public static function getMenuItems(): array
    {
        $menu = config('menu', []);
        $items = [];
        foreach (['pratos', 'drinks', 'refrigerantes'] as $cat) {
            foreach ($menu[$cat] ?? [] as $item) {
                $items[$item['id']] = $item;
            }
        }
        return $items;
    }
}
