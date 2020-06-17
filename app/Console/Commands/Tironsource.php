<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class Tironsource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tironsource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Today Ironsource Data';

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
        // $date = "2019-12-15";
        //查询游戏列表
        $gamelist = DB::table('mg_game')/*->where('mg_game.is_app_key','!=',0)*/->where('status','!=',0)->get();
        // dump($gamelist);exit;


        //ironsource数据
        foreach ($gamelist as $key2 => $value2) {
            dump($value2->name);
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
                $sumisrevenue = 0;
                if(!empty($res)){

                
                foreach ($res as $key => $value) {
                    // dump($value->name);

                    if(empty($value['data'])){
                        $null = '0';
                    }else{
                        $null = $value['data'][0]['revenue'];
                    }


                    $sumisrevenue += $null;
                    $data = array ();
                    $data ['is_revenue'] = $sumisrevenue;
                    $data ['game_id'] = $value2->id;
                    $data ['date'] = strtotime($date);
                    $count  = DB::table('mg_game_report')
                        ->where('game_id',$value2->id)
                        ->where('date',strtotime($date))
                        ->count();
                    if($count!=0){
                        DB::table('mg_game_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime($date))
                            ->update(['is_revenue' => $sumisrevenue]);
                    }else{                        
                        DB::table('mg_game_report')->insert($data);
                    }
                }
                }
            }
        }
        dump('实时ironsource处理完成');
        Log::info('实时ironsource处理完成');
    }
}
