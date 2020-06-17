<?php
/**
 * Created by PhpStorm.
 * Author: hmq <304550409@qq.com>
 * Date: 18-10-26下午1:23
 * Desc: 管理员
 */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Redis;
use Illuminate\Support\Facades\Log;
use Excel;
class ReportController extends Controller
{

    // 上传FB花费数据
    public function comparedXls(Request $request){
        
        if( $request->isMethod('post') && $_FILES['file'] ){
            ini_set("memory_limit", -1);
            $file = $_FILES;
            $excel_file_path = $file['file']['tmp_name'];
            $res = [];  
            Excel::load($excel_file_path, function($reader) use( &$res ) {  
                $reader = $reader->getSheet(0);  
                $res = $reader->toArray();  
            });
            $y = '2019'; //年 
            $m = $request->input('month'); //月 
            $d = '1'; //日 
            $t0 = date('t',strtotime('2019-'.$m.'-1')); // 本月一共有几天 
            $start_month = mktime(0, 0, 0, $m, 1, $y); // 创建本月开始时间 
            $end_month = mktime(23, 59, 59, $m, $t0, $y); // 创建本月结束时间
            dump(date('Y-m-d',$start_month).'----'.date('Y-m-d',$end_month));
            foreach ($res as $key => $value) {
                // dump($value);
                if(is_numeric($value['0'])){
                    // 获取facebook花费数据
                    $get_data = array (
                        'fields' => 'spend',
                        'time_range[since]' => date('Y-m-d',$start_month),
                        'time_range[until]' => date('Y-m-d',$end_month),
                        'access_token' => 'EAAiEr5ZBAl2oBAA7JmZBGf6dwzVsZAZB83XSfcZAR8NgFrKxFiOS2ueS46bq8taic0e3A8sSwK9PU6O2hIyFM2nrusggKCH9n4UX2oGXHUkTeFZCMZBNXOapMCLwqJZBQ77kwZAsQ6YMorya8FBSSdGoHSO4YqmNzM2FZAkRBcG9vHfQZCDL2mw6QeMhylQQzn3PlVel6HmstO5awZDZD'
                    );
                    $url1 = 'https://graph.facebook.com/v5.0/act_' . $value['0'] . '/insights?';
                    $output = (new IndexController)->curl_get_https ( $url1 . http_build_query ( $get_data ) );//获取数据
                    $res = json_decode($output,true);
                    dump($res);
                    if(!empty($res['data'])){
                        $own_spend = $res['data']['0']['spend'];
                    }else{
                        $own_spend = '0';
                    }
                    $data = array ();
                    $data ['act_id'] = $value['0'];
                    $data ['fb_spend'] = $value['1'];
                    $data ['own_spend'] = $own_spend;
                    $data ['company'] = $request->input('company');
                    $data ['month'] = $request->input('month');
                    $data ['difference'] = round($data ['own_spend'] - str_replace(',','',str_replace(' ','',$data ['fb_spend'])),2);
                    DB::table('comparedxls')->insert($data);
                    dump($value['0'].'成功');
                }else{
                    dump('1111');
                }
            }
            return '上传成功！';
        }
        
        return view('admin.compared_xls');
    }


    // 创建数据库
    public function getCompared(Request $request){


        $data = DB::table('comparedxls');

        if($request->input('company')){
            $company = $request->input('company');
        }else{
        	$company = 'MAD';
        }
        if($request->input('month')){
            $month = $request->input('month');
        }else{
        	$month = '12';
        }
        $spenddata = $data->where('month',$month)->where('company',$company)->paginate(100);

        return view('admin.get_compared',['spenddata' => $spenddata,'month' => $month,'company' => $company]);
    }


    public function Downloadcompared(Request $request){
        ini_set('memory_limit','500M');
        set_time_limit(0);//设置超时限制为不限制

        $month = $request->input('month');
        $company = $request->input('company');
        $cellData1 = DB::table('comparedxls')->where('month',$month)->where('company',$company)->get();

        $cellData = json_decode($cellData1,true);

        Excel::create($company.'--'.$month.'对账数据', function($excel) use ($cellData) { 
            $excel->sheet('数据', function($sheet) use ($cellData) { 
                $sheet->cell('A1', function($cell) {$cell->setValue('Accout ID'); });
                $sheet->cell('B1', function($cell) {$cell->setValue('Amount Due'); });
                $sheet->cell('C1', function($cell) {$cell->setValue('FB'); });
                $sheet->cell('D1', function($cell) {$cell->setValue('Compare'); });
                $sheet->setColumnFormat(array(
				    'A' => '0',
				    'D' => '@',
				));
                if (!empty($cellData)) { 
                    foreach ($cellData as $key => $value) { 
   						// dump($value);exit;
                        $i= $key+2; 
                        if($value['difference'] != '0'){
                        	$difference = $value['difference'];
                        }else{
                        	$difference = '数据正常';
                        }
                        $sheet->cell('A'.$i, (string)$value['act_id']);
                        $sheet->cell('B'.$i, str_replace(',','',str_replace(' ','',$value['fb_spend'])));
                        $sheet->cell('C'.$i, $value['own_spend']);
                        $sheet->cell('D'.$i, $difference);
                        
                       
                    } 
                } 
            }); 
        })->download('xlsx');
    }


    /**
     * @Desc: 广告日报表列表
     * @Author: hmq <304550409@qq.com>
     * @return \Illuminate\View\View
     */
    public function uplaodXls(Request $request){
    	
        if( $request->isMethod('post') && $_FILES['file'] ){
        	ini_set("memory_limit", -1);
            $file = $_FILES;
            $excel_file_path = $file['file']['tmp_name'];
            $res = [];  
            Excel::load($excel_file_path, function($reader) use( &$res ) {  
                $reader = $reader->getSheet(0);  
                $res = $reader->toArray();  
            });
            foreach ($res as $key => $value) {
                // dump($value['0']);exit;

                $data = array ();
                $data ['app_name'] = $value['0'];
                $data ['company'] = $value['1'];
                $data ['range'] = $value['2'];
                $data ['range_change'] = $value['3'];
                $data ['range_change_num'] = $value['4'];
                $data ['category'] = $value['5'];
                $data ['star'] = $value['6'];
                $data ['create_date'] = $value['7'];
                $data ['update_date'] = $value['8'];
                $data ['date'] = $request->input('date');
                DB::table('app_rank')->insert($data);
            }
            return '上传成功！';
        }
        
        return view('admin.upload_xls');
    }


    public function uplaodtoolXls(Request $request){
        
        if( $request->isMethod('post') && $_FILES['file'] ){
            ini_set("memory_limit", -1);
            $file = $_FILES;
            $excel_file_path = $file['file']['tmp_name'];
            $res = [];  
            Excel::load($excel_file_path, function($reader) use( &$res ) {  
                $reader = $reader->getSheet(0);  
                $res = $reader->toArray();  
            });
            foreach ($res as $key => $value) {
                // dump($value['0']);exit;

                $data = array ();
                $data ['app_name'] = $value['0'];
                $data ['company'] = $value['1'];
                $data ['range'] = $value['2'];
                $data ['range_change'] = $value['3'];
                $data ['range_change_num'] = $value['4'];
                $data ['category'] = $value['5'];
                $data ['star'] = $value['6'];
                $data ['create_date'] = $value['7'];
                $data ['update_date'] = $value['8'];
                $data ['date'] = $request->input('date');
                DB::table('tool_rank')->insert($data);
            }
            return '上传成功！';
        }
        
        return view('admin.upload_tool_xls');
    }





    // 上传外单admob数据
    public function uplaodAdmob(Request $request){
        $gamelist = DB::table('mg_game')->where('cate',0)->orderBy('name')->get();   
        
        if( $request->isMethod('post') && $_FILES['file'] ){
            ini_set("memory_limit", -1);
            $appid = $request->input('appid');


            $file = $_FILES;
            $excel_file_path = $file['file']['tmp_name'];
            // $excel_file_path = 'storage/import/上月admmob总数据.xlsx';
            /*$content = file_get_contents($excel_file_path);
            $fileType = mb_detect_encoding($content , array('UTF-8','GBK','LATIN1','BIG5'));//获取当前文本编码格式
            dump($fileType);*/
            $res = [];  
            Excel::load($excel_file_path, function($reader) use( &$res )  {  
                $reader1 = $reader->getSheet(0);  
                $res = $reader1->toArray();  
                unset($res[0]);
            });
            // dump($res);exit;
            $am_revenue = '0';
            $am_cpm = '0';
            foreach ($res as $key => $value) {
                // dump($value);
                $am_revenue += $value['11'];
                $am_cpm += $value['13'];
                $countrycode = DB::table('mg_country')->where('name',$value['2'])->first();
                // dump($countrycode);exit;

                if($countrycode){
                    $count = DB::table('mg_game_country_report')->where('country_code',$countrycode->code_2)->where('game_id',$request->input('appid'))->where('date',strtotime($request->input('date')))->count();
                    if($count == '0'){
                        $countryreport = DB::table('mg_game_country_report')->insert([
                            'am_revenue'=>$value['11'],
                            'am_cpm'=>$value['13'],
                            'game_id' => $request->input('appid'),
                            'date' => strtotime($request->input('date')),
                            'country_code' => $countrycode->code_2
                        ]);
                    }else{
                        $countryreport = DB::table('mg_game_country_report')->where('country_code',$countrycode->code_2)->where('game_id',$request->input('appid'))->where('date',strtotime($request->input('date')))->update(['am_revenue'=>$value['11'],'am_cpm'=>$value['13']]);
                    } 
                }


                
            }
            $countryreport = DB::table('mg_game_report')->where('game_id',$request->input('appid'))->where('date',strtotime($request->input('date')))->update(['am_revenue'=>$am_revenue,'am_cpm'=>$am_cpm]);
            return '上传处理成功！';
        }
        
        return view('admin.upload_admob',['gamelist' => $gamelist]);
    }

    // Admob Ecpm数据对比
    public function Comparison(Request $request)
    {
    	//查询国家列表
        $countrylist = DB::table('mg_country')->orderBy('code_2')->get();
        $gamelisttotal = DB::table('mg_game')->where('status',1)->get(); 
        


        if($request->input('game')){
            $gameid = explode(',',$request->input('game'));
        }else{
            $gameid = explode(',','74,100,89');
        }
        
        //dump($gameid);
        if($request->input('today')){
            $today = $request->input('today');
        }else{
            if(date("H:i:s") >= '16:00:00' && date("H:i:s") <= '24:00:00'){
                $today = date("Y-m-d");
            }else{
                $today = date("Y-m-d",strtotime("-1 day"));
            } 
        }      

        if($request->input('time2')){
            $time2 = $request->input('time2');
        }else{
            if(date("H:i:s") >= '16:00:00' && date("H:i:s") <= '24:00:00'){
                $time2 = date("Y-m-d");
            }else{
                $time2 = date("Y-m-d",strtotime("-1 day"));
            } 
        }

        if($request->input('top')){
            $top = $request->input('top');
        }else{
            $top = '10';
        }

         
        //默认查询第一个应用的admob收益排名前十的国家数据
        $cid = DB::table('mg_game_country_report')->where('game_id',$gameid['0'])->where('date',strtotime($today))->select('country_code')->orderBy('am_revenue','desc')->limit($top)->get();
         //dump($cid);
        $countryid1 = array();
        foreach ($cid as $key5 => $value5) {
            $countryid1[] = $value5->country_code;
        }
        
        if($request->input('country')){
            $countryid = explode(',',$request->input('country'));
        }else{
            $countryid = $countryid1;
        }
        //dump($countryid);
        
        // $gamelist = DB::table('mg_game')->whereIn('id',$gameid)->get();
        $gamelist = array();
        foreach ($gameid as $key3 => $value3) {
            $gamelist222 = DB::table('mg_game')->where('id',$value3)->select('name')->get();
            // dump(json_decode($gamelist222)['0']->name);
            $gamelist[] = json_decode($gamelist222)['0']->name;
        }
        sort($gamelist);
         //dump($gamelist);

         $list=array(); $null = array();
         foreach ($countryid as $key1 => $value1) {
        //dump($key1);
       $data = DB::table('mg_game')
                ->join('mg_game_country_report','mg_game.id','=','mg_game_country_report.game_id')
                ->select('mg_game.name',DB::raw('
                    sum(mg_game_country_report.am_cpm) as am_cpm,
                    sum(mg_game_country_report.am_revenue) as am_revenue,
                    sum(mg_game_country_report.install_count) as install_count,
                    sum(mg_game_country_report.cost) as cost'));
            if ($today) { 
                $data = $data->where('mg_game_country_report.date','>=',strtotime($today)); 
            }
            if ($time2) { 
                $data =$data->where('mg_game_country_report.date','<=',strtotime($time2)); 
            }
            if ($countryid) { 
            $data = $data->where('mg_game_country_report.country_code',$value1);
            }
            if ($gamelist) { 
            $data = $data->whereIn('mg_game.name',$gamelist);
            }
            $data = $data->groupBy('mg_game.name')->orderBy('name','asc')->get();//dump($data);
            if(!empty($data)){
               $list[$key1] = $data;

           }else{
            foreach ($null as $key => $value) {
            $emptydata = DB::table('mg_game')
                ->join('mg_game_country_report','mg_game.id','=','mg_game_country_report.game_id')
                ->select('mg_game.name',DB::raw('
                    sum(mg_game_country_report.am_cpm) as am_cpm,
                    sum(mg_game_country_report.am_revenue) as am_revenue,
                    sum(mg_game_country_report.install_count) as install_count,
                    sum(mg_game_country_report.cost) as cost'));
            if ($today) { 
                $data = $data->where('mg_game_country_report.date','>=',$today); 
            }
            if ($time2) { 
                $data =$data->where('mg_game_country_report.date','<=',$time2); 
            }
            if ($countryid) { 
            $data = $data->where('mg_game_country_report.country_code',$value);
            }
            if ($gamelist) { 
            $data = $data->whereIn('mg_game.name',$gamelist);
            }
            $data = $data->groupBy('mg_game.name')->paginate(300);//dump(json_decode($data));
                    $list[$key1] = $emptydata;
                }           
              }  
            }  
            //dump($list);           
        return view('admin.comparison_report',['top'=>$top,'today'=>$today,'time2'=>$time2,'gamelisttotal'=>$gamelisttotal,'gamelist'=>$gamelist,'gameid'=>$gameid,'countryid'=>$countryid,'countrylist'=>$countrylist,'list'=>$list]);
    }





    public function appRank(Request $request){

        $data = DB::table('app_rank');
        $catedata = DB::table('app_rank')->select('category')->distinct()->get();

        if($request->input('today')){
            $today1 = $request->input('today');
        }else{
            if(date("H:i:s") >= '16:00:00' && date("H:i:s") <= '24:00:00'){
	            $today1 = date("Y-m-d");
	        }else{
	            $today1 = date("Y-m-d",strtotime("-1 day"));
	        } 
	        // $today = '2019-07-02';
        }

        if($request->input('today')){
            $today = $request->input('today');
        }else{
            $today = date("Y-m-d",strtotime("-1 day"));
        }
        

        // 这段代码只运行一次  ------  开始
	        $newrank = DB::table('app_rank')->where('date',$today1)->first();
	        if($newrank&&$newrank->new_in_rank == null){
	        	$appdata = DB::table('app_rank')->where('date',date("Y-m-d", strtotime($today1."-1 days")))->select('app_name')->get();
		        $todayappdata = DB::table('app_rank')->where('date',$today)->select('app_name')->get();
		        foreach (json_decode($todayappdata) as $key => $value) {
		        	if(in_array($value,json_decode($appdata))){
		        		DB::table('app_rank')->where('date',$today1)->where('app_name','like',$value->app_name.'%')->update(['new_in_rank'=>1]);
		        	}else{
		        		DB::table('app_rank')->where('date',$today1)->where('app_name','like',$value->app_name.'%')->update(['new_in_rank'=>0]);
		        	}
		        }
	        }
        // ------------------- 结束

        if($request->input('category')){
        	$category = $request->input('category');
        }else{
        	$category = '';
        }
        
        if($category){
        	$data = $data->where('category',$request->input('category'));
        }
        if ($request->input('qushi')&&$request->input('qushi')=='up') { 
            $data = $data->where('range_change','上升'); 
        } 
        if ($request->input('rankcontinu')&&$request->input('rankcontinu')=='one') { 
            $data = $data->where('new_in_rank',0); 
        }
        if ($request->input('qushi')&&$request->input('qushi')=='down') { 
            $data = $data->where('range_change','下降'); 
        } 
        $list = $data->where('date',$today)->get();

        return view('admin.app_rank',['list'=>$list,'today'=>$today,'catedata'=>$catedata,'category'=>$category]);
    }



    public function toolRank(Request $request){

        $data = DB::table('tool_rank');
        $catedata = DB::table('app_rank')->select('category')->distinct()->get();

        if($request->input('today')){
            $today1 = $request->input('today');
        }else{
            if(date("H:i:s") >= '16:00:00' && date("H:i:s") <= '24:00:00'){
                $today1 = date("Y-m-d");
            }else{
                $today1 = date("Y-m-d",strtotime("-1 day"));
            } 
            // $today = '2019-07-02';
        }

        if($request->input('today')){
            $today = $request->input('today');
        }else{
            $today = date("Y-m-d",strtotime("-1 day"));
        }
        

        // 这段代码只运行一次  ------  开始
            $newrank = DB::table('app_rank')->where('date',$today1)->first();
            if($newrank&&$newrank->new_in_rank == null){
                $appdata = DB::table('app_rank')->where('date',date("Y-m-d", strtotime($today1."-1 days")))->select('app_name')->get();
                $todayappdata = DB::table('app_rank')->where('date',$today)->select('app_name')->get();
                foreach (json_decode($todayappdata) as $key => $value) {
                    if(in_array($value,json_decode($appdata))){
                        DB::table('app_rank')->where('date',$today1)->where('app_name','like',$value->app_name.'%')->update(['new_in_rank'=>1]);
                    }else{
                        DB::table('app_rank')->where('date',$today1)->where('app_name','like',$value->app_name.'%')->update(['new_in_rank'=>0]);
                    }
                }
            }
        // ------------------- 结束

        if($request->input('category')){
            $category = $request->input('category');
        }else{
            $category = '';
        }
        
        if($category){
            $data = $data->where('category',$request->input('category'));
        }
        if ($request->input('qushi')&&$request->input('qushi')=='up') { 
            $data = $data->where('range_change','上升'); 
        } 
        if ($request->input('rankcontinu')&&$request->input('rankcontinu')=='one') { 
            $data = $data->where('new_in_rank',0); 
        }
        if ($request->input('qushi')&&$request->input('qushi')=='down') { 
            $data = $data->where('range_change','下降'); 
        } 
        $list = $data->where('date',$today)->get();

        return view('admin.tool_rank',['list'=>$list,'today'=>$today,'catedata'=>$catedata,'category'=>$category]);
    }

    public function dreportList(Request $request)
    {



        if($request->input('time3')){
            $time3 = strtotime($request->input('time3'));
        }else{
            if(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
                $time3 = strtotime(date("Y-m-d"));
            }else{
                $time3 = strtotime(date("Y-m-d",strtotime("-1 day")));
            }
        }
        if($request->input('cost')){
            $cost = $request->input('cost');
            // dump($install);
        }else{
            $cost = 0;
        }
        if($request->input('install')){
            $install = $request->input('install');
            // dump($install);
        }else{
            $install = 0;
        }

        if($request->input('appid')){
            Redis::set('appid', $request->input('appid'));
            $appid = $request->input('appid');
        }else{
            //如果redis过期，则默认appid为143
            if(Redis::get('appid')){
                $appid = Redis::get('appid');
            }else{
                $appid = '143';
            }
            
        }


        // dump($appid);
        // dump($time3);
        $dayreportdata = DB::table('mg_game_report')->where('game_id',$appid)->where('date',$time3)->first();
        // dump($dayreportdata);
        DB::table('mg_game_report')->where('game_id',$appid)->where('date',$time3)->update(['cost' => $dayreportdata->cost+$cost,'install_count' => $dayreportdata->install_count+$install]);   



        $lastweek = date("Y-m-d",strtotime("-1 week"));
        $today1 = date("Y-m-d");
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->orderBy('name')->get();   
        $gameid = DB::table('mg_game')->where('status',1)->first();
        // dump($gameid);
        //获取页面传过来的查询条件并默认展示最近七天数据
        if($request->input('time1')){
            $time1 = strtotime($request->input('time1'));
        }else{
            $time1 = strtotime(date("Y-m-d",strtotime("-1 week")));
        }
        if($request->input('time2')){
            $time2 = strtotime($request->input('time2'));
        }else{
            $time2 = strtotime(date("Y-m-d"));
        }
        

        

       

        // 搜索条件
        $data = DB::table('mg_game_report')->orderBy('date','Desc'); 
        if ($time1) { 
            $data = $data->where('date','>=',$time1); 
        } 
        if ($time2) { 
            $data = $data->where('date','<=',$time2); 
        } 
        if ($appid) { 
            $data = $data->where('game_id',$appid); 
        } 
        if($time1&&$time2&&!$appid){
            $data = $data->where('game_id',$gameid->id); 
        }
        $list = $data->paginate(2000);
        $gamename = DB::table('mg_game')->where('id',$appid)->value('name'); 
        return view('admin.dreport_list',['list'=>$list,'gamelist'=>$gamelist,'gamename'=>$gamename,'appid'=>$appid,'time1'=>date('Y-m-d',$time1),'time2'=>date('Y-m-d',$time2)]);
    }

    

    public function countryAds(Request $request){
        //查询列表
        $gameid = DB::table('mg_game')->where('status',1)->where('upltv_id','=',0)->select('id')->get();
        $gameidarray = [];
        foreach ($gameid as $key => $value) {
            $gameidarray[] = $value->id;
        }
        //查询国家列表
        $countrylist = DB::table('mg_country')->get();

        if(strstr($request->date,' - ', TRUE)){
            $time1 = strtotime(strstr($request->date,' - ', TRUE));
        }elseif(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $time1 = strtotime(date("Y-m-d"));
        }
        else{
            $time1 = strtotime(date('Y-m-d', strtotime('-1 day')));
        }


        if(str_replace(" - ", "",strstr($request->date,' - '))){
            $time2 = strtotime(str_replace(" - ", "",strstr($request->date,' - ')));
        }elseif(date("H:i:s") >= '16:00:00' && date("H:i:s") <= '24:00:00'){
            $time2 = strtotime(date("Y-m-d"));
        }
        else{
            $time2 = strtotime(date('Y-m-d', strtotime('-1 day')));
        }

        if($request->input('countryid')){
            $countryid = $request->input('countryid');
        }else{
            $countryid = '';
        }

        $data = DB::table('mg_game_country_report')
            ->join('mg_country','mg_game_country_report.country_code', '=', 'mg_country.code_2')
            ->where('mg_game_country_report.date','>=',$time1)
            ->where('mg_game_country_report.date','<=',$time2)
            ->whereIn('mg_game_country_report.game_id',$gameidarray)
            ->select('mg_game_country_report.country_code','mg_country.english',DB::raw('
                sum(mg_game_country_report.install_count) as install_count,
                sum(mg_game_country_report.cost) as cost,
                sum(mg_game_country_report.profit) as profit,
                sum(mg_game_country_report.roi) as roi
            '));
        if (!empty($countryid)) { 
           $data = $data->where('mg_game_country_report.country_code',$countryid); 
        }    
        $list = $data->groupBy('country_code')->paginate(300);
        return view('admin.country_ads',['list'=>$list,'countrylist'=>$countrylist,'countryid'=>$countryid,'time1'=>date('m/d/Y',$time1),'time2'=>date('m/d/Y',$time2)]);

    } 





    /**
     * @Desc: 广告国家报表列表
     * @Author: hmq <304550409@qq.com>
     * @return \Illuminate\View\View
     */
    public function countryReportList(Request $request)
    {
        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->orderBy('name')->get();
        
        $gameid = DB::table('mg_game')->where('status',1)->first();
        //查询国家列表
        $countrylist = DB::table('mg_country')->get();
        //获取页面传过来的查询条件并默认展示最近七天数据


        if(strstr($request->date,' - ', TRUE)){
            $time1 = strtotime(strstr($request->date,' - ', TRUE));
        }elseif(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $time1 = strtotime(date("Y-m-d"));
        }
        else{
            $time1 = strtotime(date('Y-m-d', strtotime('-1 day')));
        }


        if(str_replace(" - ", "",strstr($request->date,' - '))){
            $time2 = strtotime(str_replace(" - ", "",strstr($request->date,' - ')));
        }elseif(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $time2 = strtotime(date("Y-m-d"));
        }
        else{
            $time2 = strtotime(date('Y-m-d', strtotime('-1 day')));
        }
        
        

        if($request->input('appid')){
            $appid = $request->input('appid');
            // dump($appid);
        }else{
            $appid = $gameid->id;
        }

        if($request->input('select')){
            $select = $request->input('select');
        }else{
            $select = 'profit';
        }

        if($request->input('countryid')&& $request->input('countryid') != 'nanya' && $request->input('countryid') != 'beiou'&& $request->input('countryid') != 'jialebi'&& $request->input('countryid') != 'lamei'&& $request->input('countryid') != 'laomei'&& $request->input('countryid') != 'xiou'&& $request->input('countryid') != 'nanou'&& $request->input('countryid') != 'dongou'){
            $countryid = [$request->input('countryid')];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'nanya') {
            $countryid = ['BT','LK','MV','AE','ID','MM','KH','VN','PH','TH','SA','QA'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'beiou') {
            $countryid = ['IE','NO','SE','FI','DK','IS','UK'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'jialebi') {
            $countryid = ['AG','AW','BB','VG','KY','DM','DO','GD','GP','HT','JM','MQ','MS','PR','KN','VC','BS','TT','TC'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'lamei') {
            $countryid = ['CL','CO','CR','MX','PA','PE','UY'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'laomei') {
            $countryid = ['AU','CA','US','NZ','JP'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'xiou') {
            $countryid = ['MC','NL','CH','GB','DK','BE','AT','LU','DE','SG','KR'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'nanou') {
            $countryid = ['IT','ES','GR','PT','MT','MC','FR','My','TW','MO'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'dongou') {
            $countryid = ['BG','BY','EE','HR','HU','LT','LV','ME','PL','RO','RS','SK','CZ','CY','PH','IL','RU','TH','AE','SA','KZ','SV'];
        }else{
            $countryid = '';
        }
        $gamename = DB::table('mg_game')->where('id',$appid)->value('name'); 
        
        //foreach($gamelist as $k=>$v) if(!empty($v->id)){$array[35]=$v->id;}

            
        if (!empty($countryid)) { 
            // 搜索条件
            $data = DB::table('mg_game_country_report')
                ->join('mg_country','mg_game_country_report.country_code', '=', 'mg_country.code_2')
                ->select('mg_game_country_report.*','mg_country.english');

            if ($time1) { 
                $data = $data->where('mg_game_country_report.date','>=',$time1); 
            }
            if ($time2) { 
                $data =$data->where('mg_game_country_report.date','<=',$time2); 
            } 
            if ($appid) { 
                $data = $data->where('mg_game_country_report.game_id',$appid); 
            }
           
            if ($select) { 
                $data = $data->orderBy('date','Desc'); 
            } 
            $data = $data->whereIn('mg_game_country_report.country_code',$countryid); 
            $list = $data->paginate(300);
        }else{
            // 搜索条件
            $data = DB::table('mg_game_country_report')
                ->join('mg_country', 'mg_game_country_report.country_code', '=', 'mg_country.code_2')
                ->select('mg_game_country_report.country_code','mg_country.english',DB::raw('
                    sum(mg_game_country_report.am_cpm) as am_cpm,
                    sum(mg_game_country_report.am_revenue) as am_revenue,
                    sum(mg_game_country_report.fb_ad_network_revenue) as fb_ad_network_revenue,
                    sum(mg_game_country_report.fb_ad_network_cpm) as fb_ad_network_cpm,
                    sum(mg_game_country_report.mp_revenue) as mp_revenue,
                    sum(mg_game_country_report.upltv) as upltv,
                    sum(mg_game_country_report.is_revenue) as is_revenue,
                    sum(mg_game_country_report.install_count) as install_count,
                    sum(mg_game_country_report.cost) as cost,
                    sum(mg_game_country_report.profit) as profit,
                    sum(mg_game_country_report.total) as total,
                    sum(mg_game_country_report.adx_revenue) as adx_revenue,
                    sum(mg_game_country_report.ctr) as ctr,
                    sum(mg_game_country_report.roi) as roi
                '));
            if ($time1) { 
                $data = $data->where('mg_game_country_report.date','>=',$time1); 
            }
            if ($time2) { 
                $data =$data->where('mg_game_country_report.date','<=',$time2); 
            } 
            if ($appid) { 
                $data = $data->where('mg_game_country_report.game_id',$appid);
            }
            
            if ($select) { 
                $data = $data->orderBy($select,'Desc'); 
            } 

    
            $list = $data->groupBy('country_code')->paginate(300);
            // dump($list);
        }        
        return view('admin.country_report',['list'=>$list,'countrylist'=>$countrylist,'countryid'=>$countryid,'gamename'=>$gamename,'time1'=>date('m/d/Y',$time1),'time2'=>date('m/d/Y',$time2),'gamelist'=>$gamelist,'appid'=>$appid,'select'=>$select]);

    }



    /**
     * @Desc: 广告应用国家报表列表
     * @Author: hmq <304550409@qq.com>
     * @return \Illuminate\View\View
     */
    public function appCountryReportList(Request $request)
    {
        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->orderBy('name')->get();
        
        $gameid = DB::table('mg_game')->where('status',1)->first();
        //查询国家列表
        $countrylist = DB::table('mg_country')->get();
        //获取页面传过来的查询条件并默认展示最近七天数据
        if($request->input('today')){
            $today = strtotime($request->input('today'));
        }elseif(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $today = strtotime(date("Y-m-d"));
        }
        else{
            $today = strtotime(date('Y-m-d', strtotime('-1 day')));
        }


        if($request->input('time2')){
            $time2 = strtotime($request->input('time2'));
        }elseif(date("H:i:s") >= '16:00:00' && date("H:i:s") <= '24:00:00'){
            $time2 = strtotime(date("Y-m-d"));
        }
        else{
            $time2 = strtotime(date('Y-m-d', strtotime('-1 day')));
        }
        


        /*if($request->input('appid')){
            $appid = $request->input('appid');
        }else{
            $appid = '';
        }*/
        if($request->input('appid')){
            $appid = $request->input('appid');
            // dump($appid);
        }else{
            $appid = '';
        }

        if($request->input('select')){
            $select = $request->input('select');
        }else{
            $select = 'profit';
        }

        $gamename = DB::table('mg_game')->where('id',$appid)->value('name');
        if($request->input('countryid')){
            $countryid = $request->input('countryid');
        }else{
            $countryid = 'AD';
        }
        
        //foreach($gamelist as $k=>$v) if(!empty($v->id)){$array[35]=$v->id;}
        
        $data = DB::table('mg_game')
            ->join('mg_game_country_report', 'mg_game_country_report.game_id', '=', 'mg_game.id')
            ->where('mg_game.cate','1')
            ->select('mg_game.name',DB::raw('
                sum(mg_game_country_report.am_cpm) as am_cpm,
                sum(mg_game_country_report.am_revenue) as am_revenue,
                sum(mg_game_country_report.fb_ad_network_revenue) as fb_ad_network_revenue,
                sum(mg_game_country_report.fb_ad_network_cpm) as fb_ad_network_cpm,
                sum(mg_game_country_report.mp_revenue) as mp_revenue,
                sum(mg_game_country_report.al_revenue) as al_revenue,
                sum(mg_game_country_report.is_revenue) as is_revenue,
                sum(mg_game_country_report.install_count) as install_count,
                sum(mg_game_country_report.cost) as cost,
                sum(mg_game_country_report.profit) as profit,
                sum(mg_game_country_report.total) as total,
                sum(mg_game_country_report.roi) as roi
            '));
        if ($today) { 
            $data = $data->where('mg_game_country_report.date','>=',$today); 
        }
        if ($time2) { 
            $data =$data->where('mg_game_country_report.date','<=',$time2); 
        } 
        if ($countryid) { 
            $data = $data->where('mg_game_country_report.country_code',$countryid);
        }
        
        if ($select) { 
            $data = $data->orderBy($select,'Desc'); 
        } 


        $list = $data->groupBy('mg_game.name')->get();
        // dump($list);
        
        return view('admin.app_country_report',['list'=>$list,'countrylist'=>$countrylist,'countryid'=>$countryid,'gamename'=>$gamename,'today'=>date('Y-m-d',$today),'time2'=>date('Y-m-d',$time2),'gamelist'=>$gamelist,'appid'=>$appid,'select'=>$select]);
    }







    /**
     * @Desc: 广告总报表列表
     * @Author: hmq <304550409@qq.com>
     * @return \Illuminate\View\View
     */



    public function totalReportList()
    {

        //获取国家报表总数据
        $list = DB::table('mg_game_report')
            ->where('pid',0)
            ->orderBy('sort','DESC')
            ->get();

        return view('admin.menu',['list'=>$list]);
    }



    // 账户清理
    public function actdeleteList(Request $request){

        $act_id = $request->input('act_id');
        $data = DB::table('act_delete');
        if ($act_id) { 
            $data = $data->where('act_id', $act_id); 
        }else{
            $data = $data->orderBy('created_at','DESC'); 
        }
        $list = $data->paginate(50);
        
        return view('admin.act_delete',['act_id'=>$act_id,'list'=>$list]);
    }

    // 新增账户清理
    public function actdeleteAdd(Request $request){
        if($request->isMethod('post')){
        	// 根据账户遍历数据
            dump($request->input());
        	if($request->input('day') == '7'){
        		$date = 'last_7d';
        	}elseif ($request->input('day') == '14') {
        		$date = 'last_14d';
        	}else{
        		$date = 'last_30d';
        	}
            $access_token = env('ACCESS_TOKEN_PAGE');
        	$act_id = $request->input('act_id');
        	// 获取账户下所有campaign信息
        	$campaigndata = (new IndexController)->curl_get_https('https://graph.facebook.com/v6.0/act_'.$act_id.'/campaigns?access_token='.$access_token);
        	$campaigndata1 = json_decode($campaigndata);
        	// dump($campaigndata1->data);exit;
        	foreach ($campaigndata1->data as $key1 => $value1) {
        		dump($value1->id);
        		// 获取campaign下所有adsets信息
        		$adsetsdata = (new IndexController)->curl_get_https('https://graph.facebook.com/v6.0/' .$value1->id. '/adsets?limit=2000&access_token='.$access_token);
        		$adsetsdata1 = json_decode($adsetsdata);
        		// dump($adsetsdata1->data);exit;
                if(!empty($adsetsdata1->data)){
                    foreach ($adsetsdata1->data as $key2 => $value2) {
                        // 获取adsets下所有安装信息
                        $get_data = array (
                            'fields' => 'actions',
                            'date_preset' => $date,
                            'access_token' => $access_token
                        );
                        $url1 = 'https://graph.facebook.com/v6.0/'.$value2->id.'/insights?';
                        $output = (new IndexController)->curl_get_https ( $url1 . http_build_query ( $get_data ) );//获取数据
                        $res = json_decode($output,true);
                        // dump($res);exit;
                        $installsum = 0;
                        //判断报表数组是否为空
                        if(!empty($res['data'][0]['actions'])&&!empty($res['data'])){
                            //查找mobile_app_install数据
                            foreach ($res['data'][0]['actions'] as $key3 => $install) {
                                if($install['action_type']=='mobile_app_install'){
                                    $installsum = $install['value'];
                                }
                            }
                        }
                        // dump($installsum);exit;
                        // 如果安装数低于设置的数值，则删除这条adsets
                        if($installsum <= $request->input('install')){
                            $delete = (new IndexController)->curl_del_https('https://graph.facebook.com/v6.0/'.$value2->id.'?access_token='.$access_token);
                            // 如果返回true，则将此条操作插入数据库
                            // dump($delete->success);exit;
                            if($delete->success == 'true'){
                                $data = array ();
                                $data ['operator_name'] = session('admin')->account;
                                $data ['act_id'] = $request->input('act_id');
                                $data ['adsets_id'] = $value2->id;
                                $data ['campaign_id'] = $value1->id;
                                $data ['created_at'] = time();
                                DB::table('act_delete')->insert($data);
                            }
                        }   
                    }
                    Log::info('Campaingn ID为'.$value1->id.'的adsets'.$value2->id.'已删除！');
                }
                // 删除campaign
        		// (new IndexController)->curl_del_https('https://graph.facebook.com/v6.0/'.$value1->id.'?access_token='.$access_token);
                
        	}
            return $this->json(200,'清理成功！');
        }else{

            return view('admin.act_deleteadd');
        }
    }



    



    // 数据同步请求
    // 上传外单admob数据
    public function synchronizeDataAdd(Request $request){
        $gamelist = DB::table('mg_game')->where('status',1)->orderBy('name')->get();   
        
        if( $request->isMethod('post')){

            // dump($request->input('appid'));exit;

            if(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
                $date = date("Y-m-d");
            }else{
                $date = date("Y-m-d",strtotime("-1 day"));
            }  
            // $date = '2020-04-26';
            //查询游戏列表
            $gamelist = DB::table('mg_game')->where('id',$request->input('appid'))->get();
            foreach ($gamelist as $key2 => $value2) {
                //获取应用下全部账户信息
                $account = DB::table('mg_game')->where('id',$value2->id)->select('fb_read_accounts')->get(); 
                $account1 = explode(',',$account[0]->fb_read_accounts);
                $array = [];
                foreach ($account1 as $key => $value) {
                    $url1 = 'https://graph.facebook.com/v7.0/act_' . $value . '/insights?';
                    $get_data = array (
                        'fields' => 'spend',
                        'breakdowns' => 'country',
                        'time_range[since]' => $date,
                        'time_range[until]' => $date,
                        'limit' => '2000',
                        'access_token' => env('ACCESS_TOKEN_Synchronize')
                    );
                    $output = (new IndexController)->curl_get_https ( $url1 . http_build_query ( $get_data ) );//获取数据
                    $res = json_decode($output,true);
                    // dump($res);exit;
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
            Log::info('同步实时分国家花费处理完成');

            
            return '同步拉取分国家花费数据处理成功！';
        }
        
        return view('admin.synchronize_dataadd',['gamelist' => $gamelist]);
    }











    /**
     * @Desc: 广告国家报表列表
     * @Author: hmq <304550409@qq.com>
     * @return \Illuminate\View\View
     */
    public function appList(Request $request)
    {

    	$app = $request->input('app');
        $operator = $request->input('operator');
        $data = DB::table('mg_game');
        if($operator){
            $data = $data->where('operator', 'like', $operator.'%'); 
        }
        if ($app) { 
            $data = $data->where('name', 'like', $app.'%'); 
        }else{
        	$data = $data->orderBy('operator','DESC'); 
        }
        $list = $data->paginate(10000);
        /*//获取应用全部数据
        $list = DB::table('mg_game')
            ->orderBy('id','ASC')
            ->paginate(10);*/
        return view('admin.app_list',['list'=>$list,'app'=>$app,'operator'=>$operator]);
    }
    //新增应用
    public function appAdd(Request $request){
        if($request->isMethod('post')){
            $post = $request->post();
            // 查询应用是否已经存在
            $count = DB::table('mg_game')->where('name',$post['name'])->count();
            if($count){
                return $this->json(500,'该应用已存在,返回编辑！');
            }
            $data = [
                'name'=>$post['name'],
                'fb_read_accounts'=>$post['account'],
                'status'=>$post['shelf'],
                'cate'=>$post['cate'],
                'fb_app_id'=>$post['fb_app_id'],
                'am_account_id'=>$post['am_account_id'],
                'is_app_key'=>$post['is_app_key'],
                'mp_app_id'=>$post['mp_app_id'],
                'am_app_name'=>$post['am_app_name'],
                'al_package_name'=>$post['al_package_name'],
                'monitor_phone_number'=>$post['monitor_phone_number'],
                'bussiness_email'=>$post['bussiness_email'],
                'monitor_install'=>$post['monitor_install'],
                'monitor_cpi'=>$post['monitor_cpi'],
                'monitor_profit'=>$post['monitor_profit'],
                'operator'=>$post['operator'],
                'alarm_email'=>$post['alarm_email'],
                'adx_appname'=>$post['adx_appname'],
                'upltv_id'=>$post['upltv_id'],
                'is_game'=>$post['is_game'],
                'is_fan'=>$post['is_fan'],
                'add_time'=>time()
            ];
            $id = DB::table('mg_game')->insertGetId($data);

            if(!$id){
                return $this->json(500,'应用添加失败,请稍后再试!');
            }
            return $this->json(200,'添加成功');
        }else{
            return view('admin.app_add');
        }

    }
    //更新应用
    public function appUpdate(Request $request,$id){
        if($request->isMethod('post')){
            $post = $request->post();
            $data = [
                'name'=>$post['name'],
                'fb_read_accounts'=>$post['account'],
                'status'=>$post['shelf'],
                'cate'=>$post['cate'],
                'fb_app_id'=>$post['fb_app_id'],
                'am_account_id'=>$post['am_account_id'],
                'is_app_key'=>$post['is_app_key'],
                'mp_app_id'=>$post['mp_app_id'],
                'am_app_name'=>$post['am_app_name'],
                'al_package_name'=>$post['al_package_name'],
                'monitor_phone_number'=>$post['monitor_phone_number'],
                'bussiness_email'=>$post['bussiness_email'],
                'monitor_install'=>$post['monitor_install'],
                'monitor_cpi'=>$post['monitor_cpi'],
                'monitor_profit'=>$post['monitor_profit'],
                'alarm_email'=>$post['alarm_email'],
                'adx_appname'=>$post['adx_appname'],
                'upltv_id'=>$post['upltv_id'],
                'is_game'=>$post['is_game'],
                'is_fan'=>$post['is_fan'],
                'operator'=>$post['operator']
            ];
            DB::table('mg_game')->where('id',$id)->update($data);
            if(!$id){
                return $this->json(500,'应用更新失败,请稍后再试!');
            }else{
                return $this->json(200,'更新成功');
            }
        }else{
            $res = DB::table('mg_game')->find($id);
            return view('admin.app_update',['res'=>$res]);
        }

    }
    /**
     * @return mixed
     * 删除应用
     */
    public function appDel($id){
        $res = DB::table('mg_game')->delete($id);
        if(!$res){
            return $this->json(500,'删除失败');
        }
        //删除关联
        //DB::table('admin_user_role')->where('admin_user_id',$id)->delete();
        return $this->json(200,'删除成功');
    }
    

    //新增url
    public function urlAdd(Request $request){
        if($request->isMethod('post')){
            $post = $request->post();
            // 查询应用是否已经存在
            $count = DB::table('page_status')->where('page_id',$post['page_id'])->first();
            if($count){
                return $this->json(500,'该Page已存在,返回编辑！');
            }
            $data = [
                'page_id'=>$post['page_id'],
                'page_name'=>$post['page_name'],
                'status'=>$post['status'],
                'create_time'=>time()
            ];
            $id = DB::table('page_status')->insertGetId($data);

            if(!$id){
                return $this->json(500,'添加失败,请稍后再试!');
            }
            return $this->json(200,'添加成功');
        }else{
            return view('admin.url_add');
        }

    }
    //更新url
    public function urlUpdate(Request $request,$id){
        if($request->isMethod('post')){
            $post = $request->post();
            $data = [
                'page_id'=>$post['page_id'],
                'page_name'=>$post['page_name'],
                'status'=>$post['status'],
                'is_verify'=>$post['is_verify'],
            ];
            DB::table('page_status')->where('id',$post['id'])->update($data);
            if(!$id){
                return $this->json(500,'更新失败,请稍后再试!');
            }else{
                return $this->json(200,'更新成功');
            }
        }else{
            $res = DB::table('page_status')->find($id);
            return view('admin.url_update',['res'=>$res]);
        }

    }
    /**
     * @return mixed
     * 删除url
     */
    public function urlDel($id){
        $res = DB::table('page_status')->delete($id);
        if(!$res){
            return $this->json(500,'删除失败');
        }
        //删除关联
        //DB::table('admin_user_role')->where('admin_user_id',$id)->delete();
        return $this->json(200,'删除成功');
    }
    public function urlList(Request $request){

        $page_name = $request->input('page_name');
        $data = DB::table('page_status');
        if ($page_name) { 
            $data = $data->where('page_name', 'like', $page_name.'%'); 
        }else{
            $data = $data->orderBy('id','ASC'); 
        }
        $list = $data->paginate(50);;
        /*//获取应用全部数据
        $list = DB::table('mg_game')
            ->orderBy('id','ASC')
            ->paginate(10);*/
        return view('admin.url_list',['list'=>$list,'page_name'=>$page_name]);
    }



    public function getData(Request $request){
        return 111;
        $gamelist = DB::table('mg_game')->get();

        $arraydata = [];
        $date = (new IndexController)->getDateFromRange(date('Y-m-d', strtotime('-7 day')),$today1);
        // 默认展示第一款游戏最近一周的数据
        (new IndexController)->getFb(date('Y-m-d', strtotime('-7 day')),$today1,'2348723372080835');
        //fb变现结果集
        foreach ($date as $key1 => $value1) {
            $arraydata[$key1]['date'] = $value1;
            foreach (session('arrayfbearn') as $key => $value) {
                $time = strtotime($value['time']);
                if(date('Y-m-d',$time+1*24*60*60) == $value1){
                    $arraydata[$key1]['fb'] = round($value['value'],2);
                }
            }                
        }
        //al变现结果集
        (new IndexController)->getAlovin(date('Y-m-d', strtotime('-7 day')),$today1,'com.highlevel.helixjumpy');
        foreach (session('applovin') as $key => $value) {
            $arraydata[$key]['al'] = $value;
        }  
        //admob变现结果集
        (new IndexController)->getAdmob(date('Y-m-d', strtotime('-7 day')),$today1,'pub-7693928579913199','Helix Jumpy');
        foreach (session('admob') as $key => $value) {
            $arraydata[$key]['ad'] = $value;
        } 
        //应用安装数结果集
        foreach ($date as $key1 => $value1) {
            $installsum = (new IndexController)->getInstallSum($value1,$value1,'8');
            $arraydata[$key1]['installsum'] = $installsum;            
        }
        //应用总花费结果集
        foreach ($date as $key1 => $value1) {
            $spendsum = (new IndexController)->getSpendSum($value1,$value1,'8');
            $arraydata[$key1]['spendsum'] = $spendsum;            
        }
        
        foreach ($arraydata as $key => $value) {
            $data = [
                'name'=>$post['name'],
                'fb_ad_network_revenue'=>$value['fb'],
                'is_revenue'=>$value['is'],
                'mp_revenue'=>$value['mp'],
                'is_app_key'=>$value['is_app_key'],
                'al_package_name'=>$value['al_package_name'],
                'add_time'=>time()
            ];
            $id = DB::table('mg_game')->insertGetId($data);
        }   
        // dump($arraydata);exit;
    }
    public function Downloadcsv(Request $request){
        ini_set('memory_limit','500M');
        set_time_limit(0);//设置超时限制为不限制
        $appid = $request->input('appid');
        $date = $request->input('date');
        $gamename = $request->input('gamename');
        $cellData1 = DB::table('mg_game_country_report')
                        ->join('mg_country', 'mg_game_country_report.country_code', '=', 'mg_country.code_2')
                        ->select('mg_country.english','mg_game_country_report.fb_ad_network_revenue','mg_game_country_report.fb_ad_network_cpm','mg_game_country_report.is_revenue','mg_game_country_report.mp_revenue','mg_game_country_report.al_revenue','mg_game_country_report.am_revenue','mg_game_country_report.cost','mg_game_country_report.install_count','mg_game_country_report.profit','mg_game_country_report.fb_ad_network_fill_rate','mg_game_country_report.roi','mg_game_country_report.ctr','mg_game_country_report.country_code','mg_game_country_report.am_cpm','mg_game_country_report.total')
                        ->where('game_id',$appid)
                        ->where('date',strtotime($date))
                        ->get();
        $cellData = json_decode($cellData1,true);

        Excel::create($gamename.'--'.$date.'分国家数据', function($excel) use ($cellData) { 
            $excel->sheet('分国家数据', function($sheet) use ($cellData) { 
                $sheet->cell('A1', function($cell) {$cell->setValue('Country'); }); 
                $sheet->cell('B1', function($cell) {$cell->setValue('Code'); }); 
                $sheet->cell('C1', function($cell) {$cell->setValue('FAN'); }); 
                $sheet->cell('D1', function($cell) {$cell->setValue('FAN Ecpm'); });
                $sheet->cell('E1', function($cell) {$cell->setValue('IronSoure Revenue'); }); 
                $sheet->cell('F1', function($cell) {$cell->setValue('Mopub Revenue'); }); 
                $sheet->cell('G1', function($cell) {$cell->setValue('Applovin Revenue'); });
                $sheet->cell('H1', function($cell) {$cell->setValue('Admob Revenue'); });
                $sheet->cell('I1', function($cell) {$cell->setValue('Admob Ecpm'); });
                $sheet->cell('J1', function($cell) {$cell->setValue('Spent'); });
                $sheet->cell('K1', function($cell) {$cell->setValue('Installs'); });
                $sheet->cell('L1', function($cell) {$cell->setValue('CPI'); });
                $sheet->cell('M1', function($cell) {$cell->setValue('Revenue'); });
                $sheet->cell('N1', function($cell) {$cell->setValue('Profit'); });
                $sheet->cell('O1', function($cell) {$cell->setValue('Roi'); });
                $sheet->cell('P1', function($cell) {$cell->setValue('Ctr'); });
                if (!empty($cellData)) { 
                    foreach ($cellData as $key => $value) { 
                    	$ctr = $value['ctr']*100;
                        $i= $key+2; 
                        $sheet->cell('A'.$i, $value['english']); 
                        $sheet->cell('B'.$i, $value['country_code']); 
                        $sheet->cell('C'.$i, $value['fb_ad_network_revenue']);
                        $sheet->cell('D'.$i, $value['am_cpm']); 
                        $sheet->cell('E'.$i, $value['is_revenue']); 
                        $sheet->cell('F'.$i, $value['mp_revenue']); 
                        $sheet->cell('G'.$i, $value['al_revenue']);
                        $sheet->cell('H'.$i, $value['am_revenue']);
                        $sheet->cell('I'.$i, $value['am_cpm']);
                        $sheet->cell('J'.$i, $value['cost']);
                        $sheet->cell('K'.$i, $value['install_count']);
                        $sheet->cell('L'.$i, $value['fb_ad_network_fill_rate']);
                        $sheet->cell('M'.$i, $value['total']);
                        $sheet->cell('N'.$i, $value['profit']);
                        $sheet->cell('O'.$i, $value['roi'].'%');
                        $sheet->cell('P'.$i, $ctr.'%');
                    } 
                } 
            }); 
        })->download('xlsx');
    }


    // 下载多日期分国家合并数据
    public function Downloadcsvmuti(Request $request){
        ini_set('memory_limit','500M');
        set_time_limit(0);//设置超时限制为不限制
        $appid = $request->input('appid');
        $today = $request->input('today');
        $date = $request->input('date');
        $gamename = $request->input('gamename');
        $cellData1 = DB::table('mg_game_country_report')
                        ->join('mg_country', 'mg_game_country_report.country_code', '=', 'mg_country.code_2')
                        ->select('mg_game_country_report.country_code','mg_country.english',DB::raw('
                            sum(mg_game_country_report.fb_ad_network_revenue) as fb_ad_network_revenue,
                            sum(mg_game_country_report.fb_ad_network_cpm) as fb_ad_network_cpm,
                            sum(mg_game_country_report.is_revenue) as is_revenue,
                            sum(mg_game_country_report.mp_revenue) as mp_revenue,
                            sum(mg_game_country_report.al_revenue) as al_revenue,
                            sum(mg_game_country_report.am_revenue) as am_revenue,
                            sum(mg_game_country_report.am_cpm) as am_cpm,
                            sum(mg_game_country_report.cost) as cost,
                            sum(mg_game_country_report.install_count) as install_count,
                            sum(mg_game_country_report.al_revenue) as al_revenue,
                            sum(mg_game_country_report.profit) as profit,
                            sum(mg_game_country_report.total) as total
                        '))
                        ->where('game_id',$appid)
                        ->where('date','>=',strtotime($date))
                        ->where('date','<=',strtotime($today))
                        ->groupBy('mg_game_country_report.country_code')
                        ->get();
        $cellData = json_decode($cellData1,true);
        Excel::create($gamename.'--'.$date.'--'.$today.'分国家数据', function($excel) use ($cellData) { 
            $excel->sheet('分国家数据', function($sheet) use ($cellData) { 
                $sheet->cell('A1', function($cell) {$cell->setValue('Country'); }); 
                $sheet->cell('B1', function($cell) {$cell->setValue('Code'); }); 
                $sheet->cell('C1', function($cell) {$cell->setValue('FAN'); }); 
                $sheet->cell('D1', function($cell) {$cell->setValue('FAN Ecpm'); });
                $sheet->cell('E1', function($cell) {$cell->setValue('IronSoure Revenue'); }); 
                $sheet->cell('F1', function($cell) {$cell->setValue('Mopub Revenue'); }); 
                $sheet->cell('G1', function($cell) {$cell->setValue('Applovin Revenue'); });
                $sheet->cell('H1', function($cell) {$cell->setValue('Admob Revenue'); });
                $sheet->cell('I1', function($cell) {$cell->setValue('Admob Ecpm'); });
                $sheet->cell('J1', function($cell) {$cell->setValue('Spent'); });
                $sheet->cell('K1', function($cell) {$cell->setValue('Installs'); });
                $sheet->cell('L1', function($cell) {$cell->setValue('CPI'); });
                $sheet->cell('M1', function($cell) {$cell->setValue('Revenue'); });
                $sheet->cell('N1', function($cell) {$cell->setValue('Profit'); });
                $sheet->cell('O1', function($cell) {$cell->setValue('Roi'); });
                if (!empty($cellData)) { 
                    foreach ($cellData as $key => $value) { 
                        $i= $key+2; 
                        if($value['install_count'] == '0'){
                            $cpi = '0';
                        }else{
                            $cpi = round($value['cost']/$value['install_count'],2);
                        }

                        if($value['cost'] == '0'){
                            $roi = '0';
                        }else{
                            $roi = round($value['total']/$value['cost'],2)*100;
                        }
                        
                        $sheet->cell('A'.$i, $value['english']); 
                        $sheet->cell('B'.$i, $value['country_code']); 
                        $sheet->cell('C'.$i, $value['fb_ad_network_revenue']);
                        $sheet->cell('D'.$i, $value['fb_ad_network_cpm']); 
                        $sheet->cell('E'.$i, $value['is_revenue']); 
                        $sheet->cell('F'.$i, $value['mp_revenue']); 
                        $sheet->cell('G'.$i, $value['al_revenue']);
                        $sheet->cell('H'.$i, $value['am_revenue']);
                        $sheet->cell('I'.$i, $value['am_cpm']);
                        $sheet->cell('J'.$i, $value['cost']);
                        $sheet->cell('K'.$i, $value['install_count']);
                        $sheet->cell('L'.$i, $cpi);
                        $sheet->cell('M'.$i, $value['total']);
                        $sheet->cell('N'.$i, $value['profit']);
                        $sheet->cell('O'.$i, $roi.'%');
                    } 
                } 
            }); 
        })->download('xlsx');
    }





    public function Downloadcsvmutiday(Request $request){
        ini_set('memory_limit','500M');
        set_time_limit(0);//设置超时限制为不限制
        // dump($request);
        $appid = $request->input('appid');
        $date = $request->input('date');
        $today = $request->input('today');
        $gamename = $request->input('gamename');
        $cellData1 = DB::table('mg_game_report')->where('game_id',$appid)->where('date','>=',strtotime($date))->where('date','<=',strtotime($today))->get();
        // dump($cellData1);exit;
        $cellData = json_decode($cellData1,true);

        Excel::create($gamename.'--'.$date.'--'.$today.'数据', function($excel) use ($cellData) { 
            $excel->sheet('数据', function($sheet) use ($cellData) { 
                $sheet->cell('A1', function($cell) {$cell->setValue('Date'); });
                $sheet->cell('B1', function($cell) {$cell->setValue('Name'); });
                $sheet->cell('C1', function($cell) {$cell->setValue('Profit'); });
                $sheet->cell('D1', function($cell) {$cell->setValue('Roi'); });
                $sheet->cell('E1', function($cell) {$cell->setValue('FAN'); }); 
                $sheet->cell('F1', function($cell) {$cell->setValue('FAN Ecpm'); });
                $sheet->cell('G1', function($cell) {$cell->setValue('IronSoure Revenue'); }); 
                $sheet->cell('H1', function($cell) {$cell->setValue('Mopub Revenue'); }); 
                $sheet->cell('I1', function($cell) {$cell->setValue('Applovin Revenue'); });
                $sheet->cell('J1', function($cell) {$cell->setValue('Admob Revenue'); });
                $sheet->cell('K1', function($cell) {$cell->setValue('Admob Ecpm'); });
                $sheet->cell('L1', function($cell) {$cell->setValue('Spent'); });
                $sheet->cell('M1', function($cell) {$cell->setValue('Installs'); });
                $sheet->cell('N1', function($cell) {$cell->setValue('CPI'); });
                
                if (!empty($cellData)) { 
                    foreach ($cellData as $key => $value) { 
                        $total = $value['fb_ad_network_revenue']+$value['is_revenue']+$value['mp_revenue']+$value['al_revenue']+$value['am_revenue'];
                        $profit = $total - $value['cost'];
                        if($value['install_count']&&$value['install_count'] != '0'){
                            $cpi = round($value['cost']/$value['install_count'],2);
                        }else{
                            $cpi = '0';
                        }
                        if($value['cost']&&$value['cost'] != '0'){
                            $roi = round($profit/$value['cost'],2)*100;
                        }else{
                            $roi = '0';
                        }
                        // $gamename = $request->input('gamename');
                        $i= $key+2; 
                        $profit = $total - $value['cost'];
                        $date = date('Y-m-d',$value['date']);
                        $gamename1 =  DB::table('mg_game')->where('id',$value['game_id'])->select('name')->first();
                        $gamename = $gamename1->name;
                        $sheet->cell('A'.$i, $date);
                        $sheet->cell('B'.$i, $gamename);
                        $sheet->cell('C'.$i, $profit);
                        $sheet->cell('D'.$i, $roi.'%');
                        $sheet->cell('E'.$i, $value['fb_ad_network_revenue']);
                        $sheet->cell('F'.$i, $value['am_cpm']); 
                        $sheet->cell('G'.$i, $value['is_revenue']); 
                        $sheet->cell('H'.$i, $value['mp_revenue']); 
                        $sheet->cell('I'.$i, $value['al_revenue']);
                        $sheet->cell('J'.$i, $value['am_revenue']);
                        $sheet->cell('K'.$i, $value['am_cpm']);
                        $sheet->cell('L'.$i, $value['cost']);
                        $sheet->cell('M'.$i, $value['install_count']);
                        $sheet->cell('N'.$i, $cpi);
                       
                    } 
                } 
            }); 
        })->download('xlsx');
    }


    // 合作外单分国家数据
    public function countryReportHezuo(Request $request)
    {
        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->where('cate',0)->orderBy('name')->get();
        
        $gameid = DB::table('mg_game')->where('status',1)->where('cate',0)->first();
        //查询国家列表
        $countrylist = DB::table('mg_country')->get();
        //获取页面传过来的查询条件并默认展示最近七天数据


        if(strstr($request->date,' - ', TRUE)){
            $time1 = strtotime(strstr($request->date,' - ', TRUE));
        }elseif(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $time1 = strtotime(date("Y-m-d"));
        }
        else{
            $time1 = strtotime(date('Y-m-d', strtotime('-1 day')));
        }


        if(str_replace(" - ", "",strstr($request->date,' - '))){
            $time2 = strtotime(str_replace(" - ", "",strstr($request->date,' - ')));
        }elseif(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $time2 = strtotime(date("Y-m-d"));
        }
        else{
            $time2 = strtotime(date('Y-m-d', strtotime('-1 day')));
        }
        
        

        if($request->input('appid')){
            $appid = $request->input('appid');
            // dump($appid);
        }else{
            $appid = $gameid->id;
        }

        if($request->input('select')){
            $select = $request->input('select');
        }else{
            $select = 'profit';
        }

        if($request->input('countryid')&& $request->input('countryid') != 'nanya' && $request->input('countryid') != 'beiou'&& $request->input('countryid') != 'jialebi'&& $request->input('countryid') != 'lamei'&& $request->input('countryid') != 'laomei'&& $request->input('countryid') != 'xiou'&& $request->input('countryid') != 'nanou'&& $request->input('countryid') != 'dongou'){
            $countryid = [$request->input('countryid')];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'nanya') {
            $countryid = ['BT','LK','MV','AE','ID','MM','KH','VN','PH','TH','SA','QA'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'beiou') {
            $countryid = ['IE','NO','SE','FI','DK','IS','UK'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'jialebi') {
            $countryid = ['AG','AW','BB','VG','KY','DM','DO','GD','GP','HT','JM','MQ','MS','PR','KN','VC','BS','TT','TC'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'lamei') {
            $countryid = ['CL','CO','CR','MX','PA','PE','UY'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'laomei') {
            $countryid = ['AU','CA','US','NZ','JP'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'xiou') {
            $countryid = ['MC','NL','CH','GB','DK','BE','AT','LU','DE','SG','KR'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'nanou') {
            $countryid = ['IT','ES','GR','PT','MT','MC','FR','My','TW','MO'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'dongou') {
            $countryid = ['BG','BY','EE','HR','HU','LT','LV','ME','PL','RO','RS','SK','CZ','CY','PH','IL','RU','TH','AE','SA','KZ','SV'];
        }else{
            $countryid = '';
        }
        $gamename = DB::table('mg_game')->where('id',$appid)->value('name'); 
        
        //foreach($gamelist as $k=>$v) if(!empty($v->id)){$array[35]=$v->id;}

            
        if (!empty($countryid)) { 
            // 搜索条件
            $data = DB::table('mg_game_country_report')
                ->join('mg_country','mg_game_country_report.country_code', '=', 'mg_country.code_2')
                ->select('mg_game_country_report.*','mg_country.english');

            if ($time1) { 
                $data = $data->where('mg_game_country_report.date','>=',$time1); 
            }
            if ($time2) { 
                $data =$data->where('mg_game_country_report.date','<=',$time2); 
            } 
            if ($appid) { 
                $data = $data->where('mg_game_country_report.game_id',$appid); 
            }
           
            if ($select) { 
                $data = $data->orderBy('date','Desc'); 
            } 
            $data = $data->whereIn('mg_game_country_report.country_code',$countryid); 
            $list = $data->paginate(300);
        }else{
            // 搜索条件
            $data = DB::table('mg_game_country_report')
                ->join('mg_country', 'mg_game_country_report.country_code', '=', 'mg_country.code_2')
                ->select('mg_game_country_report.country_code','mg_country.english',DB::raw('
                    sum(mg_game_country_report.am_cpm) as am_cpm,
                    sum(mg_game_country_report.am_revenue) as am_revenue,
                    sum(mg_game_country_report.fb_ad_network_revenue) as fb_ad_network_revenue,
                    sum(mg_game_country_report.fb_ad_network_cpm) as fb_ad_network_cpm,
                    sum(mg_game_country_report.mp_revenue) as mp_revenue,
                    sum(mg_game_country_report.upltv) as upltv,
                    sum(mg_game_country_report.is_revenue) as is_revenue,
                    sum(mg_game_country_report.install_count) as install_count,
                    sum(mg_game_country_report.cost) as cost,
                    sum(mg_game_country_report.profit) as profit,
                    sum(mg_game_country_report.total) as total,
                    sum(mg_game_country_report.adx_revenue) as adx_revenue,
                    sum(mg_game_country_report.ctr) as ctr,
                    sum(mg_game_country_report.roi) as roi
                '));
            if ($time1) { 
                $data = $data->where('mg_game_country_report.date','>=',$time1); 
            }
            if ($time2) { 
                $data =$data->where('mg_game_country_report.date','<=',$time2); 
            } 
            if ($appid) { 
                $data = $data->where('mg_game_country_report.game_id',$appid);
            }
            
            if ($select) { 
                $data = $data->orderBy($select,'Desc'); 
            } 

    
            $list = $data->groupBy('country_code')->paginate(300);
            // dump($list);
        }        
        return view('admin.hezuo_country_report',['list'=>$list,'countrylist'=>$countrylist,'countryid'=>$countryid,'gamename'=>$gamename,'time1'=>date('m/d/Y',$time1),'time2'=>date('m/d/Y',$time2),'gamelist'=>$gamelist,'appid'=>$appid,'select'=>$select]);

    }


    public function ctrUnit(Request $request){
        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->where('cate',1)->orderBy('name')->get();
        
        $gameid = DB::table('mg_game')->where('status',1)->first();
        //查询国家列表
        $countrylist = DB::table('mg_country')->get();
        //获取页面传过来的查询条件并默认展示最近七天数据


        if(strstr($request->date,' - ', TRUE)){
            $time1 = strtotime(strstr($request->date,' - ', TRUE));
        }elseif(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $time1 = strtotime(date("Y-m-d"));
        }
        else{
            $time1 = strtotime(date('Y-m-d', strtotime('-1 day')));
        }


        if(str_replace(" - ", "",strstr($request->date,' - '))){
            $time2 = strtotime(str_replace(" - ", "",strstr($request->date,' - ')));
        }elseif(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $time2 = strtotime(date("Y-m-d"));
        }
        else{
            $time2 = strtotime(date('Y-m-d', strtotime('-1 day')));
        }
        
        

        if($request->input('appid')){
            $appid = $request->input('appid');
        }else{
            $appid = $gameid->id;
        }

        if($request->input('unit_name')){
            $unit_name = $request->input('unit_name');
        }else{
            $unit_name = 'Native-sp-main';
        }

        if($request->input('countryid')&& $request->input('countryid') != 'nanya' && $request->input('countryid') != 'beiou'&& $request->input('countryid') != 'jialebi'&& $request->input('countryid') != 'lamei'&& $request->input('countryid') != 'laomei'&& $request->input('countryid') != 'xiou'&& $request->input('countryid') != 'nanou'&& $request->input('countryid') != 'dongou'){
            $countryid = [$request->input('countryid')];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'nanya') {
            $countryid = ['BT','LK','MV','AE','ID','MM','KH','VN','PH','TH','SA','QA'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'beiou') {
            $countryid = ['IE','NO','SE','FI','DK','IS','UK'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'jialebi') {
            $countryid = ['AG','AW','BB','VG','KY','DM','DO','GD','GP','HT','JM','MQ','MS','PR','KN','VC','BS','TT','TC'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'lamei') {
            $countryid = ['CL','CO','CR','MX','PA','PE','UY'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'laomei') {
            $countryid = ['AU','CA','US','NZ','JP'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'xiou') {
            $countryid = ['MC','NL','CH','GB','DK','BE','AT','LU','DE','SG','KR'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'nanou') {
            $countryid = ['IT','ES','GR','PT','MT','MC','FR','My','TW','MO'];
        }elseif ($request->input('countryid') && $request->input('countryid') == 'dongou') {
            $countryid = ['BG','BY','EE','HR','HU','LT','LV','ME','PL','RO','RS','SK','CZ','CY','PH','IL','RU','TH','AE','SA','KZ','SV'];
        }else{
            $countryid = '';
        }

        
        $gamename = DB::table('mg_game')->where('id',$appid)->value('name'); 
        
        //foreach($gamelist as $k=>$v) if(!empty($v->id)){$array[35]=$v->id;}

            
        if (!empty($countryid)) { 
            // 搜索条件
            $data = DB::table('ctr_unit')
                ->join('mg_country','ctr_unit.country_code', '=', 'mg_country.code_2')
                ->select('ctr_unit.*');

            if ($time1) { 
                $data = $data->where('ctr_unit.date','>=',$time1); 
            }
            if ($time2) { 
                $data =$data->where('ctr_unit.date','<=',$time2); 
            } 
            if ($appid) { 
                $data = $data->where('ctr_unit.game_id',$appid); 
            }
            if ($unit_name) { 
                $data = $data->where('ctr_unit.unit_name','like',$unit_name.'%'); 
            }
            $data = $data->whereIn('ctr_unit.country_code',$countryid); 
            $list = $data->paginate(300);
        }else{
            // 搜索条件
            $data = DB::table('ctr_unit')
                ->join('mg_country', 'ctr_unit.country_code', '=', 'mg_country.code_2')
                ->select('ctr_unit.*');
            if ($time1) { 
                $data = $data->where('ctr_unit.date','>=',$time1); 
            }
            if ($time2) { 
                $data =$data->where('ctr_unit.date','<=',$time2); 
            } 
            if ($appid) { 
                $data = $data->where('ctr_unit.game_id',$appid);
            }
            if ($unit_name) { 
                $data = $data->where('ctr_unit.unit_name','like',$unit_name.'%'); 
            }    

            $list = $data->paginate(300);

        }        
        return view('admin.ctrunit_country_report',['list'=>$list,'countryid'=>$countryid,'unit_name'=>$unit_name,'countrylist'=>$countrylist,'gamename'=>$gamename,'time1'=>date('m/d/Y',$time1),'time2'=>date('m/d/Y',$time2),'gamelist'=>$gamelist,'appid'=>$appid]);
    }

    // 合作外单下载单日期数据
    public function Downloadcsvhezuo(Request $request){
        ini_set('memory_limit','500M');
        // dump($request->input());
        set_time_limit(0);//设置超时限制为不限制
        $appid = $request->input('appid');
        // $today = $request->input('today');
        $date = $request->input('date');
        $gamename = $request->input('gamename');
        $cellData1 = DB::table('mg_game_country_report')
                        ->join('mg_country', 'mg_game_country_report.country_code', '=', 'mg_country.code_2')
                        ->select('mg_game_country_report.country_code','mg_country.english',DB::raw('
                            sum(mg_game_country_report.cost) as cost,
                            sum(mg_game_country_report.install_count) as install_count
                            
                        '))
                        ->where('game_id',$appid)
                        ->where('date','=',strtotime($date))
                        ->groupBy('mg_game_country_report.country_code')
                        ->get();
        $cellData = json_decode($cellData1,true);
        // dump($cellData);exit;
        Excel::create($gamename.'--'.$date.'--分国家数据', function($excel) use ($cellData) { 
            $excel->sheet('分国家数据', function($sheet) use ($cellData) { 
                $sheet->cell('A1', function($cell) {$cell->setValue('Country'); }); 
                $sheet->cell('B1', function($cell) {$cell->setValue('Code'); }); 
                $sheet->cell('C1', function($cell) {$cell->setValue('Installs'); }); 
                $sheet->cell('D1', function($cell) {$cell->setValue('CPI'); });
                $sheet->cell('E1', function($cell) {$cell->setValue('Spent'); }); 
               
                if (!empty($cellData)) { 
                    foreach ($cellData as $key => $value) { 
                        $i= $key+2; 
                        if($value['install_count'] == '0'){
                            $cpi = '0';
                        }else{
                            $cpi = round($value['cost']/$value['install_count'],2);
                        }
                        
                        $sheet->cell('A'.$i, $value['english']); 
                        $sheet->cell('B'.$i, $value['country_code']); 
                        $sheet->cell('C'.$i, $value['install_count']);
                        $sheet->cell('D'.$i, $cpi); 
                        $sheet->cell('E'.$i, $value['cost']); 
                    } 
                } 
            }); 
        })->download('xlsx');
    }
    







    public function getJixiao(Request $request){

        $gamelist = DB::table('mg_game')->get();
        // $data = DB::table('mg_jixiao_fb');
        if($request->input('appname')){
            $appname = $request->input('appname');
            // $list = $data->where('ad_account_name',$appname)->paginate();
        }else{
            // $firstgame = DB::table('mg_jixiao_fb')->first();
            $appname = 'Air Merger';
            // $list = $data->paginate();
        }

        $list = DB::table('mg_jixiao_fb')->where('ad_account_name',$appname)->get();
        return view('admin.jixiao',['list'=>$list,'gamelist'=>$gamelist,'appname'=>$appname]);
    }
    
    public function downloadFbjx(Request $request){
        ini_set('memory_limit','500M');
        set_time_limit(0);//设置超时限制为不限制
        /*$appid = $request->input('appid');
        $date = $request->input('date');
        $gamename = $request->input('gamename');*/
        $cellData1 = DB::table('mg_jixiao_fb')->get();
        $cellData = json_decode($cellData1,true);
        Excel::create('上月FB总数据', function($excel) use ($cellData) { 
            $excel->sheet('FB数据', function($sheet) use ($cellData) { 
                $sheet->cell('A1', function($cell) {$cell->setValue('Ad Accout ID'); }); 
                $sheet->cell('B1', function($cell) {$cell->setValue('Ad Accout Name'); }); 
                $sheet->cell('C1', function($cell) {$cell->setValue('Reporting Starts'); }); 
                $sheet->cell('D1', function($cell) {$cell->setValue('Reporting Ends'); });
                $sheet->cell('E1', function($cell) {$cell->setValue('Accout Name'); }); 
                $sheet->cell('F1', function($cell) {$cell->setValue('Country'); }); 
                $sheet->cell('G1', function($cell) {$cell->setValue('Impressions'); });
                $sheet->cell('H1', function($cell) {$cell->setValue('Clicks'); });
                $sheet->cell('I1', function($cell) {$cell->setValue('CTR'); });
                $sheet->cell('J1', function($cell) {$cell->setValue('Installs'); });
                $sheet->cell('K1', function($cell) {$cell->setValue('Cost per Results'); });
                $sheet->cell('L1', function($cell) {$cell->setValue('Result Indicator'); });
                $sheet->cell('M1', function($cell) {$cell->setValue('Amount spend'); });


                if (!empty($cellData)) { 
                    foreach ($cellData as $key => $value) { 
                        $i= $key+2; 
                        $sheet->cell('A'.$i, $value['account_id']); 
                        $sheet->cell('B'.$i, $value['ad_account_name']); 
                        $sheet->cell('C'.$i, $value['report_start']);
                        $sheet->cell('D'.$i, $value['report_end']); 
                        $sheet->cell('E'.$i, $value['account_name']); 
                        $sheet->cell('F'.$i, $value['country_code']); 
                        $sheet->cell('G'.$i, $value['impressions']);
                        $sheet->cell('H'.$i, $value['clicks']);
                        $sheet->cell('I'.$i, $value['cpr']);
                        $sheet->cell('J'.$i, $value['install']);
                        $sheet->cell('K'.$i, $value['cpi']);
                        $sheet->cell('L'.$i, $value['result_indicator']);
                        $sheet->cell('M'.$i, $value['spend']);
                    } 
                } 
            }); 
        })->download('xlsx');
    }
    public function getJixiaoAdmob(Request $request){

        $gamelist = DB::table('mg_game')->get();
        // $data = DB::table('mg_jixiao_fb');
        if($request->input('appname')){
            $appname = $request->input('appname');
            // $list = $data->where('ad_account_name',$appname)->paginate();
        }else{
            $firstgame = DB::table('mg_jixiao_admob')->first();
            $appname = $firstgame->app;
            // $list = $data->paginate();
        }

        $list = DB::table('mg_jixiao_admob')->where('app',$appname)->paginate();
        return view('admin.jixiaoadmob',['list'=>$list,'gamelist'=>$gamelist,'appname'=>$appname]);
    }
    public function downloadAdjx(Request $request){
        
        set_time_limit(0);//设置超时限制为不限制
        $cellData1 = DB::table('mg_jixiao_admob')->get();
        ini_set('memory_limit','-1');
        $cellData = json_decode($cellData1,true);
        
        Excel::create('上月admmob总数据', function($excel) use ($cellData) { 
            $excel->sheet('admmob数据', function($sheet) use ($cellData) { 
                $sheet->cell('A1', function($cell) {$cell->setValue('APP'); }); 
                $sheet->cell('B1', function($cell) {$cell->setValue('COUNTRY_CODE'); }); 
                $sheet->cell('C1', function($cell) {$cell->setValue('AD_REQUESTS'); });
                $sheet->cell('D1', function($cell) {$cell->setValue('AD_REQUESTS_COVERAGE'); }); 
                $sheet->cell('E1', function($cell) {$cell->setValue('AD_REQUESTS_CTR'); }); 
                $sheet->cell('F1', function($cell) {$cell->setValue('AD_REQUESTS_RPM'); });
                $sheet->cell('G1', function($cell) {$cell->setValue('CLICKS'); });
                $sheet->cell('H1', function($cell) {$cell->setValue('COST_PER_CLICK'); });
                $sheet->cell('I1', function($cell) {$cell->setValue('EARNINGS'); });
                $sheet->cell('J1', function($cell) {$cell->setValue('INDIVIDUAL_AD_IMPRESSIONS_CTR'); });
                $sheet->cell('K1', function($cell) {$cell->setValue('INDIVIDUAL_AD_IMPRESSIONS_RPM'); });
                $sheet->cell('L1', function($cell) {$cell->setValue('MATCHED_AD_REQUESTS'); });
                $sheet->cell('M1', function($cell) {$cell->setValue('MATCHED_AD_REQUESTS_CTR'); });
                $sheet->cell('N1', function($cell) {$cell->setValue('MATCHED_AD_REQUESTS_RPM'); });
                $sheet->cell('O1', function($cell) {$cell->setValue('PAGE_VIEWS'); });
                $sheet->cell('P1', function($cell) {$cell->setValue('PAGE_VIEWS_CTR'); });
                $sheet->cell('Q1', function($cell) {$cell->setValue('PAGE_VIEWS_RPM'); });
                $sheet->cell('R1', function($cell) {$cell->setValue('IMPRESSIONS'); });
                if (!empty($cellData)) { 
                    foreach ($cellData as $key => $value) { 
                        $i= $key+2; 
                        $sheet->cell('A'.$i, $value['app']); 
                        $sheet->cell('B'.$i, $value['COUNTRY_CODE']); 
                        $sheet->cell('C'.$i, $value['AD_REQUESTS']); 
                        $sheet->cell('D'.$i, $value['AD_REQUESTS_COVERAGE']); 
                        $sheet->cell('E'.$i, $value['AD_REQUESTS_CTR']); 
                        $sheet->cell('F'.$i, $value['AD_REQUESTS_RPM']);
                        $sheet->cell('G'.$i, $value['CLICKS']);
                        $sheet->cell('H'.$i, $value['COST_PER_CLICK']);
                        $sheet->cell('I'.$i, $value['EARNINGS']);
                        $sheet->cell('J'.$i, $value['INDIVIDUAL_AD_IMPRESSIONS_CTR']);
                        $sheet->cell('K'.$i, $value['INDIVIDUAL_AD_IMPRESSIONS_RPM']);
                        $sheet->cell('L'.$i, $value['MATCHED_AD_REQUESTS']);
                        $sheet->cell('M'.$i, $value['MATCHED_AD_REQUESTS_CTR']);
                        $sheet->cell('N'.$i, $value['MATCHED_AD_REQUESTS_RPM']);
                        $sheet->cell('O'.$i, $value['PAGE_VIEWS']);
                        $sheet->cell('P'.$i, $value['PAGE_VIEWS_CTR']);
                        $sheet->cell('Q'.$i, $value['PAGE_VIEWS_RPM']);
                        $sheet->cell('R'.$i, $value['impressions']);
                    } 
                } 
            }); 
        })->download('xlsx');
    }
    //2019-12-26更新修改access_token功能
    public function envList(Request $request)
    {
        return view('admin.env_list');
    }
    //更新token
    public function envUpdate(Request $request,$id){
        if($request->isMethod('post')){
            $post = $request->post();
            // dump($post);
            if($post['id'] == '1'){
                $data = [
                    'ACCESS_TOKEN_INSTALL_SPEND' => $post['token'],
                ];
                (new ReportController)->modifyEnv($data);
                if(!$id){
                    return $this->json(500,'token信息更新失败,请稍后再试!');
                }else{
                    return $this->json(200,'token信息更新成功');
                }
            }
            if($post['id'] == '2'){
                $data = [
                    'ACCESS_TOKEN_FAN' => $post['token'],
                ];
                (new ReportController)->modifyEnv($data);
                if(!$id){
                    return $this->json(500,'token信息更新失败,请稍后再试!');
                }else{
                    return $this->json(200,'token信息更新成功');
                }
            }
            if($post['id'] == '3'){
                $data = [
                    'ACCESS_TOKEN_CLOSECAMPAIGN' => $post['token'],
                ];
                (new ReportController)->modifyEnv($data);
                if(!$id){
                    return $this->json(500,'token信息更新失败,请稍后再试!');
                }else{
                    return $this->json(200,'token信息更新成功');
                }
            }
            if($post['id'] == '4'){
                $data = [
                    'ACCESS_TOKEN_PAGE' => $post['token'],
                ];
                (new ReportController)->modifyEnv($data);
                if(!$id){
                    return $this->json(500,'token信息更新失败,请稍后再试!');
                }else{
                    return $this->json(200,'token信息更新成功');
                }
            }
            
        }else{
            if($id == '1'){
                return view('admin.env_update',['token_name'=>'ADPMD-install/spend(获取Facebook安装和花费数据)','id'=>$id]);
            }
            if($id == '2'){
                return view('admin.env_update',['token_name'=>'ADPMD-fan-used(获取Facebook变现数据)','id'=>$id]);
            }
            if($id == '3'){
                return view('admin.env_update',['token_name'=>'Close-Campaign(亏损关闭campaign)','id'=>$id]);
            }
            if($id == '4'){
                return view('admin.env_update',['token_name'=>'ADPMD-page(page状态监控)','id'=>$id]);
            }

            
        }

    }
    // 修改.env配置文件
    public function modifyEnv(array $data){
        $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';
     
        $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));
     
        $contentArray->transform(function ($item) use ($data){
             foreach ($data as $key => $value){
                 if(str_contains($item, $key)){
                     return $key . '=' . $value;
                 }
             }
     
             return $item;
         });
     
        $content = implode($contentArray->toArray(), "\n");
     
        \File::put($envPath, $content);
    }

}