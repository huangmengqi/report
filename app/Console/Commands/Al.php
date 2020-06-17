<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class Al extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'al';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yesterday Al Revenue Data';

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
        //查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        // al变现数据
        $url = 'https://r.applovin.com/report?';
        $get_data = array (
            'start' => $date,
            'end' => $date,
            'format' => 'json',
            'columns' => 'revenue,package_name,ecpm',
            'api_key'=>'KrwMq14VaH6NqbdkkHmRk-iz65hn9UfUKZs8bweAClT1LAl4SUVBTD8aIqLSJ0yeaonAiPLv9uwV3fZW94hcHq'
        );
        $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
        $res_ary = json_decode ( $res, true );
        if (! empty ( $res_ary )) {
            foreach ($gamelist as $key => $value) { 
                foreach ( $res_ary['results'] as $k1 => $v1 ) {
                    if($v1["package_name"] == $value->al_package_name){
                        $data = array ();
                        $data ['al_revenue'] = round($v1 ['revenue'],2);
                        $data ['game_id'] = $value->id;
                        $data ['date'] = strtotime($date);
                        $count  = DB::table('mg_game_report')
                            ->where('game_id',$value->id)
                            ->where('date',strtotime($date))
                            ->count();
                        if($count!=0){
                            DB::table('mg_game_report')
                                ->where('game_id',$value->id)
                                ->where('date',strtotime($date))
                                ->update(['al_revenue' => round($v1 ['revenue'],2)]);
                        }else{                        
                            DB::table('mg_game_report')->insert($data);
                        }
                    }else{
                        $data = array ();
                        $data ['al_revenue'] = '0.00';
                        $data ['game_id'] = $value->id;
                        $data ['date'] = strtotime($date);
                        $count  = DB::table('mg_game_report')
                            ->where('game_id',$value->id)
                            ->where('date',strtotime($date))
                            ->count();
                        if($count!=0){
                            DB::table('mg_game_report')
                                ->where('game_id',$value->id)
                                ->where('date',strtotime($date))
                                ->update(['al_revenue' => '0.00']);
                        }else{                        
                            DB::table('mg_game_report')->insert($data);
                        }
                    }
                }
            }
        }
        Log::info('每日applovin处理完成');
    }
}
