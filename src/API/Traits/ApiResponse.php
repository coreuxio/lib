<?php

namespace Coreux\lib\API\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Coreux\Lib\API\Contracts\PaginationTransformer;
use Coreux\lib\API\Exceptions\APIValidationException;

trait ApiResponse
{
    public array $response = [
        "status" => 'success',
        "data" => null,
        "errors"=> [],
        "meta" => [],
        "pagination"=>null
    ];

    public int $statusCode = 200;

    public function apiReturn($items,int $code = 200): \Illuminate\Http\JsonResponse
    {
        $this->response['data'] = $items;
        $this->statusCode = $code;
        $this->setMeta();
        return response()->json($this->response,$code);
    }

    public function apiReturnPaginated($pagination,PaginationTransformer $paginationTransformer = null): \Illuminate\Http\JsonResponse
    {
        $data = $pagination->toArray();
        if(is_null($paginationTransformer)){
            $this->response['data'] = $data['data'];
        }else{
            $this->response['data'] = $paginationTransformer->transform($data['data']);
        }
        $this->statusCode = 200;
        $this->setPagination($data);
        return response()->json($this->response, $this->statusCode);
    }


    public function apiError($error,array $errors = [],int $code = 400): \Illuminate\Http\JsonResponse
    {
        $this->response['data'] = $error;
        $this->response['status'] = "error";
        $this->response['errors'] = $errors;
        $this->statusCode = $code;
        $this->setMeta();
        return response()->json($this->response,$code);
    }

    public function setPagination(array $paginationArray): void
    {

        if(isset($paginationArray['data'])){
            unset($paginationArray['data']);
        }
        $this->response['pagination'] = $paginationArray;
    }

    public function setMeta(array $extra=null)
    {
        if(!$this->response['meta']) $this->response['meta'] = [];
        if(!isset($this->response['meta']['milliseconds']))
        {
            $start = defined("APP_START") ? APP_START : false;
            $time  = $start===false?0: floor((microtime(true)-$start)*1000);
            $this->response['meta'] = array_merge(['milliseconds'=>$time ],$this->response['meta']);
        }
        if(!isset($this->response['meta']['httpCode']))
        {
            $this->response['meta'] = array_merge(['httpCode'=>$this->statusCode],$this->response['meta']);
        }
        if(is_array($extra))
        {
            $this->response['meta'] = array_merge($extra,$this->response['meta']);
        }
        $this->response['meta']['timestamp']=Carbon::now()->toDateTimeString();
        return $this;
    }

    /**
     * @throws APIvalidationException
     */
    public function apiValidation(array $items , array $validationRules, array $validationMessages = null): array
    {
        $validator = Validator::make($items,$validationRules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            throw new APIvalidationException("Validation Error",400,null,$errors);
        }
        return $validator->getData();
    }

    /**
     * @throws APIValidationException
     */
    public function getSimplePaginationParams(Request $request): array
    {
        $params = $this->apiValidation($request->all(),[
            "perPage"=>"nullable|integer|max:50",
            "page"=>"nullable|integer"
        ]);
        $page = $params['page'] ?? 1;
        $perPage = $params['perPage'] ?? 10;
        return ["page"=>$page,"perPage"=>$perPage];
    }

    /**
     * @throws APIValidationException
     */
    public function getCursorPaginationParams(Request $request): array
    {
        $params = $this->apiValidation($request->all(),[
            "perPage"=>"nullable|integer|max:50",
            "cursor"=>"nullable|string"
        ]);
        $cursor = $params['cursor'] ?? null;
        $perPage = $params['perPage'] ?? 10;
        return ["cursor"=>$cursor,"perPage"=>$perPage];
    }
}
