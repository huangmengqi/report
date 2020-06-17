<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class Cspend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cspend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Country Spend Data';

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
        // $date = '2020-04-26';
        //查询游戏列表
        $gamelist = DB::table('mg_game')/*->where('id',129)*/->where('status',1)->get();
        foreach ($gamelist as $key2 => $value2) {
            //获取应用下全部账户信息
            $account = DB::table('mg_game')->where('id',$value2->id)->select('fb_read_accounts')->get(); 
            $account1 = explode(',',$account[0]->fb_read_accounts);
            $array = [];
            foreach ($account1 as $key => $value) {
                $url1 = 'https://graph.facebook.com/v6.0/act_' . $value . '/insights?';
                $get_data = array (
                    'fields' => 'spend',
                    'breakdowns' => 'country',
                    'time_range[since]' => $date,
                    'time_range[until]' => $date,
                    'limit' => '2000',
                    'access_token' => env('ACCESS_TOKEN_INSTALL_SPEND')
                );
                $output = (new IndexController)->curl_get_https ( $url1 . http_build_query ( $get_data ) );//获取数据
                $res = json_decode($output,true);
                if(!empty($res['data'])){
                    foreach ($res['data'] as $key3 => $value3) {
                        if(array_key_exists('spend', $value3)){
                            if(!isset($array[$value3['country']])){
                                $array[$value3['country']] = round($value3['spend'],2);
                            }else{
                                $array[$value3['country']] += round($value3['spend'],2);
                            }
                        }
                    }
                } 
                foreach ($array as $k1 => $v1 ) {
                    $data = array ();
                    $data ['cost'] = $v1;
                    $data ['game_id'] = $value2->id;
                    $data ['country_code'] = $k1;
                    $data ['date'] = strtotime($date);
                    // dump($data);
                    $count  = DB::table('mg_game_country_report')
                        ->where('game_id',$value2->id)
                        ->where('date',strtotime($date))
                        ->where('country_code',$k1)
                        ->count();
                    if($count!=0){
                        DB::table('mg_game_country_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime($date))
                            ->where('country_code',$k1)
                            ->update(['cost' => $v1]);
                    }else{                        
                        DB::table('mg_game_country_report')->insert($data);
                    }
                }
            }
            dump($value2->name.'分国家花费数据拉取成功!');
        }
        Log::info('实时分国家花费处理完成');
    }
}
