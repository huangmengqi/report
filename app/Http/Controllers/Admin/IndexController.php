<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

class IndexController extends Controller
{
    public function index()
    {
        $admin = session('admin');
        $user_role = DB::table('admin_user_role')->where('admin_user_id',$admin->id)->first();
        $role_id = $user_role->role_id;
        $menu_list = DB::table('admin_role_menu as rm')
            ->leftJoin('admin_menu as m','m.id','=','rm.menu_id')
            ->where('rm.role_id',$role_id)
            ->where('m.pid',0)
            ->select('m.*')
            ->orderBy('m.sort','DESC')
            ->get();
        foreach ($menu_list as $k=>$v){
            $menu_list[$k]->child = DB::table('admin_role_menu as rm')
                ->leftJoin('admin_menu as m','m.id','=','rm.menu_id')
                ->where('rm.role_id',$role_id)
                ->where('m.pid',$v->id)
                ->select('m.*')
                ->orderBy('m.sort','DESC')
                ->get();
            if(count($menu_list[$k]->child)){
                $menu_list[$k]->has_child = true;
            }else{
                $menu_list[$k]->has_child = false;
            }
        }
        return view('admin.index',['menu'=>$menu_list]);
    }



    public function console(Request $request)
    {
        //查询应用列表
        $gamelist = DB::table('mg_game')->where('cate',1)->where('status',1)->orderBy('name')->get();
        if(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $date = date("Y-m-d");
        }else{
            $date = date("Y-m-d",strtotime("-1 day"));
        }  
        //从Redis获取点击率过高产品
        $highctrdata = json_decode(Redis::get('highctrdata'));
        
        if(strstr($request->date,' - ', TRUE)){
            $time1 = strtotime(strstr($request->date,' - ', TRUE));
        }elseif(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $time1 = strtotime(date("Y-m-d"));
        }else{
            $time1 = strtotime(date('Y-m-d', strtotime('-1 day')));
        }


        if(str_replace(" - ", "",strstr($request->date,' - '))){
            $time2 = strtotime(str_replace(" - ", "",strstr($request->date,' - ')));
        }elseif(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $time2 = strtotime(date("Y-m-d"));
        }else{
            $time2 = strtotime(date('Y-m-d', strtotime('-1 day')));
        }


        if($request->input('appid')){
            $appid = $request->input('appid');
        }else{
            $appid = '';
        }
        


        if($request->input('game')){
            $game = $request->input('game');
        }else{
            $game = '';
        }

        if($request->input('music')){
            $music = $request->input('music');
        }else{
            $music = '';
        }


        
        if (!empty($appid)) { 
            $game = '';
            $music = '';
            // 搜索条件
            $data = DB::table('mg_game_report')
            ->join('mg_game', 'mg_game_report.game_id', '=', 'mg_game.id')
                ->select('mg_game_report.*', 'mg_game.name','mg_game.add_time','mg_game.operator'); 
            
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

            if($game && !$music){
                $game = 'on';
                // 搜索条件
                $data = DB::table('mg_game_report')
                    ->join('mg_game', 'mg_game_report.game_id', '=', 'mg_game.id')
                    ->where('mg_game.is_game','=',1)
                    ->where('mg_game_report.date','>=',$time1)
                    ->where('mg_game_report.date','<=',$time2)
                    ->select('mg_game_report.game_id','mg_game.name','mg_game.add_time','mg_game.operator',DB::raw('
                        sum(mg_game_report.am_revenue) as am_revenue,
                        sum(mg_game_report.am_cpm) as am_cpm,
                        sum(mg_game_report.al_revenue) as al_revenue,
                        sum(mg_game_report.fb_ad_network_revenue) as fb_ad_network_revenue,
                        sum(mg_game_report.fb_ad_network_cpm) as fb_ad_network_cpm,
                        sum(mg_game_report.mp_revenue) as mp_revenue,
                        sum(mg_game_report.al_revenue) as al_revenue,
                        sum(mg_game_report.is_revenue) as is_revenue,
                        sum(mg_game_report.upltv) as upltv,
                        sum(mg_game_report.install_count) as install_count,
                        sum(mg_game_report.cost) as cost
                    '));
                /*if ($time1) { 
                    $data = $data->where('mg_game_report.date','>=',$time1); 
                }
                if ($time2) { 
                    $data =$data->where('mg_game_report.date','<=',$time2); 
                } */
                $list = $data->groupBy('mg_game_report.game_id')->where('mg_game.status',1)->where('mg_game.cate',1)->paginate(50);
            }else if(!$game && $music){
                $music = 'on';
                // 搜索条件
                $data = DB::table('mg_game_report')
                    ->join('mg_game', 'mg_game_report.game_id', '=', 'mg_game.id')
                    ->where('mg_game_report.date','>=',$time1)
                    ->where('mg_game.is_game','=',0)
                    ->where('mg_game_report.date','<=',$time2)
                    ->select('mg_game_report.game_id','mg_game.name','mg_game.add_time','mg_game.operator',DB::raw('
                        sum(mg_game_report.am_revenue) as am_revenue,
                        sum(mg_game_report.am_cpm) as am_cpm,
                        sum(mg_game_report.al_revenue) as al_revenue,
                        sum(mg_game_report.fb_ad_network_revenue) as fb_ad_network_revenue,
                        sum(mg_game_report.fb_ad_network_cpm) as fb_ad_network_cpm,
                        sum(mg_game_report.mp_revenue) as mp_revenue,
                        sum(mg_game_report.al_revenue) as al_revenue,
                        sum(mg_game_report.is_revenue) as is_revenue,
                        sum(mg_game_report.upltv) as upltv,
                        sum(mg_game_report.install_count) as install_count,
                        sum(mg_game_report.cost) as cost
                    '));
                /*if ($time1) { 
                    $data = $data->where('mg_game_report.date','>=',$time1); 
                }
                if ($time2) { 
                    $data =$data->where('mg_game_report.date','<=',$time2); 
                } */
                $list = $data->groupBy('mg_game_report.game_id')->where('mg_game.status',1)->where('mg_game.cate',1)->paginate(50);
            }else{
                $game = 'on';
                $music = 'on';
                // 搜索条件
                $data = DB::table('mg_game_report')
                    ->join('mg_game', 'mg_game_report.game_id', '=', 'mg_game.id')
                    ->where('mg_game_report.date','>=',$time1)
                    ->where('mg_game_report.date','<=',$time2)
                    ->select('mg_game_report.game_id','mg_game.name','mg_game.add_time','mg_game.operator',DB::raw('
                        sum(mg_game_report.am_revenue) as am_revenue,
                        sum(mg_game_report.am_cpm) as am_cpm,
                        sum(mg_game_report.al_revenue) as al_revenue,
                        sum(mg_game_report.fb_ad_network_revenue) as fb_ad_network_revenue,
                        sum(mg_game_report.fb_ad_network_cpm) as fb_ad_network_cpm,
                        sum(mg_game_report.mp_revenue) as mp_revenue,
                        sum(mg_game_report.al_revenue) as al_revenue,
                        sum(mg_game_report.is_revenue) as is_revenue,
                        sum(mg_game_report.upltv) as upltv,
                        sum(mg_game_report.install_count) as install_count,
                        sum(mg_game_report.cost) as cost
                    '));
                /*if ($time1) { 
                    $data = $data->where('mg_game_report.date','>=',$time1); 
                }
                if ($time2) { 
                    $data =$data->where('mg_game_report.date','<=',$time2); 
                } */
                $list = $data->groupBy('mg_game_report.game_id')->where('mg_game.status',1)->where('mg_game.cate',1)->paginate(50);
            }   
            
        }
        // dump($list);
        return view('admin.console',['highctrdata'=>$highctrdata,'game'=>$game,'music'=>$music,'list'=>$list,'gamelist'=>$gamelist,'appid'=>$appid,'time1'=>date('m/d/Y',$time1),'time2'=>date('m/d/Y',$time2)]);
    }



    public function upltv(Request $request)
    {
        

        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('cate',1)->where('status',1)->where('upltv_id','!=',0)->orderBy('name')->get();
        //获取页面传过来的查询条件并默认展示最近七天数据
        
        if($request->input('time1')){
            $time1 = strtotime($request->input('time1'));
        }elseif(date("H:i:s") >= '17:30:00' && date("H:i:s") <= '24:00:00'){
            $time1 = strtotime(date('Y-m-d', strtotime('-1 day')));
        }
        else{
            $time1 = strtotime(date('Y-m-d', strtotime('-2 day')));
        }


        if($request->input('time2')){
            $time2 = strtotime($request->input('time2'));
        }elseif(date("H:i:s") >= '17:30:00' && date("H:i:s") <= '24:00:00'){
            $time2 = strtotime(date('Y-m-d', strtotime('-1 day')));
        }
        else{
            $time2 = strtotime(date('Y-m-d', strtotime('-2 day')));
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
                ->select('mg_game_report.*', 'mg_game.name','mg_game.add_time','mg_game.operator'); 
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
                ->where('mg_game.upltv_id','!=',0)
                ->select('mg_game_report.game_id','mg_game.name','mg_game.add_time','mg_game.operator',DB::raw('
                    sum(mg_game_report.am_revenue) as am_revenue,
                    sum(mg_game_report.am_cpm) as am_cpm,
                    sum(mg_game_report.al_revenue) as al_revenue,
                    sum(mg_game_report.fb_ad_network_revenue) as fb_ad_network_revenue,
                    sum(mg_game_report.fb_ad_network_cpm) as fb_ad_network_cpm,
                    sum(mg_game_report.mp_revenue) as mp_revenue,
                    sum(mg_game_report.upltv) as upltv_revenue,
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
        
        return view('admin.upltv',['list'=>$list,'gamelist'=>$gamelist,'appid'=>$appid,'time1'=>date('Y-m-d',$time1),'time2'=>date('Y-m-d',$time2)]);
    }






    public function Line(Request $request){

        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->get();
        $appidfirst = DB::table('mg_game')->where('status',1)->first();
        if($request->input('time1')){
            $time1 = $request->input('time1');
        }
        else{
            $time1 = date('Y-m-d', strtotime('-1 week'));
        }

        if($request->input('time2')){
            $time2 = $request->input('time2');
        }
        else{
            $time2 = date('Y-m-d', strtotime('-1 day'));
        }

        if($request->input('appid')){
            $appid = $request->input('appid');
        }else{
            $appid = $appidfirst->id;
        }

        $date = (new IndexController)->getDateFromRange($time1,$time2);

        //搜索应用折线图
        if ($appid) { 
            $apptotalarray = array();
            $appdatearray = array();
            $apprevenue = array();
            $appspend = array();
            $appprofit = array();
            $appfb = array();
            $appam = array();
            $approi = array();
            foreach ($date as $key1 => $value1) {
                $list = DB::table('mg_game_report')
                    ->join('mg_game', 'mg_game_report.game_id', '=', 'mg_game.id')
                    ->select('mg_game_report.game_id','mg_game.name',DB::raw('
                        sum(mg_game_report.am_revenue) as am_revenue,
                        sum(mg_game_report.am_cpm) as am_cpm,
                        sum(mg_game_report.al_revenue) as al_revenue,
                        sum(mg_game_report.fb_ad_network_revenue) as fb_ad_network_revenue,
                        sum(mg_game_report.fb_ad_network_cpm) as fb_ad_network_cpm,
                        sum(mg_game_report.mp_revenue) as mp_revenue,
                        sum(mg_game_report.is_revenue) as is_revenue,
                        sum(mg_game_report.install_count) as install_count,
                        sum(mg_game_report.cost) as cost
                    '))
                    ->groupBy('mg_game_report.game_id')
                    ->where('mg_game_report.date',strtotime($value1))
                    ->where('mg_game.status',1)
                    ->where('mg_game_report.game_id',$appid)
                    ->first();
                    if($list){
                        $datearray[] = $value1;
                        $apprevenue1 = round($list->am_revenue,0)+round($list->al_revenue,0)+round($list->fb_ad_network_revenue,0)+round($list->mp_revenue,0)+round($list->is_revenue,0);
                        $appprofit1 = $apprevenue1-round($list->cost,0);
                        $apprevenue[] = $apprevenue1;
                        $appspend[] = $list->cost;
                        $appprofit[] = $appprofit1;
                        $appfb[] = round($list->fb_ad_network_revenue,0);
                        $appam[] = round($list->am_revenue,0);
                        if($apprevenue1!=0&&$list->cost!=0){
                             $approi[] = round($apprevenue1/$list->cost,2)*100;
                        }else{
                             $approi[] = 0; 
                        } 
                        $appname = $list->name;
                    }
                    
                }
            }
            //总报表折线图
            $totalarray = array();
            foreach ($date as $key1 => $value1) {
                $list = DB::table('mg_game_report')
                    ->join('mg_game', 'mg_game_report.game_id', '=', 'mg_game.id')
                    ->select('mg_game_report.game_id','mg_game.name',DB::raw('
                        sum(mg_game_report.am_revenue) as am_revenue,
                        sum(mg_game_report.am_cpm) as am_cpm,
                        sum(mg_game_report.al_revenue) as al_revenue,
                        sum(mg_game_report.fb_ad_network_revenue) as fb_ad_network_revenue,
                        sum(mg_game_report.fb_ad_network_cpm) as fb_ad_network_cpm,
                        sum(mg_game_report.mp_revenue) as mp_revenue,
                        sum(mg_game_report.is_revenue) as is_revenue,
                        sum(mg_game_report.install_count) as install_count,
                        sum(mg_game_report.cost) as cost
                    '))
                    ->groupBy('mg_game_report.game_id')
                    ->where('mg_game_report.date',strtotime($value1))
                    ->where('mg_game.status',1)
                    ->get();
                $total = array();
                $total['0'] = 0;
                $total['1'] = 0;
                $total['2'] = 0;
                $total['3'] = 0;
                $total['4'] = 0;
                $total['5'] = 0;
                foreach ($list as $key => $value) {
                    $revenue = round($value->am_revenue,0)+round($value->al_revenue,0)+round($value->fb_ad_network_revenue,0)+round($value->mp_revenue,0)+round($value->is_revenue,0);
                    $profit = $revenue-round($value->cost,0);
                    $total['0'] += $revenue;
                    $total['1'] += round($value->cost,0);
                    $total['2'] += $profit;
                    $total['3'] += round($value->fb_ad_network_revenue,0);
                    $total['4'] += round($value->am_revenue,0);
                    $total['5'] += $value->cost;
                }
                $totalarray[$value1] = $total;
            }
            $datearray = array();
            $revenue = array();
            $spend = array();
            $profit = array();
            $fb = array();
            $am = array();
            $roi = array();
            foreach ($totalarray as $key2 => $value2) {
                $datearray[] = $key2;
                $revenue[] = $value2['0'];
                $spend[] = $value2['1'];
                $profit[] = $value2['2'];
                $fb[] = $value2['3'];
                $am[] = $value2['4'];
                $roi[] = round($value2['0']/$value2['5'],2)*100;
            }
        
        

        return view('admin.line',['gamelist'=>$gamelist,'appid'=>$appid,'time1'=>$time1,'time2'=>$time2,'datearray'=>json_encode(array_reverse($datearray),true),'revenue'=>json_encode(array_reverse($revenue),true),'spend'=>json_encode(array_reverse($spend),true),'profit'=>json_encode(array_reverse($profit),true),'fb'=>json_encode(array_reverse($fb),true),'am'=>json_encode(array_reverse($am),true),'roi'=>json_encode(array_reverse($roi),true),'apprevenue'=>json_encode(array_reverse($apprevenue),true),'appspend'=>json_encode(array_reverse($appspend),true),'appprofit'=>json_encode(array_reverse($appprofit),true),'appfb'=>json_encode(array_reverse($appfb),true),'appam'=>json_encode(array_reverse($appam),true),'approi'=>json_encode(array_reverse($approi),true),'appname'=>$appname]);
    }



    public function appLine(Request $request){
         //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->get();
        if($request->input('time1')){
            $time1 = $request->input('time1');
        }
        else{
            $time1 = date('Y-m-d', strtotime('-1 week'));
        }

        if($request->input('time2')){
            $time2 = $request->input('time2');
        }
        else{
            $time2 = date('Y-m-d', strtotime('-1 day'));
        }
        $date = (new IndexController)->getDateFromRange($time1,$time2);
        //全部应用折线图
        $apptotalprofit = array();
        foreach ($gamelist as $key3 => $value3) {
            $appprofit = array();
            foreach ($date as $key4 => $value4) {
                $list = DB::table('mg_game_report')
                    ->select('mg_game_report.game_id',DB::raw('
                        sum(mg_game_report.am_revenue) as am_revenue,
                        sum(mg_game_report.am_cpm) as am_cpm,
                        sum(mg_game_report.al_revenue) as al_revenue,
                        sum(mg_game_report.fb_ad_network_revenue) as fb_ad_network_revenue,
                        sum(mg_game_report.fb_ad_network_cpm) as fb_ad_network_cpm,
                        sum(mg_game_report.mp_revenue) as mp_revenue,
                        sum(mg_game_report.is_revenue) as is_revenue,
                        sum(mg_game_report.install_count) as install_count,
                        sum(mg_game_report.cost) as cost
                    '))
                    ->groupBy('mg_game_report.game_id')
                    ->where('mg_game_report.date',strtotime($value4))
                    ->where('mg_game_report.game_id',$value3->id)
                    ->first();
                    if($list){
                        $apprevenue1 = round($list->am_revenue,0)+round($list->al_revenue,0)+round($list->fb_ad_network_revenue,0)+round($list->mp_revenue,0)+round($list->is_revenue,0);
                        $appprofit1 = $apprevenue1-round($list->cost,0);
                        $appprofit[] = $appprofit1;                                   
                        $appname = $value3->name;
                    }
                    
            }
                $apptotalprofit[] = array_reverse($appprofit);
        }
        return view('admin.app_line',['apptotalprofit'=>$apptotalprofit,'gamelist'=>$gamelist,'time1'=>$time1,'time2'=>$time2,'datearray'=>json_encode(array_reverse($date),true)]);
    }

    public function sendEmail(){


        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->get();
        if(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $today = strtotime(date("Y-m-d"));
        }else{
            $today = strtotime(date('Y-m-d', strtotime('-1 day')));
        }
        foreach ($gamelist as $key => $value) {
            $data = DB::table('mg_game_report')->where('game_id',$value->id)->where('date',$today)->first(); 
            $revenue = $data->fb_ad_network_revenue+$data->is_revenue+$data->mp_revenue+$data->al_revenue+$data->am_revenue;
            $profit = $revenue - $data->cost;
            if($data->cost!=0&&$data->install_count!=0){
                $cpi = $data->cost/$data->install_count;
            }else{
                $cpi = 0;
            }
            $email1 = $value->bussiness_email;
            if($email1){
                if($cpi > $value->monitor_cpi||$profit < ($value->monitor_profit > 0 ? -1 * $value->monitor_profit : abs($value->monitor_profit))||$data->install_count < $value->monitor_install){
                    $flag = Mail::send('admin.send_email',['name'=>$value->name,'cpi'=>$cpi,'profit'=>$profit,'install'=>$data->install_count],function($message) use ($email1){
                        $to = $email1;
                        $message ->to($to)->subject('产品异常通知');
                    });
                    if(!$flag){
                        echo '发送邮件成功，请查收！';
                    }else{
                        echo '发送邮件失败，请重试！';
                    }
                }
                
            }
        }
        



        /*$name = 'mobchang';
        $flag = Mail::send('admin.send_email',['name'=>$name],function($message){
            $to = 'lamtech@mobchang.com';
            $message ->to($to)->subject('测试邮件');
        });
        if(!$flag){
            echo $name.'发送邮件成功，请查收！';
        }else{
            echo $name.'发送邮件失败，请重试！';
        }*/
    }

    /**
     * @Desc: 后台图片上传
     * @Author: woann <304550409@qq.com>
     * @param Request $request
     * @return mixed
     */
    public function upload(Request $request)
    {
        $file = $request->file('image');
        $path = $request->input('path').'/';
        if($file){
            if($file->isValid()) {
                $size = $file->getSize();
                if($size > 5000000){
                    return $this->json(500,'图片不能大于5M！');
                }
                // 获取文件相关信息
                $ext = $file->getClientOriginalExtension();     // 扩展名
                if(!in_array($ext,['png','jpg','gif','jpeg','pem']))
                {
                    return $this->json(500,'文件类型不正确！');
                }
                $realPath = $file->getRealPath();   //临时文件的绝对路径
                // 上传文件
                $filename = $path.date('Ymd').'/'.uniqid() . '.' . $ext;
                // 使用我们新建的uploads本地存储空间（目录）
                $bool = Storage::disk('admin')->put($filename, file_get_contents($realPath));
                if($bool){
                    return $this->json(200,'上传成功',['filename'=>'/uploads/'.$filename]);
                }else{
                    return $this->json(500,'上传失败！');
                }
            }else{
                return $this->json(500,'文件类型不正确！');
            }
        }else{
            return $this->json(500,'上传失败！');
        }
    }

    /**
     * @Desc: 富文本上传图片
     * @Author: woann <304550409@qq.com>
     * @param Request $request
     */
    public function wangeditorUpload(Request $request)
    {
        $file = $request->file('wangEditorH5File');
        if($file){
            if($file->isValid()) {
                // 获取文件相关信息
                $ext = $file->getClientOriginalExtension();     // 扩展名
                $realPath = $file->getRealPath();   //临时文件的绝对路径
                // 上传文件
                $filename = date('Ymd') . '/' . uniqid() . '.' . $ext;
                // 使用我们新建的uploads本地存储空间（目录）
                $bool = Storage::disk('admin')->put('/wangeditor/'.$filename, file_get_contents($realPath));
                if($bool){
                    echo asset('/uploads/wangeditor/'.$filename);
                }else{
                    echo 'error|上传失败';
                }
            }else{
                echo 'error|上传失败';
            }
        }else{
            echo 'error|图片类型不正确';
        }
    }

    /**
     * @Desc: 无权限界面
     * @Author: woann <304550409@qq.com>
     * @return \Illuminate\View\View
     */
    public function noPermission()
    {
        return view('base.403');
    }
    /*//获取fb应用安装总量
    public function getInstallSum(){
        $date = (new IndexController)->getDateFromRange(date("Y-m-d",strtotime("-1 day")),date("Y-m-d"));
        //查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        //应用安装数
        foreach ($gamelist as $key2 => $value2) {   
            $account = DB::table('mg_game')->where('id',$value2->id)->select('fb_read_accounts')->get(); 
            $account1 = explode(',',$account[0]->fb_read_accounts);
            $access_token = env('ACCESS_TOKEN');
            //定义一个安装数求和后的变量
            
            foreach ($date as $key1 => $value1) {
                $installsum = 0;   
                $get_data = array (
                    'fields' => 'actions',
                    'time_range[since]' => $value1,
                    'time_range[until]' => $value1,
                    'access_token' => $access_token
                );
                //循环广告下各账户的安装数mobile_app_install并求和
                foreach ($account1 as $key => $value) {
                    $url1 = 'https://graph.facebook.com/v3.2/act_' . $value . '/insights?';
                    $output = (new IndexController)->curl_get_https ( $url1 . http_build_query ( $get_data ) );//获取数据
                     dump($url1 . http_build_query ( $get_data ));
                    $res = json_decode($output,true);
                    // dump($res);
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
                    $data ['date'] = strtotime($value1);
                    $count  = DB::table('mg_game_report')
                        ->where('game_id',$value2->id)
                        ->where('date',strtotime($value1))
                        ->count();
                    if($count!=0){
                        DB::table('mg_game_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime($value1))
                            ->update(['install_count' => $installsum]);
                    }else{                        
                        DB::table('mg_game_report')->insert($data);
                    }
                }
            } 
        }
        dump('------------------------------------安装量处理完成----------------------------------------');
        set_time_limit(0);
    }*/
    public function getAdmobJixiao(){
        //admob绩效拉取

        //查询游戏列表
        $gamelist = DB::table('mg_admob_refresh_code')->get();

        foreach ($gamelist as $key => $value) { 
            $url = 'https://www.googleapis.com/adsense/v1.4/accounts/'.$value->am_acountid.'/reports?metric=CLICKS&metric=COST_PER_CLICK&metric=EARNINGS&metric=INDIVIDUAL_AD_IMPRESSIONS&';
            $get_data = array (
                'startDate' => '2020-05-01',
                'endDate' => '2020-05-31',
                'currency' => 'USD',
                'dimension' => 'APP_NAME',
                'access_token' => Redis::get($value->am_acountid.'access_token')
            );
            $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
            $res_ary = json_decode ( $res, true );
            dump($res_ary);
            if (! empty ( $res_ary['rows'])) {
                foreach ( $res_ary['rows'] as $k1 => $v1 ) {
                    $data = array ();
                    $data ['app'] = $v1 ['0'];
                    $data ['CLICKS'] = $v1 ['1'];
                    $data ['COST_PER_CLICK'] = $v1 ['2'];
                    $data ['EARNINGS'] = $v1 ['3'];
                    $data ['impressions'] = $v1 ['4'];
                    DB::table('mg_jixiao_admob')->insert($data);
                    dump('------------------------------------'.$v1 ['0'].'绩效admob处理完成----------------------------------------');
                }

            }
            
            // dump($res_ary);exit;
        }













        /*foreach ($gamelist as $key => $value) { 
            $url = 'https://www.googleapis.com/adsense/v1.4/accounts/'.$value->am_account_id.'/reports?dimension=COUNTRY_NAME&metric=AD_REQUESTS&metric=AD_REQUESTS_COVERAGE&metric=AD_REQUESTS_CTR&metric=AD_REQUESTS_RPM&metric=CLICKS&metric=COST_PER_CLICK&metric=EARNINGS&metric=INDIVIDUAL_AD_IMPRESSIONS_CTR&metric=INDIVIDUAL_AD_IMPRESSIONS_RPM&metric=MATCHED_AD_REQUESTS&metric=MATCHED_AD_REQUESTS_CTR&metric=MATCHED_AD_REQUESTS_RPM&metric=PAGE_VIEWS&metric=PAGE_VIEWS_CTR&';
            $get_data = array (
                'startDate' => '2019-07-01',
                'endDate' => '2019-07-31',
                'currency' => 'USD',
                'dimension' => 'APP_NAME',
                'metric'=>'PAGE_VIEWS_RPM',
                'access_token' => Redis::get($value->am_account_id.'access_token')
            );
            $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
            $res_ary = json_decode ( $res, true );
            dump($res_ary);
            if (! empty ( $res_ary['rows'])) {
                foreach ( $res_ary['rows'] as $k1 => $v1 ) {
                    if($v1 ['1'] == $value->name){
                        $data = array ();
                        $data ['COUNTRY_CODE'] = $v1 ['0'];
                        $data ['app'] = $v1 ['1'];
                        $data ['AD_REQUESTS'] = $v1 ['2'];
                        $data ['AD_REQUESTS_COVERAGE'] = $v1 ['3'];
                        $data ['AD_REQUESTS_CTR'] = $v1 ['4'];
                        $data ['AD_REQUESTS_RPM'] = $v1 ['5'];
                        $data ['CLICKS'] = $v1 ['6'];
                        $data ['COST_PER_CLICK'] = $v1 ['7'];
                        $data ['EARNINGS'] = $v1 ['8'];
                        $data ['INDIVIDUAL_AD_IMPRESSIONS_CTR'] = $v1 ['9'];
                        $data ['INDIVIDUAL_AD_IMPRESSIONS_RPM'] = $v1 ['10'];
                        $data ['MATCHED_AD_REQUESTS'] = $v1 ['11'];
                        $data ['MATCHED_AD_REQUESTS_CTR'] = $v1 ['12'];
                        $data ['MATCHED_AD_REQUESTS_RPM'] = $v1 ['13'];
                        $data ['PAGE_VIEWS'] = $v1 ['14'];
                        $data ['PAGE_VIEWS_CTR'] = $v1 ['15'];
                        $data ['PAGE_VIEWS_RPM'] = $v1 ['16'];
                        // $data ['app'] = $value->name;
                        DB::table('mg_jixiao_admob')->insert($data);
                    }
                    
                    
                }

            }
            dump('------------------------------------'.$value->name.'绩效admob处理完成----------------------------------------');
        }*/
        dump('------------------------------------绩效admob处理完成----------------------------------------');
    }
    public function getFbJiXiao(){
        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('cate','1')->get();
        //应用安装数
        foreach ($gamelist as $key2 => $value2) {   
            $account = DB::table('mg_game')->where('id',$value2->id)->select('fb_read_accounts')->get(); 
            $account1 = explode(',',$account[0]->fb_read_accounts);
            $access_token = env('ACCESS_TOKEN_PAGE');
            //定义一个安装数求和后的变量
                $get_data = array (
                    'fields' => 'actions,campaign_name,clicks,account_name,impressions,spend,ctr,cpm',
                    // 'breakdowns' => 'country',
                    'time_range[since]' => '2020-05-01',
                    'time_range[until]' => '2020-05-31',
                    'limit' => '300',
                    'access_token' => $access_token
                );
                //循环广告下各账户的安装数mobile_app_install并求和
                foreach ($account1 as $key => $value) {
                    $url1 = 'https://graph.facebook.com/v6.0/act_' . $value . '/insights?';
                    $output = (new IndexController)->curl_get_https ( $url1 . http_build_query ( $get_data ) );//获取数据
                    $res = json_decode($output,true);
                    // dump($res);
                    if(!empty($res['data'])){
                        // $data = array ();
                        foreach ($res['data'] as $key1 => $data1) {
                            $data = array ();
                            $num = 0;
                            if(!empty($data1['actions'])){
                                //查找mobile_app_install数据
                                foreach ($data1['actions'] as $key2 => $data2) {
                                    if($data2['action_type'] == 'mobile_app_install'){
                                        $num = $data2['value'];
                                    }
                                }
                            }else{
                                $num = 0;
                            }    
                            
                            $data['install'] = $num;
                            $data['account_id'] = $value;
                            $data['ad_account_name'] = $value2->name;
                            $data['report_start'] = '2020-05-01';
                            $data['report_end'] = '2020-05-31';
                            $data['account_name'] = $data1['account_name'];
                            $data['country_code'] = '0';
                            $data['impressions'] = $data1['impressions'];
                            $data['clicks'] = $data1['clicks'];
                            
                            if(array_key_exists("cpm",$data1)){
                                $data['cpr'] = $data1['cpm'];
                            }else{
                                $data['cpr'] = 0;
                            }
                            $data['result_indicator'] = 'actions:mobile_app_install';
                            if(array_key_exists("spend",$data1)){
                                $data ['spend'] = $data1['spend'];
                            }else{
                                $data['spend'] = 0;
                            }
                            
                            if(array_key_exists("spend",$data1)&&$num != '0'){
                                $data ['cpi'] = $data1['spend']/$num; 
                            }else{
                                $data ['cpi'] = 0; 
                            }
                            DB::table('mg_jixiao_fb')->insert($data);             
                        }
                    }                  
                }
                dump('------------------------------------'.$value2->name.'绩效fb处理完成----------------------------------------');
                sleep(1);

            } 
            dump('------------------------------------绩效fb处理完成----------------------------------------');
    }

//获取fb应用安装总量
    public function getInstallSum(){

        /*//单个遗漏产品拉取
        $account = DB::table('mg_game')->where('id',50)->select('fb_read_accounts')->get(); 
        $account1 = explode(',',$account[0]->fb_read_accounts);
        $access_token = env('ACCESS_TOKEN');
        //定义一个安装数求和后的变量
        $get_data = array (
            'fields' => 'actions,campaign_name,clicks,account_name,impressions,spend,ctr,cpm',
            'breakdowns' => 'country',
            'time_range[since]' => '2019-03-01',
            'time_range[until]' => '2019-03-31',
            'limit' => '1000',
            'access_token' => $access_token
        );
        $url1 = 'https://graph.facebook.com/v3.2/act_562079077584284/insights?';
            $output = (new IndexController)->curl_get_https ( $url1 . http_build_query ( $get_data ) );//获取数据
            $res = json_decode($output,true);
            dump($res);*/
        /*foreach ($account1 as $key => $value) {
            $url1 = 'https://graph.facebook.com/v3.2/act_' . $value . '/insights?';
            $output = (new IndexController)->curl_get_https ( $url1 . http_build_query ( $get_data ) );//获取数据
            $res = json_decode($output,true);
            dump($res);
            if(!empty($res['data'])){
                $num = 0;
                foreach ($res['data'] as $key1 => $data1) {
                    if(!empty($data1['actions'])){
                        //查找mobile_app_install数据
                        foreach ($data1['actions'] as $key2 => $data2) {
                            if($data2['action_type'] == 'mobile_app_install'){
                                $num = $data2['value'];
                            }
                        }
                    }
                    $data = array ();
                    $data['account_id'] = $value;
                    $data['ad_account_name'] = 'Sleep Sounds';
                    $data['report_start'] = '2019-03-01';
                    $data['report_end'] = '2019-03-31';
                    $data['account_name'] = $data1['account_name'];
                    $data['country_code'] = $data1['country'];
                    $data['impressions'] = $data1['impressions'];
                    $data['clicks'] = $data1['clicks'];
                    $data['install'] = $num;
                    $data['cpr'] = $data1['cpm'];
                    $data['result_indicator'] = 'actions:mobile_app_install';
                    $data ['spend'] = $data1['spend'];
                    
                    if($data1['spend']&&$num != '0'){
                        $data ['cpi'] = $data1['spend']/$num; 
                    }else{
                        $data ['cpi'] = 0; 
                    }
                    DB::table('mg_jixiao_fb')->insert($data);
                }
            }                  
        }
*/
        // 整体产品拉取
        // $date = (new IndexController)->getDateFromRange(date("Y-m-d",strtotime("-1 day")),date("Y-m-d"));
        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('cate','1')->get();
        //应用安装数
        foreach ($gamelist as $key2 => $value2) {   
            $account = DB::table('mg_game')->where('id',$value2->id)->select('fb_read_accounts')->get(); 
            $account1 = explode(',',$account[0]->fb_read_accounts);
            $access_token = env('ACCESS_TOKEN');
            //定义一个安装数求和后的变量
                $get_data = array (
                    'fields' => 'actions,campaign_name,clicks,account_name,impressions,spend,ctr,cpm',
                    'breakdowns' => 'country',
                    'time_range[since]' => '2019-07-01',
                    'time_range[until]' => '2019-07-31',
                    'limit' => '300',
                    'access_token' => 'EAADXu7ZBbD90BAFaMQYNrTEx0WpT4RUV53d53YBxKDNAhj0mC0R5P5tgY8QoJYFtftDdiZCpEneT1jyysgvRG9lzwZAdZBZCbVBZB4A4tZBN2w2SwaDZAeNMu8NdPkeqUWs3KZCPCPYjtOENrnY5eGDVAJUHAZALbyFWkzRcFQERoZBemeTjKrlHB1Re36IwTBsIyiSNnAo7A2ToAZDZD'
                );
                //循环广告下各账户的安装数mobile_app_install并求和
                foreach ($account1 as $key => $value) {
                    $url1 = 'https://graph.facebook.com/v3.3/act_' . $value . '/insights?';
                    $output = (new IndexController)->curl_get_https ( $url1 . http_build_query ( $get_data ) );//获取数据
                    $res = json_decode($output,true);
                    // dump($res);exit;
                    if(!empty($res['data'])){
                        // $data = array ();
                        foreach ($res['data'] as $key1 => $data1) {
                        	$data = array ();
                        	$num = 0;
                            if(!empty($data1['actions'])){
                                //查找mobile_app_install数据
                                foreach ($data1['actions'] as $key2 => $data2) {
                                    if($data2['action_type'] == 'mobile_app_install'){
                                        $num = $data2['value'];
                                    }
                                }
                        	}else{
                        		$num = 0;
                        	}    
                    		
                            $data['install'] = $num;
                            $data['account_id'] = $value;
                            $data['ad_account_name'] = $value2->name;
                            $data['report_start'] = '2019-07-01';
                            $data['report_end'] = '2019-07-31';
                            $data['account_name'] = $data1['account_name'];
                            $data['country_code'] = $data1['country'];
                            $data['impressions'] = $data1['impressions'];
                            $data['clicks'] = $data1['clicks'];
                            
                            if(array_key_exists("cpm",$data1)){
                            	$data['cpr'] = $data1['cpm'];
                            }else{
                            	$data['cpr'] = 0;
                            }
                            $data['result_indicator'] = 'actions:mobile_app_install';
                            if(array_key_exists("spend",$data1)){
                            	$data ['spend'] = $data1['spend'];
                            }else{
                            	$data['spend'] = 0;
                            }
                            
                            if(array_key_exists("spend",$data1)&&$num != '0'){
                                $data ['cpi'] = $data1['spend']/$num; 
                            }else{
                                $data ['cpi'] = 0; 
                            }
                            DB::table('mg_jixiao_fb')->insert($data);             
                        }
                    }                  
                }
                dump('------------------------------------'.$value2->name.'绩效fb处理完成----------------------------------------');
                sleep(1);

            } 
            // dump('------------------------------------绩效fb处理完成----------------------------------------');


        


        /*//查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        // foreach ($gamelist as $key2 => $value2) {   
            // $url = 'https://graph.facebook.com/v3.2/'.$value2->fb_app_id.'/adnetworkanalytics/?';
            $url = 'https://graph.facebook.com/v3.2/1985768314777810/adnetworkanalytics/?';
            $get_data = array (
                'date_preset'=>'last_month',
                'since'=>'2019-03-01',
                'until'=>'2019-03-07',
                'breakdowns'=>['country'],
                'aggregation_period'=>'total',
                'metrics'=>['fb_ad_network_revenue'],
                'access_token' => env('ACCESS_TOKEN')
            );
            $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
            $res_ary = json_decode ( $res, true );
            dump($res_ary);*/

        

            /*if (! empty ( $res_ary['data'] )) {
                foreach ( $res_ary ['data'] [0] ['results'] as $k1 => $v1 ) {
                    $data = array ();
                    $data ['revenue'] = $v1 ['value'];
                    $data ['country'] = $v1 ['breakdowns']['0']['value'];
                    $data ['app_id'] = $value2->fb_app_id;
                    $data ['app'] = $value2->name;
                    // $data ['date'] = strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] )));
                    $count  = DB::table('mg_jixiao_fbrevenue')
                        ->where('app',$value2->name)
                        ->where('app_id',$value2->fb_app_id)
                        ->count();
                    if($count!=0){
                        DB::table('mg_jixiao_fbrevenue')
                            ->where('app_id',$value2->fb_app_id)
                            ->where('app',$value2->name)
                            ->where('country',$v1 ['breakdowns']['0']['value'])
                            ->update(['revenue' => $v1 ['value']]);
                    }else{                        
                        DB::table('mg_jixiao_fbrevenue')->insert($data);
                    }

                }
            }
        }*/

            /*$curl = curl_init(); // 启动一个CURL会话
            $url = 'https://graph.facebook.com/v3.2/1985768314777810/adnetworkanalytics/?';
            $get_data = array (
                'date_preset'=>'last_month',
                'breakdowns'=>['country'],
                'aggregation_period'=>'total',
                'metrics'=>['fb_ad_network_bidding_request', 'fb_ad_network_cpm', 'fb_ad_network_click','fb_ad_network_revenue'],
                'access_token' => env('ACCESS_TOKEN')
            );
            curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
            curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($get_data)); // Post提交的数据包
            curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
            curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
            $tmpInfo = curl_exec($curl); // 执行操作
            if (curl_errno($curl)) {
                echo 'Errno'.curl_error($curl);//捕抓异常
            }
            curl_close($curl); // 关闭CURL会话
            return $tmpInfo; // 返回数据，json格式*/

        dump('------------------------------------fb收益绩效处理完成----------------------------------------');





       



}
































    //获取fb应用花费总量
    public function getSpendSum(){
        $date = (new IndexController)->getDateFromRange(date("Y-m-d",strtotime("-1 day")),date("Y-m-d"));
        //查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        // 应用总花费
        //获取应用下全部账户信息
        foreach ($gamelist as $key2 => $value2) {
            $account = DB::table('mg_game')->where('id',$value2->id)->select('fb_read_accounts')->get(); 
            $account1 = explode(',',$account[0]->fb_read_accounts);
            $access_token = env('ACCESS_TOKEN');
            //定义一个安装数求和后的变量
            
            foreach ($date as $key1 => $value1) {
                $spendsum = 0;    
                $get_data = array (
                    'fields' => 'spend',
                    'time_range[since]' => $value1,
                    'time_range[until]' => $value1,
                    'access_token' => $access_token
                );
                //循环广告下各账户的安装数mobile_app_install并求和
                foreach ($account1 as $key => $value) {
                    $url1 = 'https://graph.facebook.com/v3.2/act_' . $value . '/insights?';
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
                    $data ['date'] = strtotime($value1);
                    $count  = DB::table('mg_game_report')
                        ->where('game_id',$value2->id)
                        ->where('date',strtotime($value1))
                        ->count();
                    if($count!=0){
                        DB::table('mg_game_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime($value1))
                            ->update(['cost' => $spendsum]);
                    }else{                        
                        DB::table('mg_game_report')->insert($data);
                    }
                }
            }
        }
        dump('------------------------------------总花费处理完成----------------------------------------');
        set_time_limit(0);
    }
    //获取FB收入,需传入起始时间、终止时间、应用id三个参数
    public function getFb(){


        /*$date = '2019-05-07';
        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->get();
        foreach ($gamelist as $key2 => $value2) {   
            $url = 'https://graph.facebook.com/v3.2/'.$value2->fb_app_id.'/adnetworkanalytics/?';
            $get_data = array (
                'since' => $date, 
                'until' => $date,
                'metrics'=>'fb_ad_network_revenue',
                'access_token' => 'EAAFAVXklZCsEBAIGPaXBCKAZBQQ3ut8yl4IXCrtchksLgsuCgEXZBZCZAkgbcT2ZB9HZAn060cKl9lRGEqGmpgrS6NGXl0krJ7bNnEGXmf3fqsTpzg6URlJSbo9HbZB6WoGPdnsHsxXvBA3EcE3TmC8eNd5ozVQNnt04O6SbR4ZC1HF2n2LuTglbjtB93IPhtJb3fqVKGBSyaYwZDZD'
            );
            $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
            $res_ary = json_decode ( $res, true );
            dump($res_ary);
            if (! empty ( $res_ary['data'] )) {
                foreach ( $res_ary ['data'] [0] ['results'] as $k1 => $v1 ) {
                    $data = array ();
                    $data ['fb_ad_network_revenue'] = round($v1 ['value'],2);
                    $data ['game_id'] = $value2->id;
                    $data ['date'] = strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] )));
                    $count  = DB::table('mg_game_report')
                        ->where('game_id',$value2->id)
                        ->where('date',strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] ))))
                        ->count();
                    if($count!=0){
                        DB::table('mg_game_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] ))))
                            ->update(['fb_ad_network_revenue' => round($v1 ['value'],2)]);
                    }else{                        
                        DB::table('mg_game_report')->insert($data);
                    }

                }
            }
            dump($value2->name.'------------------------------------fb变现处理完成----------------------------------------');
        }*/

        $date = '2019-07-23';
        $url = 'https://graph.facebook.com/v3.2/2056812751280937/adnetworkanalytics/?';
        $get_data = array (
            'since' => $date, 
            'until' => $date,
            'metrics'=>'fb_ad_network_revenue',
            'access_token' => 'EAAOe7oZCISKwBAF00jOnll35x0XRHZAqn3SZBSfRbobQTc9xsQvca9TMmbquMJl6WoNjdmdnMO4M3civVT1HqPLu7tD06SC1eiqFCCErpZAF9vAHaHsvZBf6QTp6OViZBWuAKk7xxjWSakCTJMD0qu7SI44V6PHkA8ZBX6PbZBH9iAZDZD'
        );
        $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
        $res_ary = json_decode ( $res, true );
        dump($res_ary);
        /*if (! empty ( $res_ary ['data'] [0] )) {
            foreach ( $res_ary ['data'] [0] ['results'] as $k1 => $v1 ) {
                $data = array ();
                $data ['fb_ad_network_revenue'] = round($v1 ['value'],2);
                $data ['game_id'] = 45;
                $data ['date'] = strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] )));
                $count  = DB::table('mg_game_report')
                    ->where('game_id',45)
                    ->where('date',strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] ))))
                    ->count();
                if($count!=0){
                    DB::table('mg_game_report')
                        ->where('game_id',45)
                        ->where('date',strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] ))))
                        ->update(['fb_ad_network_revenue' => round($v1 ['value'],2)]);
                }else{                        
                    DB::table('mg_game_report')->insert($data);
                }

            }
        }
*/
        /* $date = (new IndexController)->getDateFromRange('2019-02-28','2019-03-06');
        // $date = (new IndexController)->getDateFromRange('2019-02-26','2019-03-05');
        //查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        // fb变现数据
        foreach ($date as $key1 => $value1) {   
            $url = 'https://graph.facebook.com/v3.2/376063519863402/adnetworkanalytics/?';
            $get_data = array (
                'since' => $value1, 
                'until' => $value1,
                'metrics'=>'fb_ad_network_revenue',
                'access_token' => env('ACCESS_TOKEN')
            );
            $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
            $res_ary = json_decode ( $res, true );
            // dump($res_ary);
            if (! empty ( $res_ary ['data'] [0] )) {
                foreach ( $res_ary ['data'] [0] ['results'] as $k1 => $v1 ) {
                    $data = array ();
                    $data ['fb_ad_network_revenue'] = round($v1 ['value'],2);
                    $data ['game_id'] = 45;
                    $data ['date'] = strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] )));
                    $count  = DB::table('mg_game_report')
                        ->where('game_id',45)
                        ->where('date',strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] ))))
                        ->count();
                    if($count!=0){
                        DB::table('mg_game_report')
                            ->where('game_id',45)
                            ->where('date',strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] ))))
                            ->update(['fb_ad_network_revenue' => round($v1 ['value'],2)]);
                    }else{                        
                        DB::table('mg_game_report')->insert($data);
                    }

                }
            }
    }*/
        
        set_time_limit(0);




         /*$date = (new IndexController)->getDateFromRange('2019-02-28','2019-03-06');
        // $date = (new IndexController)->getDateFromRange('2019-02-26','2019-03-05');
        //查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        // fb变现数据
        foreach ($date as $key1 => $value1) {
   
        foreach ($gamelist as $key2 => $value2) {   
            
            $url = 'https://graph.facebook.com/v3.2/376063519863402/adnetworkanalytics/?';
            $get_data = array (
                'since' => $value1, 
                'until' => $value1,
                'metrics'=>'fb_ad_network_revenue',
                'access_token' => 'EAADXu7ZBbD90BAGkpAj1DGOyyFRzZCopfCTX2q8Dvff0IbnTMvFBrhnUaY7g60HoxW4RCrZCqkPBjKhoWV5tcpt1IDO52WMgAJQ4G0IOyInjZCKU3jOVGLcvRF0jNEbCKKSs8c6DjT9PpGC9Kr0TZBWAwLO78sWZB7jIhHKsvWANkHWqyPt9j5NWkNVbgwcZCUM1AkFH5MrugZDZD'
            );
            $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
            $res_ary = json_decode ( $res, true );
            dump($res_ary);
            if (! empty ( $res_ary ['data'] [0] )) {
                foreach ( $res_ary ['data'] [0] ['results'] as $k1 => $v1 ) {
                    $data = array ();
                    $data ['fb_ad_network_revenue'] = round($v1 ['value'],2);
                    $data ['game_id'] = 45;
                    $data ['date'] = strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] )));
                    $count  = DB::table('mg_game_report')
                        ->where('game_id',45)
                        ->where('date',strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] ))))
                        ->count();
                    if($count!=0){
                        DB::table('mg_game_report')
                            ->where('game_id',45)
                            ->where('date',strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] ))))
                            ->update(['fb_ad_network_revenue' => round($v1 ['value'],2)]);
                    }else{                        
                        DB::table('mg_game_report')->insert($data);
                    }

                }
            }
        }
    }
        dump('------------------------------------fb变现处理完成----------------------------------------');
        set_time_limit(0);*/
    }
    
    //获取admob收入
    public function getAdmob(){
        dump(Redis::get('pub-8594727977662805access_token'));

        // $date = date("Y-m-d",strtotime("-1 day"));
        $date = '2019-10-10';
        $url = 'https://www.googleapis.com/adsense/v1.4/accounts/pub-8594727977662805/reports?metric=EARNINGS&';
        $get_data = array (
            'startDate' => $date,
            'endDate' => $date,
            'currency' => 'USD',
            'dimension' => 'APP_NAME',
            'metric'=>'INDIVIDUAL_AD_IMPRESSIONS_RPM',
            'access_token' => Redis::get('pub-8594727977662805access_token')
        );
        $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
        $res_ary = json_decode ( $res, true );
        dump($res_ary);exit;









        $gamelist = DB::table('mg_game')->get();
        
        foreach ($gamelist as $key => $value) { 
        $url = 'https://www.googleapis.com/adsense/v1.4/accounts/'.$value->am_account_id.'/reports?metric=EARNINGS&';
        $get_data = array (
            'startDate' => $date,
            'endDate' => $date,
            'currency' => 'USD',
            'dimension' => 'APP_NAME',
            'metric'=>'INDIVIDUAL_AD_IMPRESSIONS_RPM',
        	'access_token' => Redis::get($value->am_account_id.'access_token')
        );
        $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
        $res_ary = json_decode ( $res, true );
        dump($res_ary);exit;
        if (! empty ( $res_ary['rows'])) {
                foreach ( $res_ary['rows'] as $k1 => $v1 ) {
                    if($v1['0'] == $value->am_app_name){
                        $data = array ();
                        $data ['am_revenue'] = round($v1 ['1'],2);
                        $data ['am_cpm'] = round($v1 ['2'],2);
                        $data ['game_id'] = $value->id;
                        $data ['date'] = strtotime($date);
                        $count  = DB::table('mg_game_report')
                            ->where('game_id',$value->id)
                            ->where('date',strtotime($date))
                            ->count();

                        if($count!=0){
                            DB::table('mg_game_report')
                                ->where('game_id',$value->id)
                                ->where('date',strtotime($date))
                                ->update(['am_revenue' => round($v1 ['1'],2),'am_cpm' => round($v1 ['2'],2)]);
                        }else{                        
                            DB::table('mg_game_report')->insert($data);
                        }
                    }
                    
                }

            }
        }
        /*$date = (new IndexController)->getDateFromRange(date("Y-m-d",strtotime("-1 day")),date("Y-m-d"));
        // $date = (new IndexController)->getDateFromRange('2019-02-26','2019-03-05');
        //查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        // admob变现数据
        foreach ($gamelist as $key => $value) { 
            $url = 'https://www.googleapis.com/adsense/v1.4/accounts/'.$value->am_account_id.'/reports?metric=EARNINGS&';
            foreach ($date as $key1 => $value1) {
                $get_data = array (
                    'startDate' => $value1,
                    'endDate' => $value1,
                    'currency' => 'USD',
                    'dimension' => 'APP_NAME',
                    'metric'=>'INDIVIDUAL_AD_IMPRESSIONS_RPM',
                    'access_token' => Redis::get($value->am_account_id.'access_token')
                );
                $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
                $res_ary = json_decode ( $res, true );

                if (! empty ( $res_ary['rows'])) {
                    foreach ( $res_ary['rows'] as $k1 => $v1 ) {
                        if($v1['0'] == $value->am_app_name){
                            $data = array ();
                            $data ['am_revenue'] = round($v1 ['1'],2);
                            $data ['am_cpm'] = round($v1 ['2'],2);
                            $data ['game_id'] = $value->id;
                            $data ['date'] = strtotime($value1);
                            $count  = DB::table('mg_game_report')
                                ->where('game_id',$value->id)
                                ->where('date',strtotime($value1))
                                ->count();

                            if($count!=0){
                                DB::table('mg_game_report')
                                    ->where('game_id',$value->id)
                                    ->where('date',strtotime($value1))
                                    ->update(['am_revenue' => round($v1 ['1'],2),'am_cpm' => round($v1 ['2'],2)]);
                            }else{                        
                                DB::table('mg_game_report')->insert($data);
                            }
                        }
                    }
                }
            }
        }*/
        dump('------------------------------------admob处理完成----------------------------------------');
        set_time_limit(0);
        
}
    public function getAdmobCode() {
        require_once '/web/nginx/www/vendor/autoload.php';
   
    $oauth2 = new OAuth2([
        'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
        'tokenCredentialUri' => 'https://www.googleapis.com/oauth2/v4/token',
        'redirectUri' => 'http://' . $_SERVER['HTTP_HOST']. '/admin/index/getAdmobCode',
        'clientId' => '92994361661-c2fa8fqr4leorru3iljc86su0ie15nkl.apps.googleusercontent.com',
        'clientSecret' => 'Ii1oW6A9il6JcoC0RdubBlXl',
        'scope' => 'https://www.googleapis.com/auth/adsense.readonly'
        ]);
    if (!isset($_GET['code'])) {
        $oauth2->setState(sha1(openssl_random_pseudo_bytes(1024)));
        Redis::set('oauth2state',$oauth2->getState());
        $config = [
            // Set to 'offline' if you require offline access.
            'access_type' => 'offline',
            'prompt' => 'consent' 
        ];
        header('Location: ' . $oauth2->buildFullAuthorizationUri($config));
        // exit;
    }else {
        $oauth2->setCode($_GET['code']);
        $authToken = $oauth2->fetchAuthToken();
        $refreshToken = $authToken['refresh_token'];
        $data = array ();
        $data ['clientId'] = '92994361661-c2fa8fqr4leorru3iljc86su0ie15nkl.apps.googleusercontent.com';
        $data ['clientSecret'] = 'Ii1oW6A9il6JcoC0RdubBlXl';
        $data ['am_acountid'] = 'pub-8594727977662805';
        $data ['code'] = $authToken['refresh_token'];
        $count = DB::table('mg_admob_refresh_code')->where('am_acountid','pub-8594727977662805')->count();
        if($count == 0){
            DB::table('mg_admob_refresh_code')->insert($data);
        }else{
            DB::table('mg_admob_refresh_code')->where('am_acountid','pub-8594727977662805')->update(['code' => $authToken['refresh_token']]);
        }
        
        echo"<pre>";
        var_dump($authToken);
        echo "</pre>";
        echo "GET :" .$_GET['code'];
    }
    //查询Admob Refresh Code列表
        /*$codelist = DB::table('mg_admob_refresh_code')->get();

        $url1 = 'https://www.googleapis.com/oauth2/v4/token?';
        // foreach ($codelist as $key => $value) {
            
            $get_data = array (
                'client_id' => '418763551104-f6k5qs4fcv0dgdmfacn34me49h138qub.apps.googleusercontent.com',
                'client_secret' => 'QZZqFXKHgMjcUY7DJ3sI_Oy1',
                'refresh_token' => '1/9Dyzzv7xx_UvI4WQFY85mPxm6VoT9Kh61CQH7C7dUrM',
                'grant_type' => 'refresh_token',
            );
        
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_URL, $url1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $get_data);
            $result = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($result,true);
            dump($data);
            Redis::set('pub-5456881337868996access_token', $data['access_token']);
            dump(Redis::get($value->am_acountid.'access_token'));
        // }*/
        
    }
    
    //获取ironsource数据--有请求限制
    public function getIsource(){
        //$date = (new IndexController)->getDateFromRange(date("Y-m-d",strtotime("-1 day")),date("Y-m-d"));
     

        //查询游戏列表
        $gamelist = DB::table('mg_game')
        				->leftJoin('mg_game_report','mg_game.id','=','mg_game_report.id')
        				->select('mg_game.*','mg_game_report.date')
        				->get();
        				
        //ironsource数据
        foreach ($gamelist as $key2 => $value2) {
            // foreach ($date as $key1 => $value1) {
        		$value1 = date('Y-m-d',$value2->date);
                $crl = curl_init();
                $base64encoded = base64_encode("andappsok@gmail.com:0cc11edb4c4dff4d28eeb5d93ab71dcc");
                $header = array();
                $header[] = 'Authorization: Basic '. $base64encoded;
                $url1 = 'https://platform.ironsrc.com/partners/publisher/mediation/applications/v5/stats?';
                if($value2->is_app_key != '0'){
                    $get_data = array (
                        'startDate' => $value1,
                        'endDate' => $value1,
                        'metrics' => 'revenue,impressions',
                        'appKey' => $value2->is_app_key     //Snake Bubble
                    );
                    $URL = $url1.http_build_query($get_data);
                    curl_setopt($crl, CURLOPT_URL, $URL);
                    curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1); //设置不直接输出
                    curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
                    $response = curl_exec($crl);
                    curl_close($crl);
                    $res = json_decode($response,true);
                    dump($res);
                    echo $value2->name;exit();
                    $sumisrevenue = 0;
                    foreach ($res as $key => $value) {
                        if(!empty($value['data'])){
                            $is = $value['data'][0]['revenue'];
                        }else{
                            $is = 0;
                        }
                        $sumisrevenue += $is;
                        dump($sumisrevenue.'--'.$value2->name);
                        $data = array ();
                        $data ['is_revenue'] = $sumisrevenue;
                        $data ['game_id'] = $value2->id;
                        $data ['date'] = strtotime($value1);
                        $count  = DB::table('mg_game_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime($value1))
                            ->count();
                        if($count!=0){
                            DB::table('mg_game_report')
                                ->where('game_id',$value2->id)
                                ->where('date',strtotime($value1))
                                ->update(['is_revenue' => $sumisrevenue]);
                        }else{                        
                            DB::table('mg_game_report')->insert($data);
                        }
                    }
                // }
            }exit;
        }
        dump('------------------------------------ironsource处理完成----------------------------------------');
        set_time_limit(0);
        
    }
     //获取applovin数据
    public function getAlovin(){
        // $date = (new IndexController)->getDateFromRange(date("Y-m-d",strtotime("-1 day")),date("Y-m-d"));
        $date = (new IndexController)->getDateFromRange('2019-02-26','2019-03-05');
        //查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        // al变现数据
        $url = 'https://r.applovin.com/report?';
        
        foreach ($date as $key1 => $value1) {
            $get_data = array (
                'start' => $value1,
                'end' => $value1,
                'format' => 'json',
                'columns' => 'revenue,package_name,ecpm',
                'api_key'=>'KrwMq14VaH6NqbdkkHmRk-iz65hn9UfUKZs8bweAClT1LAl4SUVBTD8aIqLSJ0yeaonAiPLv9uwV3fZW94hcHq'
            );
            $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
            $res_ary = json_decode ( $res, true );
            if (! empty ( $res_ary )) {
                foreach ($gamelist as $key => $value) { 
                    foreach ( $res_ary['results'] as $k1 => $v1 ) {
                        if($v1["package_name"] == $value->al_package_name){
                            $data = array ();
                            $data ['al_revenue'] = round($v1 ['revenue'],2);
                            $data ['game_id'] = $value->id;
                            $data ['date'] = strtotime($value1);
                            $count  = DB::table('mg_game_report')
                                ->where('game_id',$value->id)
                                ->where('date',strtotime($value1))
                                ->count();
                            if($count!=0){
                                DB::table('mg_game_report')
                                    ->where('game_id',$value->id)
                                    ->where('date',strtotime($value1))
                                    ->update(['al_revenue' => round($v1 ['revenue'],2)]);
                            }else{                        
                                DB::table('mg_game_report')->insert($data);
                            }
                        }else{
                            $data = array ();
                            $data ['al_revenue'] = '0.00';
                            $data ['game_id'] = $value->id;
                            $data ['date'] = strtotime($value1);
                            $count  = DB::table('mg_game_report')
                                ->where('game_id',$value->id)
                                ->where('date',strtotime($value1))
                                ->count();
                            if($count!=0){
                                DB::table('mg_game_report')
                                    ->where('game_id',$value->id)
                                    ->where('date',strtotime($value1))
                                    ->update(['al_revenue' => '0.00']);
                            }else{                        
                                DB::table('mg_game_report')->insert($data);
                            }
                        }
                    }
                }
            }
        }
        dump('------------------------------------applovin处理完成----------------------------------------');
        set_time_limit(0);
    }
    public function getMobpub(){
        // $date = (new IndexController)->getDateFromRange(date("Y-m-d",strtotime("-1 day")),date("Y-m-d"));
        //$date = (new IndexController)->getDateFromRange('2019-02-26','2019-03-05');
        //查询游戏列表
        $date='2019-08-20';
        $gamelist = DB::table('mg_game')->get();
        
        //mobpub数据
        foreach ($gamelist as $key2 => $value2) {
            $mp = DB::table('mg_mobpub')->where('app_id',$value2->mp_app_id)->where('day',strtotime($date))->get();
            dump($mp);
            $sumrevenue = 0;
            foreach ($mp as $key3 => $value3) {
                $sumrevenue += $value3->revenue;
            }
            $data = array ();
            $data ['mp_revenue'] = round($sumrevenue,2);
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
                    ->update(['mp_revenue' => round($sumrevenue,2)]);
            }else{                        
                DB::table('mg_game_report')->insert($data);
            }
        }
        dump('------------------------------------mobpub处理完成----------------------------------------');
        set_time_limit(0);
    }
    // 每天定时下载mobpub csv文件
    public function mpDownloadCsv() {
        $date = '2019-08-24';
        
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
                $data1=DB::table('mg_game_country_report') 
                        ->where('game_id',$value2->id)  
                        ->where('date',strtotime($date))
                        ->where('country_code',$v->country)
                        ->update(['mp_revenue' => $v->revenue]);
                }else{                        
                    DB::table('mg_game_country_report')->insert($data);
                }
            }
        }
        
    
        /*$url = 'https://app.mopub.com/reports/custom/api/download_report?';
        $date = (new IndexController)->getDateFromRange('2019-08-25','2019-08-25');
        foreach ($date as $key1 => $value1) {
            
            $get_data = array (
                'report_key' => '0693ea4a0d084de78101ce8ee1662fcd',
                'api_key' => 'EW1sVmg2NvZfSTJ1RuWMNIXRIXuO_mfK',
                'date' => $value1 
            );
            $url = $url . http_build_query ( $get_data );
            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, $url );
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
            curl_exec ( $ch );
            $info = curl_getinfo ( $ch );
            curl_close ( $ch );
            $content = (new IndexController)->curl_get_https ( $info ['redirect_url'] );dump($content);exit();
            $file_name = 'mp-'.$value1.'.csv';
            (new IndexController)->downfile( $content, $file_name );
        }
        dump('-----------------------------定时下载csv数据完成-----------------------------------------');*/
    }
    public function Mpinsert()
    {
        // $date = date("Y-m-d",strtotime("-1 day"));
        //查询游戏列表
        $date = (new IndexController)->getDateFromRange('2019-02-22','2019-03-03');
        // $gamelist = DB::table('mg_game')->get();
        ini_set('max_execution_time','0');
            $filePath = '/web/nginx/www/storage/downloads/mp-2019-03-03.csv';
            Excel::load($filePath, function($reader) {
                $data = $reader->all();
                foreach ($data as $key => $value) {
                    $array = json_decode($value,true);
                    $data = array ();
                    $data ['day'] = strtotime($array['day']);
                    $data ['app'] = $array['app'];
                    $data ['app_id'] = $array['app_id'];
                    $data ['adunit'] = $array['adunit'];
                    $data ['adunit_id'] = $array['adunit_id'];
                    $data ['adunit_format'] = $array['adunit_format'];
                    $data ['device'] = $array['device'];
                    $data ['country'] = $array['country'];
                    $data ['os'] = $array['os'];
                    // $data ['requests'] = $array['requests'];
                    $data ['impressions'] = $array['impressions'];
                    $data ['clicks'] = $array['clicks'];
                    $data ['conversions'] = $array['conversions'];
                    $data ['revenue'] = $array['revenue'];
                    $data ['ctr'] = $array['ctr'];
                    DB::table('mg_mobpub')->insert($data);
                }
            });

        

    }
    //生成csv文件指定目录
    public function downfile($content, $file_name) {
        $fp = fopen ( '/web/nginx/www/storage/downloads/' . $file_name, "w" );
        fwrite ( $fp, $content );
        fclose ( $fp );
    }
    // 时间段日期时间遍历方法
    public function getDateFromRange($startdate, $enddate){ 
        $stimestamp = strtotime($startdate); 
        $etimestamp = strtotime($enddate); 
        $days = ($etimestamp-$stimestamp)/86400+1; 
        $date = array(); 
        for($i=0; $i<$days; $i++){ 
            $date[] = date('Y-m-d', $etimestamp-(86400*$i)); 
        } 
        return $date;
    }
    //curl聚合函数
    public function curl_get_https($url, $header = array()) {
        $curl = curl_init ();
        curl_setopt ( $curl, CURLOPT_URL, $url );
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 );
        curl_setopt ( $curl, CURLOPT_HEADER, 0 );
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
        
        if (! empty ( $header )) {
            curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header );
        }
        $res = curl_exec ( $curl );
        if (curl_errno ( $curl )) {
            echo 'Errno' . curl_error ( $curl );
        }
        // dump(curl_getinfo($curl));
        curl_close ( $curl );
        return $res;
    }

    public function curl_post_https($url, $header = array()) {
        $curl = curl_init ();
        curl_setopt ( $curl, CURLOPT_URL, $url );
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 );
        curl_setopt ( $curl, CURLOPT_HEADER, 0 );
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
        
        if (! empty ( $header )) {
            curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header );
        }
        $res = curl_exec ( $curl );
        if (curl_errno ( $curl )) {
            echo 'Errno' . curl_error ( $curl );
        }
        // dump(curl_getinfo($curl));
        curl_close ( $curl );
        return $res;
    }



    public function curl_del_https($path, $json = ''){
	    $url = $path;
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $result = curl_exec($ch);
	    $result = json_decode($result);
	    curl_close($ch);

	    return $result;
	}

    // fb花费分国家数据
    public function getCspend(){
        $date = '2019-03-07';
        //查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        foreach ($gamelist as $key2 => $value2) {
            //获取应用下全部账户信息
            $account = DB::table('mg_game')->where('id',$value2->id)->select('fb_read_accounts')->get(); 
            $account1 = explode(',',$account[0]->fb_read_accounts);
            $access_token = env('ACCESS_TOKEN');
            $array = [];
            foreach ($account1 as $key => $value) {
                $url1 = 'https://graph.facebook.com/v3.2/act_' . $value . '/insights?';
                $get_data = array (
                    'fields' => 'spend',
                    'breakdowns' => 'country',
                    'time_range[since]' => $date,
                    'time_range[until]' => $date,
                    'limit' => '2000',
                    'access_token' => $access_token
                );
                $output = (new IndexController)->curl_get_https ( $url1 . http_build_query ( $get_data ) );//获取数据
                $res = json_decode($output,true);
                if(!empty($res['data'])){
                    foreach ($res['data'] as $key3 => $value3) {
                        if(array_key_exists('spend', $value3)){
                            if(!isset($array[$value3['country']])){
                                $array[$value3['country']] = round($value3['spend'],2);
                            }else{
                                $array[$value3['country']] += round($value3['spend'],2);
                            }
                        }
                    }
                } 
                foreach ($array as $k1 => $v1 ) {
                    $data = array ();
                    $data ['cost'] = $v1;
                    $data ['game_id'] = $value2->id;
                    $data ['country_code'] = $k1;
                    $data ['date'] = strtotime($date);
                    $count  = DB::table('mg_game_country_report')
                        ->where('game_id',$value2->id)
                        ->where('date',strtotime($date))
                        ->where('country_code',$k1)
                        ->count();
                    if($count!=0){
                        DB::table('mg_game_country_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime($date))
                            ->where('country_code',$k1)
                            ->update(['cost' => $v1]);
                    }else{                        
                        DB::table('mg_game_country_report')->insert($data);
                    }
                }
            }
        }

        dump('fb分国家花费数据处理完成');
    }
    // 应用安装分国家数据
    public function getCinstall(){
        // $date = (new IndexController)->getDateFromRange('2019-03-05','2019-03-06');
        $date = '2019-03-07';
        $gamelist = DB::table('mg_game')->get();
        foreach ($gamelist as $key => $value5) {
            $account = DB::table('mg_game')->where('id',$value5->id)->select('fb_read_accounts')->get(); 
            $account1 = explode(',',$account[0]->fb_read_accounts);
            $access_token = env('ACCESS_TOKEN');
            $get_data = array (
                'fields' => 'actions',
                'breakdowns' => 'country',
                'time_range[since]' => $date,
                'time_range[until]' => $date,
                'limit' => '2000',
                'access_token' => env('ACCESS_TOKEN')
            );
            $array = [];
            foreach ($account1 as $key => $value) {
                $url1 = 'https://graph.facebook.com/v3.2/act_' . $value . '/insights?';
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
        }
        /*$date = '2019-03-07';
        $account = DB::table('mg_game')->where('id',22)->select('fb_read_accounts')->get(); 
        $account1 = explode(',',$account[0]->fb_read_accounts);
        $access_token = env('ACCESS_TOKEN');
        $get_data = array (
            'fields' => 'actions',
            'breakdowns' => 'country',
            'time_range' => '{"since":"' . $date . '","until":"' . $date . '"}',
            'limit' => '2000',
            'access_token' => env('ACCESS_TOKEN')
        );
        $array = [];
        foreach ($account1 as $key => $value) {
            $url1 = 'https://graph.facebook.com/v3.2/act_' . $value . '/insights?';
            $output = (new IndexController)->curl_get_https ( $url1 . http_build_query ( $get_data ) );//获取数据
            $res = json_decode($output,true);
            //dump($res);
            if(!empty($res['data'])){
                    foreach ($res['data'] as $k => $v) {
                        if(array_key_exists('actions', $v)){
                            foreach ($v['actions'] as $key3 => $value3) {
                                if($value3['action_type']=='mobile_app_install'){
                                    dump('2019-03-07'.'--'.$value.'--'.$v['country'].'--'.$value3['value']);
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
        dump($array);*/
        dump('---------------分国家安装数处理完成-----------------------');
}

    // 应用fb变现分国家数据
    public function getCfb(){
        /*$date = (new IndexController)->getDateFromRange('2019-02-25','2019-03-04');
        //查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        set_time_limit(0);
        foreach ($gamelist as $key2 => $value2) {  
            $url = 'https://graph.facebook.com/v3.2/'.$value2->fb_app_id.'/adnetworkanalytics/?';
            $get_data = array (
                'since' => '2019-02-25', 
                'until' => '2019-03-03',
                'metrics'=>'fb_ad_network_revenue',
                'breakdowns' => 'country',
                'access_token' => 'EAADXu7ZBbD90BAJoa1SvKsvDAwyvTOjSmsp5ORgvEZAljg8yG1dI3S8o2XsseGZBXL4GvxICZAKdtBKfM17MVeJuqTnGlt3nvZCOYLqVmexZAlJjhuIzlkGb7oQk3QpXmNoqtb0h1MxcS8xGZC26QHyNfvQh9kV08h8ZAv5nxKnW6AZDZD'
            );
            $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
            $res_ary = json_decode ( $res, true );
            dump($res_ary);
            if (! empty ( $res_ary['data'] )) {
                foreach ( $res_ary ['data'] [0] ['results'] as $k1 => $v1 ) {
                    $data = array ();
                    $data ['fb_ad_network_revenue'] = round($v1 ['value'],2);
                    $data ['game_id'] = $value2->id;
                    $data ['date'] = strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] )));
                    $count  = DB::table('mg_game_report')
                        ->where('game_id',$value2->id)
                        ->where('date',strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] ))))
                        ->count();
                    if($count!=0){
                        DB::table('mg_game_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime(date ( 'Y-m-d', strtotime ( $v1 ['time'] ))))
                            ->update(['fb_ad_network_revenue' => round($v1 ['value'],2)]);
                    }else{                        
                        DB::table('mg_game_report')->insert($data);
                    }

                }
            }
        }*/
        /*$url = 'https://graph.facebook.com/v3.2/380926222738059/adnetworkanalytics/?';
        $get_data = array (
            'since' => '2019-03-10', 
            'until' => '2019-03-10',
            'metrics'=>'fb_ad_network_revenue','fb_ad_network_cpm',
            'breakdowns' => 'country',
            'access_token' => env('ACCESS_TOKEN')
        );
        $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
        $res_ary = json_decode ( $res, true );
        dump($res_ary);*/

        $fb_metrics = array (
            'fb_ad_network_revenue',
            'fb_ad_network_cpm'
            // 'fb_ad_network_fill_rate' 
        );
        $date = '2019-03-29';
        $gamelist = DB::table('mg_game')->get();
        foreach ( $gamelist as $g ) {
            foreach ( $fb_metrics as $v ) {
                $url = 'https://graph.facebook.com/v3.2/'.$g->fb_app_id.'/adnetworkanalytics/?';
                $get_data = array (
                    'metrics' => '["' . $v . '"]',
                    'since' => $date,
                    'until' => $date,
                    'breakdowns' => '["country"]',
                    'access_token' => env('ACCESS_TOKEN')
                );
                
                $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
                // exit($res);
                $res_ary = json_decode ( $res, true );

                if (! empty ( $res_ary['data'] )) {
                    foreach ( $res_ary ['data'] [0] ['results'] as $k1 => $v1 ) {
                        // dump($v1);
                        $data = array ();
                        $data [$v] = $v1 ['value'];
                        $data ['game_id'] = $g->id;
                        $data ['country_code'] = $v1['breakdowns']['0']['value'];
                        $data ['date'] = strtotime($date);
                        $count  = DB::table('mg_game_country_report')
                            ->where('game_id',$g->id)
                            ->where('date',strtotime($date))
                            ->where('country_code',$v1['breakdowns']['0']['value'])
                            ->count();
                        if($count!=0){
                            DB::table('mg_game_country_report')
                                ->where('game_id',$g->id)
                                ->where('date',strtotime($date))
                                ->where('country_code',$v1['breakdowns']['0']['value'])
                                ->update([$v => $v1 ['value']]);
                        }else{                        
                            DB::table('mg_game_country_report')->insert($data);
                        }
                    }
                }
            }
        }





        /*$gamelist = DB::table('mg_game')->get();
        // foreach ($gamelist as $key2 => $value2) {   
            $url = 'https://graph.facebook.com/v3.2/380926222738059/adnetworkanalytics/?';
            $get_data = array (
                'since' => '2019-03-10', 
                'until' => '2019-03-10',
                'metrics'=>'fb_ad_network_revenue',
                'breakdowns' => 'country',
                'access_token' => env('ACCESS_TOKEN')
            );
            $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
            $res_ary = json_decode ( $res, true );
            if (! empty ( $res_ary['data'] )) {
                foreach ( $res_ary ['data'] [0] ['results'] as $k1 => $v1 ) {
                    $data = array ();
                    $data ['fb_ad_network_revenue'] = round($v1 ['value'],2);
                    $data ['game_id'] = $value2->id;
                    $data ['country_code'] = $value2->id;
                    $data ['date'] = strtotime('2019-03-10');
                    $count  = DB::table('mg_game_report')
                        ->where('game_id',$value2->id)
                        ->where('date',strtotime('2019-03-10')
                        ->count();
                    if($count!=0){
                        DB::table('mg_game_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime('2019-03-10')
                            ->where('game_id',$value2->id)
                            ->update(['fb_ad_network_revenue' => round($v1 ['value'],2)]);
                    }else{                        
                        DB::table('mg_game_report')->insert($data);
                    }

                }
            }else{
                $data = array ();
                $data ['fb_ad_network_revenue'] = '0.00';
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
                        ->update(['fb_ad_network_revenue' => '0.00']);
                }else{                        
                    DB::table('mg_game_report')->insert($data);
                }
            }*/
        // }
        

        dump('------------------------------------fb变现处理完成----------------------------------------');
        
    }
    // Mobpub变现分国家数据
    public function getCmobpub(){
        $date = (new IndexController)->getDateFromRange('2019-02-28','2019-03-05');
        //查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        set_time_limit(0);
        foreach ($date as $key1 => $value1) {
            foreach ($gamelist as $key2 => $value2) {
                $data = DB::table('mg_mobpub')->where('app_id',$value2->mp_app_id)->where('day',strtotime($value1))->get();
                foreach ($data as $k => $v) {
                    $data = array ();
                    $data ['mp_revenue'] = $v->revenue;
                    $data ['game_id'] = $value2->id;
                    $data ['date'] = strtotime($value1);
                    $count  = DB::table('mg_game_country_report')
                        ->where('game_id',$value2->id)
                        ->where('date',strtotime($value1))
                        ->where('country_code',$v->country)
                        ->count();
                    if($count!=0){
                        DB::table('mg_game_country_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime($value1))
                            ->where('country_code',$v->country)
                            ->update(['mp_revenue' => $v->revenue]);
                    }else{                        
                        DB::table('mg_game_country_report')->insert($data);
                    }
                }
            }
            
        }
        
        dump('------------------------------------mobpub处理完成----------------------------------------');
        
    }
    // Applovin变现分国家数据-----待测试
    public function getCalovin(){
        
        // $date = (new IndexController)->getDateFromRange('2019-02-25','2019-03-03');
        $date = (new IndexController)->getDateFromRange(date("Y-m-d",strtotime("-1 day")),date("Y-m-d"));
        //查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        set_time_limit(0);
        $url = 'https://r.applovin.com/report?';
        foreach ($date as $key1 => $value1) { 
            foreach ($gamelist as $key2 => $value2) {
                $get_data = array (
                    'start' => $value1,
                    'end' => $value1,
                    'format' => 'json',
                    'columns' => 'revenue,package_name,ecpm,country',
                    'api_key'=>'KrwMq14VaH6NqbdkkHmRk-iz65hn9UfUKZs8bweAClT1LAl4SUVBTD8aIqLSJ0yeaonAiPLv9uwV3fZW94hcHq'
                );
                $res = (new IndexController)->curl_get_https($url.http_build_query($get_data));
                $res_ary = json_decode ( $res, true );
                $array = [];
                if(!$res_ary['results']){
                    foreach ($res_ary['results'] as $key => $value) {
                        if($value['package_name'] == $value2->al_package_name){
                            $array[$value['country']] = $value['revenue'];
                        }
                    }
                }
                foreach ($array as $key3 => $value3) {
                    $data = array ();
                    $data ['al_revenue'] = $value3;
                    $data ['game_id'] = $value2->id;
                    $data ['date'] = strtotime($value1);
                    $data ['country_code'] = $key3;
                    $count  = DB::table('mg_game_country_report')
                        ->where('game_id',$value2->id)
                        ->where('date',strtotime($value1))
                        ->where('country_code',$key3)
                        ->count();

                    if($count!=0){
                        DB::table('mg_game_country_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime($value1))
                            ->where('country_code',$key3)
                            ->update(['al_revenue' => $value3]);
                    }else{                        
                        DB::table('mg_game_country_report')->insert($data);
                    }
                }
            }
        }
        dump('------------------------------------applovin处理完成----------------------------------------');
    }
    // Admob变现分国家数据
    public function getCadmob(){
        $date = '2019-03-10';
        $url = 'https://www.googleapis.com/adsense/v1.4/accounts/pub-5456881337868996/reports?metric=EARNINGS&dimension=COUNTRY_CODE&';
        $get_data = array (
            'startDate' => $date,
            'endDate' => $date,
            'currency' => 'USD',
            'dimension' => 'APP_NAME',
            'metric'=>'INDIVIDUAL_AD_IMPRESSIONS_RPM',
            'access_token' => Redis::get('pub-5456881337868996access_token')
        );
        $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
        $res_ary = json_decode ( $res, true );
        
        if (! empty ( $res_ary['rows'])) { 
            foreach ( $res_ary['rows'] as $k1 => $v1 ) {
                
                if($v1['1'] == 'Star Music - Free Music Player'){
                    dump($v1);
                    $data = array ();
                    $data ['am_revenue'] = $v1['2'];
                    $data ['game_id'] = 22;
                    $data ['date'] = strtotime($date);
                    $data ['country_code'] = $v1['0'];
                    $count  = DB::table('mg_game_country_report')
                        ->where('game_id',22)
                        ->where('date',strtotime($date))
                        ->where('country_code',$v1['0'])
                        ->count();

                    if($count!=0){
                        DB::table('mg_game_country_report')
                            ->where('game_id',22)
                            ->where('date',strtotime($date))
                            ->where('country_code',$v1['0'])
                            ->update(['am_revenue' => $v1['2']]);
                    }else{                        
                        DB::table('mg_game_country_report')->insert($data);
                    }
                }
            }
        }
        /*$date = '2019-03-07';
        $gamelist = DB::table('mg_game')->get();
        set_time_limit(0);
        foreach ($gamelist as $key2 => $value2) { 
            $url = 'https://www.googleapis.com/adsense/v1.4/accounts/'.$value2->am_account_id.'/reports?metric=EARNINGS&dimension=COUNTRY_CODE&';
            $get_data = array (
                'startDate' => $date,
                'endDate' => $date,
                'currency' => 'USD',
                'dimension' => 'APP_NAME',
                'metric'=>'INDIVIDUAL_AD_IMPRESSIONS_RPM',
                'access_token' => Redis::get($value2->am_account_id.'access_token')
            );
            $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
            $res_ary = json_decode ( $res, true );
            if (! empty ( $res_ary['rows'])) { 
                foreach ( $res_ary['rows'] as $k1 => $v1 ) {
                    if($v1['1'] == $value2->am_app_name){
                        // dump($v1['0'].'--'.$v1['2']);
                        $data = array ();
                        $data ['am_revenue'] = $v1['2'];
                        $data ['game_id'] = $value2->id;
                        $data ['date'] = strtotime($date);
                        $data ['country_code'] = $v1['0'];
                        $count  = DB::table('mg_game_country_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime($date))
                            ->where('country_code',$v1['0'])
                            ->count();

                        if($count!=0){
                            DB::table('mg_game_country_report')
                                ->where('game_id',$value2->id)
                                ->where('date',strtotime($date))
                                ->where('country_code',$v1['0'])
                                ->update(['am_revenue' => $v1['2']]);
                        }else{                        
                            DB::table('mg_game_country_report')->insert($data);
                        }
                    }
                }
            }
        }*/
        
        dump('------------------------------------分国家admob处理完成----------------------------------------');
    }
    // Ironsource变现分国家数据
    public function getCisource(){
        set_time_limit(0);
        // $date = (new IndexController)->getDateFromRange('2019-02-22','2019-03-03');
        // $date = (new IndexController)->getDateFromRange(date("Y-m-d",strtotime("-1 day")),date("Y-m-d"));
        if(date("H:i:s") >= '16:00:00' && date("H:i:s") <= '24:00:00'){
            $date = date("Y-m-d");
        }else{
            $date = date("Y-m-d",strtotime("-1 day"));
        }
        //查询游戏列表
        $gamelist = DB::table('mg_game')->get();
        foreach ($gamelist as $key2 => $value2) {
            // foreach ($date as $key1 => $value1) {
                $crl = curl_init();
                $base64encoded = base64_encode("andappsok@gmail.com:0cc11edb4c4dff4d28eeb5d93ab71dcc");
                $header = array();
                $header[] = 'Authorization: Basic '. $base64encoded;
                $url1 = 'https://platform.ironsrc.com/partners/publisher/mediation/applications/v5/stats?';
                    $get_data = array (
                        'startDate' => $date,
                        'endDate' => $date,
                        'metrics' => 'revenue,impressions',
                        'breakdowns' => 'country',
                        'appKey' => $value2->is_app_key     //Snake Bubble
                    );
                    $URL = $url1.http_build_query($get_data);
                    curl_setopt($crl, CURLOPT_URL, $URL);
                    curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1); //设置不直接输出
                    curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
                    $response = curl_exec($crl);
                    curl_close($crl);
                    $res = json_decode($response,true);dump($res);
                    $sumisrevenue = 0;
                    $array = [];
                    foreach ($res as $key => $value) {
                        if(!empty($value['data'])){
                            foreach ($value['data'] as $key => $value) {
                                if(!isset($array[$value['countryCode']])){
                                    $array[$value['countryCode']] = $value['revenue'];
                                }else{
                                    $array[$value['countryCode']] += $value['revenue'];
                                }
                            }
                        }
                    }
                    dump($array);
                    foreach ($array as $key3 => $value3) {
                        $data = array ();
                        $data ['is_revenue'] = $value3;
                        $data ['game_id'] = $value2->id;
                        $data ['date'] = strtotime($date);
                        $data ['country_code'] = $key3;
                        $count  = DB::table('mg_game_country_report')
                            ->where('game_id',$value2->id)
                            ->where('date',strtotime($date))
                            ->where('country_code',$key3)
                            ->count();
                        if($count!=0){
                            DB::table('mg_game_country_report')
                                ->where('game_id',$value2->id)
                                ->where('date',strtotime($date))
                                ->where('country_code',$key3)
                                ->update(['is_revenue' => $value3]);
                        }else{                        
                            DB::table('mg_game_country_report')->insert($data);
                        }
                    }
                // }
            }
        dump('------------------------------------ironsource处理完成----------------------------------------');
    }

    public function export()
    {
        ini_set('memory_limit','500M');
        set_time_limit(0);//设置超时限制为不限制
        $cellData =  $data = DB::table('mg_game_country_report')
                        ->join('mg_country', 'mg_game_country_report.country_code', '=', 'mg_country.code_2')
                        ->select('mg_country.name','mg_game_country_report.fb_ad_network_revenue','mg_game_country_report.is_revenue','mg_game_country_report.mp_revenue','mg_game_country_report.al_revenue','mg_game_country_report.am_revenue','mg_game_country_report.cost','mg_game_country_report.install_count')
                        ->get()
                        ->toArray();
        $cellData[0] = array('国家','FB预计收入(昨日)','IronSoure预计收入','Mopub预计收入','Applovin预计收入','Admob预计收入','总支出','安装数量');
        for($i=0;$i<count($cellData);$i++){
            $cellData[$i] = array_values($cellData[$i]);
            $cellData[$i][0] = str_replace('=',' '.'=',$cellData[$i][0]);
        }
        //dd($cellData);
        Excel::create('分国家数据',function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
        die;
    }



    public function url(){
        /*$url = 'https://graph.facebook.com/v3.2/2198194620441817?';
        $get_data = array (
            'field'=>'is_published',
            'access_token' => env('ACCESS_TOKEN')
        );*/
        $res = (new IndexController)->curl_get_https ('https://graph.facebook.com/2198194620441817?fields=is_published,name&access_token='.env('ACCESS_TOKEN'));
        $res_ary = json_decode ( $res, true );
        dump($res_ary);
    }

    
}
