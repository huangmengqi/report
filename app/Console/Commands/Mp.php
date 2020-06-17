<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class Mp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yesterday Mobpub Data';

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
        $date = date("Y-m-d",strtotime("-2 day"));
        // $date = '2019-08-06';
        //查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        //mobpub数据

        foreach ($gamelist as $key2 => $value2) {
            $mp = DB::table('mg_mobpub')->where('app_id',$value2->mp_app_id)->where('day',strtotime($date))->get();

            $sumrevenue = 0;
            foreach ($mp as $key3 => $value3) {
                $sumrevenue += $value3->revenue;
            }
            $data = array ();
            $data ['mp_revenue'] = round($sumrevenue,2);
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
                    ->update(['mp_revenue' => round($sumrevenue,2)]);
            }else{                        
                DB::table('mg_game_report')->insert($data);
            }
        }
        Log::info('每日mobpub数据完成');
    }
}
