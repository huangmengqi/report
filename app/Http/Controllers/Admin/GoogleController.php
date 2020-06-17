<?php
header('Content-Type:text/html; charset=utf-8');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/14
 * Time: 16:15
 */
class Google{
	function getGoogle(){
		require_once '/web/nginx/www/app/lib/google-api-php-client/vendor/autoload.php';
		$client = new Google_Client();
		$client->setAuthConfig('client_secrets.json');
		$client->setAccessType("offline");        // offline access
		$client->setIncludeGrantedScopes(true);   // incremental auth
		$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
		$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');
	}
}