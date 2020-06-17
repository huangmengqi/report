<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class Cap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Country Applovin Data';

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
        //查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        set_time_limit(0);
        $url = 'https://r.applovin.com/report?';

        foreach ($gamelist as $key2 => $value2) {
            $get_data = array (
                'start' => $date,
                'end' => $date,
                'format' => 'json',
                'columns' => 'revenue,package_name,ecpm,country',
                'api_key'=>'KrwMq14VaH6NqbdkkHmRk-iz65hn9UfUKZs8bweAClT1LAl4SUVBTD8aIqLSJ0yeaonAiPLv9uwV3fZW94hcHq'
            );
            $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
            $res_ary = json_decode ( $res, true );
            $array = [];
            if(!$res_ary['results']){
                foreach ($res_ary['results'] as $key => $value) {
                    if($value['package_name'] == $value2->al_package_name){
                        $array[$value['country']] = $value['revenue'];
                    }
                }
            }
            foreach ($array as $key3 => $value3) {
                $data = array ();
                $data ['al_revenue'] = $value3;
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
                        ->update(['al_revenue' => $value3]);
                }else{                        
                    DB::table('mg_game_country_report')->insert($data);
                }
            }
        }
        Log::info('实时分国家applovin数据处理完成');
    }
}
