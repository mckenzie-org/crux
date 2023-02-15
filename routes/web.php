<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

$resources = config('crux.routes.web');

if ($resources) {
    foreach ($resources as $resource => $data) {
        Route::resource($resource, $data['controller'])
            ->except(
                [
                    'store',
                    'update',
                    'destroy'
                ]
            )
            ->missing(
                function (\Illuminate\Http\Request $request) use ($resource) {
                    return Redirect::route($resource . '.index');
                }
            );
    }
}