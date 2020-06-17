<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;
use Redis;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;
class CloseCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'closecampaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close Campaign';

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
    	

        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->where('cate',1)->get();
        if(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $today = strtotime(date("Y-m-d"));
        }else{
            $today = strtotime(date('Y-m-d', strtotime('-1 day')));
        }
       $access_token = env('ACCESS_TOKEN_CLOSECAMPAIGN');
        foreach ($gamelist as $key => $value) {
            dump($value->name);
            $data = DB::table('mg_game_report')->where('game_id',$value->id)->where('date',$today)->first(); 
            $revenue = $data->fb_ad_network_revenue+$data->is_revenue+$data->mp_revenue+$data->al_revenue+$data->am_revenue;
            $profit = $revenue - $data->cost;
            if($data->cost!=0&&$data->install_count!=0){
                $cpi = round($data->cost/$data->install_count,2);
            }else{
                $cpi = 0;
            }
            // $email1 = explode(',',$value->alarm_email);
            dump($profit);
            $profitup = $profit * -1;
            
            dump($profitup);
            if($value->monitor_profit){
                $monitor_profit = $value->monitor_profit;
            }else{
                $monitor_profit = 50;
            }
            if($profitup >= $monitor_profit){
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
                
            }
        }
        Log::info('检查利润关闭campaign完成');
    }
}
