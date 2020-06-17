<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;
use Redis;

class Ctrunit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ctrunint';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Today Admob Unit Click Data';

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
        $gamelist = DB::table('mg_game')->where('status',1)->where('cate',1)/*->where('id',152)*/->get();
        // dump($gamelist);exit;
        foreach ($gamelist as $key => $value) { 
            $url = 'https://www.googleapis.com/adsense/v1.4/accounts/'.$value->am_account_id.'/reports?metric=INDIVIDUAL_AD_IMPRESSIONS_CTR&metric=AD_REQUESTS&metric=CLICKS&metric=INDIVIDUAL_AD_IMPRESSIONS&dimension=AD_UNIT_NAME&dimension=COUNTRY_CODE&';
                $get_data = array (
                    'startDate' => $date,
                    'endDate' => $date,
                    'currency' => 'USD',
                    'dimension' => 'APP_NAME',
                    'access_token' => Redis::get($value->am_account_id.'access_token')
                );
                $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
                $res_ary = json_decode ( $res, true );
                // dump($res_ary);exit;
                if (! empty ( $res_ary['rows'])) {
                    foreach ( $res_ary['rows'] as $k1 => $v1 ) {
                        if($v1['2'] == $value->am_app_name){
                            $data = array ();
                            $data ['unit_name'] = $v1 ['0'];
                            $data ['country_code'] = $v1 ['1'];
                            $data ['game_id'] = $value->id;
                            $data ['date'] = strtotime($date);
                            $data ['ctr'] = $v1 ['3'];
                            $data ['request'] = $v1 ['4'];
                            $data ['clicks'] = $v1 ['5'];
                            $data ['impressions'] = $v1 ['6'];
                            $count  = DB::table('ctr_unit')
                                ->where('game_id',$value->id)
                                ->where('date',strtotime($date))
                                ->where('unit_name',$v1 ['0'])
                                ->where('country_code',$v1 ['1'])
                                ->count();

                            if($count!=0){
                                DB::table('ctr_unit')
                                    ->where('game_id',$value->id)
                                    ->where('date',strtotime($date))
                                    ->where('unit_name',$v1 ['0'])
                                    ->where('country_code',$v1 ['1'])
                                    ->update(['ctr' => $v1 ['3'],'request' => $v1 ['4'],'clicks' => $v1 ['5'],'impressions' => $v1 ['6']]);
                            }else{                        
                                DB::table('ctr_unit')->insert($data);
                            }
                        }
                    }
                }
                dump($value->am_app_name.'数据分广告位点击数据拉取完成！');
                sleep(5);
            }
            Log::info($value->am_app_name.'数据分广告位点击数据拉取完成！');
    }
}
