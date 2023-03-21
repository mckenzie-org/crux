<?php

namespace Etlok\Crux\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class CruxModelController extends CruxBaseController {

    public $model = '';
    public $modelPlural = '';
    public $modelType = '';

    public function setModel($model)
    {
        $this->modelType = $model;
        $this->modelPlural = Str::plural($model);
        $this->model = 'App\Models\\'.Str::studly($model);
    }

    /**
     * @return JsonResponse
     */
    public function definition()
    {
        $obj = (new $this->model);
        if(!method_exists($obj,'definition')) {
            return response()->json([
                'status'=>1,
                'errorMessage'=>[
                    'title'=>'Configuration Error!',
                    'text'=>'Unable to find model definition'
                ]
            ],404);
        }
        $definition = $obj->definition();
        return response()->json([
            'status'=>0,
            'definition'=>$definition
        ],200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->view('crux::generic.index',[
            'model'=>$this->modelPlural
        ]);
    }

    private function processFilters($filters, &$query) {

        if($filters) {
            foreach ($filters as $k=>$filter) {
                if(!isset($filter['_t']) || $filter['_t'] === 's') {
                    $query->where($filter['k'], $filter['v']);
                } else {
                    switch ($filter['_t']) {
                        case 'o':
                            if(!isset($filter['o'])) {
                                $filter['o'] = '=';
                            }
                            $query->where([
                                [$filter['k'],$filter['o'],$filter['v']]
                            ]);
                            break;
                        case 'in':
                            $query->whereIn($filter['k'], $filter['v']);
                            break;
                        case 'null':
                            $query->whereNull($filter['k']);
                            break;
                        case 'nonull':
                            $query->whereNotNull($filter['k']);
                            break;
                        case 'c':
                            $query->whereHas($filter['c'], function($q) use ($filter) {
                                $q->whereIn($filter['k'], $filter['v']);
                            });
                            break;
                        case 'has':
                            if(isset($filter['o']) && isset($filter['v'])) {
                                $query->has($filter['k'],$filter['o'],$filter['v']);
                            } else {
                                $query->has($filter['k']);
                            }
                            break;
                        case 'search':

                            if($filter['k'] && $filter['v'] !== '') {
                                $where = "(";
                                foreach($filter['k'] as $i=>$term) {

                                    $where .= ($i==0?"":" OR ").$term." LIKE '%".$filter['v']."%' ";
                                }
                                $where .= ")";
                                if(isset($filter['c'])) {
                                    $query->whereHas($filter['c'], function($q) use ($where) {
                                        $q->whereRaw($where);
                                    });
                                } else {
                                    $query->whereRaw($where);
                                }

                            }
                            break;
                        case 'lookup':
                            if($filter['k'] && $filter['v'] !== '') {
                                $where = "(";
                                $where .= $filter['k']." LIKE '".$filter['v']."%' ";
                                $where .= ")";
                                $query->whereRaw($where);
                            }


                            break;
                    }
                }
            }
        }
    }


    private function processMetadata($query, $fields, &$data)
    {
        if ($fields) {
            foreach ($fields as $field) {
                $f = explode(':',$field);
                if(isset($f[1])) {
                    switch($f[0]) {
                        case 'max':
                            $data[$field] = $query->max($f[1]);
                            break;
                        case 'min':
                            $data[$field] = $query->min($f[1]);
                            break;
                        case 'count':
                            $data[$field] = $query->count();
                            break;
                    }
                }
            }
        }
    }

    /**
     * List of all resources
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request)
    {
        $query = $this->model::whereNotNull('id');
        $meta = [];
        $filters = [];
        if($request->has('filters')) {
            $filters = @json_decode($request->filters,true);
            $this->processFilters($filters,$query);
        }

        if($request->has('with')) {
            $query->with($request->with);
        }

        $sort= [
            'k'=>'id',
            'd'=>'desc'
        ];

        if($request->has('sort')) {
            $sort = @json_decode($request->sort,true);
        }

        $results = $query->orderBy($sort['k'],$sort['d'])->get();

        if($request->has('meta')) {
            $mfields = $request->meta;
            $this->processMetadata($query, $mfields, $meta);
        }

        return response()->json([
            'status'=>0,
            $this->modelPlural=>$results,
            'metadata'=>$meta
        ]);
    }

    /**
     * List resources paginated
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginated(Request $request){

        $query = $this->model::whereNotNull('id');
        $meta = [];
        $filters = [];
        if($request->has('filters')) {
            if(gettype($request->filters) === 'string') {
                $filters = @json_decode($request->filters,true);
            } else {
                $filters = $request->filters;
            }
            $this->processFilters($filters,$query);
        }

        if($request->has('with')) {
            $query->with($request->with);
        }

        $sort= [
            'k'=>'id',
            'd'=>'desc'
        ];

        if($request->has('sort')) {
            if(gettype($request->sort) === 'string') {
                $sort = @json_decode($request->sort,true);
            } else {
                $sort = $request->sort;
            }
        }

        $results = $query->orderBy($sort['k'],$sort['d'])->paginate();

        if($request->has('meta')) {
            $mfields = $request->meta;
            $this->processMetadata($query, $mfields, $meta);
        }

        return response()->json([
            'status'=>0,
            $this->modelPlural=>$results,
            'metadata'=>$meta
        ]);
    }

    /**
     * List resources paginated
     * @return \Illuminate\Http\JsonResponse
     */
    public function childrenPaginated(Request $request, $id,$child){

        $obj = $this->model::find($id);
        if(!$obj) {
            return response()->json([
                'status'=>1,
                'errorMessage'=>[
                    'title'=>'Not Found!',
                    'text'=>'Unable to find '.$this->modelType
                ]
            ],404);
        }
        if(!method_exists($obj, $child)) {
            return response()->json([
                'status'=>1,
                'errorMessage'=>[
                    'title'=>'Not Found!',
                    'text'=>'Unable to find '.$child
                ]
            ],404);
        }

        $query = $obj->$child();
        $meta = [];
        $filters = [];
        if($request->has('filters')) {
            $filters = @json_decode($request->filters,true);
            $this->processFilters($filters,$query);
        }

        if($request->has('with')) {
            $query->with($request->with);
        }

        $sort= [
            'k'=>'id',
            'd'=>'desc'
        ];

        if($request->has('sort')) {
            $sort = @json_decode($request->sort,true);
        }

        $results = $query->orderBy($sort['k'],$sort['d'])->paginate();

        if($request->has('meta')) {
            $mfields = $request->meta;
            $this->processMetadata($query, $mfields, $meta);
        }

        return response()->json([
            'status'=>0,
            $child=>$results,
            'metadata'=>$meta
        ]);
    }

    /**
     * List resources paginated
     * @return \Illuminate\Http\JsonResponse
     */
    public function allChildren(Request $request, $id,$child){

        $obj = $this->model::find($id);
        if(!$obj) {
            return response()->json([
                'status'=>1,
                'errorMessage'=>[
                    'title'=>'Not Found!',
                    'text'=>'Unable to find '.$this->modelType
                ]
            ],404);
        }
        if(!method_exists($obj, $child)) {
            return response()->json([
                'status'=>1,
                'errorMessage'=>[
                    'title'=>'Not Found!',
                    'text'=>'Unable to find '.$child
                ]
            ],404);
        }

        $query = $obj->$child();
        $meta = [];
        $filters = [];
        if($request->has('filters')) {
            $filters = @json_decode($request->filters,true);
            $this->processFilters($filters,$query);
        }

        if($request->has('with')) {
            $query->with($request->with);
        }

        $sort= [
            'k'=>'id',
            'd'=>'desc'
        ];

        if($request->has('sort')) {
            $sort = @json_decode($request->sort,true);
        }

        $results = $query->orderBy($sort['k'],$sort['d'])->get();

        if($request->has('meta')) {
            $mfields = $request->meta;
            $this->processMetadata($query, $mfields, $meta);
        }

        return response()->json([
            'status'=>0,
            $child=>$results,
            'metadata'=>$meta
        ]);
    }

    /**
     * Get resource by id
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getById(Request $request, $id)
    {
        if($request->has('with')) {
            $with = $request->input('with');
            $obj = $this->model::with($with)->find($id);
        } else {
            $obj = $this->model::find($id);
        }

        if(!$obj) {
            return response()->json([
                'status'=>1,
                'errorMessage'=>[
                    'title'=>'Not Found!',
                    'text'=>'Unable to find {$this->modelType}.'
                ]
            ],404);
        }


        return response()->json([
            'status'=>0,
            'object'=>$obj
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view('crux::generic.create',[
            'model'=>$this->modelPlural,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Array $validated
     * @return \Illuminate\Http\JsonResponse
     */
    public function save($validated)
    {
        $obj = $this->performSave($validated);
        return response()->json([
            'status'=>0,
            $this->modelType=>$obj
        ]);
    }

    /***
     * @param $validated
     * @return mixed
     */
    protected function performSave($validated){
        if(isset($validated['id'])) {
            $obj = $this->model::find($validated['id']);
            unset($validated['id']);
        } else {
            $obj = new $this->model;
        }

        $obj->fill($validated);
        $obj->save();
        return $obj;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->view('crux::generic.show',[
            'model'=>$this->modelPlural,
            'id'=>$id
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->view('crux::generic.edit',[
            'model'=>$this->modelPlural,
            'id'=>$id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $obj = $this->model::find($id);
        if(!$obj) {
            return response()->json([
                'status'=>1,
                'errorMessage'=>[
                    'title'=>'Not Found!',
                    'text'=>'Unable to find {$this->modelType}.'
                ]
            ],404);
        }

        $obj->delete();
        return response()->json(['status'=>0]);
    }

    /**
     * @param int $id
     * @param String $child
     * @param int $child_id
     * @param array $validated
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePivot($id, $child, $child_id, $validated)
    {
        $obj = $this->model::find($id);
        if(!$obj) {
            return response()->json([
                'status'=>1,
                'errorMessage'=>[
                    'title'=>'Not Found!',
                    'text'=>'Unable to find {$this->modelType}.'
                ]
            ],404);
        }
        if(!method_exists($obj,$child)) {
            return response()->json([
                'status'=>1,
                'errorMessage'=>[
                    'title'=>'Not Found!',
                    'text'=>'Unable to find {$child}.'
                ]
            ],404);
        }

        $obj->$child()->updateExistingPivot($child_id,$validated);
        return response()->json(['status'=>0],200);

    }

    public function attach($id, $child, $child_id, Request $request)
    {
        $obj = $this->model::find($id);

        if(!$obj) {
            return response()->json([
                'status'=>1,
                'errorMessage'=>[
                    'title'=>'Not Found!',
                    'text'=>'Unable to find {$this->modelType}.'
                ]
            ],404);
        }
        $data = $request->input();
        $obj->$child()->attach($child_id,$data);

        return response()->json(['status'=>0]);
    }

    public function detach($id, $child, $child_id)
    {
        $obj = $this->model::find($id);

        if(!$obj) {
            return response()->json([
                'status'=>1,
                'errorMessage'=>[
                    'title'=>'Not Found!',
                    'text'=>'Unable to find {$this->modelType}.'
                ]
            ],404);
        }

        $obj->$child()->detach($child_id);


        return response()->json(['status'=>0]);
    }



}