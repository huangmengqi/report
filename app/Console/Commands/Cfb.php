<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class Cfb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cfb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Country Facebook Revenue';

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
        // $date = '2020-02-24';
        $fb_metrics = array (
            'fb_ad_network_revenue',
            'fb_ad_network_cpm'
            // 'fb_ad_network_fill_rate' 
        );
        // $date = '2019-03-09';
        $gamelist = DB::table('mg_game')->where('status',1)->where('cate',1)->where('is_fan',1)->get();
        foreach ( $gamelist as $g ) {
            foreach ( $fb_metrics as $v ) {
                $url = 'https://graph.facebook.com/v6.0/'.$g->fb_app_id.'/adnetworkanalytics/?';
                $get_data = array (
                    'metrics' => '["' . $v . '"]',
                    'since' => $date,
                    'until' => $date,
                    'breakdowns' => '["country"]',
                    'access_token' => env('ACCESS_TOKEN_FAN')
                );
                
                $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
                $res_ary = json_decode ( $res, true );
                dump($res_ary);
                if (! empty ( $res_ary['data'] )) {
                    foreach ( $res_ary ['data'] [0] ['results'] as $k1 => $v1 ) {
                        // dump($v1);
                        $data = array ();
                        $data [$v] = $v1 ['value'];
                        $data ['game_id'] = $g->id;
                        $data ['country_code'] = $v1['breakdowns']['0']['value'];
                        $data ['date'] = strtotime($date);
                        $count  = DB::table('mg_game_country_report')
                            ->where('game_id',$g->id)
                            ->where('date',strtotime($date))
                            ->where('country_code',$v1['breakdowns']['0']['value'])
                            ->count();
                        if($count!=0){
                            DB::table('mg_game_country_report')
                                ->where('game_id',$g->id)
                                ->where('date',strtotime($date))
                                ->where('country_code',$v1['breakdowns']['0']['value'])
                                ->update([$v => $v1 ['value']]);
                        }else{                        
                            DB::table('mg_game_country_report')->insert($data);
                        }
                    }
                }
            }
        }
        Log::info('实时分国家Fb数据处理完成');
    }
}
