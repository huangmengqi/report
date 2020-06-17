<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;
use Mail;

class Ctr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ctr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send ctr';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        

        $list=DB::table('mg_game')->where('status',1)->get();
        if(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $date = date("Y-m-d");
        }else{
            $date = date("Y-m-d",strtotime("-1 day"));
        }  
        foreach ($list as $key => $value) {

        	// 获取请求超过500点击率超过20%的投放国家
        	$country = DB::table('ctr_unit')->where('game_id',$value->id)->where('date',strtotime($date))->whereNotBetween('request', [0, 500])->where('ctr','>','0.2')->select('country_code','ctr','request')->get();
        	$code = [];
        	foreach ($country as $key1 => $value1) {
        		$code[] = $value1->country_code;
        	}
        	if(count($code) == '0'){
        		dump($value->name."数据正常！");
        	}else{
        		$countrycode = json_encode($code);
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
	            $content="【Mobchang】：".$value->name."的投放国家".$countrycode."点击率过高,请调整投放！";//要发送的短信内容
	            // $phone = "15712648232,13429415700,13480576164,18623943972";//要发送短信的手机号码
                $phone = "15236409332";
	            $smsapi = 'https://api.smsbao.com/sms?u='.$user.'&p='.$pass.'&m='.$phone.'&c='.$content; // $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
	            $result =file_get_contents($smsapi) ;
	            echo $statusStr[$result];
        	}
            // exit;
        }
    
    }
}
