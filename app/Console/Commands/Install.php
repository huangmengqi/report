<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;
class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yesterday APP Install Sum';

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
        $gamelist = DB::table('mg_game')->get();
        //应用安装数
        foreach ($gamelist as $key2 => $value2) {   
            $account = DB::table('mg_game')->where('id',$value2->id)->select('fb_read_accounts')->get(); 
            $account1 = explode(',',$account[0]->fb_read_accounts);
            /*$access_token = 'EAADXu7ZBbD90BAJoa1SvKsvDAwyvTOjSmsp5ORgvEZAljg8yG1dI3S8o2XsseGZBXL4GvxICZAKdtBKfM17MVeJuqTnGlt3nvZCOYLqVmexZAlJjhuIzlkGb7oQk3QpXmNoqtb0h1MxcS8xGZC26QHyNfvQh9kV08h8ZAv5nxKnW6AZDZD';*/
            //定义一个安装数求和后的变量
            $installsum = 0;   
            $get_data = array (
                'fields' => 'actions',
                'time_range[since]' => $date,
                'time_range[until]' => $date,
                'access_token' => env('ACCESS_TOKEN')
            );
            //循环广告下各账户的安装数mobile_app_install并求和
            foreach ($account1 as $key => $value) {
                $url1 = 'https://graph.facebook.com/v3.2/act_' . $value . '/insights?';
                $output = (new IndexController)->curl_get_https ( $url1 . http_build_query ( $get_data ) );//获取数据
                $res = json_decode($output,true);

                //判断报表数组是否为空
                if(!empty($res['data'][0]['actions'])){
                    //查找mobile_app_install数据
                    foreach ($res['data'][0]['actions'] as $key1 => $install) {
                        if($install['action_type']=='mobile_app_install'){
                            $num = $install['value'];
                        }
                    }
                }else{
                    $num = 0;
                }
                // 应用安装总数--安装数量
                $installsum += $num;
                $data = array ();
                $data ['install_count'] = $installsum;
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
                        ->update(['install_count' => $installsum]);
                }else{                        
                    DB::table('mg_game_report')->insert($data);
                }
            }
        }
        Log::info('每日安装量处理完成');
    }
}
