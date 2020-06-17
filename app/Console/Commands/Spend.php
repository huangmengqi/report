<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\IndexController;
class Spend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yesterday APP Spend Sum';

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
        // 应用总花费
        //获取应用下全部账户信息
        foreach ($gamelist as $key2 => $value2) {
            $account = DB::table('mg_game')->where('id',$value2->id)->select('fb_read_accounts')->get(); 
            $account1 = explode(',',$account[0]->fb_read_accounts);
            /*$access_token = 'EAADXu7ZBbD90BAJoa1SvKsvDAwyvTOjSmsp5ORgvEZAljg8yG1dI3S8o2XsseGZBXL4GvxICZAKdtBKfM17MVeJuqTnGlt3nvZCOYLqVmexZAlJjhuIzlkGb7oQk3QpXmNoqtb0h1MxcS8xGZC26QHyNfvQh9kV08h8ZAv5nxKnW6AZDZD';*/
            //定义一个安装数求和后的变量
            $spendsum = 0;    
            $get_data = array (
                'fields' => 'spend',
                'time_range[since]' => $date,
                'time_range[until]' => $date,
                'access_token' => env('ACCESS_TOKEN')
            );
            //循环广告下各账户的安装数mobile_app_install并求和
            // curl -i -X GET "https://graph.facebook.com/v3.3/oauth/access_token?grant_type=fb_exchange_token&client_id=440716260114311&client_secret=237651f01928b6193cc15e2bf7e0796e&fb_exchange_token=EAAGQ1EAhf4cBAGLMcY8DjpvnPhEaEM4ZAp5EPD3LZClQZCyVZCEH0XIoH2wZA9SR0yBc0waMjweCHek4EqZB4CKtRmtFegZCDVnotO51sUF3lTvnC54KDyJuFUV2ZCIDI2ysUs8HUNGgOvWSZCWEuASKI3TZBZBFU9qFcSrzRDaeXQyagZDZD"

            // EAAGQ1EAhf4cBAJ8VbuGyPveZAm2TkxBrpei34lRF7LZAIl0vTs7VXuK8kntUsedvIZBm9iFnHvX2gvLVl0LDOWWXZBGjB8aAQ9MAQta8nzONP48tm0GUbZAVkxqZATZClVeluAD65PN2SYW6SwAHqlG46ky4b1LnM7ZBjEoLbwPiKwZDZD   永久
            // EAAGQ1EAhf4cBADvMy6oVjExGAvXE3UYjTnZAcVePnTZAmDoPWZCT2j1ifJFZAjbwYvsWqWP7hzQ4ZAyXjcSXUBoJ3Ld9pD8LafRWH9WJYZAkCfTNo9y1TTfNV0A9AvIF9IiCk7HgEOURbzfkeZCnIXcHvu9tBKKFni8x6yKqqJTBAZDZD   两个月

            foreach ($account1 as $key => $value) {
                $url1 = 'https://graph.facebook.com/v3.3/act_' . $value . '/insights?';
                $output = (new IndexController)->curl_get_https ( $url1 . http_build_query ( $get_data ) );//获取数据
                $res = json_decode($output,true);

                //判断报表数组是否为空
                if(!empty($res['data'])){
                    //查找spend数据
                    $num = $res['data'][0]['spend'];
                }else{
                    $num = 0;
                }
                // 应用安装的总花费--总支出
                $spendsum += $num;
                $data = array ();
                $data ['cost'] = $spendsum;
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
                        ->update(['cost' => $spendsum]);
                }else{                        
                    DB::table('mg_game_report')->insert($data);
                }
            }
        }
        Log::info('每日花费处理完成');
    }
}
