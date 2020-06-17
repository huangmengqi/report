<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;
use Redis;
class HighCtr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'highctr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'High Ctr Scan';

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
        //查询应用列表
        $gamelist = DB::table('mg_game')->where('cate',1)->where('status',1)->orderBy('name')->get();
        if(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $date = date("Y-m-d");
        }else{
            $date = date("Y-m-d",strtotime("-1 day"));
        }  
        $highctrdata = [];
        $list=DB::table('mg_game')->where('status',1)->get();
        foreach ($list as $key => $value) {
            $clicks = DB::table('ctr_unit')->where('game_id',$value->id)->where('unit_name','Native-sp-main')->where('date',strtotime($date))->sum('clicks');
            $impressions = DB::table('ctr_unit')->where('game_id',$value->id)->where('unit_name','Native-sp-main')->where('date',strtotime($date))->sum('impressions');
            if($impressions != '0' && $impressions){
                $ctr = round($clicks/$impressions,4);
            }else{
                $ctr = '0';
            }
            if($ctr > 0.15){
                $highctrdata[] = $value->name;
            }
        }   
        Redis::set('highctrdata',json_encode($highctrdata));
        Log::info('轮询点击率超过15%产品');
    }
}
