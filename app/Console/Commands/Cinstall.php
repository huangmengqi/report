<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class Cinstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cinstall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Country Install Data';

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
        // $date = '2019-05-12';
        $gamelist = DB::table('mg_game')->where('status',1)->get();
        foreach ($gamelist as $key => $value5) {
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
                $url1 = 'https://graph.facebook.com/v6.0/act_' . $value . '/insights?';
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
            sleep(3);
            dump($value5->name.'分国家安装数据拉取成功!');
        }
        Log::info('实时分国家安装量处理完成');
    }
}
