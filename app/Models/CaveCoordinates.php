<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class caveCoordinates extends Model
{
    use HasFactory;

    // Mass assignable attributes
    protected $fillable = [
        'cave_id',
        'location',
        'z',
        //'protected',
    ];

    //relation to cave table
    public function cave(): belongsTo
    {
        return $this->belongsTo(Cave::class);
    }

    public static function get(string $caveUuid): ?Collection
    {
        $cave = Cave::getByUuid($caveUuid);
        if(!$cave)
        {
            return null;
        }
        
        $coords =  self::where('cave_id', $cave->id)->selectRaw('ST_X(location) as x, ST_Y(location) as y, z');
 
        Log::debug(__METHOD__ . ' coordinates.', [
                        'caveId' => $cave->id,
                        'sql' => $coords->toSql(),
                        'bindings' => $coords->getBindings(),       
        ]);

        $results = $coords->get();
        if($results->isEmpty()) {
            return collect([
                [
                    'x' => 0,
                    'y' => 0,
                    'lon' => 0,
                    'lat' => 0,
                ],
            ]);
        }
        
        return $coords->get()->map(function ($c) {
            return [
                'x'    => (float) $c->x,
                'y'    => (float) $c->y,
                'lon' => (float) $c->x,
                'lat'  => (float) $c->y,
                'z'    => (float) $c->z,
            ];
        });
    }

    public static function findNearCaves(
        Collection $origin,
        float $maxRadius,
        int $maxCavesToFind,
        int $excludeCaveId,
        bool $jsarray = false
    ): Collection
    {
        if ($origin->isEmpty()) {
            return collect(); 
        }
        
        $poi = $origin->first();    
        $originLon = $poi['x'];
        $originLat = $poi['y'];

        $caves = DB::table('cave_coordinates as cc')
            ->join('caves', 'cc.cave_id', '=', 'caves.id')
            ->select(
                'caves.uuid',
                'caves.name',
                'cc.z',
                DB::raw('ST_AsText(cc.location) as coords'),
                DB::raw('ST_X(cc.location) as longitude'),
                DB::raw('ST_Y(cc.location) as latitude'),
                DB::raw("ST_Distance_Sphere(Point(?, ?), Point(ST_X(cc.location), ST_Y(cc.location))) as distance")
            )
            ->where('caves.id', '<>', $excludeCaveId)
            ->havingRaw("distance < ?", [$maxRadius])
            ->orderBy('distance', 'asc')
            //->orderBy('cave.name', 'asc')
            ->limit($maxCavesToFind)
            ->setBindings([$originLon, $originLat], 'select') // bindings for ST_Distance_Sphere
            ->get();

        return $caves->map(fn($c) => [
                'uuid' => $c->uuid,
                'name' => $c->name,
                'lat' => (float)$c->latitude,
                'lon' => (float)$c->longitude,
                'z' => (float)$c->z,
                'distance' => (int)$c->distance,
            ]);
    }
}