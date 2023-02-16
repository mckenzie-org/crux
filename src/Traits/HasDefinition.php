<?php

namespace Etlok\Crux\Traits;

trait HasDefinition
{
    public function definition()
    {
        $definition_path = config('crux.definitions_path');
        $file = base_path($definition_path.'/'.$this->table.'.json');
        if(file_exists($file)) {
            return @json_decode(file_get_contents($file),true);
        }
        return [];
    }
}
