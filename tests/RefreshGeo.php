<?php

namespace Leitom\Geo\Tests;

use Leitom\Geo\Facades\Geo;

trait RefreshGeo
{
    protected function tearDown(): void
    {
        Geo::clear();

        parent::tearDown();
    }
}
