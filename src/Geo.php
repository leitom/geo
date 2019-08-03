<?php

namespace Leitom\Geo;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

class Geo
{
    protected $app;
    protected $index;
    protected $config;
    protected $indexCache = [];
    protected $previousGeoSearch = [];

    public function __construct($app)
    {
        $this->app = $app;
        $this->config = $app['config']->get('geo');
    }

    public function index($index)
    {
        $this->indexCache[] = $this->index = $index;

        return $this;
    }

    public function search($longitude, $latitude, $radius, $sort = 'ASC')
    {
        return $this->formatResults(
            Redis::georadius($this->resolveIndex(), $longitude, $latitude, $radius, $this->config['unit'], 'WITHDIST', $sort)
        );
    }

    public function between($locationA, $locationB)
    {
        return Redis::geodist($this->resolveIndex(), $locationA->id, $locationB->id, $this->config['unit']);
    }

    public function from($location, $radius, $sort = 'ASC')
    {
        return $this->formatResults(
            Redis::georadiusbymember($this->resolveIndex(), $location->id, $radius, $this->config['unit'], 'WITHDIST', $sort)
        );
    }

    public function add(...$locations)
    {
        if (! Arr::first($locations) instanceof Coordinate) {
            $locations = Arr::collapse($locations);
        }

        Redis::pipeline(function ($pipe) use ($locations) {
            foreach ($locations as $coordinate) {
                $pipe->geoadd($this->resolveIndex(), $coordinate->longitude, $coordinate->latitude, $coordinate->id);
            }
        });

        return $this;
    }

    public function remove(...$ids)
    {
        Redis::pipeline(function ($pipe) use ($ids) {
            foreach ($ids as $id) {
                $pipe->zrem($this->resolveIndex(), $id);
            }
        });

        return $this;
    }

    public function clear($indexes = null)
    {
        $indexes = $indexes ?? $this->indexCache;

        if (empty($indexes)) {
            return;
        }

        $indexes = Collection::make($indexes)->map(function ($index) {
            return $this->resolveIndex($index);
        })->all();

        Redis::del($indexes);
    }

    public function previousGeoSearch($index = null)
    {
        return Arr::get($this->previousGeoSearch, $this->resolveIndex($index ?? $this->index), []);
    }

    protected function formatResults($results)
    {
        return $this->previousGeoSearch[$this->resolveIndex()] = Collection::make($results)->mapWithKeys(function ($result) {
            return [$result[0] => (float) $result[1]];
        })->all();
    }

    protected function resolveIndex($index = null)
    {
        $index = $index ?? $this->index;

        return $this->app->environment() === 'testing' ? "testing.{$index}" : $index;
    }
}
