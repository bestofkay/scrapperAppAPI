<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;

class BaseController extends Controller
{

    private function successResponse($data, $code)

    {
        return response()->json([$data, 'status'=>true, 'code'=>$code], $code);

    }

    protected function errorResponse($message, $code)

    {
        return response()->json(['error'=>$message, 'status'=>false, 'code'=>$code], $code);

    }

    protected function showAll(Collection $collection, $code=200){
        $collection = $this->sortData($collection);

        $collection = $this->filterData($collection);

        $collection = $this->paginate($collection);

        return $this->successResponse(['data'=>$collection], $code);
    }

    protected function showOne(Model $model, $code=200){
        return $this->successResponse(['data'=>$model], $code);
    }

    protected function saves($array, $code=201){
        return $this->successResponse($array, $code);
    }

    protected function showMessage($message, $code=201){
        return $this->successResponse(['data'=>$message], $code);
    }


    protected function password_generate($chars)
    {
      $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
      return substr(str_shuffle($data), 0, $chars);
    }

    protected function sortData(Collection $collection){
        if(request()->has('sort_by')){
            $attribute = request()->sort_by;
            $collection = $collection->sortBy->{$attribute};
        }
        return $collection;
    }

    protected function filterData(Collection $collection){
        foreach(request()->query() as $query => $value){
            $attribute = $query;
            if($attribute !='sort_by' && $attribute !='per_page' && $attribute !='page'){
            if(isset($attribute, $value)){
                $collection = $collection->where($attribute, $value);
            }
        }
        }

        return $collection;
    }


    protected function paginate(Collection $collection){
        $rules=[
            'per_page' => 'integer|min:5',
        ];
        Validator::validate(request()->all(), $rules);
        $perPage = 20;
        if(request()->has('per_page')){
            $perPage = (int)request()->per_page;
        }
        $output=$this->paginateCollection($collection, $perPage);
        return $output;
    }

    public function paginateCollection($collection, $perPage){
        $page = LengthAwarePaginator::resolveCurrentPage();
                $exact_link_num=1;
                $link_return=false;
                $links=request()->fullUrl();
                $links=explode('?', $links);
                if(count($links) > 1){
                $link_query=$links[1];
                $link_query=explode('&', $link_query);
                $x=0;
                $str='page';
                //$current_page=LengthAwarePaginator::resolveCurrentPage();
            // $str2='per_page';
                foreach($link_query as $li){
                    $li=explode('=', $li);
                    //$find=similar_text($li, $str, $pcent);
                    if($li[0]==$str){
                        $exact_link_num=$x;
                        $link_return=true;
                    break;
                    }
                    $x++;
                }

            }
            $last_page=8;

            $results = $collection->slice(($page - 1) * $perPage, $perPage)->values();
            $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
                'path'=>LengthAwarePaginator::resolveCurrentPath(),
            ] );
            $paginated->appends(request()->all());
            $output=$paginated->toArray();
            $link_path=array();
            if($collection->count() > $perPage){
            $num=0;

            $last_url=($output['current_page'] - $last_page) + 2;
            $current_page=$output['current_page'];
            if($current_page < $last_page){
                $last_url=1;
                $current_page=$last_page;
            }else{
                $current_page=$output['current_page'] + 1;
            }
                for($x=$last_url; $x <= $current_page; $x++){$num++;
                    if($num <= $last_page){
                    $link_query[$exact_link_num]='page='.$x;
                    $url=$links[0].'?'.implode('&', $link_query);
                // $o=$paginated->setCurrentPage($x, 'any');
                    $link_path[]=$url;
                    }else{
                    break;
                    }
                }
            }
                $output['links']=$link_path;
                return $output;
    }
/*
    public function paginateCollection($items, $perPage, $page=null, $options = []){
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
    */
}
