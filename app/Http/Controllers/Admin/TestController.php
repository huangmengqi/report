<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\IndexController;
use Illuminate\Http\Request;
use DB;
use Redis;
use Google_Client;
use Google_Service_Drive;
use Storage;
use Illuminate\Support\Facades\Log;
use Hhxsv5\PhpMultiCurl\Curl;
use Hhxsv5\PhpMultiCurl\MultiCurl;
use Google\Auth\OAuth2;
use Mail;
use QL\QueryList;
use voku\helper\HtmlDomParser;
use voku\helper\SimpleHtmlDomInterface;
use voku\helper\SimpleHtmlDomNode;
use voku\helper\SimpleHtmlDomNodeInterface;
class TestController extends Controller
{



	public function test()
    {
        /*if(date("H:i:s") >= '15:00:00' && date("H:i:s") <= '24:00:00'){
            $date = date("Y-m-d");
        }else{
            $date = date("Y-m-d",strtotime("-1 day"));
        }  
        // $date = '2019-05-06';
        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->get();
        foreach ($gamelist as $key => $value) { 
            $url = 'https://www.googleapis.com/adsense/v1.4/accounts/'.$value->am_account_id.'/reports?metric=AD_REQUESTS_CTR&dimension=AD_UNIT_NAME&dimension=COUNTRY_CODE&';
                $get_data = array (
                    'startDate' => $date,
                    'endDate' => $date,
                    'currency' => 'USD',
                    'dimension' => 'APP_NAME',
                    'access_token' => Redis::get($value->am_account_id.'access_token')
                );
                $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
                $res_ary = json_decode ( $res, true );
                dump($res_ary);
                if (! empty ( $res_ary['rows'])) {
                    foreach ( $res_ary['rows'] as $k1 => $v1 ) {
                        if($v1['2'] == $value->am_app_name){
                            $data = array ();
                            $data ['unit_name'] = $v1 ['0'];
                            $data ['country_code'] = $v1 ['1'];
                            $data ['game_id'] = $value->id;
                            $data ['date'] = strtotime($date);
                            $data ['ctr'] = $v1 ['3'];
                            $count  = DB::table('ctr_unit')
                                ->where('game_id',$value->id)
                                ->where('date',strtotime($date))
                                ->where('unit_name',$v1 ['0'])
                                ->where('country_code',$v1 ['1'])
                                ->count();

                            if($count!=0){
                                DB::table('ctr_unit')
                                    ->where('game_id',$value->id)
                                    ->where('date',strtotime($date))
                                    ->where('unit_name',$v1 ['0'])
                                    ->where('country_code',$v1 ['1'])
                                    ->update(['ctr' => $v1 ['3']]);
                            }else{                        
                                DB::table('ctr_unit')->insert($data);
                            }
                        }
                    }
                }
                dump($value->am_app_name.'数据分广告位点击数据拉取完成！');
            }*/



        $date = '2019-05-06';
        //查询游戏列表
        $gamelist = DB::table('mg_game')->where('status',1)->get();
        foreach ($gamelist as $key => $value) { 
            $url = 'https://www.googleapis.com/adsense/v1.4/accounts/'.$value->am_account_id.'/reports?metric=INDIVIDUAL_AD_IMPRESSIONS_CTR&metric=AD_REQUESTS&metric=CLICKS&metric=INDIVIDUAL_AD_IMPRESSIONS&dimension=AD_UNIT_NAME&dimension=COUNTRY_CODE&';
                $get_data = array (
                    'startDate' => $date,
                    'endDate' => $date,
                    'currency' => 'USD',
                    'dimension' => 'APP_NAME',
                    'access_token' => Redis::get($value->am_account_id.'access_token')
                );
                $res = (new IndexController)->curl_get_https ( $url . http_build_query ( $get_data ) );
                $res_ary = json_decode ( $res, true );
                dump($res_ary);
        }



    }

}



