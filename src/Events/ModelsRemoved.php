<?php

namespace Leitom\Geo\Events;

class ModelsRemoved
{
    public $models;

    public function __construct($models)
    {
        $this->models = $models;
    }
}
