<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;
use Excel;

class Mpinsert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mpinsert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yesterday Mobpub Csv Data Insert Into Database';

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
    
        $date = date("Y-m-d",strtotime("-2 day"));
        // $date = '2019-08-06';
        //查询游戏列表
        // $gamelist = DB::table('mg_game')->where('status',1)->get();
        $filePath = '/web/nginx/www/storage/downloads/mp-'.$date.'.csv';
        
        Excel::filter('chunk')->load($filePath)->chunk(5000, function($results){
            foreach($results as $row){
                $array = json_decode($row,true);
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
                $data ['impressions'] = $array['impressions'];
                $data ['clicks'] = $array['clicks'];
                $data ['conversions'] = $array['conversions'];
                $data ['revenue'] = $array['revenue'];
                $data ['ctr'] = $array['ctr'];
                DB::table('mg_mobpub')->insert($data);
            }
        });


    }
}