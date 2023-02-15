<?php

namespace Etlok\Crux\Console;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class BuildModel extends ModelMakeCommand
{
    protected $name = 'build:model';

    protected $description = 'Create a new crux model';

    protected $type = 'Model';

    protected function getStub()
    {
        $stub = '/stubs/crux/model.php.stub';
        return $this->resolveStubPath($stub);
    }

    public function handle()
    {
        parent::handle();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],
            ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model']
        ];
    }
}