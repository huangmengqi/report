<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class Cmp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cmp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Country Mobpub Data';

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
        // $date = date("Y-m-d",strtotime("-1 day"));
        $date = date("Y-m-d",strtotime("-2 day"));
        
        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->get();
        foreach ($gamelist as $key2 => $value2) {
            $data = DB::table('mg_mobpub')->where('app_id',$value2->mp_app_id)->where('day',strtotime($date))->get();
            foreach ($data as $k => $v) {
                $data = array ();
                $data ['mp_revenue'] = $v->revenue;
                $data ['game_id'] = $value2->id;
                $data ['date'] = strtotime($date);
                $count  = DB::table('mg_game_country_report')
                    ->where('game_id',$value2->id)
                    ->where('date',strtotime($date))
                    ->where('country_code',$v->country)
                    ->count();
                if($count!=0){
                    DB::table('mg_game_country_report')
                        ->where('game_id',$value2->id)
                        ->where('date',strtotime($date))
                        ->where('country_code',$v->country)
                        ->update(['mp_revenue' => $v->revenue]);
                }else{                        
                    DB::table('mg_game_country_report')->insert($data);
                }
            }
        }
        sleep(1);
    }
}
