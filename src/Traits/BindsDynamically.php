<?php

namespace Etlok\Crux\Traits;

trait BindsDynamically
{
    protected $connection = null;
    protected $table = null;

    public function bind(string $connection, string $table)
    {
        $this->setConnection($connection);
        $this->setTable($table);
    }

    public function newInstance($attributes = [], $exists = false)
    {
        // Overridden in order to allow for late table binding.

        $model = parent::newInstance($attributes, $exists);
        $model->setTable($this->table);

        return $model;
    }

    public function replicate(array $except = null)
    {
        // Overridden in order to allow for late table binding.
        $instance = parent::replicate($except);
        $instance->setTable($this->table);

        return $instance;
    }
}
