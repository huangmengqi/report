<?php

namespace App\Console\Commands;
use App\Http\Controllers\Controller;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;
use Redis;
class Tadx extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tadx';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yesterday Adx';

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
        if(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $date = date("Y-m-d");
        }else{
            $date = date("Y-m-d",strtotime("-1 day"));
        }  
        // $date = '2019-09-12';
        $url = 'http://ads.api.woso.cn/api/v2/request.ashx?';
        $nonce = rand(1000,999999999);
        $timeStamp = time();
        $signature = $timeStamp.$nonce.'10053gskEvCP9aTTLBbAIYLEYpP6MO9rJ39GTRjQ4';
        $get_data = array (
            'wsopenId' => '10053',
            'timeStamp' => $timeStamp,
            'nonce' => $nonce,
            'signature' => strtoupper(MD5($signature)),
            'stattype' => '13',
            'datetype' => '1',
           /* 'start_date' => $date,
            'end_date' => $date,*/
        );
        dump($get_data);
        $res = (new Controller)->posturl($url,$get_data);
        dump($res->data);
        if (!empty($res->data)) {
            foreach ( $res->data as $k1 => $v1 ) {
                //查询应用名称
                $adxid = DB::table('mg_game')->where('adx_appname',$v1->ADX_MOBILE_APP_NAME)->select('id')->first();
                // dump($adxid);exit;
                if($adxid->id){
                    $data = array ();
                    $data ['game_id'] = $adxid->id;
                    $data ['adx_revenue'] = $v1->ADX_ESTIMATED_REVENUE;
                    $data ['country_code'] = $v1->ADX_GEO_COUNTRY_NAME_CODE;
                    $data ['date'] = strtotime($date);
                    $count  = DB::table('mg_game_country_report')
                        ->where('game_id',$adxid->id)
                        ->where('date',strtotime($date))
                        ->where('country_code',$v1->ADX_GEO_COUNTRY_NAME_CODE)
                        ->count();
                    if($count == 0){
                        DB::table('mg_game_country_report')->insert($data);
                    }else{                        
                        DB::table('mg_game_country_report')
                            ->where('game_id',$adxid->id)
                            ->where('date',strtotime($date))
                            ->where('country_code',$v1->ADX_GEO_COUNTRY_NAME_CODE)
                            ->update(['adx_revenue' => $v1->ADX_ESTIMATED_REVENUE]);
                    }
                }
            }
        }
        dump('ADX REVENUE更新完成！');
        Log::info('每日ADX REVENUE更新完成');
    }
}
