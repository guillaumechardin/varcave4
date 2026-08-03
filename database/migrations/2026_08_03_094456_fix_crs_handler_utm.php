<?php

use App\Models\CoordinateSystemHandler;
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
        $crs = CoordinateSystemHandler::where('epsg_name', 'UTM/WGS84')
        ->firstOrFail();

        $crs->php_handler = 'proj4-crs-handler/326xx.php';
        $crs->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //nothing to reverse
    }
};
