<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;
class Ironsource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ironsource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yesterday Ironsource';

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
        //$gamelist = DB::table('mg_game')->get();
        if(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $date = date("Y-m-d");
        }else{
            $date = date("Y-m-d",strtotime("-1 day"));
        }
        //ironsource数据
        foreach ($gamelist as $key2 => $value2) {
            //$value1 = date('Y-m-d',$value2->date);
                $crl = curl_init();
                $base64encoded = base64_encode("andappsok@gmail.com:0cc11edb4c4dff4d28eeb5d93ab71dcc");
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
                    $sumisrevenue = 0;
                    foreach ($res as $key => $value) {
                        $sumisrevenue += $value['data'][0]['revenue'];
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
                }else{
                    $data = array ();
                    $data ['is_revenue'] = '0.00';
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
                            ->update(['is_revenue' => '0.00']);
                    }else{                        
                        DB::table('mg_game_report')->insert($data);
                    }
                }
    
        }
        Log::info('每日ironsource处理完成');
    }
}
