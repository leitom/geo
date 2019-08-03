<?php

namespace Leitom\Geo;

use Leitom\Geo\Events\ModelsRemoved;
use Leitom\Geo\Events\ModelsImported;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class GeoScope implements Scope
{
    public function apply(EloquentBuilder $builder, Model $model)
    {
        //
    }

    public function extend(EloquentBuilder $builder)
    {
        $builder->macro('addGeo', function (EloquentBuilder $builder, $chunk = null) {
            $builder->chunk($chunk ?: config('geo.chunk', 500), function ($models) {
                $models->filter->geoEnabled()->addGeo();
                event(new ModelsImported($models));
            });
        });

        $builder->macro('removeGeo', function (EloquentBuilder $builder, $chunk = null) {
            $builder->chunk($chunk ?: config('geo.chunk', 500), function ($models) {
                $models->removeGeo();
                event(new ModelsRemoved($models));
            });
        });
    }
}
