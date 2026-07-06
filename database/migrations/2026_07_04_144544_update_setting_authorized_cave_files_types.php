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
         DB::table('settings')
            ->where('name', 'authorized_cave_file_type')
            ->update([
                'value' => [
                "jpg",
                "jpeg",
                "png",
                "txt",
                "pdf",
                "doc",
                "docx",
                "xls",
                "xlsx",
                "ppt",
                "pptx",
                "zip",
                "odt",
            ],
                'type'  => 'json',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')
            ->where('name', 'authorized_cave_file_type')
            ->update([
                'value' => "empty set",
                'type'  => 'txt',
        ]);
    }
};
