<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Product extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'products';

    protected $fillable = [
        'name',
        'category',
        'price',
        'stock',
        'is_active',
        'image_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    const CATEGORY_FLOWER = 'flor';

    const CATEGORY_PLANT = 'planta';

    const CATEGORY_GIFT = 'presente';

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_FLOWER => 'Buquês & Arranjos',
            self::CATEGORY_PLANT => 'Plantas de Interior',
            self::CATEGORY_GIFT => 'Presentes & Acessórios',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::getCategories()[$this->category] ?? 'Outros';
    }
}
