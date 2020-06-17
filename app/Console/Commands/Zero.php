<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;

class Zero extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zero';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Everyday Insert Data At Midnignt';

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
    	$date = date("Y-m-d");
    	//查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        foreach ($gamelist as $key2 => $value2) { 
	        $data = array ();
	        $data ['game_id'] = $value2->id;
	        $data ['date'] = strtotime($date);
	        $count  = DB::table('mg_game_report')
                ->where('game_id',$value2->id)
                ->where('date',strtotime($date))
                ->count();
            if($count==0){
                DB::table('mg_game_report')->insert($data);
            }
	    }
	    Log::info('每日新增数据处理完成');
    }
}
