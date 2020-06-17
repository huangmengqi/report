<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class Fb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yesterday Fb Revenue Data';

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
        $date = date("Y-m-d",strtotime("-1 day"));
        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->get();
        foreach ($gamelist as $key2 => $value2) {   
            $url = 'https://graph.facebook.com/v3.2/'.$value2->fb_app_id.'/adnetworkanalytics/?';
            $get_data = array (
                'since' => $date, 
                'until' => $date,
                'metrics'=>'fb_ad_network_revenue',
                'access_token' => env('ACCESS_TOKEN')
            );
            $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
            $res_ary = json_decode ( $res, true );
            if (! empty ( $res_ary['data'] )) {
                foreach ( $res_ary ['data'] [0] ['results'] as $k1 => $v1 ) {
                    $data = array ();
                    $data ['fb_ad_network_revenue'] = round($v1 ['value'],2);
                    $data ['game_id'] = $value2->id;
                    $data ['date'] = strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] )));
                    $count  = DB::table('mg_game_report')
                        ->where('game_id',$value2->id)
                        ->where('date',strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] ))))
                        ->count();
                    if($count!=0){
                        DB::table('mg_game_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] ))))
                            ->update(['fb_ad_network_revenue' => round($v1 ['value'],2)]);
                    }else{                        
                        DB::table('mg_game_report')->insert($data);
                    }

                }
            }
        }
        Log::info('每日Fb数据处理完成');
    }
}
