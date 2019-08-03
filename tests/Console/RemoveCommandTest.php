<?php

namespace Leitom\Geo\Tests\Console;

use Leitom\Geo\Facades\Geo;
use Leitom\Geo\Tests\TestCase;
use Leitom\Geo\Tests\RefreshGeo;
use Leitom\Geo\Tests\Fixtures\GeoModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RemoveCommandTest extends TestCase
{
    use RefreshDatabase, RefreshGeo;

    /** @test */
    public function remove_all_records_of_the_given_model()
    {
        tap(new GeoModel(['name' => 'locationA', 'longitude' => -115.17087, 'latitude' => 36.12306]))->save();
        tap(new GeoModel(['name' => 'locationB', 'longitude' => -115.171971, 'latitude' => 36.120609]))->save();

        $this->assertNotEmpty(Geo::search(-115.17258, 36.11996, 10));

        $this->artisan('geo:remove', ['model' => GeoModel::class]);

        $this->assertEmpty(Geo::search(-115.17258, 36.11996, 10));
    }
}
