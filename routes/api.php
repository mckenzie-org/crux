<?php
use Illuminate\Support\Facades\Route;

$resources = config('crux.routes.api');

if($resources) {
    foreach ($resources as $resource=>$data) {
        $controller = $data['controller'];
        Route::get('/'.$resource.'/paginated', [$controller,'paginated']);
        Route::get('/'.$resource.'/all', [$controller,'all']);
        Route::get('/'.$resource.'/definition', [$controller,'definition']);
        Route::get('/'.$resource.'/{id}', [$controller,'getById']);
        Route::get('/'.$resource.'/{id}/{child}/paginated', [$controller,'childrenPaginated']);
        Route::get('/'.$resource.'/{id}/{child}/all', [$controller,'allChildren']);
        Route::resource($resource, $controller)
            ->only([
                'store', 'update', 'destroy'
            ])
            ->missing(function(\Illuminate\Http\Request $request) use ($resource){
                return response()->json([
                    'status'=>1,
                    'errorMessage'=>[
                        'title'=>'Not Found!',
                        'text'=>'Unable to find {$resource}.'
                    ],
                ],404);
            });

        Route::post('/'.$resource.'/{id}/{child}/{child_id}', [$controller,'attach']);
        Route::delete('/'.$resource.'/{id}/{child}/{child_id}', [$controller,'detach']);
        Route::put('/'.$resource.'/{id}/{child}/{child_id}/update', [$controller,'pivotUpdate']);
    }
}