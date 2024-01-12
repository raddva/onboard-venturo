<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTSalesDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_sales_detail', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('t_sales_id')
                ->comment('Fill with id of t_sales');
            $table->bigInteger('m_product_id')
                ->comment('Fill with id of m_product, keep null if insert detail of product')
                ->nullable();
            $table->bigInteger('m_product_detail_id')
                ->comment('Fill with id of m_product_detail, keep null if insert parent of product')
                ->nullable();
            $table->double('total_item')
                ->comment('Fill with total of item')
                ->default(0);
            $table->double('price')
                ->comment('Fill with price of product or product detail')
                ->default(0);
            $table->double('discount_nominal')
                ->comment('Fill with nominal of discount in rupiah')
                ->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->integer('deleted_by')->default(0);

            $table->index('t_sales_id');
            $table->index('m_product_id');
            $table->index('m_product_detail_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_sales_detail');
    }
}
