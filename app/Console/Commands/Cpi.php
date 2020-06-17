<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class Cpi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cpi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CPI';

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
        $coutrydata = DB::table('mg_game_country_report')->where('date',strtotime($date))->get();
        foreach ($coutrydata as $k => $v) {
            if($v->install_count!=0&&$v->cost!=0){
                $cpi = round($v->cost/$v->install_count,2);
            }else{
                $cpi = 0;
            }
           
            DB::table('mg_game_country_report')->where('game_id',$v->game_id)->where('date',$v->date)->where('country_code',$v->country_code)->update(['fb_ad_network_fill_rate' => $cpi]);
        }
        Log::info('实时CPI处理完成');
    }
}
