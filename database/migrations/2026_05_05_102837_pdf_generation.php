<?php

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
        //Add pdf author
        DB::table('settings')->insert([
            'name' =>'pdf_author',
            'value' => 'CDS 83',
            'type'  => 'string',
            'category' => 'pdf',
            'is_advanced_option' => 0,
            'legacy_mtime' => 0,
        ]);

        //optionnal keywords
        DB::table('settings')->insert([
            'name' =>'keywords',
            'value' => '',
            'type'  => 'string',
            'category' => 'pdf',
            'is_advanced_option' => 0,
            'legacy_mtime' => 0,
        ]);

        //optionnal keywords
        DB::table('settings')->insert([
            'name' =>'pdf_header_title',
            'value' => 'Fichier des Cavités du Var',
            'type'  => 'string',
            'category' => 'pdf',
            'is_advanced_option' => 0,
            'legacy_mtime' => 0,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')
            ->whereIn('name', ['keywords', 'pdf_author', 'pdf_header_title'])
            ->delete();
    }
};
