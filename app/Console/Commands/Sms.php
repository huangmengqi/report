<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class Sms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Message';

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
        $coutrydata = DB::table('mg_game_country_report')->where('date',strtotime($date))->get();
        foreach ($coutrydata as $k => $v) {
            $total = $v->fb_ad_network_revenue+$v->is_revenue+$v->mp_revenue+$v->al_revenue+$v->am_revenue; 
            if($total!=0&&$v->cost!=0){
                $roi = round($total/$v->cost,2)*100;
            }else{
                $roi = 0;
            }
           
            DB::table('mg_game_country_report')->where('game_id',$v->game_id)->where('date',$v->date)->where('country_code',$v->country_code)->update(['roi' => $roi]);
        }
        $gamelist = DB::table('mg_game')->where('status',1)->get();
        foreach ($gamelist as $key2 => $value2) {
            $get_data = array (
                'u' => 'hmq1994',
                'p' => '930809q',
                'm' => 'PHONE',
                'c' => $content
            );
        }
        Log::info('每分钟更新预警信息完成');
    }
}
