<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Product;

class Order extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_cpf',
        'customer_address',
        'items',
        'total_price',
        'status',
        'payment_method',
        'notes',
    ];

    const PAYMENT_CASH = 'dinheiro';
    const PAYMENT_CARD = 'cartao';

    public static function getPaymentMethods(): array
    {
        return [
            self::PAYMENT_CASH => 'Dinheiro',
            self::PAYMENT_CARD => 'Cartão',
        ];
    }

    public function getPaymentLabelAttribute(): string
    {
        return self::getPaymentMethods()[$this->payment_method] ?? '-';
    }

    protected $casts = [
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
        $products = Product::where('is_active', true)->get();
        $items = [];
        foreach ($products as $p) {
            $items[$p->id] = [
                'id' => clone $p->_id,
                'name' => $p->name,
                'price' => $p->price,
                'stock' => $p->stock
            ];
        }
        return $items;
    }
}
