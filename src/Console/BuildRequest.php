<?php

namespace Etlok\Crux\Console;

use Illuminate\Foundation\Console\RequestMakeCommand;

class BuildRequest extends RequestMakeCommand
{
    protected $name = 'build:request';

    protected $description = 'Create a new crux request';

    protected $type = 'Request';

    protected function getStub()
    {
        $stub = '/stubs/crux/request.php.stub';
        return $this->resolveStubPath($stub);
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    public function handle()
    {
        parent::handle();
    }

}