<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class Tfb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tfb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Today Facebook Data';

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
        // $date = '2020-02-20';
        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->where('cate',1)->where('is_fan',1)->get();

        foreach ($gamelist as $key2 => $value2) {   
            dump($value2->name);
            $url = 'https://graph.facebook.com/v6.0/'.$value2->fb_app_id.'/adnetworkanalytics/?';
            $get_data = array (
                'since' => $date, 
                'until' => $date,
                'metrics'=>'fb_ad_network_revenue',
                'access_token' => env('ACCESS_TOKEN_FAN')
            );
            $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
            // dump($url . http_build_query ( $get_data ));exit;

            $res_ary = json_decode ( $res, true );
            dump($res_ary);
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
        Log::info('实时Fb数据处理完成');
    }
}
