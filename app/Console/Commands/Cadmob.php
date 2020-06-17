<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;
use Redis;
class Cadmob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cadmob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Country Admob Data';

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
        // $date = '2019-05-01';
        $gamelist = DB::table('mg_game')->where('status',1)->get();
        set_time_limit(0);
        foreach ($gamelist as $key2 => $value2) { 
            $url = 'https://www.googleapis.com/adsense/v1.4/accounts/'.$value2->am_account_id.'/reports?metric=EARNINGS&metric=CLICKS&metric=VIEWED_IMPRESSIONS&dimension=COUNTRY_CODE&';
            $get_data = array (
                'startDate' => $date,
                'endDate' => $date,
                'currency' => 'USD',
                'dimension' => 'APP_NAME',
                'metric'=>'INDIVIDUAL_AD_IMPRESSIONS_RPM',
                'access_token' => Redis::get($value2->am_account_id.'access_token')
            );
            $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
            $res_ary = json_decode ( $res, true );
            // dump($res_ary);
            if (! empty ( $res_ary['rows'])) { 
                foreach ( $res_ary['rows'] as $k1 => $v1 ) {
                    if($v1['1'] == $value2->am_app_name){
                        // dump($v1['0'].'--'.$v1['2']);
                        if($v1['3'] && $v1['4'] && $v1['4'] != '0'){
                        	$ctr = round($v1['3']/$v1['4'],2);
                        }else{
                        	$ctr = 0;
                        }
                        
                        $data = array ();
                        $data ['am_revenue'] = $v1['2'];
                        $data ['am_cpm'] = $v1['5'];
                        $data ['ctr'] = $ctr;
                        $data ['game_id'] = $value2->id;
                        $data ['date'] = strtotime($date);
                        $data ['country_code'] = $v1['0'];
                        $count  = DB::table('mg_game_country_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime($date))
                            ->where('country_code',$v1['0'])
                            ->count();

                        if($count!=0){
                            DB::table('mg_game_country_report')
                                ->where('game_id',$value2->id)
                                ->where('date',strtotime($date))
                                ->where('country_code',$v1['0'])
                                ->update(['am_revenue' => $v1['2'],'am_cpm' => $v1['5'],'ctr' => $ctr]);
                        }else{                        
                            DB::table('mg_game_country_report')->insert($data);
                        }
                    }
                }
            }
            sleep(3);
            dump($value2->name.'分国家admob数据拉取成功!');
        }
       Log::info('每日分国家Admob数据处理完成');
    }
}
