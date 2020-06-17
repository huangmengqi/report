<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Admin\IndexController;
use Redis;
use Illuminate\Support\Facades\Log;

class PhoneController extends Controller
{
    //
	public function Phone(){
       $gamelist = DB::table('mg_game')->where('status',1)->get();
        if(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $today = strtotime(date("Y-m-d"));
        }else{
            $today = strtotime(date('Y-m-d', strtotime('-1 day')));
        }
        foreach ($gamelist as $key => $value) {
            $data = DB::table('mg_game_report')->where('game_id',$value->id)->where('date',$today)->first(); 
            $revenue = $data->fb_ad_network_revenue+$data->is_revenue+$data->mp_revenue+$data->al_revenue+$data->am_revenue;
            $profit = $revenue - $data->cost;
            
            if(!empty($value->monitor_phone_number)) { 
                $number=array();
                $number = explode(',',$value->monitor_phone_number); 
                $b=$number[0];
            for ($sum=1;$sum<count($number);$sum++) {
                
                $b=$b.','.$number[$sum];
                
            }
           
            if($profit < ($value->monitor_profit > 0 ? -1 * $value->monitor_profit : abs($value->monitor_profit))){
                $statusStr = array(
                        "0" => "短信发送成功",
                        "-1" => "参数不全",
                        "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
                        "30" => "密码错误",
                        "40" => "账号不存在",
                        "41" => "余额不足",
                        "42" => "帐户已过期",
                        "43" => "IP地址限制",
                        "50" => "内容含有敏感词"
                );
                $user = "mobhe"; //短信平台帐号
                $pass = md5("shanghai"); //短信平台密码
                $content="【上海点畅网络科技有限公司】：账户亏损严重，请检查您的账户！";//要发送的短信内容
                $phone = "$b";//要发送短信的手机号码
                $smsapi = 'https://api.smsbao.com/sms?u='.$user.'&p='.$pass.'&m='.$phone.'&c=%E3%80%90%E4%B8%8A%E6%B5%B7%E7%82%B9%E7%95%85%E7%BD%91%E7%BB%9C%E7%A7%91%E6%8A%80%E6%9C%89%E9%99%90%E5%85%AC%E5%8F%B8%E3%80%91%EF%BC%9A%E8%B4%A6%E6%88%B7%E4%BA%8F%E6%8D%9F%E4%B8%A5%E9%87%8D%EF%BC%8C%E8%AF%B7%E6%A3%80%E6%9F%A5%E6%82%A8%E7%9A%84%E8%B4%A6%E6%88%B7%EF%BC%81'; // $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
                $result =file_get_contents($smsapi) ;
                echo $statusStr[$result];
                
                }
            }    
        }        
	}
}