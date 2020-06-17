<?php

namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Install::class,
        Commands\Tinstall::class,
        Commands\Spend::class,
        Commands\Tspend::class,
        Commands\Ironsource::class,
        Commands\Tironsource::class,
        Commands\Admob::class,
        Commands\Tadmob::class,
        Commands\Zero::class,
        Commands\Email::class,
        Commands\Tfb::class,
        Commands\Al::class,
        Commands\Tal::class,
        Commands\Downcsv::class,
        Commands\Mpinsert::class,
        Commands\Mp::class,
        Commands\Getgoogle::class,
        Commands\Cadmob::class,
        Commands\Cap::class,
        Commands\Cmp::class,
        Commands\Cis::class,
        Commands\Cinstall::class,
        Commands\Cspend::class,
        Commands\Delmp::class,
        Commands\Cprofit::class,
        Commands\Crevenue::class,
        Commands\Cfb::class,
        Commands\Roi::class,
        Commands\Cpi::class,
        Commands\Page::class,
        Commands\Delmp::class,
        // Commands\Phone::class,
        Commands\SendEmails::class,
        // Commands\Ctr::class,
        Commands\Tadx::class,
        Commands\CloseCampaign::class,
        Commands\Upltv::class,
        Commands\OtherReport::class,
        Commands\Cinstallhezuo::class,
        Commands\Cspendhezuo::class,
        Commands\Ctrunit::class,
        Commands\Ctrunitcheck::class,
        Commands\HighCtr::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //每日凌晨插入一条空数据
        $schedule->command('zero')->daily();
        $schedule->command('delmp')->daily();
        //每日实时facebook变现数据（每三十分钟）
        $schedule->command('tfb')->hourly();
        //每日实时applovin变现数据（每五分钟）
        $schedule->command('tal')->everyFifteenMinutes();
        //每日实时install安装数据（每五分钟）
        $schedule->command('tinstall')->everyThirtyMinutes();
        //每日实时spend安装数据（每五分钟）
        $schedule->command('tspend')->everyThirtyMinutes();
        //每日实时Ironsource变现数据（每五分钟）
        $schedule->command('tironsource')->everyThirtyMinutes();
        //每日实时Admob变现数据（每五分钟）
        $schedule->command('tadmob')->everyThirtyMinutes();
        //每日下午四点下载前一天Mobpub Csv变现数据
        $schedule->command('downcsv')->dailyAt('09:00');
        //每日下午四点前一天Mobpub Csv数据入库
        $schedule->command('mpinsert')->dailyAt('09:10');
        //每日下午四点更新前一天Mobpub变现数据
        $schedule->command('mp')->dailyAt('09:20');
        //每日下午四点更新前一天Mobpub分国家变现数据
        $schedule->command('cmp')->dailyAt('10:00');
        //每小时更新一次Admob access_token
        $schedule->command('getgoogle')->hourly();
        //每日实时分国家Admob安装数据（每十五分钟）
        $schedule->command('cadmob')->everyThirtyMinutes();
        //每日实时分国家Ironsource安装数据（每五分钟）
        $schedule->command('cis')->everyThirtyMinutes();
        //每日实时分国家Applovin安装数据（每五分钟）
        $schedule->command('cap')->everyThirtyMinutes();
        //每日实时分国家应用安装数据
        $schedule->command('cinstall')->everyThirtyMinutes();
        //每日实时分国家应用安装数据
        $schedule->command('cspend')->everyThirtyMinutes();
        //每日实时分国家利润数据（每五分钟）
        $schedule->command('cprofit')->everyFiveMinutes();
        //每日实时分国家roi数据（每五分钟）
        $schedule->command('roi')->everyFiveMinutes();
        //每日实时分国家总收益数据（每五分钟）
        $schedule->command('cpi')->everyFiveMinutes();
        //每日实时分国家总收益数据（每五分钟）
        $schedule->command('crevenue')->everyFiveMinutes();
        //每日实时分国家Facebook数据（每三十分钟）
        $schedule->command('cfb')->hourly();
       
        $schedule->command('email')->everyThirtyMinutes();
        $schedule->command('page')->everyThirtyMinutes();

        // $schedule->command('phone')->everyFiveMinutes();//每五分钟检查利润亏损情况，并发送短信
        $schedule->command('sendemail')->everyFifteenMinutes();//每十五分钟检查产品下架情况，并发送短信
        // $schedule->command('ctr')->hourly()->between('20:00', '9:00');//每十五分钟检查投放点击率，异常则发送短信
        $schedule->command('tadx')->everyFiveMinutes();
        $schedule->command('closecampaign')->everyFifteenMinutes();
        $schedule->command('upltv')->dailyAt('17:20');
        $schedule->command('cinstallhezuo')->dailyAt('16:00');//外单产品每周五下午四点拉取上周投放分国家安装数据
        $schedule->command('cspendhezuo')->dailyAt('16:00');//外单产品每周五下午四点拉取上周投放分国家投放数据
        $schedule->command('otherreport')->everyFiveMinutes();
        //每小时更新一次Admob Unit Click Data
        $schedule->command('ctrunint')->hourly();
        $schedule->command('ctrunintcheck')->everyThirtyMinutes()->between('18:00', '15:00');
        $schedule->command('highctr')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

//        require base_path('routes/console.php');
    }
}
