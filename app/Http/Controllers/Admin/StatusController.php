<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\MobpubImport;
use Excel;
use Redis;
use Google_Client;
use Google_Service_Drive;
use Storage;
use DB;
use Hhxsv5\PhpMultiCurl\Curl;
use Hhxsv5\PhpMultiCurl\MultiCurl;
use Google\Auth\OAuth2;
use Mail;

class StatusController extends Controller
{
    public function Status(){
     /*   function GetHttpStatusCode($url){ 
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);//获取内容url
        curl_setopt($curl,CURLOPT_HEADER,1);//获取http头信息
        curl_setopt($curl,CURLOPT_NOBODY,1);//不返回html的body信息
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);//返回数据流，不直接输出
        curl_setopt($curl,CURLOPT_TIMEOUT,30); //超时时长，单位秒
        curl_exec($curl);
        $rtn= curl_getinfo($curl,CURLINFO_HTTP_CODE);
        curl_close($curl);
        return  $rtn;
    }
    $url="https://play.google.com/store/apps/details?id=com.floatingtunes.youtubeplayer.topmusic";
    $res=GetHttpStatusCode($url); dump($res);exit();



    	$list=DB::table('mg_game')->where('status',1)->get();

    	foreach ($list as $key => $value) {
    		//$email = explode(',',$value->bussiness_email);dump($email);
    		$package=$value->al_package_name;
    		if(!empty($package)){
    		//$res = @file_get_contents('https://play.google.com/store/apps/details?id='.$package);
                $ch = curl_init('https://play.google.com/store/apps/details?id='.$package);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_exec($ch);
                echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

			if($res){
    			echo $res;
			}else{
    			// echo "404";exit();
    			$email = '1060116113@qq.com';
    			$flag = Mail::send('admin.send_appstatus',['name'=>$value->name,'id'=>$package],function($message) use ($email){
                        $to = $email;
                        $message ->to($to)->subject('产品异常通知');
                    });
                    if(!$flag){
                        echo '发送邮件成功，请查收！';
                    }else{
                        echo '发送邮件失败，请重试！';
                    }
				}
    	    }
    	}*/
        $list=DB::table('mg_game')->where('status',1)->get();dump($list);
$sum=0;
        foreach ($list as $key => $value) {
            $email = explode(',',$value->bussiness_email);
            $package=$value->al_package_name;
            if(!empty($package)){
            $curl = curl_init();
				$url='https://play.google.com/store/apps/details?id='.$package;
				curl_setopt($curl, CURLOPT_URL, $url); //设置URL
				curl_setopt($curl, CURLOPT_HEADER, 1); //获取Header
				curl_setopt($curl,CURLOPT_NOBODY,true); //Body就不要了吧，我们只是需要Head
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //数据存到成字符串吧，别给我直接输出到屏幕了
				curl_exec($curl); //开始执行啦～
				$httpcode=curl_getinfo($curl,CURLINFO_HTTP_CODE); //我知道HTTPSTAT码哦～
				curl_close($curl); //用完记得关掉他
				echo $httpcode;
            if($httpcode=='200'){
                dump($value->name.'-----'.$value->al_package_name.'-----'.'1111');$sum++;
            }else{
                //echo "404";               
                $flag = Mail::send('admin.send_appstatus',['name'=>$value->name,'id'=>$package],function($message) use ($email){
                        $to = $email;
                        $message ->to($to)->subject('产品异常通知');
                    });
                    if(!$flag){
                        echo '发送邮件成功，请查收！';
                        
                    }else{
                        echo '发送邮件失败，请重试！';
                    }
                }
            }
        }dump($sum);
    }

}
