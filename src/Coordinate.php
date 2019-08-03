<?php

namespace Leitom\Geo;

class Coordinate
{
    public $id;
    public $latitude;
    public $longitude;

    public function __construct($id, $longitude = null, $latitude = null)
    {
        $this->id = $id;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
}
