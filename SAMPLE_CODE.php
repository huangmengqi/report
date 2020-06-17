<?php
/**
 * Copyright (c) 2015-present, Facebook, Inc. All rights reserved.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */

require __DIR__ . '/vendor/autoload.php';

use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdsInsights;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;

$access_token = 'EAADXu7ZBbD90BAFHQZCoipGof1wIJMDhAsDtcA6JWnL1kAZAnNgusEMv7FNKuZC4hIKNd15Y1vna3MsTkFft8x6FbcRuzu8I1OHuCs4lWkt5zXNcwnX5ViKoMZCZA2F7VEExoG9vPaZAroj62xbZAhAdhHWMNLaZCELs3HYgM1CqPwzKZBhtuu7gIr4fGiIR2qkZCJsQQr5WKX24uTFMnTYslFadLg0pLwjjrYZD';
$ad_account_id = 'act_102428613771792';
$app_secret = 'e308a21a8de3ba7ec135650eb97c9a6d';
$app_id = '237201373466589';

$api = Api::init($app_id, $app_secret, $access_token);
$api->setLogger(new CurlLogger());

$fields = array(
  'account_name',
  'campaign_group_id',
  'campaign_group_name',
  'campaign_name',
  'campaign_id',
  'results',
  'result_rate',
  'reach',
  'impressions',
  'frequency',
  'spend',
);
$params = array(
  'level' => 'ad',
  'filtering' => array(array('field' => 'delivery_info','operator' => 'IN','value' => array('active','limited','inactive','completed','recently_completed','not_delivering','not_published','rejected'))),
  'breakdowns' => array('ad_id'),
  'time_range' => array('since' => '2018-12-01','until' => '2019-01-01'),
);
echo json_encode((new AdAccount($ad_account_id))->getInsights(
  $fields,
  $params
)->getResponse()->getContent(), JSON_PRETTY_PRINT);

