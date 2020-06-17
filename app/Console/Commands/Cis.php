<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class Cis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Country Ironsource Data';

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
        if(date("H:i:s") >= '16:00:00' && date("H:i:s") <= '24:00:00'){
            $date = date("Y-m-d");
        }else{
            $date = date("Y-m-d",strtotime("-1 day"));
        }  
        // $date = "2019-12-15";
        //查询游戏列表
        $gamelist = DB::table('mg_game')/*->where('mg_game.upltv_id','!=',0)*/->where('status','!=',0)->get();
        foreach ($gamelist as $key2 => $value2) {

            $crl = curl_init();
            $base64encoded = base64_encode("andappsok@gmail.com:867f7d76baad8c2968864eb03e388bfd");
            $header = array();
            $header[] = 'Authorization: Basic '. $base64encoded;
            $url1 = 'https://platform.ironsrc.com/partners/publisher/mediation/applications/v5/stats?';
            if($value2->is_app_key != '0'){
                $get_data = array (
                    'startDate' => $date,
                    'endDate' => $date,
                    'metrics' => 'revenue,impressions',
                    'breakdowns' => 'country',
                    'appKey' => $value2->is_app_key     //Snake Bubble
                );
                $URL = $url1.http_build_query($get_data);
                curl_setopt($crl, CURLOPT_URL, $URL);
                curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1); //设置不直接输出
                curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
                $response = curl_exec($crl);
                curl_close($crl);
                $res = json_decode($response,true);
                dump($res);
                $array = [];
                foreach ($res as $key => $value) {
                    if(!empty($value['data'])){
                        foreach ($value['data'] as $key => $value) {

                            
                            if(!isset($array[$value['countryCode']])){
                                $array[$value['countryCode']] = $value['revenue'];
                            }else{
                                $array[$value['countryCode']] += $value['revenue'];
                            }
                        }
                    }
                }
                foreach ($array as $key3 => $value3) {
                    $data = array ();
                    $data ['is_revenue'] = $value3;
                    $data ['game_id'] = $value2->id;
                    $data ['date'] = strtotime($date);
                    $data ['country_code'] = $key3;
                    $count  = DB::table('mg_game_country_report')
                        ->where('game_id',$value2->id)
                        ->where('date',strtotime($date))
                        ->where('country_code',$key3)
                        ->count();
                    if($count!=0){
                        DB::table('mg_game_country_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime($date))
                            ->where('country_code',$key3)
                            ->update(['is_revenue' => $value3]);
                    }else{                        
                        DB::table('mg_game_country_report')->insert($data);
                    }
                }
            }
        }
        dump('实时分国家ironsource处理完成');
        Log::info('实时分国家ironsource处理完成');
    }
}
