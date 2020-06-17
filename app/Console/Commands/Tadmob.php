<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;
use Redis;

class Tadmob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tadmob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Today Admob Data';

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
        if(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $date = date("Y-m-d");
        }else{
            $date = date("Y-m-d",strtotime("-1 day"));
        }  
        // $date = '2019-05-06';
        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->get();
        foreach ($gamelist as $key => $value) { 
            $url = 'https://www.googleapis.com/adsense/v1.4/accounts/'.$value->am_account_id.'/reports?metric=EARNINGS&';
                $get_data = array (
                    'startDate' => $date,
                    'endDate' => $date,
                    'currency' => 'USD',
                    'dimension' => 'APP_NAME',
                    'metric'=>'INDIVIDUAL_AD_IMPRESSIONS_RPM',
                    'access_token' => Redis::get($value->am_account_id.'access_token')
                );
                $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
                $res_ary = json_decode ( $res, true );
                // dump(Redis::get($value->am_account_id.'access_token'));
                dump($res_ary);
                if (! empty ( $res_ary['rows'])) {
                    foreach ( $res_ary['rows'] as $k1 => $v1 ) {
                        if($v1['0'] == $value->am_app_name){
                            $data = array ();
                            $data ['am_revenue'] = round($v1 ['1'],2);
                            $data ['am_cpm'] = round($v1 ['2'],2);
                            $data ['game_id'] = $value->id;
                            $data ['date'] = strtotime($date);
                            $count  = DB::table('mg_game_report')
                                ->where('game_id',$value->id)
                                ->where('date',strtotime($date))
                                ->count();

                            if($count!=0){
                                DB::table('mg_game_report')
                                    ->where('game_id',$value->id)
                                    ->where('date',strtotime($date))
                                    ->update(['am_revenue' => round($v1 ['1'],2),'am_cpm' => round($v1 ['2'],2)]);
                            }else{                        
                                DB::table('mg_game_report')->insert($data);
                            }
                        }
                    }
                }
            }
        Log::info('实时Admob数据处理完成');
    }
}
