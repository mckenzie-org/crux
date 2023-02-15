<?php
namespace Etlok\Crux\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BuildDefinition extends Command {

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    protected $signature = 'build:definition {name}';

    protected $description = 'Build a definition file for the model';

    public function handle()
    {
        $model = strtolower($this->argument('name'));

        $path = $this->getPath();
        $this->makeDirectory($path);
        $this->files->put($path, $this->buildDefinition($model));

        $this->info('Definition Created Successfully!');

    }

    public function buildDefinition($model)
    {
        $stub = $this->files->get($this->getStub());
        return $this->replaceModel($stub,$model);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceModel($stub, $model)
    {
        $pl_model = Str::plural($model);
        $model_name = Str::ucfirst(Str::camel($model));
        $searches = [
            ['__model__', '__pl_model__', '__title__']
        ];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [$model, $pl_model, $model_name],
                $stub
            );
        }

        return $stub;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath()
    {
        $definitions_path = config('crux.definitions_path');

        return $this->laravel['path'].'/'.$definitions_path;
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }
}