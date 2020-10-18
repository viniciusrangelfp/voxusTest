<?php

namespace Voxus\App\Controller;
use Voxus\App\Model\User;

class UserController extends Controller {
    public function get($params,$request,$response){
       $id = $params[0];
       $user = new User();
       $data = $user->findById($id);
       if($data->id > 0){
            $geo = new GeoLocation($data->lat,$data->long);
            $data->address = $geo->address;
           $response->setData(['status'=>'success','users'=>$data]);
       }else{
           $response->setData(['status'=>'fail','error'=>'No data found']);
       }

       return $response;
    }

    public function getAll($params,$request,$response){
       $page = $request->query->get('page');

        $max_items = 10;
        if(!$page){
            $page = 1;
        }

        $rowCount = $page * $max_items;

        $user = new User();
        $data = $user->find([],'LIMIT '.$page.', '.$rowCount);
        if(count($data) > 0){
            $response->setData(['status'=>'success','users'=>$data]);
        }else{
            $response->setData(['status'=>'fail','error'=>'No data found']);
        }

        return $response;

    }

    public function post($params,$request,$response){
        try{
            $param = json_decode($request->getContent());
            $user = new User();
            $user->name = $param->name;
            $user->lat = $param->lat;
            $user->long = $param->long;

            $user->save();
            if($user->id > 0){
                $response->setData(['status'=>'success','created_user'=>$user->id]);
            }else{
                $response->setData(['status'=>'fail','created_user'=>'Wrong data format']);
            }

        }catch (\Exception $e){
            $response->setData(['status'=>'fail','error'=>$e]);
        }

        return $response;
    }
}
