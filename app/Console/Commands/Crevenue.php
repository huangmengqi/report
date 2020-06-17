<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class Crevenue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crevenue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Total Revenue';

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
        $coutrydata = DB::table('mg_game_country_report')->where('date',strtotime($date))->get();
        foreach ($coutrydata as $k => $v) {
            $total = $v->fb_ad_network_revenue+$v->is_revenue+$v->mp_revenue+$v->al_revenue+$v->am_revenue; 
            DB::table('mg_game_country_report')->where('game_id',$v->game_id)->where('date',$v->date)->where('country_code',$v->country_code)->update(['total' => $total]);
        }
        Log::info('实时总收益处理完成');
    }
}
