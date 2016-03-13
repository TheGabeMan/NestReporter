CREATE DATABASE nest;
GRANT ALL PRIVILEGES ON nest.* TO 'nest_admin'@'localhost' IDENTIFIED BY 'N35tP@55';
FLUSH PRIVILEGES;


CREATE USER 'nest_user'@'localhost' IDENTIFIED BY  '9qqTMiJqU3xCtqGcxHYp';
GRANT SELECT , 
INSERT ,
UPDATE ON nest . * TO  'nest_user'@'localhost' IDENTIFIED BY  '9qqTMiJqU3xCtqGcxHYp' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

USE nest;
DROP TABLE IF EXISTS `rawdata`;
CREATE TABLE IF NOT EXISTS `rawdata` (
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `NestName` char(30) DEFAULT NULL,
  `NestUpdated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `NestCurrentKelvin` decimal(7,3) NOT NULL,
  `NestTargetKelvin` decimal(7,3) NOT NULL,
  `NestTimeToTarget` int(15) NOT NULL,
  `NestHumidity` tinyint(3) unsigned NOT NULL,
  `NestHeating` tinyint(3) NOT NULL,
  `NestPostal_code` char(10) NOT NULL,
  `NestCountry` char(200) NOT NULL,
  `NestAutoAway` tinyint(3) unsigned NOT NULL,
  `NestManualAway` tinyint(3) unsigned NOT NULL,
  `WeatherMain` char(30) DEFAULT NULL,
  `WeatherDescription` char(100) DEFAULT NULL,
  `WeatherTempKelvin` decimal(7,3) NOT NULL,
  `WeatherHumidity` decimal(7,3) NOT NULL,
  `WeatherTempMinKelvin` decimal(7,3) NOT NULL,
  `WeatherTempMaxKelvin` decimal(7,3) NOT NULL,
  `WeatherPressure` decimal(7,3) NOT NULL,
  `WeatherWindspeed` decimal(7,3) NOT NULL,
  `WeatherCityName` char(30) DEFAULT NULL,
  `WeatherCloudiness` tinyint(3) NOT NULL,
  `WeatherSunRise` int(15) NOT NULL,
  `WeatherSunSet` int(15) NOT NULL,
  PRIMARY KEY (`timestamp`),
  UNIQUE KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
