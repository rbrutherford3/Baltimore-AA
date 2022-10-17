CREATE DATABASE `baltaa`;
USE `baltaa`;

CREATE TABLE `assignments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` date NOT NULL,
  `Meeting` int(11) NOT NULL,
  `Group` int(11) DEFAULT NULL,
  `Notes` tinytext COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `assignments_fk1` (`Meeting`),
  KEY `assignments_fk2` (`Group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `groups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `DOW` int(11) NOT NULL,
  `Gender` int(1) NOT NULL,
  `BG` tinyint(1) NOT NULL DEFAULT 1,
  `Rep` int(11) DEFAULT NULL,
  `Rep2` int(11) DEFAULT NULL,
  `Standby` int(11) DEFAULT NULL,
  `Notes` tinytext COLLATE utf8_unicode_ci DEFAULT NULL,
  `Active` tinyint(1) NOT NULL DEFAULT 1,
  `Probation` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  KEY `groups_fk0` (`Rep`),
  KEY `groups_fk2` (`Standby`),
  KEY `groups_fk1` (`Rep2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `institutions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `Address` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `City` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `Zip` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `BG` tinyint(1) NOT NULL,
  `NotesPublic` tinytext COLLATE utf8_unicode_ci DEFAULT NULL,
  `NotesPrivate` tinytext COLLATE utf8_unicode_ci DEFAULT NULL,
  `Active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `meetings` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DisplayID` int(11) NOT NULL,
  `Institution` int(11) NOT NULL,
  `DOW` int(11) NOT NULL,
  `Time` time NOT NULL,
  `Gender` int(1) NOT NULL,
  `Sponsor` int(11) DEFAULT NULL,
  `CoSponsor` int(11) DEFAULT NULL,
  `CoSponsor2` int(11) DEFAULT NULL,
  `NotesPublic` tinytext COLLATE utf8_unicode_ci DEFAULT NULL,
  `NotesPrivate` tinytext COLLATE utf8_unicode_ci DEFAULT NULL,
  `Active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID`),
  KEY `meetings_fk0` (`Institution`),
  KEY `meetings_fk1` (`Sponsor`),
  KEY `meetings_fk2` (`CoSponsor`),
  KEY `meetings_fk3` (`CoSponsor2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `people` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `Initial` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Phone` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Notes` tinytext COLLATE utf8_unicode_ci DEFAULT NULL,
  `Active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
