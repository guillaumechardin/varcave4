<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('featured_caves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cave_id');
            $table->boolean('is_active');
            $table->timestamps();
            $table->foreign('cave_id')->references('id')->on('caves')->restrictOnDelete();
        });
        
        DB::table('settings')->insert([
            'name' =>'featured_caves_delay',
            'value' => 7200,
            'type'  => 'numeric',
            'category' => 'general',
            'is_advanced_option' => 0,
            'legacy_mtime' => 0,
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_caves');

        DB::table('settings')
            ->where('name', 'featured_caves_delay')
            ->delete();
    }

};
