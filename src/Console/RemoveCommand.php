<?php

namespace Leitom\Geo\Console;

use Illuminate\Console\Command;
use Leitom\Geo\Events\ModelsRemoved;
use Illuminate\Contracts\Events\Dispatcher;

class RemoveCommand extends Command
{
    protected $signature = 'geo:remove {model}';

    protected $description = 'Remove all records for the given model from the redis index';

    public function handle(Dispatcher $events)
    {
        $class = $this->argument('model');

        $model = new $class;

        $events->listen(ModelsRemoved::class, function ($event) use ($class) {
            $key = $event->models->last()->geoKey();
            $this->line('<comment>Removed ['.$class.'] models up to ID:</comment> '.$key);
        });

        $model::geoRemoveAll();

        $events->forget(ModelsRemoved::class);

        $this->info('All ['.$class.'] records have been removed.');
    }
}
