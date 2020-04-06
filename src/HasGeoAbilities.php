<?php

namespace Leitom\Geo;

use Illuminate\Support\Arr;
use Leitom\Geo\Facades\Geo;
use Illuminate\Support\Facades\DB;
use Leitom\Geo\Events\ModelsRemoved;
use Leitom\Geo\Events\ModelsImported;
use Illuminate\Support\Collection as BaseCollection;

trait HasGeoAbilities
{
    public $geoEnabled = true;

    public static function bootHasGeoAbilities()
    {
        static::addGlobalScope(new GeoScope);

        static::observe(new ModelObserver);

        (new static)->registerGeoMacros();
    }

    public function registerGeoMacros()
    {
        $self = $this;

        BaseCollection::macro('addGeo', function () use ($self) {
            $self->addGeo($this);
        });

        BaseCollection::macro('removeGeo', function () use ($self) {
            $self->removeGeo($this);
        });
    }

    public function addGeo($models)
    {
        if ($models->isEmpty()) {
            return;
        }

        Geo::index($models->first()->geoIndex())->add($models->map->toCoordinate());

        event(new ModelsImported($models));
    }

    public function removeGeo($models)
    {
        if ($models->isEmpty()) {
            return;
        }

        Geo::index($models->first()->geoIndex())->remove($models->map->geoKey()->all());

        event(new ModelsRemoved($models));
    }

    public static function geoImportAll()
    {
        $self = new static;

        $self->newQuery()->orderBy($self->geoKeyName())->addGeo();
    }

    public static function geoRemoveAll()
    {
        $self = new static;

        $self->newQuery()->orderBy($self->geoKeyName())->removeGeo();
    }

    public function geoDistanceFrom($model)
    {
        return [
            'unit' => $this->geoUnit,
            'distance' => (float) Geo::between($this->toCoordinate(), $model->toCoordinate()),
        ];
    }

    public function scopeGeoNearest($builder, $radius = 10)
    {
        $locations = array_keys(Geo::index($this->geoIndex())->from($this->toCoordinate(), $radius));

        $builder->whereIn($this->geoKeyName(), $locations);

        if ($this->databaseDriver() !== 'sqlite' && ! empty($locations)) {
            $builder->orderByRaw(DB::raw(sprintf('FIELD(%s,%s)', $this->geoKeyName(), implode(',', $locations))));
        }
    }

    public function scopeGeoSearch($builder, $longitude, $latitude, $radius, $sort = 'ASC')
    {
        $locations = array_keys(Geo::index($this->geoIndex())->search($longitude, $latitude, $radius, $sort));

        $builder->whereIn($this->geoKeyName(), $locations);

        if ($this->databaseDriver() !== 'sqlite' && ! empty($locations)) {
            $builder->orderByRaw(DB::raw(sprintf('FIELD(%s,%s)', $this->geoKeyName(), implode(',', $locations))));
        }
    }

    public function getGeoUnitAttribute()
    {
        return config('geo.unit');
    }

    public function getGeoDistanceAttribute()
    {
        if ($this->geoEnabled() && array_key_exists($this->geoKey(), $results = Geo::previousGeoSearch($this->geoIndex()))) {
            return Arr::get($results, $this->geoKey());
        }

        return 0;
    }

    public function geoEnabled()
    {
        return $this->geoEnabled;
    }

    public function geoIndex()
    {
        return $this->getTable();
    }

    public function geoKeyName()
    {
        return $this->getKeyName();
    }

    public function geoKey()
    {
        return $this->getKey();
    }

    public function geoLongitude()
    {
        return $this->longitude;
    }

    public function geoLatitude()
    {
        return $this->latitude;
    }

    public function toCoordinate()
    {
        return new Coordinate($this->geoKey(), $this->geoLongitude(), $this->geoLatitude());
    }

    public function toArray()
    {
        $attributes = parent::toArray();

        if ($this->geoEnabled()) {
            $attributes['geo_unit'] = $this->geoUnit;
            $attributes['geo_distance'] = $this->geoDistance;
        }

        return $attributes;
    }

    protected function databaseDriver()
    {
        $connection = config('database.default');

        return config("database.connections.{$connection}.driver");
    }
}
