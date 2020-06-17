<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;
use Mail;

class Email extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Email Monitor';

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
        //查询游戏列表
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
            if($data->cost!=0&&$data->install_count!=0){
                $cpi = round($data->cost/$data->install_count,2);
            }else{
                $cpi = 0;
            }
            $email1 = explode(',',$value->alarm_email);
            /*foreach ($email1 as $key1 => $value1) {
                if($value1){
                    if($cpi > $value->monitor_cpi){
                        $flag = Mail::send('admin.send_email',['err'=>'产品CPI异常','name'=>$value->name,'cpi'=>$cpi,'profit'=>$profit,'install'=>$data->install_count],function($message) use ($value1){
                            $to = $value1;
                            $message ->to($to)->subject('产品CPI异常通知');
                        });
                        if(!$flag){
                            Log::info($value->name.$value1.'异常邮件发送成功');
                        }else{
                            Log::info($value->name.$value1.'异常邮件发送失败');
                        }
                    }
                }
            }*/
            dump($profit);
            foreach ($email1 as $key2 => $value2) {
                if($value2){
                    $profitup = $profit * -1;
                    // if($profit < ($value->monitor_profit > 0 ? -1 * $value->monitor_profit : abs($value->monitor_profit))){
                    if($profitup >= $value->monitor_profit){
                        $flag = Mail::send('admin.send_email',['err'=>'产品利润异常','name'=>$value->name,'cpi'=>$cpi,'profit'=>$profit,'install'=>$data->install_count],function($message) use ($value2){
                            $to = $value2;
                            $message ->to($to)->subject('产品利润异常通知');
                        });
                        if(!$flag){
                            Log::info($value->name.$value1.'异常邮件发送成功');
                        }else{
                            Log::info($value->name.$value1.'异常邮件发送失败');
                        }
                    }
                }
            }
            /*foreach ($email1 as $key3 => $value3) {
                if($value3){
                    if($data->install_count > $value->monitor_install){
                        $flag = Mail::send('admin.send_email',['err'=>'产品量级异常','name'=>$value->name,'cpi'=>$cpi,'profit'=>$profit,'install'=>$data->install_count],function($message) use ($value3){
                            $to = $value3;
                            $message ->to($to)->subject('产品量级异常通知');
                        });
                        if(!$flag){
                            Log::info($value->name.$value1.'异常邮件发送成功');
                        }else{
                            Log::info($value->name.$value1.'异常邮件发送失败');
                        }
                    }
                }
            }*/
        }
    }
}