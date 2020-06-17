<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Admin\IndexController;
use Redis;
class Getgoogle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getgoogle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Getgoogle Access_token';

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
        //查询Admob Refresh Code列表
        $codelist = DB::table('mg_admob_refresh_code')->get();

        $url1 = 'https://www.googleapis.com/oauth2/v4/token?';
        foreach ($codelist as $key => $value) {
            $get_data = array (
                'client_id' => $value->clientId,
                'client_secret' => $value->clientSecret,
                'refresh_token' => $value->code,
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
            dump($value->am_acountid);
            dump(json_decode($result,true));
            Redis::set($value->am_acountid.'access_token', $data['access_token']);
        }
        
        Log::info('每小时更新一次Admob Code完成');
    }
}
