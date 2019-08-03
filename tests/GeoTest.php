<?php

namespace Leitom\Geo\Tests;

use Leitom\Geo\Coordinate;
use Leitom\Geo\Facades\Geo;

class GeoTest extends TestCase
{
    use RefreshGeo;

    /** @test */
    public function add_a_single_geo_location()
    {
        Geo::index('cars')->add(new Coordinate('my-car', -115.17087, 36.12306));

        $this->assertEquals([
            'my-car' => '0.3775',
        ], Geo::search(-115.17258, 36.11996, 10));
    }

    /** @test */
    public function add_multiple_geo_locations()
    {
        Geo::index('cars')->add([
            new Coordinate('my-car', -115.17087, 36.12306),
            new Coordinate('robins-car', -115.171971, 36.120609),
        ]);

        $this->assertEquals([
            'my-car' => 0.3775,
            'robins-car' => 0.0906,
        ], Geo::search(-115.17258, 36.11996, 10));
    }

    /** @test */
    public function records_the_previous_search_results()
    {
        Geo::index('cars')->add([
            new Coordinate('my-car', -115.17087, 36.12306),
            new Coordinate('robins-car', -115.171971, 36.120609),
        ]);

        Geo::search(-115.17258, 36.11996, 10);

        $this->assertEquals([
            'my-car' => 0.3775,
            'robins-car' => 0.0906,
        ], Geo::previousGeoSearch());
    }

    /** @test */
    public function remove_a_geo_location_by_key()
    {
        Geo::index('cars')->add(new Coordinate('my-car', -115.17087, 36.12306));

        $this->assertNotEmpty(Geo::search(-115.17258, 36.11996, 10));

        Geo::remove('my-car');

        $this->assertEmpty(Geo::search(-115.17258, 36.11996, 10));
    }

    /** @test */
    public function remove_multiple_geo_locations_by_keys()
    {
        Geo::index('cars')->add([
            new Coordinate('my-car', -115.17087, 36.12306),
            new Coordinate('robins-car', -115.171971, 36.120609),
        ]);

        Geo::remove('my-car', 'robins-car');

        $this->assertEmpty(Geo::search(-115.17258, 36.11996, 10));
    }

    /** @test */
    public function clean_all_by_index()
    {
        Geo::index('cars')->add([
            new Coordinate('my-car', -115.17087, 36.12306),
            new Coordinate('robins-car', -115.171971, 36.120609),
        ]);

        Geo::clear('cars');

        $this->assertEmpty(Geo::search(-115.17258, 36.11996, 10));
    }

    /** @test */
    public function get_distance_between_two_locations()
    {
        Geo::index('cars')->add([
            $locationA = new Coordinate('my-car', -115.17087, 36.12306),
            $locationB = new Coordinate('robins-car', -115.171971, 36.120609),
        ]);

        $this->assertEquals(0.2900, Geo::between($locationA, $locationB));
    }

    /** @test */
    public function get_distance_from_a_given_location_to_the_rest()
    {
        Geo::index('cars')->add([
            $locationA = new Coordinate('my-car', -115.17087, 36.12306),
            $locationB = new Coordinate('robins-car', -115.171971, 36.120609),
        ]);

        $this->assertEquals([
            'my-car' => 0.0000,
            'robins-car' => 0.2900,
        ], Geo::from($locationA, 10));
    }
}
