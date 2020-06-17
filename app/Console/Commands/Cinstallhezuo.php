<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class Cinstallhezuo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cinstallhezuo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Country Install Data Last Week';

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
        /*$date = [date('Y-m-d',strtotime("Monday last week")),date('Y-m-d',strtotime("Tuesday last week")),date('Y-m-d',strtotime("Wednesday last week")),date('Y-m-d',strtotime("Thursday last week")),date('Y-m-d',strtotime("Friday last week")),date('Y-m-d',strtotime("Saturday last week")),date('Y-m-d',strtotime("Sunday last week"))];*/
        $gamelist = DB::table('mg_game')->whereIn('id',['76', '176'])->get();
        // $date = '2020-05-27';
        if(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $date = date("Y-m-d");
        }else{
            $date = date("Y-m-d",strtotime("-1 day"));
        }  
        foreach ($gamelist as $key => $value5) {
            // foreach ($date as $datekey => $datevalue) {
            	$account = DB::table('mg_game')->where('id',$value5->id)->select('fb_read_accounts')->get(); 
	            $account1 = explode(',',$account[0]->fb_read_accounts);
	            $get_data = array (
	                'fields' => 'actions',
	                'breakdowns' => 'country',
	                'time_range[since]' => $date,
	                'time_range[until]' => $date,
	                'limit' => '2000',
	                'access_token' => env('ACCESS_TOKEN_INSTALL_SPEND')
	            );
	            $array = [];
	            foreach ($account1 as $key => $value) {
	                $url1 = 'https://graph.facebook.com/v5.0/act_' . $value . '/insights?';
	                $output = (new IndexController)->curl_get_https ( $url1 . http_build_query ( $get_data ) );//获取数据
	                $res = json_decode($output,true);
	                // dump($res);
	                // $installsum = 0;
	                if(!empty($res['data'])){
	                    foreach ($res['data'] as $k => $v) {
	                        if(array_key_exists('actions', $v)){
	                            foreach ($v['actions'] as $key3 => $value3) {
	                                if($value3['action_type']=='mobile_app_install'){
	                                    // dump('2019-03-02'.'--'.$value.'--'.$v['country'].'--'.$value3['value']);
	                                   if(!isset($array[$v['country']])){
	                                        $array[$v['country']] = $value3['value'];
	                                    }else{
	                                        $array[$v['country']] += $value3['value'];
	                                    }
	                                }
	                            }
	                            // dump($installsum);
	                        }
	                    }
	                }
	            }
	            foreach ($array as $key4 => $value4) {
	                $data = array ();
	                $data ['install_count'] = $value4;
	                $data ['game_id'] = $value5->id;
	                $data ['country_code'] = $key4;
	                $data ['date'] = strtotime($date);
	                $count  = DB::table('mg_game_country_report')
	                    ->where('game_id',$value5->id)
	                    ->where('date',strtotime($date))
	                    ->where('country_code',$key4)
	                    ->count();
	                if($count!=0){
	                    DB::table('mg_game_country_report')
	                        ->where('game_id',$value5->id)
	                        ->where('date',strtotime($date))
	                        ->where('country_code',$key4)
	                        ->update(['install_count' => $value4]);
	                }else{                        
	                    DB::table('mg_game_country_report')->insert($data);
	                }
	            }
	            dump($value5->name.'--'.$date.'外单分国家安装量处理完成');
            // }
        }
        Log::info('外单分国家安装量处理完成');
    }
}
