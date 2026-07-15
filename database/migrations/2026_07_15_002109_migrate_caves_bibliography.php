<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::table('caves', function (Blueprint $table) {
            $table->renameColumn('bibliography', 'bibliography_legacy');
            $table->json('bibliography')->nullable()->after('bibliography_legacy');
        });
        $this->migrateBibliography();

        DB::table('fields')
        ->where('key', 'bibliography')
        ->update(['data_type' => 'json']);
         
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('caves', function (Blueprint $table) {
            $table->dropColumn('bibliography');    
            $table->renameColumn('bibliography_legacy', 'bibliography');
        });

        DB::table('fields')
        ->where('key', 'bibliography')
        ->update(['data_type' => 'delimitedArray']);
    }

    public function migrateBibliography()
    {
        $caves = DB::table('caves')
        ->whereNotNull('bibliography_legacy')
        ->where('bibliography_legacy', '<>', '')
        ->orderBy('id')
        ->get();

        foreach ($caves as $cave) {

            $bibliography = $cave->bibliography_legacy;
            $data = [];

            // Save URL if found
            $urls = [];

            $bibliography = preg_replace_callback(
                '~https?://\S+~i',
                function ($matches) use (&$urls) {
                    $token = '__URL_' . count($urls) . '__';
                    $urls[$token] = $matches[0];
                    return $token;
                },
                $bibliography
            );

            // Split
            $items = preg_split(
                '/\s\/\s|•/u',
                $bibliography,
                -1,
                PREG_SPLIT_NO_EMPTY
            );

            foreach ($items as $item) {

                $itemUrl = null;

                // Find URL token in item
                foreach ($urls as $token => $url) {
                    if (str_contains($item, $token)) {
                        $itemUrl = $url;
                        $item = str_replace($token, '', $item);
                        break;
                    }
                }

                $data[] = [
                    'id' => (string) Str::ulid(),
                    'text' => trim(preg_replace('/\s+/', ' ', $item)),
                    'url' => $itemUrl,
                ];
            }


            DB::table('caves')
                ->where('id', $cave->id)
                ->update([
                    'bibliography' => json_encode($data, JSON_UNESCAPED_UNICODE),
                ]);
        }
    }
};
