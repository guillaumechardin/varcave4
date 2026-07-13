<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaveCoordinates extends Model
{
    use HasFactory;

    // Mass assignable attributes
    protected $fillable = [
        'cave_id',
        'location',
        'z',
    ];

    protected static array $emptyCoords  = 
    [
        'x' => 0,
        'y' => 0,
        'lon' => 0,
        'lat' => 0,
        'z' => 0,
    ];
    
    //relation to cave table
    public function cave(): belongsTo
    {
        return $this->belongsTo(Cave::class);
    }

    /**
     * Retrieves cave coordinates from the database and checks user permissions
     * when the cave is geo-protected.
     *
     * If the user is not authorized to access the coordinates, an empty
     * collection is returned. Returns null if an unexpected error occurs.
     *
     * @param string $caveUuid UUID of the cave.
     * @param User|null $user User requesting the coordinates.
     *
     * @return Collection|null Collection of coordinates, an empty collection if
     *                         access is denied, or null on failure.
     */
    public static function get(string $caveUuid, $user): ?Collection
    {
        $cave = Cave::getByUuid($caveUuid);
        if(!$cave)
        {
            return null;
        }
        
        if(
            $cave->is_location_protected 
            && !$user->hasRole('admin')
        ){
            return collect([self::$emptyCoords]);
        }
        
        $coords =  self::where('cave_id', $cave->id)->selectRaw('id, ST_X(location) as x, ST_Y(location) as y, z');
 
        Log::debug(__METHOD__ . ' coordinates.', [
                        'caveId' => $cave->id,
                        'sql' => $coords->toSql(),
                        'bindings' => $coords->getBindings(),       
        ]);

        $results = $coords->get();
        if($results->isEmpty()) {
            return collect([self::$emptyCoords]);
        }
        
        return $coords->get()->map(function ($c) {
            return [
                'id'   => $c->id,
                'x'    => (float) $c->x,
                'y'    => (float) $c->y,
                'lon'  => (float) $c->x,
                'lat'  => (float) $c->y,
                'z'    => (float) $c->z,
            ];
        });
    }

    /**
     * Finds caves located within a specified radius of the given origin coordinates.
     *
     * The search is limited to a maximum number of caves and can exclude a
     * specific cave from the results. The returned collection may optionally
     * be formatted for direct use as a JavaScript array.
     *
     * @param Collection $origin Origin coordinates used as the search center.
     *                           Expected to contain the coordinate values required
     *                           by the distance calculation logic.
     * @param float $maxRadius Maximum search radius around the origin coordinates.
     * @param int $maxCavesToFind Maximum number of caves to return.
     * @param int $excludeCaveId Cave ID to exclude from the search results.
     * @param bool $jsarray When true, formats the result for JavaScript array usage.
     *
     * @return Collection Collection of nearby caves matching the search criteria.
     */
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

    public static function add(int $caveId, float $lon, float $lat, float $z): CaveCoordinates
    {
        Log::debug(__METHOD__ . ' called.');
        Log::info('Add a new coordinate set', [$lon, $lat, $z, $caveId]);

         $id = DB::table('cave_coordinates')->insertGetId([
            'cave_id' => $caveId,
            'location' => DB::raw("ST_PointFromText('POINT($lon $lat)')"),
            'z' => $z,
            'created_at' => now(),
        ]);

        Log::info('Coord set added: '. $id);

        return self::findOrFail($id);
    }
}