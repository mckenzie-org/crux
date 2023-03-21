<?php

namespace Etlok\Crux\Traits;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait ManagesDynamicTable
{
    protected static function bootManagesDynamicTable()
    {
        static::created(function ($model) {
            if(Schema::hasTable($model->getItemsTable())) {
                return false;
            }

            Schema::create($model->getItemsTable(), function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->integer('inventory_id');
                $table->uuid('item_id');
                $table->integer('quantity')->default(1);
                $table->string('hash',500)->nullable();
            });
        });

    }
}
