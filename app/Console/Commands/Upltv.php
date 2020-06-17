<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;
use Redis;
class Upltv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upltv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yesterday Upltv';

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
        // $date = '2020-03-04';
        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->where('upltv_id','!=','0')->get();
        // dump($gamelist);
        foreach ($gamelist as $key => $value) { 
            dump($value->name);
            // if($value->upltv_id != 0){
            $url1 = 'https://reporting.upltv.com/api/report?';
            $get_data1 = array (
                'country' => 'all',
                'start_day' => $date,
                'end_day' => $date,
                'pid' => $value->upltv_id,
                'offer_type' => 'all',
                'key' => '7d6b2195c5254be1ef14a35c75fbf393'
            );
            $res = (new IndexController)->curl_get_https ( $url1.http_build_query($get_data1 ));
            // dump(json_decode($res));exit;
            foreach ((json_decode($res)->data)->country_report as $k1 => $v1 ) {
                dump($v1);
                $cost = DB::table('mg_game_country_report')->where('game_id',$value->id)->where('date',strtotime($date))->where('country_code',$k1)->select('cost')->first();
                $data = array ();
                $data ['upltv'] = $v1->revenue;
                $data ['game_id'] = $value->id;
                $data ['date'] = strtotime($date);
                $data ['country_code'] = $k1;
                $count  = DB::table('mg_game_country_report')
                    ->where('game_id',$value->id)
                    ->where('country_code',$k1)
                    ->where('date',strtotime($date))
                    ->count();

                if($count!=0){
                    DB::table('mg_game_country_report')
                        ->where('game_id',$value->id)
                        ->where('date',strtotime($date))
                        ->where('country_code',$k1)
                        ->update(['upltv' => $v1->revenue,'profit' => round($v1->revenue - $cost->cost,2)]);

                }else{                        
                    DB::table('mg_game_country_report')->insert($data);
                }
            }

            dump($value->name.'分国家Upltv数据处理完成');  
                // sleep(10);
            // }
            $upltvdata = DB::table('mg_game_country_report')->where('game_id',$value->id)->where('date',strtotime($date))->sum('upltv');
            // 将upltv数据更新到日报表中
            DB::table('mg_game_report')->where('game_id',$value->id)->where('date',strtotime($date))->update(['upltv' => $upltvdata]);

        }

        dump('分国家Upltv数据处理完成');
        Log::info('分国家Upltv数据处理完成');
    }
}
