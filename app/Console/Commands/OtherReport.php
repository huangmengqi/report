<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;

class OtherReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otherreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Other Report Data';

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
        // $date = '2020-01-01';
        //http://2cm.mobflower.com数据拉取
        $res = (new IndexController)->curl_get_https ('http://2cm.mobflower.com/dreportapi');
        // dump($res);
        foreach (json_decode($res) as $key => $value) {
            $gameid = DB::table('mg_game')->where('name',$value->name)->select('id')->first();
            $data = array ();
            $data ['install_count'] = $value->install_count;
            $data ['cost'] = $value->cost;
            $data ['am_revenue'] = $value->am_revenue;
            $data ['game_id'] = $gameid->id;
            $data ['date'] = strtotime($date);
            $count  = DB::table('mg_game_report')
                ->where('game_id',$gameid->id)
                ->where('date',strtotime($date))
                ->count();
            if($count!=0){
                DB::table('mg_game_report')
                    ->where('game_id',$gameid->id)
                    ->where('date',strtotime($date))
                    ->update([
                        'install_count' => $value->install_count,
                        'am_revenue' => $value->am_revenue,
                        'cost' => $value->cost
                    ]);
            }else{                        
                DB::table('mg_game_report')->insert($data);
            }
        }
        dump('http://2cm.mobflower.com数据拉取成功');
        Log::info('http://2cm.mobflower.com数据拉取成功');
        // exit;
        //http://3shinepro.diannaoshu.com数据拉取
        $res = (new IndexController)->curl_get_https ('http://3shinepro.diannaoshu.com/dreportapi');
        // dump(json_decode($res));exit;
        foreach (json_decode($res) as $key => $value) {
            $gameid = DB::table('mg_game')->where('name',$value->name)->select('id')->first();
            $data = array ();
            $data ['install_count'] = $value->install_count;
            $data ['cost'] = $value->cost;
            $data ['am_revenue'] = $value->am_revenue;
            $data ['game_id'] = $gameid->id;
            $data ['date'] = strtotime($date);
            $count  = DB::table('mg_game_report')
                ->where('game_id',$gameid->id)
                ->where('date',strtotime($date))
                ->count();
            if($count!=0){
                DB::table('mg_game_report')
                    ->where('game_id',$gameid->id)
                    ->where('date',strtotime($date))
                    ->update([
                        'install_count' => $value->install_count,
                        'am_revenue' => $value->am_revenue,
                        'cost' => $value->cost
                    ]);
            }else{                        
                DB::table('mg_game_report')->insert($data);
            }
        }
        
        dump('http://3shinepro.diannaoshu.com数据拉取成功');
        Log::info('http://3shinepro.diannaoshu.com数据拉取成功');

        //http://4chic.mobfine.com数据拉取
        $res = (new IndexController)->curl_get_https ('http://4chic.mobfine.com/dreportapi');
        // dump($date);
        // dump(json_decode($res));
        foreach (json_decode($res) as $key => $value) {
            $gameid = DB::table('mg_game')->where('name',$value->name)->select('id')->first();
            $data = array ();
            $data ['install_count'] = $value->install_count;
            $data ['cost'] = $value->cost;
            $data ['am_revenue'] = $value->am_revenue;
            $data ['game_id'] = $gameid->id;
            $data ['date'] = strtotime($date);
            $count  = DB::table('mg_game_report')
                ->where('game_id',$gameid->id)
                ->where('date',strtotime($date))
                ->count();
            if($count!=0){
                DB::table('mg_game_report')
                    ->where('game_id',$gameid->id)
                    ->where('date',strtotime($date))
                    ->update([
                        'install_count' => $value->install_count,
                        'am_revenue' => $value->am_revenue,
                        'cost' => $value->cost
                    ]);
            }else{                        
                DB::table('mg_game_report')->insert($data);
            }
        }
        dump('http://4chic.mobfine.com数据拉取成功');
        Log::info('http://4chic.mobfine.com数据拉取成功');

        //http://5easy.linkedmob.com数据拉取
        $res = (new IndexController)->curl_get_https ('http://5easy.linkedmob.com/dreportapi');
        foreach (json_decode($res) as $key => $value) {
            $gameid = DB::table('mg_game')->where('name',$value->name)->select('id')->first();
            $data = array ();
            $data ['install_count'] = $value->install_count;
            $data ['cost'] = $value->cost;
            $data ['am_revenue'] = $value->am_revenue;
            $data ['game_id'] = $gameid->id;
            $data ['date'] = strtotime($date);
            $count  = DB::table('mg_game_report')
                ->where('game_id',$gameid->id)
                ->where('date',strtotime($date))
                ->count();
            if($count!=0){
                DB::table('mg_game_report')
                    ->where('game_id',$gameid->id)
                    ->where('date',strtotime($date))
                    ->update([
                        'install_count' => $value->install_count,
                        'am_revenue' => $value->am_revenue,
                        'cost' => $value->cost
                    ]);
            }else{                        
                DB::table('mg_game_report')->insert($data);
            }
        }
        dump('http://5easy.linkedmob.com/数据拉取成功');
        Log::info('http://5easy.linkedmob.com/数据拉取成功');


        //http://6high.topbreaking.online数据拉取
        $res = (new IndexController)->curl_get_https ('http://6hign.topbreaking.online/dreportapi');

        // dump($res);exit;
        foreach (json_decode($res) as $key => $value) {
            $gameid = DB::table('mg_game')->where('name',$value->name)->select('id')->first();
            $data = array ();
            $data ['install_count'] = $value->install_count;
            $data ['cost'] = $value->cost;
            $data ['am_revenue'] = $value->am_revenue;
            $data ['game_id'] = $gameid->id;
            $data ['date'] = strtotime($date);
            $count  = DB::table('mg_game_report')
                ->where('game_id',$gameid->id)
                ->where('date',strtotime($date))
                ->count();
            if($count!=0){
                DB::table('mg_game_report')
                    ->where('game_id',$gameid->id)
                    ->where('date',strtotime($date))
                    ->update([
                        'install_count' => $value->install_count,
                        'am_revenue' => $value->am_revenue,
                        'cost' => $value->cost
                    ]);
            }else{                        
                DB::table('mg_game_report')->insert($data);
            }
        }
        dump('http://6hign.topbreaking.online/dreportapi数据拉取成功');
        Log::info('http://6hign.topbreaking.online/dreportapi数据拉取成功');

        //http://7bloom.profunstudio.com数据拉取
        $res = (new IndexController)->curl_get_https ('http://7bloom.profunstudio.com/dreportapi');
        // dump(json_decode($res));exit;
        foreach (json_decode($res) as $key => $value) {
            $gameid = DB::table('mg_game')->where('name',$value->name)->select('id')->first();
            // dump($gameid);exit;
            $data = array ();
            $data ['install_count'] = $value->install_count;
            $data ['cost'] = $value->cost;
            $data ['am_revenue'] = $value->am_revenue;
            $data ['game_id'] = $gameid->id;
            $data ['date'] = strtotime($date);
            $count  = DB::table('mg_game_report')
                ->where('game_id',$gameid->id)
                ->where('date',strtotime($date))
                ->count();
            if($count!=0){
                DB::table('mg_game_report')
                    ->where('game_id',$gameid->id)
                    ->where('date',strtotime($date))
                    ->update([
                        'install_count' => $value->install_count,
                        'am_revenue' => $value->am_revenue,
                        'cost' => $value->cost
                    ]);
            }else{                        
                DB::table('mg_game_report')->insert($data);
            }
        }
        dump('http://7bloom.profunstudio.com/数据拉取成功');
        Log::info('http://7bloom.profunstudio.com/数据拉取成功');

    }
}
