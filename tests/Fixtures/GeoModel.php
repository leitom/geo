<?php

namespace Leitom\Geo\Tests\Fixtures;

use Leitom\Geo\HasGeoAbilities;
use Illuminate\Database\Eloquent\Model;

class GeoModel extends Model
{
    use HasGeoAbilities;

    protected $fillable = ['id', 'name', 'longitude', 'latitude'];
}
