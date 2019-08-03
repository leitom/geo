<?php

namespace Leitom\Geo;

use Leitom\Geo\Facades\Geo;
use Illuminate\Support\Collection;
use Leitom\Geo\Events\ModelsRemoved;
use Leitom\Geo\Events\ModelsImported;

class ModelObserver
{
    public function saved($model)
    {
        if (! $model->geoEnabled()) {
            return;
        }

        $coordinate = new Coordinate(
            $model->geoKey(), $model->geoLongitude(), $model->geoLatitude()
        );

        Geo::index($model->geoIndex())->add($coordinate);

        event(new ModelsImported(Collection::make([$model])));
    }

    public function deleted($model)
    {
        if (! $model->geoEnabled()) {
            return;
        }

        Geo::index($model->geoIndex())->remove($model->geoKey());

        event(new ModelsRemoved(Collection::make([$model])));
    }
}
