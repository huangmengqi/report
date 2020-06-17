<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\IndexController;

class Downcsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'downcsv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yesterday Mobpub Csv';

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
        // $date = '2019-07-22';
        $url = 'https://app.mopub.com/reports/custom/api/download_report?';
        $get_data = array (
            'report_key' => '0693ea4a0d084de78101ce8ee1662fcd',
            'api_key' => 'EW1sVmg2NvZfSTJ1RuWMNIXRIXuO_mfK',
            'date' => $date 
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
        $content = (new IndexController)->curl_get_https ( $info ['redirect_url'] );
        $file_name = 'mp-'.$date.'.csv';
        (new IndexController)->downfile( $content, $file_name );
        Log::info('每日Mobpub csv表下载完成');
    }
}
