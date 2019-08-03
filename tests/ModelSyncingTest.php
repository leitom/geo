<?php

namespace Leitom\Geo\Tests;

use Leitom\Geo\Facades\Geo;
use Leitom\Geo\Events\ModelsRemoved;
use Leitom\Geo\Events\ModelsImported;
use Leitom\Geo\Tests\Fixtures\GeoModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelSyncingTest extends TestCase
{
    use RefreshDatabase, RefreshGeo;

    /** @test */
    public function add_location_when_a_model_is_saved()
    {
        $this->expectsEvents(ModelsImported::class);

        tap(new GeoModel(['name' => 'locationA', 'longitude' => -115.17087, 'latitude' => 36.12306]))->save();

        $this->assertEquals([
            1 => '0.3775',
        ], Geo::search(-115.17258, 36.11996, 10));
    }

    /** @test */
    public function remove_location_when_a_model_is_deleted()
    {
        $this->expectsEvents(ModelsRemoved::class);

        tap(new GeoModel(['name' => 'locationA', 'longitude' => -115.17087, 'latitude' => 36.12306]), function ($model) {
            $model->save();
        })->delete();

        $this->assertEmpty(Geo::search(-115.17258, 36.11996, 10));
    }

    /** @test */
    public function add_multiple_locations_when_collection_is_used()
    {
        $this->expectsEvents(ModelsImported::class);

        tap(new GeoModel(['name' => 'locationA', 'longitude' => -115.17087, 'latitude' => 36.12306]), function ($model) {
            $model->geoEnabled = false;
        })->save();

        tap(new GeoModel(['name' => 'locationB', 'longitude' => -115.171971, 'latitude' => 36.120609]), function ($model) {
            $model->geoEnabled = false;
        })->save();

        $this->assertEmpty(Geo::search(-115.17258, 36.11996, 10));

        GeoModel::all()->addGeo();

        $this->assertEquals([
            1 => 0.3775,
            2 => 0.0906,
        ], Geo::search(-115.17258, 36.11996, 10));
    }

    /** @test */
    public function add_multiple_locations_directly_from_database()
    {
        $this->expectsEvents(ModelsImported::class);

        tap(new GeoModel(['name' => 'locationA', 'longitude' => -115.17087, 'latitude' => 36.12306]), function ($model) {
            $model->geoEnabled = false;
        })->save();

        tap(new GeoModel(['name' => 'locationB', 'longitude' => -115.171971, 'latitude' => 36.120609]), function ($model) {
            $model->geoEnabled = false;
        })->save();

        $this->assertEmpty(Geo::search(-115.17258, 36.11996, 10));

        GeoModel::whereNull('deleted_at')->addGeo();

        $this->assertEquals([
            1 => 0.3775,
            2 => 0.0906,
        ], Geo::search(-115.17258, 36.11996, 10));
    }

    /** @test */
    public function remove_multiple_locations_when_collection_is_used()
    {
        $this->expectsEvents(ModelsRemoved::class);

        tap(new GeoModel(['name' => 'locationA', 'longitude' => -115.17087, 'latitude' => 36.12306]))->save();
        tap(new GeoModel(['name' => 'locationB', 'longitude' => -115.171971, 'latitude' => 36.120609]))->save();

        $this->assertNotEmpty(Geo::search(-115.17258, 36.11996, 10));

        GeoModel::all()->removeGeo();

        $this->assertEmpty(Geo::search(-115.17258, 36.11996, 10));
    }

    /** @test */
    public function remove_multiple_locations_directly_from_database()
    {
        $this->expectsEvents(ModelsRemoved::class);

        tap(new GeoModel(['name' => 'locationA', 'longitude' => -115.17087, 'latitude' => 36.12306]))->save();
        tap(new GeoModel(['name' => 'locationB', 'longitude' => -115.171971, 'latitude' => 36.120609]))->save();

        $this->assertNotEmpty(Geo::search(-115.17258, 36.11996, 10));

        GeoModel::whereNull('deleted_at')->removeGeo();

        $this->assertEmpty(Geo::search(-115.17258, 36.11996, 10));
    }
}
