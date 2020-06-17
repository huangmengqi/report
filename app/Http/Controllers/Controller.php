<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Redis;
class Controller extends BaseController
{
   
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    
    /**
     * @Author woann <304550409@qq.com>
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return mixed
     * @description 接口返回数据格式
     */
    protected function json($code = 200,$msg = '',$data = [])
    {
        if ($data == []) {
            $res = [
                'code'  =>$code,
                'msg'   =>$msg,
            ];
        }else{
            $res = [
                'code'  =>$code,
                'msg'   =>$msg,
                'data'  =>$data
            ];
        }
        return response()->json($res)->header('Content-Type', 'text/html; charset=UTF-8');
    }
    function posturl($url,$data){
        $data  = json_encode($data);    
        $headerArray =array("Content-type:application/json;charset='utf-8'","Accept:application/json");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headerArray);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return json_decode($output);
    }
}
