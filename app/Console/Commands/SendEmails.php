<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;
use Mail;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendemail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send appstatus';

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

        foreach ($list as $key => $value) {
            $email = explode(',',$value->bussiness_email);
            $package=$value->al_package_name;
            if(!empty($package)&&$package != '0'){
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
                if($httpcode=='404'){

                    // 关闭所有campaign止损
                    $access_token = env('ACCESS_TOKEN_CLOSECAMPAIGN');
                    $account = explode(',',$value->fb_read_accounts);
                    foreach ($account as $key1 => $value1) {
                        //遍历这个超额账户下所有状态为ACTIVE的campaign
                        $url = 'https://graph.facebook.com/v6.0/act_'.$value1.'/campaigns?fields=status,name&limit=2000&access_token='.$access_token;
                        $res = (new IndexController)->curl_get_https ($url);
                        $res_ary = json_decode ( $res, true );
                        dump($res_ary);
                        if(!empty($res_ary['data'])){
                            //关闭这个账户下所有campaign
                            foreach ($res_ary['data'] as $key2 => $value2) {
                                if($value2['status'] == 'ACTIVE'){
                                    $url1 = 'https://graph.facebook.com/v5.0/'.$value2['id'].'?';
                                    $get_data1 = array (
                                        'status' => 'PAUSED',
                                        'access_token' => $access_token
                                    );
                                    $res = (new IndexController)->curl_post_https ( $url1.http_build_query($get_data1 ));
                                    dump(json_decode($res));
                                    dump('关闭'.$value2['name'].'('.$value2['id'].')'.'campaign完成');
                                    Log::info('关闭'.$value2['name'].'('.$value2['id'].')'.'campaign完成');
                                }
                            }
                        }
                    }


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
                    $content="【Mobchang】：".$value->name."产品下架，请检查您的账户！";//要发送的短信内容
                    $phone = "15712648232,13429415700,13480576164,18623943972";//要发送短信的手机号码
                    $smsapi = 'https://api.smsbao.com/sms?u='.$user.'&p='.$pass.'&m='.$phone.'&c='.$content; // $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
                    $result =file_get_contents($smsapi) ;
                    // 更新应用状态为下架，避免重复发送短信
                    DB::table('mg_game')->where('id',$value->id)->update(['status'=>'0']);
                    dump('更新应用状态为下架');
                    echo $statusStr[$result];
                }else{
                    echo $httpcode;               
                }
            }
        }
    
    }
}
