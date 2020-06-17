<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;
use Mail;

class Page extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Email Monitor Page';

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
    public function handle(){
        //查询page页列表
        $pagelist = DB::table('page_status')->get();
        
        foreach ($pagelist as $key => $value) {
            $res = (new IndexController)->curl_get_https ('https://graph.facebook.com/'.$value->page_id.'?fields=is_published,name&access_token='.env('ACCESS_TOKEN_PAGE'));
            $res_ary = json_decode ( $res, true );
            dump($res_ary);

            if($value->status == '0' && $value->is_verify == '0'){
                foreach ($email1 as $key1 => $value1) {
                    if($res_ary['is_published'] == false){
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
	                    $content="【Mobchang】：".$value->page_name.$value1."page页unpublished通知，page页！";//要发送的短信内容
	                    $phone = "13480576164";//要发送短信的手机号码
	                    $smsapi = 'https://api.smsbao.com/sms?u='.$user.'&p='.$pass.'&m='.$phone.'&c='.$content; // $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
	                    $result =file_get_contents($smsapi) ;
	                    echo $statusStr[$result];
                    }
                }
            }else{
            	if($res_ary['is_published']=='true'){
	                DB::table('page_status')->where('page_id',$res_ary['id'])->update(['status' => 1]);
	            }else{
	                DB::table('page_status')->where('page_id',$res_ary['id'])->update(['status' => 0]);
	            }
            }
            
            
            
        }


        /*foreach ($pagelist as $k => $v) {
            if($v->is_verify=='1'){
            $res1 = (new IndexController)->curl_get_https ('https://graph.facebook.com/'.$v->page_id.'?fields=is_published,name&access_token='.env('ACCESS_TOKEN_PAGE'));
            $res_ary1 = json_decode ( $res1, true );
            //dump($res_ary1);
            }
            $value22 = 'shirley18sss@gmail.com,carol.wang@mobchang.com';// 
            $email11 = explode(',',$value22);

            if($v->status == '0' && $v->is_verify == '1'){
                foreach ($email11 as $k1 => $v1) {
                    if($res_ary1['is_published'] == true){
                        $flag = Mail::send('admin.send_pageemail1',['err'=>'page页unpublished','name'=>$v->page_name,'url'=>'https://www.facebook.com/'.$v->page_name.'-'.$v->page_id],function($message) use ($value1){
                            $to = $v1;
                            $message ->to($to)->subject('page页unpublished通知');
                        });
                        DB::table('page_status')->where('page_id',$res_ary1['id'])->update(['is_verify' => 0]);
                        if(!$flag){
                            Log::info($v->page_name.$v1.'page页unpublished邮件发送成功');
                            dump($v->page_name.$value1.'page页unpublished邮件发送成功');
                        }else{
                            Log::info($v->page_name.$v1.'page页unpublished邮件发送失败');
                            dump($v->page_name.$v1.'page页unpublished邮件发送失败');
                        }
                    }
                }
            }
            
            if($res_ary1['is_published']=='true'){
                DB::table('page_status')->where('page_id',$res_ary1['id'])->update(['status' => 1]);
            }else{
                DB::table('page_status')->where('page_id',$res_ary1['id'])->update(['status' => 0]);
            }
            
        }*/
    }
}
