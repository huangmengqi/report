<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware'=>'rbac'], function () use($router) {
    //框架
    $router->get('/','Admin\IndexController@index');
    //控制台
    $router->get('/console','Admin\IndexController@console');
    
    //403无访问权限
    $router->get('/403','Admin\IndexController@noPermission');
    $router->group(['prefix' => 'admin'], function () use($router) {
        //菜单管理
        $router->get('/menu/list', 'Admin\AdministratorController@menuList');
        $router->any('/menu/add', 'Admin\AdministratorController@menuAdd');
        $router->any('/menu/update/{id}', 'Admin\AdministratorController@menuUpdate');
        $router->post('/menu/del/{id}', 'Admin\AdministratorController@menuDel');

        //角色管理
        $router->get('/role/list', 'Admin\AdministratorController@roleList');
        $router->any('/role/add', 'Admin\AdministratorController@roleAdd');
        $router->any('/role/update/{id}', 'Admin\AdministratorController@roleUpdate');
        $router->post('/role/del/{id}', 'Admin\AdministratorController@roleDel');
        //权限管理
        $router->get('/permission/list','Admin\AdministratorController@permissionList');
        $router->any('/permission/add','Admin\AdministratorController@permissionAdd');
        $router->any('/permission/update/{id}','Admin\AdministratorController@permissionUpdate');
        $router->post('/permission/del/{id}','Admin\AdministratorController@permissionDel');
        //管理员管理
        $router->get('/administrator/list','Admin\AdministratorController@administratorList');
        $router->any('/administrator/add','Admin\AdministratorController@administratorAdd');
        $router->any('/administrator/update/{id}','Admin\AdministratorController@administratorUpdate');
        $router->post('/administrator/del/{id}','Admin\AdministratorController@administratorDel');
        //配置管理
        $router->get('/config/list','Admin\ConfigController@configList');
        $router->any('/config/add','Admin\ConfigController@configAdd');
        $router->any('/config/update/{id}','Admin\ConfigController@configUpdate');
        $router->post('/config/del/{id}','Admin\ConfigController@configDel');
        //运营报表
        $router->any('/report/dayreport','Admin\ReportController@dreportList');
        $router->any('/report/countryreport','Admin\ReportController@countryReportList');
        $router->any('/report/appcountryreport','Admin\ReportController@appCountryReportList');
        $router->any('/report/totalreport','Admin\ReportController@totalReportList');
        $router->get('/report/applist','Admin\ReportController@appList');
        $router->any('/report/appadd','Admin\ReportController@appAdd');
        $router->post('/report/appdel/{id}','Admin\ReportController@appDel');
        $router->any('/report/appupdate/{id}','Admin\ReportController@appUpdate');

        $router->get('/report/envlist','Admin\ReportController@envList');
        $router->any('/report/envadd','Admin\ReportController@envAdd');
        $router->post('/report/envdel/{id}','Admin\ReportController@envDel');
        $router->any('/report/envupdate/{id}','Admin\ReportController@envUpdate');


        $router->any('/report/getyesrterdaydata','Admin\ReportController@getYesterdayData');
        
        $router->any('/index/getdata', 'Admin\ReportController@getData');
        $router->any('/report/countryads', 'Admin\ReportController@countryAds');
        //图片上传
        $router->post('/upload','Admin\IndexController@upload');
        $router->post('/wangeditor/upload','Admin\IndexController@wangeditorUpload');
        $router->any('/index/getAdmobCode', 'Admin\IndexController@getAdmobCode');
        $router->any('/index/getMpcsv', 'Admin\IndexController@mpDownloadCsv');
        $router->any('/index/getMpinsert', 'Admin\IndexController@Mpinsert');
        $router->any('/index/iron', 'Admin\IndexController@getIsource');
        $router->any('/index/mp', 'Admin\IndexController@getMobpub');
        $router->any('/index/lovin', 'Admin\IndexController@getAlovin');
        $router->any('/index/fb', 'Admin\IndexController@getFb');

        $router->any('/index/getadmobcode', 'Admin\IndexController@getAdmobCode');
        $router->any('/index/getam', 'Admin\IndexController@getAdmob');
        $router->any('/index/ciron', 'Admin\IndexController@getCisource');
        $router->any('/index/upltv','Admin\IndexController@upltv');
        $router->any('/index/line', 'Admin\IndexController@Line');
        $router->any('/index/appline', 'Admin\IndexController@appLine');
        $router->any('/index/sendemail', 'Admin\IndexController@sendEmail');
        // $router->any('/index/sendemail', 'Admin\IndexController@sendEmail');
        $router->any('/index/install', 'Admin\IndexController@getInstallSum');

        $router->any('/index/jixiao', 'Admin\ReportController@getJixiao');

        $router->any('/index/am', 'Admin\IndexController@getAdmob');

        $router->any('/index/url', 'Admin\IndexController@url');

        $router->any('/report/urladd', 'Admin\ReportController@urlAdd');

        $router->any('/report/urlupdate/{id}', 'Admin\ReportController@urlUpdate');

        $router->any('/report/urllist', 'Admin\ReportController@urlList');

        $router->any('/report/urldel/{id}', 'Admin\ReportController@urlDel');


        $router->any('/report/downloadfbjx', 'Admin\ReportController@downloadFbjx');

        $router->any('/index/jixiaoadmob', 'Admin\ReportController@getJixiaoAdmob');

        $router->any('/report/downloadadjx', 'Admin\ReportController@downloadAdjx');

        $router->any('/report/apprank', 'Admin\ReportController@appRank');

        $router->any('/report/toolrank', 'Admin\ReportController@toolRank');
        $router->any('/report/uploadtoolxls', 'Admin\ReportController@uplaodtoolXls');
        $router->any('/report/comparison', 'Admin\ReportController@Comparison');
        $router->any('/report/uploadxls', 'Admin\ReportController@uplaodXls');
        $router->any('/report/uploadadmob', 'Admin\ReportController@uplaodAdmob');
        //自研分国家数据报表下载
        $router->any('/report/downloadmuti', 'Admin\ReportController@Downloadcsvmuti');
        $router->any('/report/download','Admin\ReportController@Downloadcsv');
        $router->any('/report/downloadmutiday', 'Admin\ReportController@Downloadcsvmutiday');
        //外单分国家数据报表下载
        $router->any('/report/downloadhezuo','Admin\ReportController@Downloadcsvhezuo');
        $router->any('/report/hezuocountryreport', 'Admin\ReportController@countryReportHezuo');
        // 分国家分广告位点击率
        $router->any('/report/ctrunitcountryreport', 'Admin\ReportController@ctrUnit');

        $router->any('/report/comparedxls', 'Admin\ReportController@comparedXls');
        $router->any('/report/getcompared', 'Admin\ReportController@getCompared');
        $router->any('/report/downloadcompared','Admin\ReportController@Downloadcompared');
        
        $router->any('/index/getadmobjixiao', 'Admin\IndexController@getAdmobJixiao');
        $router->any('/index/getfbjixiao', 'Admin\IndexController@getFbJixiao');

        });
	    //修改个人信息
	    $router->any('/edit/info/{id}','Admin\AdministratorController@editInfo');
	    //退出登录
	    $router->get('/logout','Admin\AdministratorController@logout');

        $router->any('/phone', 'Admin\PhoneController@Phone');
        $router->any('/mobile', 'Admin\MobileController@mobile');
        $router->any('/Status', 'Admin\StatusController@Status');
        $router->any('/app','Admin\ReportController@appCountryReportList');    
        $router->any('/test','Admin\TestController@Test');

        // 账户清理
        $router->any('/report/actdeletelist','Admin\ReportController@actdeleteList');
        $router->any('/report/actdeleteadd','Admin\ReportController@actdeleteAdd');
        $router->any('/report/appadd','Admin\ReportController@appAdd');

        // 数据同步
        $router->any('/report/synchronizedatalist','Admin\ReportController@synchronizeDataList');
        $router->any('/report/synchronizedataadd','Admin\ReportController@synchronizeDataAdd');


	});
	$router->any('/login','Admin\AdministratorController@login');
    $router->get('/envapi', 'Admin\TTTTController@envApi');
	$router->get('/icon', function(){
	    return view('admin.icon');
	});