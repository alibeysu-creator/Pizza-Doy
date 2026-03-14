<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_extras', function (Blueprint $table) {
            if (!Schema::hasColumn('item_extras', 'apply_to_all')) {
                $table->tinyInteger('apply_to_all')->default(\App\Enums\Ask::NO)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_extras', function (Blueprint $table) {
            if (Schema::hasColumn('item_extras', 'apply_to_all')) {
                $table->dropColumn('apply_to_all');
            }
        });
    }
};
