<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            // Идентификаторы
            $table->string('g_number')->nullable(); // очень большое число как строка (20+ цифр)
            $table->bigInteger('income_id')->nullable(); // номер дохода/поступления
            $table->bigInteger('nm_id')->nullable(); // номер номенклатуры
            $table->string('sale_id')->nullable(); // ID продажи (строка)
            $table->string('odid')->nullable(); // может быть NULL

            // Даты
            $table->date('date')->nullable(); // дата операции (YYYY-MM-DD)
            $table->date('last_change_date')->nullable(); // дата последнего изменения

            // Товарные идентификаторы
            $table->string('supplier_article')->nullable(); // UUID поставщика
            $table->string('tech_size')->nullable(); // технический размер (UUID)
            $table->bigInteger('barcode')->nullable(); // числовой штрих‑код
            $table->string('subject')->nullable(); // UUID категории товара
            $table->string('category')->nullable(); // UUID категории
            $table->string('brand')->nullable(); // UUID бренда

            // Финансовые данные
            $table->decimal('total_price', 14, 4)->nullable(); // общая цена с высокой точностью
            $table->integer('discount_percent')->nullable(); // процент скидки (целое число)
            $table->decimal('spp', 10, 2)->nullable(); // скидка постоянного покупателя
            $table->decimal('for_pay', 12, 2)->nullable(); // сумма к оплате
            $table->decimal('finished_price', 12, 2)->nullable(); // итоговая цена
            $table->decimal('price_with_disc', 12, 2)->nullable(); // цена со скидкой
            $table->decimal('promo_code_discount', 12, 2)->nullable(); // скидка по промокоду (может быть NULL)

            // Булевы значения
            $table->boolean('is_supply')->default(false); // флаг поставки
            $table->boolean('is_realization')->default(false); // флаг реализации
            $table->boolean('is_storno')->nullable(); // флаг сторно (может быть NULL)

            // Локация
            $table->string('warehouse_name', 255)->nullable(); // название склада
            $table->string('country_name', 255)->nullable(); // страна
            $table->string('oblast_okrug_name', 255)->nullable(); // федеральный округ
            $table->string('region_name', 255)->nullable(); // регион

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
