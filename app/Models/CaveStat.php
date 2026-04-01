<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaveStat extends Model
{
    // Mass assignable attributes
    protected $fillable = [
        'cave_id',

    ];

    public function cave(): belongsTo
    {
        return $this->belongsTo(Cave::class);
    }

    public static function updateStat(Cave $cave, ?User $user = null)
    {
        Log::debug(__METHOD__ . ' called.', [$cave->uuid, $cave->id]);
    
        $statsExists = CaveStat::where('cave_id', $cave->id)
            ->exists();
        
        Log::debug('stat state:', [$statsExists]);
        
        $stat = CaveStat::firstOrCreate(
            ['cave_id' => $cave->id],
            [
                'auth_views' => 0,
                'anon_views' => 0,
            ] //values on creation
        );

        if ($user) {
            // user logged in
            Log::debug('Update cave stats as authenticated');
            DB::table('cave_stats')
            ->where('cave_id', $cave->id)
            ->increment('auth_views', 1, ['updated_at' => now() ]);
        } else {
            //non-auth user 
            Log::debug('Update cave stats as un-authenticated');
            DB::table('cave_stats')
            ->where('cave_id', $cave->id)
            ->increment('anon_views', 1, ['updated_at' => now() ]);
        }
    }

    public static function getGlobalStats()
    {
        $limit = Setting::get('displayed_stats');

        return DB::table('cave_stats as cs')
            ->join('caves as c', 'c.id', '=', 'cs.cave_id')
            ->select(
                'c.id',
                'c.uuid',
                'c.name',
                'cs.auth_views',
                'cs.anon_views',
                DB::raw('(cs.auth_views + cs.anon_views) as total_views')
            )
            //->orderByRaw('(cs.auth_views + cs.anon_views) DESC')
            ->orderByRaw('total_views DESC')
            ->limit($limit)
            ->get();
    }

}