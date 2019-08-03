<?php

namespace Leitom\Geo\Tests;

use Leitom\Geo\Facades\Geo;
use Leitom\Geo\Tests\Fixtures\GeoModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelSearchTest extends TestCase
{
    use RefreshDatabase, RefreshGeo;

    /** @test */
    public function search_for_the_given_coordinates_and_return_models()
    {
        $locationA = GeoModel::create(['name' => 'locationA', 'longitude' => -115.17087, 'latitude' => 36.12306]);
        $locationB = GeoModel::create(['name' => 'locationB', 'longitude' => -115.171971, 'latitude' => 36.120609]);

        $collection = GeoModel::geoSearch(-115.17258, 36.11996, 10)->get();

        $this->assertEquals(2, $collection->count());
        $this->assertTrue($locationA->is($collection->first()));
        $this->assertTrue($locationB->is($collection->last()));
        $this->assertEquals('km', $collection->first()->geoUnit);
        $this->assertEquals(0.3775, $collection->first()->geoDistance);
        $this->assertEquals(0.0906, $collection->last()->geoDistance);
        $this->assertArrayHasKey('geo_unit', $collection->first()->toArray());
        $this->assertArrayHasKey('geo_distance', $collection->first()->toArray());
    }

    /** @test */
    public function get_distance_between_a_given_model_and_another()
    {
        $locationA = GeoModel::create(['name' => 'locationA', 'longitude' => -115.17087, 'latitude' => 36.12306]);
        $locationB = GeoModel::create(['name' => 'locationB', 'longitude' => -115.171971, 'latitude' => 36.120609]);

        $this->assertEquals([
            'unit' => 'km',
            'distance' => 0.2900,
        ], $locationA->geoDistanceFrom($locationB));
    }

    /** @test */
    public function get_a_list_of_the_nearest_models_for_the_given_model()
    {
        $locationA = GeoModel::create(['name' => 'locationA', 'longitude' => -115.17087, 'latitude' => 36.12306]);
        $locationB = GeoModel::create(['name' => 'locationB', 'longitude' => -115.171971, 'latitude' => 36.120609]);

        $locations = $locationA->geoNearest()->get();

        $this->assertTrue($locations->last()->is($locationB));
    }
}
