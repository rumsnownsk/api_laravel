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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();

            // Идентификаторы
            $table->bigInteger('income_id')->nullable(); // номер дохода/поступления
            $table->string('number')->nullable(); // номер документа (может быть пустым)

            // Даты
            $table->date('date')->nullable(); // дата поступления
            $table->date('last_change_date')->nullable(); // дата последнего изменения
            $table->date('date_close')->nullable(); // дата закрытия

            // Товарные идентификаторы
            $table->string('supplier_article')->nullable(); // артикул поставщика
            $table->string('tech_size')->nullable(); // технический размер (UUID)
            $table->bigInteger('barcode')->nullable(); // штрих‑код
            $table->bigInteger('nm_id')->nullable(); // номер номенклатуры

            // Количественные и финансовые данные
            $table->integer('quantity')->default(0); // количество единиц
            $table->decimal('total_price', 12, 2)->default(0); // общая стоимость с точностью до копеек

            // Локация
            $table->string('warehouse_name', 255)->nullable(); // название склада

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
