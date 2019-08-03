<?php

namespace Leitom\Geo\Console;

use Illuminate\Console\Command;
use Leitom\Geo\Events\ModelsImported;
use Illuminate\Contracts\Events\Dispatcher;

class ImportCommand extends Command
{
    protected $signature = 'geo:import {model}';

    protected $description = 'Import all records from the given model into the redis index';

    public function handle(Dispatcher $events)
    {
        $class = $this->argument('model');

        $model = new $class;

        $events->listen(ModelsImported::class, function ($event) use ($class) {
            $key = $event->models->last()->geoKey();
            $this->line('<comment>Imported ['.$class.'] models up to ID:</comment> '.$key);
        });

        $model::geoImportAll();

        $events->forget(ModelsImported::class);

        $this->info('All ['.$class.'] records have been imported.');
    }
}
