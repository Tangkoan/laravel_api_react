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
        Schema::table('roles', function (Blueprint $table) {
            // លុប field ឈ្មោះ test_colunm ចោល
            $table->dropColumn('test');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // បង្កើតវាមកវិញ ករណីយើងចង់ rollback (ការពារការបាត់បង់ទិន្នន័យ)
            $table->string('test')->nullable();
        });
    }
};
