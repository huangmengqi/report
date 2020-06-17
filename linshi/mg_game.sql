-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2019 �?02 �?14 �?15:14
-- 服务器版本: 5.5.53
-- PHP 版本: 5.6.27

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `mg`
--

-- --------------------------------------------------------

--
-- 表的结构 `mg_game`
--

CREATE TABLE IF NOT EXISTS `mg_game` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `add_time` int(10) NOT NULL,
  `fb_read_access_token` varchar(200) DEFAULT NULL,
  `fb_read_accounts` varchar(500) DEFAULT NULL,
  `fb_app_id` varchar(50) DEFAULT NULL,
  `fb_access_token` varchar(200) DEFAULT NULL,
  `is_app_key` varchar(50) DEFAULT NULL,
  `is_username` varchar(50) DEFAULT NULL,
  `is_secrect_key` varchar(100) DEFAULT NULL,
  `mp_api_key` varchar(100) DEFAULT NULL,
  `mp_report_key` varchar(100) DEFAULT NULL,
  `mp_app_id` varchar(100) DEFAULT NULL,
  `al_api_key` varchar(100) DEFAULT NULL,
  `al_package_name` varchar(50) DEFAULT NULL,
  `am_account_id` varchar(50) DEFAULT NULL,
  `am_app_name` varchar(50) DEFAULT NULL,
  `am_json` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `channel_id` int(10) NOT NULL DEFAULT '0',
  `monitor_phone_number` varchar(200) DEFAULT NULL,
  `monitor_per_cost` smallint(3) NOT NULL DEFAULT '50',
  PRIMARY KEY (`id`),
  KEY `mp_report_key` (`mp_report_key`),
  KEY `status` (`status`,`channel_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `mg_game`
--

INSERT INTO `mg_game` (`id`, `name`, `add_time`, `fb_read_access_token`, `fb_read_accounts`, `fb_app_id`, `fb_access_token`, `is_app_key`, `is_username`, `is_secrect_key`, `mp_api_key`, `mp_report_key`, `mp_app_id`, `al_api_key`, `al_package_name`, `am_account_id`, `am_app_name`, `am_json`, `status`, `channel_id`, `monitor_phone_number`, `monitor_per_cost`) VALUES
(1, 'Snake Pixel', 1542001254, 'EAADXu7ZBbD90BAJKMNzMCpW9NwvaMowlVe8wxnaYgJdf1iYJUb8ZC0xB1aVSYhTpOUyESMa4nZAZABA0HfYzFJRf2u56RaweGBtt7X8PNZAJqIrwZBZBWTyhTwT1xFZAVreKNBEAADXu7ZBbD90BAGbeLPak2SsIBFbFg0ttZBbCcwE4kSvXi88ec08qeZB6jtzuehz', '1706317736071562,160214698159845,2209216432439023,456982744770168,476212712870340,345724759524457,1756655207793375,304708420133093,338630520051682,107222887024173', '237201373466589', 'EAADXu7ZBbD90BAGbeLPak2SsIBFbFg0ttZBbCcwE4kSvXi88ec08qeZB6jtzuehzGoMZB1H8P3kmT1M8RmYVosVjO2GtWdQsi0XfSPqGeOYe6rYzKgZCZAzXVAXerf0l9gCelAojXGIqJNL56zwbmvjCE1dTrH5ZAcuqnw2ooVF4K7oBGisj1ZB3qNbBu16FRU2A1ZB', '71e2dc25', 'andappsok@gmail.com', '0665d88a3386623d8adf7456e0b30d61', 'EW1sVmg2NvZfSTJ1RuWMNIXRIXuO_mfK', '8f210736ad5d4c44b938156fbf205228', '9d1ca6667a0e4fe982fc1bcf8e4cccb8', 'KrwMq14VaH6NqbdkkHmRk-iz65hn9UfUKZs8bweAClT1LAl4SUVBTD8aIqLSJ0yeaonAiPLv9uwV3fZW94hcHq', 'com.etec.snake_pixel', 'pub-5456881337868996', 'Snake Pixel', 's1112.json', 1, 2, '', 50),
(2, 'Magic Cube Splash', 1542005854, '', '', '', '', '', '', '', '', '', '', '', '', 'pub-9542353568215068', 'Magic Cube Splash', 'h1112.json', 1, 3, '', 50),
(3, 'Free Music Player', 1542006644, '', '', '', '', '', '', '', '', '', '', '', '', 'pub-9394036372895964', 'Free Music Player', 'd1112.json', 1, 4, '', 50),
(4, 'Snake Gear', 1542006697, 'EAACTmudLYuIBAGZC7TZAREXXJEFMGTq2r3cg4fpyBf3nRTZAZAkEWlK7gcd0IlLPIKU5ddYiWTCpzOKdFdv0KE28FumGvOCoMIG2kGHhHByVdKpHFUMzu8YNXIMMzlv5daGSwgYzpZCQz38JZA2XZC4bsLyAsRBHDetJMrxcrFyY6c96Ojj456G', '2164823970218226,1929928437122891,764330707244120,1146011812213160,257162368317579,326629591455695', '2142585852443030', 'EAAecq6SiuZAYBAJSFsWG5qxQKjTiquvO6xnxCxtWIFTggIfQ07fjN7G3VZBX6VpG9iZBMbCR6ZBrdaz0uNHybWkAJ043Wjzsm8NxrQn9HmW53Ro3zKttORKZAchcl6O4BZBu3SJ01QewD0hCW253jGAJcPTQoYZCEZBWo2q6yk81KX8YtKFm2hz4', '7e9f4385', 'andappsok@gmail.com', '0665d88a3386623d8adf7456e0b30d61', 'EW1sVmg2NvZfSTJ1RuWMNIXRIXuO_mfK', '8f210736ad5d4c44b938156fbf205228', '80bba7b92f6d461ab119bf65b104aa83', 'KrwMq14VaH6NqbdkkHmRk-iz65hn9UfUKZs8bweAClT1LAl4SUVBTD8aIqLSJ0yeaonAiPLv9uwV3fZW94hcHq', 'com.etec.minigame.snakegear', 'pub-8211485927723458', 'Snake Gear', 'a1112.json', 1, 2, '', 50),
(5, 'Dot Music', 1542021773, 'EAAKWbQw6pYIBAK01y0XNoGQgl4bjPU9bwFX3bpb1EQagNXrwSrF1DFhyvJzWZC1X0Iuza5Ovp1xPG2GRNTrhdOqi85fCi3utpAc4sII3qJDntiA7OERXnwjsuD5tJPtNXAlwFEqWVA2iOW5wBLbXCZCCkyyqBICOiupIrxFycqCvQ3kcj7', '2209555735957099,281561625804496,489956474824230,261825371203024,333636123851195', '451214692012764', '', '', '', '', '', '', '6a20e20192f74d678d2d054f67729603', 'KrwMq14VaH6NqbdkkHmRk-iz65hn9UfUKZs8bweAClT1LAl4SUVBTD8aIqLSJ0yeaonAiPLv9uwV3fZW94hcHq', '', '', '', 's1112.json', 1, 4, '', 50),
(6, 'Blue Tunes', 1542022901, 'EAADXu7ZBbD90BAJj5FqevZCQZCuv0sJSOQwtWTnmJy3v7k2ZAqZAM7ZAWeKbZBqu6gqZCjY3h9BtSaWzTFd2Swcis4FtbKP2yz03MBZBcpvALMIUTSsPIiiRGLWnGdFHxdEwCMaQwfoNuMY9zPvdQgQ1dimWC6U9C23my0ksx67ZCfnJFnP41NuWpb', '112208269858968', '237201373466589', '', '', '', '', 'EW1sVmg2NvZfSTJ1RuWMNIXRIXuO_mfK', '8f210736ad5d4c44b938156fbf205228', '25d64edb0f2246d9b66db36da56070cb', '', '', 'pub-9542353568215068', '', 'h1112.json', 1, 4, '', 50);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
