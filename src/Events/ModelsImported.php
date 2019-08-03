<?php

namespace Leitom\Geo\Events;

class ModelsImported
{
    public $models;

    public function __construct($models)
    {
        $this->models = $models;
    }
}
