SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
CREATE DATABASE IF NOT EXISTS `test_task`
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci;
USE `test_task`;

CREATE TABLE IF NOT EXISTS `ApiTables` (
  `Name` VARCHAR(125) NOT NULL DEFAULT '',
  PRIMARY KEY (`Name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `ApiTables` (`Name`) VALUES
  ('News'),
  ('Session');

CREATE TABLE IF NOT EXISTS `News` (
  `ID`            INT(11)      NOT NULL AUTO_INCREMENT,
  `ParticipantId` INT(11)      NOT NULL,
  `NewsTitle`     VARCHAR(255) NOT NULL,
  `NewsMessage`   TEXT         NOT NULL,
  `LikesCounter`  INT(11)      NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `unique_news` (`ParticipantId`, `NewsTitle`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 2;

INSERT INTO `News` (`ID`, `ParticipantId`, `NewsTitle`, `NewsMessage`, `LikesCounter`) VALUES
  (1, 1, 'New agenda!', 'Please visit our site!', 0);

CREATE TABLE IF NOT EXISTS `Participant` (
  `ID`    INT(11)      NOT NULL AUTO_INCREMENT,
  `Email` VARCHAR(255) NOT NULL,
  `Name`  VARCHAR(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `unique_email` (`Email`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 2;

INSERT INTO `Participant` (`ID`, `Email`, `Name`) VALUES
  (1, 'airmail@code-pilots.com', 'The first user');

CREATE TABLE IF NOT EXISTS `Session` (
  `ID`          INT(11)      NOT NULL AUTO_INCREMENT,
  `Name`        VARCHAR(255) NOT NULL,
  `TimeOfEvent` DATETIME     NOT NULL,
  `Description` TEXT         NOT NULL,
  `Limit`       INT(11)      NOT NULL,
  PRIMARY KEY (`ID`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `SessionHasParticipant` (
  `SessionId`     INT(11) UNSIGNED NOT NULL,
  `ParticipantId` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`SessionId`, `ParticipantId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `Speaker` (
  `ID`   INT(11)      NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`ID`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 3;

INSERT INTO `Speaker` (`ID`, `Name`) VALUES
  (1, 'Watson'),
  (2, 'Arnold');
