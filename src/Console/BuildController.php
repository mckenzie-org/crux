<?php

namespace Etlok\Crux\Console;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class BuildController extends ControllerMakeCommand
{
    protected $name = 'build:controller';

    protected $description = 'Create a new crux controller';

    protected $type = 'Controller';

    protected function getStub()
    {
        $stub = '/stubs/crux/controller.php.stub';
        return $this->resolveStubPath($stub);
    }

    public function handle()
    {
        parent::handle();
    }

    /**
     * Generate the form requests for the given model and classes.
     *
     * @param  string  $modelName
     * @param  string  $storeRequestClass
     * @param  string  $updateRequestClass
     * @return array
     */
    protected function generateFormRequests($modelClass, $storeRequestClass, $updateRequestClass)
    {
        $storeRequestClass = 'Store'.class_basename($modelClass).'Request';

        $this->call('build:request', [
            'name' => $storeRequestClass,
        ]);

        $updateRequestClass = 'Update'.class_basename($modelClass).'Request';

        $this->call('build:request', [
            'name' => $updateRequestClass,
        ]);

        return [$storeRequestClass, $updateRequestClass];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the controller already exists'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource controller for the given model.']
        ];
    }
}