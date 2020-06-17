<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\MobpubImport;
use Excel;
use Redis;
use Google_Client;
use Google_Service_Drive;
use Storage;
use DB;
use Hhxsv5\PhpMultiCurl\Curl;
use Hhxsv5\PhpMultiCurl\MultiCurl;
use Google\Auth\OAuth2;
use Mail;

class MobileController extends Controller
{
    public function mobile(Request $request){
    	$gamelist = DB::table('mg_game')->where('cate',1)->where('status',1)->get();
        //获取页面传过来的查询条件并默认展示最近七天数据
        
        if($request->input('time1')){
            $time1 = strtotime($request->input('time1'));
        }elseif(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $time1 = strtotime(date("Y-m-d"));
        }
        else{
            $time1 = strtotime(date('Y-m-d', strtotime('-1 day')));
        }


        if($request->input('time2')){
            $time2 = strtotime($request->input('time2'));
        }elseif(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $time2 = strtotime(date("Y-m-d"));
        }
        else{
            $time2 = strtotime(date('Y-m-d', strtotime('-1 day')));
        }



        if($request->input('appid')){
            $appid = $request->input('appid');
        }else{
            $appid = '';
        }
        
        
        if (!empty($appid)) { 
            // 搜索条件
            $data = DB::table('mg_game_report')
            ->join('mg_game', 'mg_game_report.game_id', '=', 'mg_game.id')
                ->select('mg_game_report.*', 'mg_game.name'); 
            if ($time1) { 
                $data = $data->where('mg_game_report.date','>=',$time1); 
            }
            if ($time2) { 
                $data =$data->where('mg_game_report.date','<=',$time2); 
            } 
            if ($appid) { 
                $data = $data->where('mg_game_report.game_id',$appid)->where('mg_game.status',1); 
            }
            $list = $data->orderBy('date','Desc')->paginate(50);
        }else{
            // 搜索条件
            $data = DB::table('mg_game_report')
                ->join('mg_game', 'mg_game_report.game_id', '=', 'mg_game.id')
                ->select('mg_game_report.game_id','mg_game.name',DB::raw('
                    sum(mg_game_report.am_revenue) as am_revenue,
                    sum(mg_game_report.am_cpm) as am_cpm,
                    sum(mg_game_report.al_revenue) as al_revenue,
                    sum(mg_game_report.fb_ad_network_revenue) as fb_ad_network_revenue,
                    sum(mg_game_report.fb_ad_network_cpm) as fb_ad_network_cpm,
                    sum(mg_game_report.mp_revenue) as mp_revenue,
                    sum(mg_game_report.al_revenue) as al_revenue,
                    sum(mg_game_report.is_revenue) as is_revenue,
                    sum(mg_game_report.install_count) as install_count,
                    sum(mg_game_report.cost) as cost
                '));
            if ($time1) { 
                $data = $data->where('mg_game_report.date','>=',$time1); 
            }
            if ($time2) { 
                $data =$data->where('mg_game_report.date','<=',$time2); 
            } 
            $list = $data->groupBy('mg_game_report.game_id')->where('mg_game.status',1)->where('mg_game.cate',1)->paginate(50);
        }
    	return view('admin.mobile',['list'=>$list,'gamelist'=>$gamelist,'appid'=>$appid,'time1'=>date('Y-m-d',$time1),'time2'=>date('Y-m-d',$time2)]);
    }
}
