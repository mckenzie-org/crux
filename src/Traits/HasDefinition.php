<?php

namespace Etlok\Crux\Traits;

trait HasDefinition
{
    public function definition($id = null)
    {
        $definition_path = config('crux.definitions_path');
        $def_name = $this->table.($id=== null?'':('('.$id.')'));
        $file = base_path($definition_path.'/'.$def_name.'.json');
        if(file_exists($file)) {
            return @json_decode(file_get_contents($file),true);
        }
        return [];
    }
}
