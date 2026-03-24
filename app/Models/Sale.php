<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    /**
     * Имя таблицы, связанной с моделью.
     */
    protected $table = 'sales';

    /**
     * Поля, которые можно массово заполнять.
     */
    protected $fillable = [
        'g_number',
        'date',
        'last_change_date',
        'supplier_article',
        'tech_size',
        'barcode',
        'total_price',
        'discount_percent',
        'is_supply',
        'is_realization',
        'promo_code_discount',
        'warehouse_name',
        'country_name',
        'oblast_okrug_name',
        'region_name',
        'income_id',
        'sale_id',
        'odid',
        'spp',
        'for_pay',
        'finished_price',
        'price_with_disc',
        'nm_id',
        'subject',
        'category',
        'brand',
        'is_storno',
    ];

    /**
     * Поля, которые должны быть преобразованы в типы данных.
     */
    protected $casts = [
        'date' => 'date',
        'last_change_date' => 'date',
        'total_price' => 'decimal:4',
        'discount_percent' => 'integer',
        'is_supply' => 'boolean',
        'is_realization' => 'boolean',
        'for_pay' => 'decimal:2',
        'finished_price' => 'decimal:2',
        'price_with_disc' => 'decimal:2',
        'spp' => 'decimal:2',
        'promo_code_discount' => 'decimal:2',
        'is_storno' => 'boolean',
        'income_id' => 'integer',
        'barcode' => 'integer',
        'nm_id' => 'integer',
    ];

//    public function save()
//    {
//
//    }
}
