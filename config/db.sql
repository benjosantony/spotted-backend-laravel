CREATE TABLE `sp_academyconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(3) DEFAULT NULL COMMENT 'level of academy',
  `buildingToken` int(11) DEFAULT NULL COMMENT 'building token require for this level',
  `bonusRate` int(3) DEFAULT '0' COMMENT 'value from 0 -> 100',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sp_academyconfig` VALUE (null, 1, 0, 0);

/*Table structure for table `sp_cashoutinfo` */

CREATE TABLE `sp_cashoutinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `bankName` varchar(150) DEFAULT NULL,
  `bankAccount` varchar(20) DEFAULT NULL,
  `gender` int(1) DEFAULT '0' COMMENT '0: female, 1: male',
  `age` int(3) DEFAULT '18',
  `expireDate` date DEFAULT NULL COMMENT 'date expire of bank account',
  `dateCreated` datetime DEFAULT NULL,

  `status` int(1) DEFAULT '0' COMMENT '0: not update, 1: admin approved, 2: pedding (wait admin approve), 3: denied',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sp_compassrate` */

CREATE TABLE `sp_compassrate` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `multiple` int(1) DEFAULT NULL COMMENT 'multiple: x1, x2, x3, x4, x5',
  `rate` int(3) DEFAULT NULL COMMENT 'sum of this must be equal to 100',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sp_dockconfig` */

CREATE TABLE `sp_dockconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(3) DEFAULT NULL,
  `buildingToken` int(11) DEFAULT NULL COMMENT 'buildingToken require for this level',
  `bonusShip` int(2) DEFAULT NULL COMMENT 'ship give to user when get that level.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sp_dockconfig` VALUE (null, 1, 0, 0);

/*Table structure for table `sp_factoryconfig` */

CREATE TABLE `sp_factoryconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(3) DEFAULT NULL COMMENT 'level of factory',
  `buildingToken` int(11) DEFAULT NULL COMMENT 'building token require',
  `reduceTime` int(11) DEFAULT NULL COMMENT 'unit: second',
  `reducePercent` int (3) default 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sp_factoryconfig` VALUE (null, 1, 0, 0, 0);

/*Table structure for table `sp_levelconfig` */

CREATE TABLE `sp_levelconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fromExp` int(11) DEFAULT NULL,
  `toExp` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `buildingToken` int(11) DEFAULT NULL COMMENT 'bonus building token when get level',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sp_levelconfig` VALUE (null, 0, 500, 1, 0);

CREATE TABLE `sp_dailyrewardconfig` (
  `id`       INT(5) NOT NULL AUTO_INCREMENT,
  `name`     VARCHAR(30) COMMENT 'Name of the key',
  `rate` INT(3) NULL COMMENT 'percent appear this name',
  `minValue` INT(11)         DEFAULT 0
  COMMENT 'minValue for that name',
  `maxValue` INT(11)         DEFAULT 0
  COMMENT 'maxValue for that name',
  PRIMARY KEY (`id`)
)ENGINE = INNODB CHARSET = utf8 COLLATE = utf8_general_ci;
INSERT INTO `sp_dailyrewardconfig` (`name`, `rate`, `maxValue`) VALUES ('exp', 5, '500');
INSERT INTO `sp_dailyrewardconfig` (`name`, `rate`, `maxValue`) VALUES ('token', 5, '500');
INSERT INTO `sp_dailyrewardconfig` (`name`, `rate`, `maxValue`) VALUES ('gold', 5, '500');

/*Table structure for table `sp_answerresult` */
CREATE TABLE `sp_answerresult` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL COMMENT 'user answer',
  `questionId` int(11) DEFAULT NULL COMMENT 'the question was asked',
  `answerId` int(11) DEFAULT NULL COMMENT 'the answer for this question',
  `correct` int(1) DEFAULT NULL COMMENT '0: incorrect, 1: correct',
  `dateAnwser` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sp_question` */
CREATE TABLE `sp_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `advertiseId` int(11) DEFAULT '0' COMMENT 'reference to adverting, 0: use for all adverting',
  `status` int(1) DEFAULT '0' COMMENT '0: inactive, 1: active',
  `dateFrom` datetime DEFAULT CURRENT_TIMESTAMP,
  `dateTo` datetime DEFAULT '2020-01-01 00:00:00' COMMENT 'default valid date',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sp_questionanwser` */
CREATE TABLE `sp_questionanwser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `questionId` int(11) DEFAULT NULL COMMENT 'reference to sp_question.id',
  `title` varchar(1000) DEFAULT NULL,
  `correct` int(1) DEFAULT '0' COMMENT '0: incorrect, 1: correct',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sp_dailyrewardreceived` (
  `id`          INT(20) NOT NULL AUTO_INCREMENT,
  `userId`      INT(11),
  `dateReceive` DATETIME,
  `multiplier`  INT(1) COMMENT '1,2,3,4,5, after 5 times, we rollback to 1',
  `type`        varchar(5) DEFAULT 'exp' COMMENT 'exp or token or gold',
  `value`       int(11) DEFAULT 0 COMMENT 'the value in one of exp/token/gold that user get from daily reward',
  PRIMARY KEY (`id`)
) ENGINE = INNODB CHARSET = utf8 COLLATE = utf8_general_ci;

CREATE TABLE `sp_askforship` (
  `id`         INT(11) NOT NULL AUTO_INCREMENT,
  `requestId`  VARCHAR(50) COMMENT 'facebook request id',
  `senderId`   VARCHAR(20) COMMENT 'facebook sender id',
  `receiverId` VARCHAR(20) COMMENT 'facebook receiver id',
  `dateSent`   DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'date send request',
  `dateAccept` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'date receiver accepted request',
  `status`     INT(1)   DEFAULT 0 COMMENT '0: request ship, 1: send ship',
  PRIMARY KEY (`id`)
) ENGINE = INNODB CHARSET = utf8 COLLATE = utf8_general_ci;

CREATE TABLE `sp_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(1) DEFAULT '0' COMMENT '0: inactive, 1: active, 2: ban',
  `level` int(4) DEFAULT '1' COMMENT 'current level',
  `fbId` varchar(20) DEFAULT NULL,
  `fullname` varchar(300) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `gender` int(1) DEFAULT '0' COMMENT '-1: not enter, 0: girl, 1: boy',
  `age` int(3) DEFAULT '0',
  `phone` varchar(15) DEFAULT NULL,
  `dateCreate` datetime DEFAULT CURRENT_TIMESTAMP,
  `exp` int(11) DEFAULT 0,
  `ship` int(3) DEFAULT 0 COMMENT 'current ship',
  `dateLastPlay` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'The last time that user play a game',
  `dateLastShipUp` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'this value use to calculator number of ship that player can use',
  `buildingToken` int(11) DEFAULT 0,
  `coin` int(11) DEFAULT 0,
  `token` varchar(300) DEFAULT NULL,
  `shareForKey` date DEFAULT NULL COMMENT 'if equal to current date, it means shared. This column should be use trigger to log changed',
  `factoryLevel` int(3) DEFAULT '1' COMMENT 'current level of factory',
  `dockLevel` int(3) DEFAULT '1' COMMENT 'current level of dock',
  `academyLevel` int(3) DEFAULT '1' COMMENT 'current level of academy',
  `inProgress` int(1) DEFAULT '0' COMMENT 'check this row is on progress or not, 0: No, 1: Yes',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ALTER TABLE `spottedpuzzle`.`sp_user` ADD COLUMN `factoryLevel` INT(3) DEFAULT 1 NULL COMMENT 'current level of factory' AFTER `shareForKey`, ADD COLUMN `dockLevel` INT(3) DEFAULT 1 NULL COMMENT 'current level of dock' AFTER `factoryLevel`, ADD COLUMN `academyLevel` INT(3) DEFAULT 1 NULL COMMENT 'current level of academy' AFTER `dockLevel`;

CREATE TABLE `sp_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `session` varchar(64) DEFAULT NULL,
  `lastAccess` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `sp_advertisement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'date upload advertisement',
  `name` varchar(30) DEFAULT NULL COMMENT 'name of this advertisement',
  `imageUrl` varchar(255) NOT NULL,
  `firstKey` int(4) NOT NULL COMMENT 'time to drop first key',
  `secondKey` int(4) NOT NULL COMMENT 'time to drop second key',
  `thirdKey` int(4) NOT NULL COMMENT 'time to drop third key',
  `expRate` int(11) NOT NULL COMMENT 'rate of receiving exp',
  `expMin` int(11) NOT NULL COMMENT 'minimum exp value',
  `expMax` int(11) NOT NULL COMMENT 'maximum exp value',
  `cashRate` int(11) NOT NULL COMMENT 'rate of receiving cash',
  `cashMin` int(11) NOT NULL COMMENT 'minimum cash value',
  `cashMax` int(11) NOT NULL COMMENT 'maximum cash value',
  `tokenRate` int(11) NOT NULL COMMENT 'rate of receiving token',
  `tokenMin` int(11) NOT NULL COMMENT 'minimum token value',
  `tokenMax` int(11) NOT NULL COMMENT 'maximum token value',
  `trashRate` int(11) NOT NULL COMMENT 'rate of receiving trash',
  `dealType` int(1) NOT NULL DEFAULT '0' COMMENT '0: undefined type, 1: video, 2: website, 3: deal/offer one times, 4: deal/offer multi times',
  `dealValue` varchar(255) DEFAULT NULL,
  `status` int(1) DEFAULT '1' COMMENT '0: inactive, 1: active',
  `timeToPlay` int(4) NOT NULL DEFAULT '120' COMMENT 'total time to play this advertisement in seconds',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
ALTER TABLE `spottedpuzzle`.`sp_advertisement` ADD COLUMN `lastUpdate` DATE NULL AFTER `timeToPlay`;
ALTER TABLE `spottedpuzzle`.`sp_advertisement` ADD COLUMN `thumbUrl` varchar(255) NOT NULL AFTER `imageUrl`;

CREATE TABLE `spottedpuzzle`.`sp_loveadvertisement` (
  `id`         INT(11) NOT NULL AUTO_INCREMENT,
  `userId`     INT(11) NOT NULL,
  `adverId`    INT(11) NOT NULL,
  `name`       VARCHAR(30),
  `imageUrl`   VARCHAR(255),
  `dealType`   INT(1) COMMENT '0: undefined type, 1: video, 2: website, 3: deal/offer one times, 4: deal/offer multi times',
  `dealValue`  VARCHAR(255),
  `isUsed`     DATETIME COMMENT 'date that user use this deal',
  `dateCreate` DATETIME,
  PRIMARY KEY (`id`)
) ENGINE = INNODB CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
ALTER TABLE `spottedpuzzle`.`sp_loveadvertisement` ADD COLUMN `thumbUrl` varchar(255) NOT NULL AFTER `imageUrl`;

CREATE TABLE `spottedpuzzle`.sp_trackaskforship(
  `id`         INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `userId`     INT(11) NOT NULL,
  `dateCreate` DATETIME
)ENGINE = INNODB CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

DROP FUNCTION IF EXISTS getMaxShip;
CREATE FUNCTION getMaxShip(userId int(11))
  RETURNS int
  BEGIN
    DECLARE _maxShip int(3) default 3;
    select case when (bonusShip + _maxShip) is null then _maxShip else (bonusShip + _maxShip) end into _maxShip from sp_dockconfig where level = (select dockLevel from sp_user WHERE id = userId LIMIT 0, 1);
    return _maxShip;
  END;


DROP FUNCTION IF EXISTS getCurrentShip;
CREATE FUNCTION getCurrentShip(userId int(11), _currentDate DATETIME)
  RETURNS int
  BEGIN
    DECLARE _ship int(3) DEFAULT 0;
    DECLARE _shipUp int(3) default 0;
    DECLARE _timeReduceNextShip INT(4) DEFAULT 1800; -- 30*60 seconds (30 minutes for each ship)
    DECLARE _maxShip int(3) DEFAULT 3;
    DECLARE _dateLastShipUp DATETIME DEFAULT NULL;
    DECLARE _totalTime INT(11) DEFAULT 0;

    SET _maxShip = getMaxShip(userId);

    SELECT dateLastShipUp, ship, (_timeReduceNextShip - reduceTime) INTO _dateLastShipUp, _ship, _timeReduceNextShip FROM sp_user u LEFT JOIN sp_factoryconfig f ON u.level = f.level WHERE u.id = userId and status = 1 LIMIT 0, 1;
    if _dateLastShipUp is null then return -1; end if;
    IF _ship < _maxShip THEN
      SET _totalTime = TIMESTAMPDIFF(SECOND, _dateLastShipUp, _currentDate);
      SET _shipUp = FLOOR(_totalTime/_timeReduceNextShip);
      if _shipUp <> 0 then
        SET _ship = _ship + _shipUp;
        IF _ship >= _maxShip THEN
          SET _ship = _maxShip;
          update sp_user set ship = _ship, dateLastShipUp = _currentDate where id = userId;
        else
          update sp_user set ship = _ship, dateLastShipUp = date_add(dateLastShipUp, INTERVAL (_timeReduceNextShip * _shipUp) SECOND ) where id = userId;
        END IF;
      END IF;
    END IF;
    return _ship;
  END;
DROP PROCEDURE IF EXISTS getCurrentShipAndTimeNextShip;
CREATE PROCEDURE getCurrentShipAndTimeNextShip(userId int(11), _currentDate DATETIME, OUT outShip int(3), OUT nextShip int(4))
  -- this procedure use in another procedure
  BEGIN
    DECLARE _ship int(3) DEFAULT 0;
    DECLARE _shipUp int(3) default 0;
    DECLARE _timeForOneShip INT(4) DEFAULT 1800; -- 30*60 seconds (30 minutes for each ship)
    DECLARE _maxShip int(3) DEFAULT 3;
    DECLARE _dateLastShipUp DATETIME DEFAULT NULL;
    DECLARE _totalTime INT(11) DEFAULT 0;

    SET _maxShip = getMaxShip(userId);
    SELECT dateLastShipUp, `ship`, (_timeForOneShip - reduceTime) INTO _dateLastShipUp, _ship, _timeForOneShip FROM sp_user u LEFT JOIN sp_factoryconfig f ON u.factoryLevel = f.level WHERE u.id = userId and status = 1 LIMIT 0, 1;

    IF _ship < _maxShip THEN
      SET _totalTime = TIMESTAMPDIFF(SECOND, _dateLastShipUp, _currentDate);
      SET _shipUp = FLOOR(_totalTime/_timeForOneShip);
      if _shipUp > 0 then
        SET _ship = _ship + _shipUp;
        IF _ship >= _maxShip THEN
          SET _ship = _maxShip;
          update sp_user set ship = _ship, dateLastShipUp = _currentDate where id = userId;
        else
          update sp_user set ship = _ship, dateLastShipUp = date_add(dateLastShipUp, INTERVAL (_timeForOneShip * _shipUp) SECOND ) where id = userId;
        END IF;
      END IF;
      set outShip = _ship;
      set nextShip = _timeForOneShip - (_totalTime - (_timeForOneShip * _shipUp));
      IF nextShip < 0 then
        SET nextShip = 0;
      END IF ;
    else
      set outShip = _maxShip;
      set nextShip = 0;
    END IF;
--    select outShip as ship, nextShip, _timeForOneShip as timeForOneShip from dual;
  END;
DROP PROCEDURE IF EXISTS getCurrentShipAndTimeNextShipEx;
CREATE PROCEDURE getCurrentShipAndTimeNextShipEx(userId int(11), _currentDate DATETIME)
  -- This procedure use for laravel
  BEGIN
    DECLARE _ship int(3) DEFAULT 0;
    DECLARE _shipUp int(3) default 0;
    DECLARE _timeForOneShip INT(4) DEFAULT 1800; -- 30*60 seconds (30 minutes for each ship)
    DECLARE _maxShip int(3) DEFAULT 3;
    DECLARE _dateLastShipUp DATETIME DEFAULT NULL;
    DECLARE _totalTime INT(11) DEFAULT 0;
    declare debug text;
    SET _maxShip = getMaxShip(userId);

    SELECT dateLastShipUp, ship, (_timeForOneShip - reduceTime) INTO _dateLastShipUp, _ship, _timeForOneShip FROM sp_user u LEFT JOIN sp_factoryconfig f ON u.factoryLevel = f.level WHERE u.id = userId and status = 1 LIMIT 0, 1;

    IF _ship < _maxShip THEN
      SET _totalTime = TIMESTAMPDIFF(SECOND, _dateLastShipUp, _currentDate);
      SET _shipUp = FLOOR(_totalTime/_timeForOneShip);
      if _shipUp > 0 then
        SET _ship = _ship + _shipUp;
        IF _ship >= _maxShip THEN
          SET _ship = _maxShip;
          set _dateLastShipUp = _currentDate;
        else
          set _dateLastShipUp = date_add(_dateLastShipUp, INTERVAL (_timeForOneShip * _shipUp) SECOND );
        END IF;
        update sp_user set ship = _ship, dateLastShipUp = _dateLastShipUp where id = userId;
      END IF;
      set _totalTime = _timeForOneShip - (_totalTime - (_timeForOneShip * _shipUp));
      select _ship as ship, case when _totalTime < 0 then 0 else _totalTime end as nextShip, _timeForOneShip as timeForOneShip from dual;
    ELSE
      select _maxShip as ship, 0 as nextShip, _timeForOneShip as timeForOneShip from dual;
    END IF;
  END;

DROP FUNCTION IF EXISTS isFirstGameOfDay;
CREATE FUNCTION isFirstGameOfDay(_userId int(11), _date datetime)
  RETURNS int
  BEGIN
    DECLARE _first int (3) default 0;
    SELECT count(userId) INTO _first FROM sp_singerplayrecord WHERE userId = _userId and date(dateStart) = date(_date);
    IF _first = 1 then return 1;
    else return 0;
    end if;
  END;

DROP PROCEDURE IF EXISTS getUserInfo;
DELIMITER $$
CREATE
PROCEDURE `spottedpuzzle`.`getUserInfo`(IN _fbId VARCHAR(20), IN _currentDate DATETIME)
  BEGIN
    DECLARE _userId INT(11) DEFAULT 0;
    DECLARE _userStatus INT(1) DEFAULT 0;
    DECLARE _userName VARCHAR(300);
    DECLARE _userLevel INT(4) DEFAULT 1;
    DECLARE _didReceivedDailyReward INT(1) DEFAULT 0;
    DECLARE _dailyRewardMultiplier INT(1) DEFAULT 1;
    DECLARE _ship INT(3) DEFAULT 0;
    DECLARE _dateLastPlay DATETIME DEFAULT NULL;
    -- DECLARE _timeForNextShip INT(2) DEFAULT 0;
    DECLARE _timeReduceNextShip INT(4) DEFAULT 1800; -- 30*60 second
    DECLARE _askForShipNumber INT(1) DEFAULT 3;
    DECLARE _shareForKey DATETIME DEFAULT NULL;
    DECLARE _shareForKeyBool INT(1) DEFAULT 1; -- 1: shared, 0: not share today
    DECLARE _dockLevel int (3) default 0;
    DECLARE _maxShip int (3) default 3;
    DECLARE _tmp INT(11) DEFAULT 0;
    DECLARE _debug VARCHAR(500) DEFAULT '';
    DECLARE _curDate DATE DEFAULT date(_currentDate);
    DECLARE _exp int(11) default 0;
    DECLARE _token int(11) default 0;
    DECLARE _gold int(11) default 0;

    SELECT id, ship, dateLastPlay, shareForKey, `status`, fullName, `level`, `dockLevel`, exp, buildingToken, coin INTO _userId, _ship, _dateLastPlay, _shareForKey, _userStatus, _userName, _userLevel, _dockLevel, _exp, _token, _gold FROM `sp_user` WHERE fbId = _fbId;

    -- check received daily reward or not (1: received, 0: not received)
    SELECT CASE WHEN COUNT(id) > 0 THEN 1 ELSE 0 END INTO _didReceivedDailyReward FROM sp_dailyrewardreceived WHERE userId = _userId AND dateReceive >= _curDate AND dateReceive < DATE_ADD(_curDate, INTERVAL 1 DAY);
    IF _didReceivedDailyReward = 0 THEN
      SELECT COUNT(id) INTO _dailyRewardMultiplier FROM sp_dailyrewardreceived WHERE userId = _userId AND dateReceive < _curDate AND dateReceive >= DATE_SUB(_curDate, INTERVAL 4 DAY);
      IF _dailyRewardMultiplier = 4 THEN
        SET _dailyRewardMultiplier = 5;
      ELSE
        SELECT COUNT(id) INTO _dailyRewardMultiplier FROM sp_dailyrewardreceived WHERE userId = _userId AND dateReceive < _curDate AND dateReceive >= DATE_SUB(_curDate, INTERVAL 3 DAY);
        IF _dailyRewardMultiplier = 3 THEN
          SET _dailyRewardMultiplier = 4;
        ELSE
          SELECT COUNT(id) INTO _dailyRewardMultiplier FROM sp_dailyrewardreceived WHERE userId = _userId AND dateReceive < _curDate AND dateReceive >= DATE_SUB(_curDate, INTERVAL 2 DAY);
          IF _dailyRewardMultiplier = 2 THEN
            SET _dailyRewardMultiplier = 3;
          ELSE
            SELECT COUNT(id) INTO _dailyRewardMultiplier FROM sp_dailyrewardreceived WHERE userId = _userId AND dateReceive < _curDate AND dateReceive >= DATE_SUB(_curDate, INTERVAL 1 DAY);
            IF _dailyRewardMultiplier = 1 THEN
              SET _dailyRewardMultiplier = 2;
            ELSE
              SET _dailyRewardMultiplier = 1;
            END IF;
          END IF;
        END IF;
      END IF;
    END IF;

    call getCurrentShipAndTimeNextShip(_userId, _currentDate, _ship, _timeReduceNextShip);

    -- calculator ask for ship number
    SELECT COUNT(id) INTO _askForShipNumber FROM `sp_trackaskforship` WHERE userId = _userId AND dateCreate >= _curDate AND dateCreate < DATE_ADD(_curDate, INTERVAL 1 DAY);

    -- calculator share For Key
    IF _shareForKey IS NULL OR _shareForKey < _curDate THEN
      SET _shareForKeyBool = 0;
    END IF;

    SELECT _userId, _didReceivedDailyReward , _dailyRewardMultiplier, _debug, _ship, _timeReduceNextShip as _timeForNextShip, _askForShipNumber, _shareForKeyBool FROM DUAL;
  END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS createNewUser;
CREATE PROCEDURE createNewUser(IN _fbId VARCHAR(20), IN _fullName VARCHAR(300), IN _gender int(1), IN _currentDate DATETIME)
  BEGIN
    INSERT INTO sp_user (status, level, fbId, fullname, picture, gender, age, phone, dateCreate, exp, ship, dateLastPlay, dateLastShipUp, buildingToken, coin, token, shareForKey, factoryLevel, dockLevel, academyLevel) VALUES
      (0, 1, _fbId, _fullName, NULL, _gender, -1, NULL, _currentDate, 0, 3, _currentDate, _currentDate, 0, 0, NULL, NULL, 1, 1, 1);
    INSERT INTO sp_cashoutinfo (userId, bankAccount, bankName, gender) VALUES (LAST_INSERT_ID(), '','',_gender);
  END;

DROP PROCEDURE IF EXISTS getUpgradeInfo;
CREATE PROCEDURE getUpgradeInfo(IN _userId int(11))
  BEGIN
    declare 30minutes int(4) default 1800;
    declare maxShip int (2) default 3;

    DECLARE factory int(3) DEFAULT 1;
    DECLARE maxFactory int(3) DEFAULT (SELECT MAX(LEVEL) FROM sp_factoryconfig);
    DECLARE _reduceTime int(11) DEFAULT 0;
    DECLARE _buildingTokenFactory int(11) DEFAULT -1;

    DECLARE dock int(3) DEFAULT 1;
    DECLARE maxDock int(3) DEFAULT (SELECT MAX(LEVEL) FROM sp_dockconfig);
    DECLARE _bonusShip int(2) DEFAULT 0;
    DECLARE _buildingTokenDock int(11) DEFAULT -1;

    DECLARE academy int(3) DEFAULT 1;
    DECLARE maxAcademy int(3) DEFAULT (SELECT MAX(LEVEL) FROM sp_academyconfig);
    DECLARE _bonusRate int(3) DEFAULT 0;
    DECLARE _buildingTokenAcademy int(11) DEFAULT -1;

    SELECT factoryLevel, dockLevel, academyLevel INTO factory, dock, academy FROM sp_user WHERE id = _userId;

    SELECT reduceTime into _reduceTime from sp_factoryconfig WHERE level = factory;
    select buildingToken into _buildingTokenFactory from sp_factoryconfig where level = 1 + factory;

    SELECT bonusShip into _bonusShip from sp_dockconfig WHERE level = dock;
    select buildingToken into _buildingTokenDock from sp_dockconfig where level = 1 + dock;

    select bonusRate into _bonusRate from sp_academyconfig WHERE level = academy;
    select buildingToken into _buildingTokenAcademy from sp_academyconfig WHERE level = 1 + academy;

    select case when (_bonusShip + maxShip) is null then maxShip else (_bonusShip + maxShip) end into maxShip from sp_dockconfig where level = dock;
    -- if buildingToken equal -1, user can't upgrade more
    select
      factory,
      case
        when _buildingTokenFactory = -1 or maxFactory <= factory then 0
        else 1 end as canUpgradeFactory,
      _buildingTokenFactory,
      dock,
      case when _buildingTokenDock = -1 or maxDock <= dock then 0 else 1 end as canUpgradeDock, _buildingTokenDock,
      academy,
      case when _buildingTokenAcademy = -1 or maxAcademy <= academy then 0 else 1 end as canUpgradeAcademy, _buildingTokenAcademy,
      30minutes - _reduceTime as timeToGain1Ship,
      maxShip
    FROM dual;
  END;

CREATE TABLE `sp_codeactive` (
  `id`          INT(11) NOT NULL AUTO_INCREMENT,
  `userId`      INT(11),
  `code`        VARCHAR(6),
  `phone`       VARCHAR(15) DEFAULT NULL,
  `dateCreated` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `dateConfirmed` DATETIME NULL ,
  `status`      INT(1) COMMENT '0: not use, 1: used, 2:expired',
  PRIMARY KEY (`id`)
)ENGINE = INNODB CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `sp_ringcaptchasending` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `request` varchar(1000) DEFAULT NULL,
  `response` varchar(1000) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL COMMENT 'if action=send, this phone is not null',
  `token` varchar(50) DEFAULT NULL COMMENT 'the token from action send',
  `country` varchar(4) DEFAULT NULL,
  `status` varchar(7) DEFAULT NULL COMMENT 'ERROR/SUCCESS',
  `retry_in` int(11) DEFAULT NULL COMMENT 'Seconds you need to wait until a new request can be made',
  `expires_in` int(11) DEFAULT NULL COMMENT 'Seconds this token is still active',
  `dateCreate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `sp_ringcaptchaverifying` */

CREATE TABLE `sp_ringcaptchaverifying` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(15) DEFAULT NULL COMMENT 'Phone number in international format as described in E.123. Either this parameter or token must be sent.',
  `code` varchar(4) DEFAULT NULL COMMENT 'The 4 digit PIN code to verify with the one sent in the code endpoint',
  `token` varchar(50) DEFAULT NULL COMMENT 'The token received by the code endpoint when requesting a PIN code to be sent. Either this parameter or phone must be sent.',
  `request` varchar(1000) DEFAULT NULL,
  `response` varchar(1000) DEFAULT NULL,
  `status` varchar(7) DEFAULT NULL COMMENT 'ERROR/SUCCESS',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP PROCEDURE IF EXISTS activeByPhone;
CREATE PROCEDURE activeByPhone(IN _userId int(11), IN _code VARCHAR(6), IN _date DATETIME)
  BEGIN
    DECLARE _error int(1) DEFAULT 0;
    DECLARE _phone varchar(15) DEFAULT null;
    select phone into _phone FROM sp_codeactive where status = 0 and userId = _userId and code = _code;
    if _phone is not null THEN
      select count(phone) into _error from sp_user WHERE phone = _phone and status = 0 and id = _userId;
      if _error = 1 then
        UPDATE sp_user set status = 1 WHERE id = _userId;
        UPDATE sp_codeactive set status = 1, dateConfirmed = _date WHERE userId = _userId;
        select 1 as code from dual;
      else
        select 0 as code from dual; -- phone incorrect or this account is active
      end if;
    ELSE
      select 0 as code from dual; -- phone incorrect or this account is active
    end if;
  END;

CREATE TABLE `sp_singerplayrecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT '0' COMMENT 'the level when play this game',
  `imageId` int(11) DEFAULT NULL COMMENT 'The image for this game',
  `imageUrl` varchar(255) DEFAULT NULL,
  `row` int(1) DEFAULT '4' COMMENT 'size in heigh (3x4, 4x5, 5x6)',
  `col` int(1) DEFAULT '3' COMMENT 'size in width (3x4, 4x5, 5x6)',
  `compassRate` int(1) DEFAULT '1' COMMENT '1x, 2x, 3x, 4x, 5x',
  `timeToPlay` int(4) DEFAULT NULL COMMENT 'total time to play in seconds',
  `firstKey` int(4) DEFAULT NULL COMMENT 'time to drop first key',
  `secondKey` int(4) DEFAULT NULL COMMENT 'time to drop second key',
  `thirdKey` int(4) DEFAULT NULL COMMENT 'time to drop third key',
  `dateStart` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'start game',
  `dateEnd` datetime DEFAULT NULL COMMENT 'end game',
  `history` text COMMENT 'lịch sử các bước đi',
  `win` int(1) DEFAULT '0' COMMENT '1: win, 0: loss',
  `numberOfKey` int(1) DEFAULT '0' COMMENT '1, 2, 3',
  `firstRewardType` varchar(5) DEFAULT 'troll' COMMENT 'exp, gold, token, troll',
  `firstRewardValue` int(2) DEFAULT 1 COMMENT 'if type = (exp, gold, token) => int; if type=troll: 1: shoes, 2: hat, ...',
  `secondRewardType` varchar(5) DEFAULT 'troll' COMMENT 'exp, gold, token, troll',
  `secondRewardValue` int(2) DEFAULT 1 COMMENT 'if type = (exp, gold, token) => int; if type=troll: 1: shoes, 2: hat, ...',
  `thirdRewardType` varchar(5) DEFAULT 'troll' COMMENT 'exp, gold, token, troll',
  `thirdRewardValue` int(2) DEFAULT 1 COMMENT 'if type = (exp, gold, token) => int; if type=troll: 1: shoes, 2: hat, ...',
  `shareRewardType` varchar(5) DEFAULT 'troll' COMMENT 'exp, gold, token, troll',
  `shareRewardValue` int(2) DEFAULT 1 COMMENT 'if type = (exp, gold, token) => int; if type=troll: 1: shoes, 2: hat, ...',
  `quizRewardType` varchar(5) DEFAULT 'troll' COMMENT 'exp, gold, token, troll',
  `quizRewardValue` int(2) DEFAULT 1 COMMENT 'if type = (exp, gold, token) => int; if type=troll: 1: shoes, 2: hat, ...',
  `timeActualPlay` int (4) DEFAULT NULL COMMENT 'time that player play this game',
  `gold` int(11) DEFAULT '0' COMMENT 'gold get by play this game',
  `exp` int(11) DEFAULT '0' COMMENT 'The exp get by play this game',
  `buildingToken` int(11) DEFAULT '0' COMMENT 'buildingToken get by play this game',
  `kind` INT(1) DEFAULT 0 NULL COMMENT '0: single, 1: multi',
  `playWith` INT(11) NULL COMMENT 'singerplayrecord.id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP PROCEDURE IF EXISTS generateSinglePlay;
CREATE PROCEDURE generateSinglePlay(IN _userId INT(11), IN _kind int(1), IN _date DATETIME)
    proc_label: BEGIN
    DECLARE _history text;
    DECLARE _advId int(11);
    DECLARE _playWith int(11) default 0;
    DECLARE _rand INT(3) DEFAULT ROUND(RAND() * (100)); -- random from 0 to 100
    DECLARE x1, x2, x3, x4, x5, _rate, rank INT(3);
    declare _maxLevel int(11);
    -- check this user can play more
    if getCurrentShip(_userId, _date) <= 0 then
      select -1 as error from dual;
      leave proc_label;
    end if;

    -- calculator compass rate
    SELECT SUM(IF(multiple = 1, rate, 0)) AS x1,
           SUM(IF(multiple = 2, rate, 0)) AS x2,
           SUM(IF(multiple = 3, rate, 0)) AS x3,
           SUM(IF(multiple = 4, rate, 0)) AS x4,
           SUM(IF(multiple = 5, rate, 0)) AS x5,
           1 AS id
    INTO x1, x2, x3, x4, x5, _rate
    FROM sp_compassrate GROUP BY 6;

    IF _rand <= x1 THEN SET _rate = 1;
    ELSEIF _rand <= (x1+x2) THEN SET _rate = 2;
    ELSEIF _rand <= (x1+x2+x3) THEN SET _rate = 3;
    ELSEIF _rand <= (x1+x2+x3+x4) THEN SET _rate = 4;
    ELSE SET _rate = 5;
    END IF;

    if _kind = 1 then -- multi play
      SELECT `level` INTO x1 FROM sp_user WHERE id = _userId;
      -- select random adv that ready played
      set rank = 1;
      select max(`level`) into _maxLevel from sp_levelconfig;
      while _playWith = 0 DO -- try to find best competitor
        SELECT id, history, imageId, row, col into _playWith, _history, _advId, x2, x3 from sp_singerplayrecord where userId <> _userId and win = 1 and (level BETWEEN x1 - rank and x1 + rank) ORDER BY rand() limit 1;
        set rank = rank + 1;
        if(rank > _maxLevel) then -- can't find any competitor
          select 5 as error FROM dual;
          leave proc_label;
        end if;
      end WHILE ;
      -- update dateLastPlay and sub 1 ship for table sp_user
      update sp_user set ship = ship - 1, dateLastPlay = _date where id = _userId;

      INSERT INTO sp_singerplayrecord (userId, level, imageId, imageUrl, row, col, compassRate, timeToPlay, firstKey, secondKey, thirdKey, dateStart, kind, playWith)
        (SELECT _userId as userId, x1 as level, id as imageId, imageUrl, x2 as row, x3 as col, _rate as compassRate, timeToPlay, firstKey, secondKey, thirdKey, _date as dateStart, _kind as kind, _playWith as playWith
         FROM sp_advertisement WHERE id=_advId);
    ELSE
      -- choose size of the game (3x4 or 4x5 or 5x6) base on user.level
      SELECT `level` INTO x1 FROM sp_user WHERE id = _userId;
      IF x1 < 3 THEN
        -- SET x1 = 1; -- tutorial
        set x2 = 3;
        set x3 = 4;
      ELSEIF x1 < 20 THEN -- normal
      -- SET x1 = 2;
        set x2 = 4;
        set x3 = 5;
      ELSE -- random normal or difficulty
        SET x5 = 1 + ROUND(RAND() * (1));
        if x5 = 2 then
          set x2 = 4;
          set x3 = 5;
        ELSE
          set x2 = 5;
          set x3 = 6;
        end if;
      END IF;
      -- update dateLastPlay and sub 1 ship for table sp_user
      update sp_user set ship = ship - 1, dateLastPlay = _date where id = _userId;
      INSERT INTO sp_singerplayrecord (userId, level, imageId, imageUrl, row, col, compassRate, timeToPlay, firstKey, secondKey, thirdKey, dateStart, kind)
        (SELECT _userId as userId, x1 as level, id as imageId, imageUrl, x3 as row, x2 as col, _rate as compassRate, timeToPlay, firstKey, secondKey, thirdKey, _date as dateStart, _kind as kind
         FROM sp_advertisement WHERE STATUS = 1 ORDER BY RAND() LIMIT 1);
    END IF ;
    SELECT id, imageId, imageUrl,  col, row, compassRate, timeToPlay, firstKey, secondKey, thirdKey, _history as history FROM sp_singerplayrecord WHERE id = LAST_INSERT_ID();
  END;

DROP PROCEDURE IF EXISTS preFinishSinglePlay;
CREATE PROCEDURE preFinishSinglePlay(IN _userId int(11), IN _gameId int(11), IN _date DATETIME)
  BEGIN
    UPDATE sp_singerplayrecord set dateEnd = _date WHERE id=_gameId and userId=_userId and dateEnd is null;
    if row_count() > 0 THEN
      select * from sp_singerplayrecord where id = _gameId and userId = _userId limit 1;
    ELSE
      select 1 as error from dual;
    END IF;
  END;

DROP PROCEDURE IF EXISTS finishSinglePlay;
CREATE PROCEDURE finishSinglePlay(IN _userId int(11), IN _gameId int(11), IN _tiles int(2), IN _timeActualPlay int(4), IN _currentDate DATETIME, IN _history text)
    proc_label:BEGIN
    DECLARE error int(2) DEFAULT 0;
    DECLARE isFirstGameOfDay int(1) DEFAULT isFirstGameOfDay(_userId, _currentDate);
    DECLARE _rand INT(3) DEFAULT 0; -- random from 0 to 100
    DECLARE _win int (1) DEFAULT 0; -- loss
    DECLARE _i int (1) default 0;
    DECLARE _noOfKeys int(1) DEFAULT 0;
    DECLARE _expRate int(11) DEFAULT 0;
    DECLARE _expMin int(11) DEFAULT 0;
    DECLARE _expMax int(11) DEFAULT 0;
    DECLARE _cashRate int(11) DEFAULT 0;
    DECLARE _cashMin int(11) DEFAULT 0;
    DECLARE _cashMax int(11) DEFAULT 0;
    DECLARE _tokenRate int(11) DEFAULT 0;
    DECLARE _tokenMin int(11) DEFAULT 0;
    DECLARE _tokenMax int(11) DEFAULT 0;
    DECLARE _trashRate int(11) DEFAULT 0;

    -- check if matchId is correct
    select id INTO error from sp_singerplayrecord WHERE userId = _userId and id = _gameId and dateEnd is not null;
    if error <> 0 then SELECT 1 as error from dual; leave proc_label; end if; -- matchId incorrect

    -- check win or not
    select case when row*col = _tiles then 1
           when row*col < _tiles then 2
           else 0 end ,
      case when _timeActualPlay > thirdKey then 0
      when _timeActualPlay > secondKey then 1
      when _timeActualPlay > thirdKey then 2 else 3 end
    into _win, _noOfKeys from sp_singerplayrecord where id = _gameId;

    -- random rewards
    while _i < _noOfKeys do
      set _rand = ROUND(RAND() * (100));
      if _i = 0 THEN

      elseif _i = 1 THEN

      ELSEIF _i = 2 THEN

      end if;
      set _i = _i + 1;
    end while;

    if _win = 2 then select 2 as error from dual; LEAVE  proc_label; end if; -- incorrect data tiles

    -- count no of key

    if _noOfKeys = 3 THEN -- random 3 times

    ELSEIF _noOfKeys = 2 THEN -- random 2 times

    ELSEIF _noOfKeys = 1 THEN -- random 1 times

    END IF;


    -- update table sp_singerplayrecord
    UPDATE sp_singerplayrecord SET dateEnd = _currentDate, history = _history, win = _win, numberOfKey = _noOfKeys
    WHERE id = _gameId and userId = _userId;
  END;

drop PROCEDURE IF EXISTS getRandomQuizzes;
CREATE PROCEDURE getRandomQuizzes(IN _userId int(11), IN _matchId int(11))
  BEGIN
    DECLARE qId int(11) DEFAULT 0;
    DECLARE qTitle VARCHAR(1000) DEFAULT NULL;

    if ROUND(RAND()) = 0 then select 0 as status from dual; end if;

    select id, title
    INTO qId, qTitle
    from sp_question
    where status = 1 -- active
          AND advertiseId = (select imageId from sp_singerplayrecord WHERE id = _matchId and userId = _userId and dateEnd is not null limit 0, 1)
          and id not in (select questionId from sp_answerresult WHERE userId = _userId)
    order by rand()
    limit 1;
    if qId = 0 then -- there are no quizzes for this user
      select 0 as status from dual;
    else
      select qId as id, qTitle as title from dual
      union all
      select id, title from sp_questionanwser where questionId = qId;
    end if;
  END;

DROP PROCEDURE IF EXISTS updateUser;
CREATE PROCEDURE updateUser(IN _userId int(11), IN _exp int(11), IN _buildingToken int(11), IN _gold int(11), IN _reason varchar(1000), IN _date DATETIME)
  BEGIN
    DECLARE userExp int(11) DEFAULT 0;
    DECLARE _token int (11) DEFAULT 0;
    DECLARE _currentLevel int(11) DEFAULT 0;
    DECLARE _nextLevel int(11) DEFAULT 0;
    DECLARE _coin int(11) default 0;
    DECLARE _currentBuildingToken int(11) default 0;

    IF _exp = 0 then
      update sp_user set coin = coin + _gold, buildingToken = buildingToken + _buildingToken where id = _userId;
      insert into sp_userindexhistory (userId, dateCreated, level, exp, token, coin, reason)  (select id, _date, level, exp, buildingToken, coin, _reason from sp_user where id = _userId limit 1);
    ELSE
      select coin, exp, level, buildingToken into _coin, userExp, _currentLevel, _currentBuildingToken from sp_user WHERE id=_userId;
      set _coin = _coin + _gold;
      set userExp = userExp + _exp;

      SELECT level, buildingToken into _nextLevel, _token from sp_levelconfig where fromExp <= userExp and userExp <= toExp;
      if _nextLevel > _currentLevel then
        -- if up level, we get bonus buildingToken
        select sum(buildingToken) into _token from sp_levelconfig where level > _currentLevel and level <= _nextLevel;
        set _currentLevel = _nextLevel;
      ELSE
        set _token = 0;
      END IF ;

      set _buildingToken = _currentBuildingToken + _buildingToken + _token;
      update sp_user set coin = _coin, buildingToken = _buildingToken, exp = userExp, level = _currentLevel where id = _userId;
      insert into sp_userindexhistory (userId, dateCreated, level, exp, token, coin, reason) VALUES (_userId, _date, _currentLevel, userExp, _buildingToken, _coin, _reason);
    end IF ;
    select * from sp_user where id = _userId limit 1;
  END;

DROP PROCEDURE IF EXISTS updateUserProc;
CREATE PROCEDURE updateUserProc(IN _userId int(11), IN _exp int(11), IN _buildingToken int(11), IN _gold int(11), IN _reason varchar(1000), IN _date DATETIME)
  -- use for internal procedure
  BEGIN
    DECLARE userExp int(11) DEFAULT 0;
    DECLARE _token int (11) DEFAULT 0;
    DECLARE _currentLevel int(11) DEFAULT 0;
    DECLARE _nextLevel int(11) DEFAULT 0;
    DECLARE _coin int(11) default 0;
    DECLARE _currentBuildingToken int(11) default 0;

    IF _exp = 0 then
      update sp_user set coin = coin + _gold, buildingToken = buildingToken + _buildingToken where id = _userId;
      insert into sp_userindexhistory (userId, dateCreated, level, exp, token, coin, reason)  (select id, _date, level, exp, buildingToken, coin, _reason from sp_user where id = _userId limit 1);
    ELSE
      select coin, exp, level, buildingToken into _coin, userExp, _currentLevel, _currentBuildingToken from sp_user WHERE id=_userId;
      set _coin = _coin + _gold;
      set userExp = userExp + _exp;

      SELECT level, buildingToken into _nextLevel, _token from sp_levelconfig where fromExp <= userExp and userExp <= toExp;
      if _nextLevel > _currentLevel then
        -- if up level, we get bonus buildingToken
        select sum(buildingToken) into _token from sp_levelconfig where level > _currentLevel and level <= _nextLevel;
        set _currentLevel = _nextLevel;
      ELSE
        set _token = 0;
      END IF ;

      set _buildingToken = _currentBuildingToken + _buildingToken + _token;
      update sp_user set coin = _coin, buildingToken = _buildingToken, exp = userExp, level = _currentLevel where id = _userId;
      insert into sp_userindexhistory (userId, dateCreated, level, exp, token, coin, reason) VALUES (_userId, _date, _currentLevel, userExp, _buildingToken, _coin, _reason);
    end IF ;
  END;

drop PROCEDURE IF EXISTS submitQuiz;
CREATE PROCEDURE submitQuiz(IN _userId int(11), IN _gameId int(11), IN _quizId int(11), IN _answerId int(11), IN _currentDate DATETIME)
    proc_label: BEGIN
    DECLARE _error int(1) DEFAULT  0;
    DECLARE _correct int(1) DEFAULT 0;
    DECLARE _correctAnswer VARCHAR(1000);
    DECLARE _rightAnswerId int(11);
    -- check if question/answer is exist
    select count(*), correct, title into _error, _correct, _correctAnswer from sp_questionanwser WHERE  id = _answerId and questionId = _quizId;
    if _error = 0 then select 1 as error from dual; leave proc_label; end if;

    -- check if user answer this question before
    select count(*) into _error from sp_answerresult where userId = _userId and questionId = _quizId;
    if _error <> 0 then select 2 as error from dual; leave proc_label; end if;

    -- check if user play a game that have this question
    select count(*) into _error from sp_question WHERE id = _quizId and advertiseId in (select imageId from sp_singerplayrecord where userId = _userId and id = _gameId);
    if _error = 0 then select 3 as error from dual; leave proc_label; end if;
    -- find correct answer from quizId
    select id into _rightAnswerId from sp_questionanwser WHERE  correct = 1 and questionId = _quizId;
    if _correct = 1 THEN -- user answer correct
      select 1 as correct, _correctAnswer as title, _rightAnswerId as rightAnswer from dual;
    ELSE
      select 0 as correct, title, _rightAnswerId as rightAnswer from sp_questionanwser where questionId = _quizId and correct = 1;
    end if;
    insert into sp_answerresult (userId, questionId, answerId, correct, dateAnwser) VALUES (_userId, _quizId, _answerId, _correct, _currentDate);
  END;

DROP PROCEDURE IF EXISTS prepareDailyReward;
CREATE PROCEDURE prepareDailyReward(IN _userId int(11), IN _curDateTime DATETIME)
  BEGIN
    DECLARE _curDate DATE DEFAULT DATE (_curDateTime);
    DECLARE _didReceivedDailyReward INT(1) DEFAULT 0;
    DECLARE _dailyRewardMultiplier INT(1) DEFAULT 1;
    -- check received daily reward or not (1: received, 0: not received)
    SELECT CASE WHEN COUNT(id) > 0 THEN 1 ELSE 0 END INTO _didReceivedDailyReward FROM sp_dailyrewardreceived WHERE userId = _userId AND dateReceive >= _curDate AND dateReceive < DATE_ADD(_curDate, INTERVAL 1 DAY);
    IF _didReceivedDailyReward = 0 THEN

      SELECT COUNT(id) INTO _dailyRewardMultiplier FROM sp_dailyrewardreceived WHERE userId = _userId AND dateReceive < _curDate AND dateReceive >= DATE_SUB(_curDate, INTERVAL 4 DAY);
      IF _dailyRewardMultiplier = 4 THEN
        SET _dailyRewardMultiplier = 5;
      ELSE
        SELECT COUNT(id) INTO _dailyRewardMultiplier FROM sp_dailyrewardreceived WHERE userId = _userId AND dateReceive < _curDate AND dateReceive >= DATE_SUB(_curDate, INTERVAL 3 DAY);
        IF _dailyRewardMultiplier = 3 THEN
          SET _dailyRewardMultiplier = 4;
        ELSE
          SELECT COUNT(id) INTO _dailyRewardMultiplier FROM sp_dailyrewardreceived WHERE userId = _userId AND dateReceive < _curDate AND dateReceive >= DATE_SUB(_curDate, INTERVAL 2 DAY);
          IF _dailyRewardMultiplier = 2 THEN
            SET _dailyRewardMultiplier = 3;
          ELSE
            SELECT COUNT(id) INTO _dailyRewardMultiplier FROM sp_dailyrewardreceived WHERE userId = _userId AND dateReceive < _curDate AND dateReceive >= DATE_SUB(_curDate, INTERVAL 1 DAY);
            IF _dailyRewardMultiplier = 1 THEN
              SET _dailyRewardMultiplier = 2;
            ELSE
              SET _dailyRewardMultiplier = 1;
            END IF;
          END IF;
        END IF;
      END IF;
      INSERT INTO sp_dailyrewardreceived (userId, dateReceive, multiplier ) VALUES (_userId, _curDateTime,_dailyRewardMultiplier);
    END IF;
    SELECT _didReceivedDailyReward, _dailyRewardMultiplier, LAST_INSERT_ID() as _dailyRewardId from dual;
  END;

DROP PROCEDURE IF EXISTS getDailyRewardRate;
CREATE PROCEDURE getDailyRewardRate(IN _rand int(3))
    proc_label: BEGIN

    DECLARE _exp int(3);
    DECLARE _token int(3);
    DECLARE _gold int(3);
    DECLARE _reward int(11);

    SELECT SUM(IF(`name` = 'exp', rate, 0)) AS `exp`,
           SUM(IF(`name` = 'token', rate, 0)) AS `token`,
           SUM(IF(`name` = 'gold', rate, 0)) AS `gold`,
           1 AS id
    INTO _exp, _token, _gold, _reward
    FROM `sp_dailyrewardconfig` GROUP BY 4;

    IF _rand <= _exp THEN
      select * from sp_dailyrewardconfig where name='exp';
    ELSEIF _rand <= (_exp + _token) THEN
      select * from sp_dailyrewardconfig where name='token';
    ELSE
      select * from sp_dailyrewardconfig where name='gold';
    END IF;

  END;

drop PROCEDURE IF EXISTS bookmarkAdv;
CREATE PROCEDURE bookmarkAdv(IN _userId int(11), IN _advid int(11), IN _currentDate DATETIME)
  BEGIN
    DECLARE error int(1) DEFAULT 0;
    SELECT count(id) INTO error from sp_loveadvertisement WHERE userId = _userId and adverId = _advid;
    if error = 0 THEN
      -- check if user have play this game
      select count(id) into error from sp_singerplayrecord where dateEnd is not null and userId = _userId and imageId = _advid;
      if error <> 0 then
        INSERT INTO sp_loveadvertisement (userId, adverId, name, imageUrl, thumbUrl, dealType, dealValue, isUsed, dateCreate)
          select _userId as userId, id, name, imageUrl, thumbUrl, dealType, dealValue, NULL as isUsed, _currentDate as dateCreate from sp_advertisement where id = _advid;
        select -1 as error from dual; -- book mark success
      ELSE
        select 2 as error from dual; -- this user has not played this advertisement before
      END IF ;
    ELSE
      select 1 as error from dual; -- this advertisement was bookmarked
    end if;
  END;

DROP PROCEDURE IF EXISTS upgradeAbilities;
CREATE PROCEDURE upgradeAbilities(IN _userId INT(11), IN _type INT(1))
  proc_label: BEGIN
    DECLARE _ok INT(1);
    DECLARE _enoughToken INT(1);
    DECLARE _maxUpgrade INT(1);
    DECLARE _currentFactoryToken INT(3) DEFAULT 0;
    DECLARE _currentDockToken INT(3) DEFAULT 0;
    DECLARE _currentAcademyToken INT(3) DEFAULT 0;
    DECLARE _nextFactoryToken INT(3) DEFAULT -1;
    DECLARE _nextDockToken INT(3) DEFAULT -1;
    DECLARE _nextAcademyToken INT(3) DEFAULT -1;
    DECLARE _timeToGain1Ship INT(3);
    DECLARE _maxShip INT(2);

    IF _type = 0 THEN -- upgrade factory
      SELECT count(*) FROM sp_user WHERE id = _userId AND factoryLevel >= (SELECT max(level) FROM sp_factoryconfig) INTO _maxUpgrade;
      IF _maxUpgrade = 0 THEN -- factory not max upgrade
        SELECT count(*) FROM sp_user spu LEFT JOIN sp_factoryconfig spfc ON spu.factoryLevel + 1 = spfc.level WHERE spu.id = _userId AND spu.buildingToken >= spfc.buildingToken INTO _enoughToken;
        IF _enoughToken = 0 THEN -- not enough token to upgrade
          SELECT 5 AS _error from dual;
          LEAVE proc_label;
        ELSE -- enough token to upgrade
          SELECT count(*) FROM sp_user WHERE id = _userId AND inProgress = 0 INTO _ok;
          IF _ok = 1 THEN -- not in any progress, go to update
            UPDATE sp_user SET inProgress = 1 WHERE id = _userId; -- on progress
            UPDATE sp_user SET factoryLevel = factoryLevel + 1 WHERE id = _userId;
            UPDATE sp_user SET inProgress = 0 WHERE id = _userId; -- done
          ELSE -- this row on progress, return
            SELECT 1 AS _error from dual;
            LEAVE proc_label;
          END IF ;
        END IF ;
      ELSE -- factory max upgrade
        SELECT 2 AS _error from dual;
        LEAVE proc_label;
      END IF ;
    ELSEIF _type = 1 THEN -- upgrade dock
      SELECT count(*) FROM sp_user WHERE id = _userId AND dockLevel >= (SELECT max(level) FROM sp_dockconfig) INTO _maxUpgrade;
      IF _maxUpgrade = 0 THEN -- dock not max upgrade
        SELECT count(*) FROM sp_user spu LEFT JOIN sp_dockconfig spd ON spu.dockLevel + 1 = spd.level WHERE spu.id = _userId AND spu.buildingToken >= spd.buildingToken INTO _enoughToken;
        IF _enoughToken = 0 THEN -- not enough token to upgrade
          SELECT 5 AS _error from dual;
          LEAVE proc_label;
        ELSE -- enough token to upgrade
          SELECT count(*) FROM sp_user WHERE id = _userId AND inProgress = 0 INTO _ok;
          IF _ok = 1 THEN -- not in any progress, go to update
            UPDATE sp_user SET inProgress = 1 WHERE id = _userId; -- on progress
            UPDATE sp_user SET dockLevel = dockLevel + 1 WHERE id = _userId;
            UPDATE sp_user SET inProgress = 0 WHERE id = _userId; -- done
          ELSE -- this row on progress, return
            SELECT 1 AS _error from dual;
            LEAVE proc_label;
          END IF ;
        END IF ;
      ELSE -- dock max upgrade
        SELECT 3 AS _error from dual;
        LEAVE proc_label;
      END IF ;
    ELSEIF _type = 2 THEN -- upgrade academy
      SELECT count(*) FROM sp_user WHERE id = _userId AND academyLevel >= (SELECT max(level) FROM sp_academyconfig) INTO _maxUpgrade;
      IF _maxUpgrade = 0 THEN -- academy not max upgrade
        SELECT count(*) FROM sp_user spu LEFT JOIN sp_academyconfig spa ON spu.academyLevel + 1 = spa.level WHERE spu.id = _userId AND spu.buildingToken >= spa.buildingToken INTO _enoughToken;
        IF _enoughToken = 0 THEN -- not enough token to upgrade
          SELECT 5 AS _error from dual;
          LEAVE proc_label;
        ELSE -- enough token to upgrade
          SELECT count(*) FROM sp_user WHERE id = _userId AND inProgress = 0 INTO _ok;
          IF _ok = 1 THEN -- not in any progress, go to update
            -- UPDATE sp_user SET inProgress = 1 WHERE id = _userId; -- on progress
            UPDATE sp_user SET academyLevel = academyLevel + 1 WHERE id = _userId;
            -- UPDATE sp_user SET inProgress = 0 WHERE id = _userId; -- done
          ELSE -- this row on progress, return
            SELECT 1 AS _error from dual;
            LEAVE proc_label;
          END IF ;
        END IF ;
      ELSE -- academy max upgrade
        SELECT 4 AS _error from dual;
        LEAVE proc_label;
      END IF ;
    END IF ;
    SELECT buildingToken FROM sp_factoryconfig WHERE level = (SELECT factoryLevel FROM sp_user WHERE id = _userId) + 1 INTO _nextFactoryToken;
    SELECT buildingToken FROM sp_dockconfig WHERE level = (SELECT dockLevel FROM sp_user WHERE id = _userId) + 1 INTO _nextDockToken;
    SELECT buildingToken FROM sp_academyconfig WHERE level = (SELECT academyLevel FROM sp_user WHERE id = _userId) + 1 INTO _nextAcademyToken;
    SELECT buildingToken FROM sp_factoryconfig WHERE level = (SELECT factoryLevel FROM sp_user WHERE id = _userId) INTO _currentFactoryToken;
    SELECT buildingToken FROM sp_dockconfig WHERE level = (SELECT dockLevel FROM sp_user WHERE id = _userId) INTO _currentDockToken;
    SELECT buildingToken FROM sp_academyconfig WHERE level = (SELECT academyLevel FROM sp_user WHERE id = _userId) INTO _currentAcademyToken;
    SELECT 0 AS _error, _currentFactoryToken, _currentDockToken, _currentAcademyToken, _nextFactoryToken, _nextDockToken, _nextAcademyToken from dual;
  END ;

DROP PROCEDURE IF EXISTS askFacebookForShip;
CREATE PROCEDURE askFacebookForShip (IN _userId INT(11), IN _currentDate DATETIME)
  BEGIN
    DECLARE noOfTrack int(1) DEFAULT 0;
    select count(*) INTO noOfTrack from sp_trackaskforship where userId = _userId and dateCreate = _currentDate;
    if noOfTrack >= 3 THEN -- max ask for ship today
      select 1 as error from dual;
    ELSE
      insert INTO sp_trackaskforship (userId, dateCreate) VALUES (_userId, _currentDate);
      select 0 as error from dual;
    end if;
  END ;

DROP PROCEDURE IF EXISTS getRewardFacebook;
CREATE PROCEDURE getRewardFacebook (IN _requestId varchar(50), IN _userId INT(11), IN _currentDate DATETIME)
  BEGIN
    DECLARE _validRequest INT(1);
    DECLARE _currentShip INT(3);
    DECLARE _maxShip INT(3);

    SELECT count(*) FROM sp_askforship WHERE requestId = _requestId INTO _validRequest;
    IF _validRequest = 0 THEN -- request id not exist
      SELECT 1 AS _error FROM dual;
    ELSE -- request id is available
      SELECT getCurrentShip(_userId, _currentDate) INTO _currentShip;
      SELECT getMaxShip(_userId) INTO _maxShip;
      IF _currentShip >= _maxShip THEN
        SELECT 2 AS _error FROM dual;
      ELSE
        UPDATE sp_user SET ship = _currentShip + 1 WHERE id = _userId;
        UPDATE sp_askforship SET dateAccept = _currentDate WHERE requestId = _requestId;
        SELECT 0 AS _error FROM dual;
      END IF ;
    END IF ;
  END ;

CREATE TABLE `sp_payout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `xferId` VARCHAR(40) NULL,
  `fullname` varchar(300),
  `dateRequest` datetime DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `bankName` VARCHAR(150) NULL,
  `bankAccount` VARCHAR(20) NULL,
  `phone` VARCHAR(15) NULL,
  `datePayout` datetime DEFAULT NULL,
  `dateSuccess` datetime DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL COMMENT 'WAIT:wait admin, CANCEL: cancel by admin, PAYOUT: send out and wait infor from the bank; SUCCESS: payout success; ERROR',
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP PROCEDURE createPayout;
CREATE PROCEDURE createPayout(IN _userId int(11), IN amount int(11), IN _currentDate DATETIME)
  BEGIN
    insert into sp_payout (userId, fullname, dateRequest, amount, bankName, bankAccount, phone, status)
      (SELECT _userId as userId, fullname, _currentDate as dateRequest, amount, bankName, bankAccount, phone, 'WAIT' as status FROM sp_user u LEFT JOIN  sp_cashoutinfo co ON u.id = co.userId AND co.status = 1
      WHERE u.id = _userId AND u.status = 1 AND coin > amount AND co.status IS NOT NULL);
    if last_insert_id() <> 0 THEN
      call updateUserProc(_userId, 0, 0, 0 - amount, 'REQUEST_PAYOUT', _currentDate);
      select last_insert_id() as id FROM dual;
    ELSE
      select 0 as id from dual;
    END IF ;
  END;
CREATE TABLE `sp_payouthistory` (
  `id`          INT(11) NOT NULL AUTO_INCREMENT,
  `payoutId`    INT(11),
  `dateCreated` DATETIME,
  `status`      VARCHAR(50) COMMENT 'unclaimed 	Payout has not been accepted by recipient.completed 	Payout has been completed.cancelled 	Payout has been cancelled.',
  `message`     TEXT,
  `action`      VARCHAR(20),
  `request`     VARCHAR(300),
  `response`    TEXT,
  `xferId` VARCHAR(40) NULL,
  PRIMARY KEY (`id`)
) ENGINE = INNODB CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `spottedpuzzle`.`sp_userindexhistory` (
  `id`          INT(11) NOT NULL AUTO_INCREMENT,
  `userId`      INT(11) NOT NULL,
  `dateCreated` DATETIME    NOT NULL,
  `level`       INT(11),
  `exp`         INT(11),
  `token`       INT(11),
  `coin`        INT(11),
  `reason`      VARCHAR(30),
  PRIMARY KEY (`id`)
)ENGINE = INNODB CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*database for event*/
CREATE TABLE `sp_event_advertisement` (
  `id`         INT(11) NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(30) COMMENT 'name of advertisement',
  `imageUrl`   VARCHAR(255),
  `thumbUrl`   VARCHAR(255),
  `status`     INT(1) COMMENT '0: inactive, 1: active',
  `timeToPlay` INT(4) COMMENT 'seconds',
  `dateStart`  DATETIME COMMENT 'date start event',
  `dateEnd`    DATETIME COMMENT 'date end event',
  `dateCreate` DATETIME,
  `publish`   int(1) DEFAULT 0 COMMENT '0: not publish, 1: published',
  PRIMARY KEY (`id`)
)ENGINE = INNODB CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `sp_event_singleplay` (
  `id`         INT(11) NOT NULL AUTO_INCREMENT,
  `userId`     INT(11),
  `advId`      INT(11),
  `imageUrl`   VARCHAR(255),
  `row`        INT(1)           DEFAULT 5,
  `col`        INT(1)           DEFAULT 6,
  `timeToPlay` INT(4) COMMENT 'seconds',
  `timePlayed` INT(11) NULL COMMENT 'seconds',
  `history`    TEXT,
  `dateStart`  DATETIME,
  `dateEnd`    DATETIME,
  PRIMARY KEY (`id`)
)ENGINE = INNODB CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `sp_event_share` (
  `id`         INT(11) NOT NULL AUTO_INCREMENT,
  `userId`     INT(11),
  `dateShared` DATETIME,
  PRIMARY KEY (`id`)
) ENGINE = INNODB CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `sp_event_leadboard` (
  `id`        INT(11) NOT NULL AUTO_INCREMENT,
  `userId`     INT(11),
  `fullname`   VARCHAR(300),
  `timePlayed` INT(11) DEFAULT 999999999 COMMENT 'best time that played',
  `dateUpdate` DATETIME,
  `win`        INT(0) default 0 COMMENT '0: not win, 1: win ingame, 2: win outgame',
  `winValue`   VARCHAR(500),
  `dateConfirmed`    DATETIME DEFAULT NULL COMMENT 'not null: confirm to receive reward',
  `publish` int(1) default 0 comment '1: publish winners',
  `received` int(1) default 0 COMMENT '1: user received the coins',
  PRIMARY KEY (`id`)
)ENGINE = INNODB CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
ALTER TABLE `sp_event_leadboard` ADD UNIQUE INDEX `user` (`userId`);

DROP PROCEDURE generateEventSinglePlay;
CREATE PROCEDURE generateEventSinglePlay(IN _userId INT(11), IN _date DATETIME)
proc_label: BEGIN
  DECLARE _advId int(11);
  DECLARE _advImg varchar(255);
  DECLARE _advTimePlay int(4) DEFAULT 0;
  DECLARE _played int(1) default 0;
  DECLARE _share int(1) DEFAULT 0;
  -- check event is available
  SELECT id, imageUrl, timeToPlay INTO _advId, _advImg, _advTimePlay FROM sp_event_advertisement WHERE status = 1 and dateStart <= _date and _date <= dateEnd;
  if _advId is null THEN
    select 1 as error from dual; -- these are no event
    LEAVE proc_label;
  END IF;

  -- check ship for this event
  select count(id) INTO _played from sp_event_singleplay where userId = _userId;
  if _played >= 4 THEN
    select 2 as error from dual; -- you can't play more for this event
    LEAVE proc_label;
  ELSEIF _played <> 0 THEN
    -- check share
    select count(id) INTO _share FROM sp_event_share where userId = _userId;
    if _share < _played THEN
      select 3 as error, _share, _played from dual; -- you don't have enough ship to play more, please share
      LEAVE proc_label;
    END IF;
  END IF;
  -- player can play
  insert into sp_event_singleplay (userId, advId, imageUrl, row, col, timeToPlay, dateStart) VALUE (_userId, _advId, _advImg, 6, 5, _advTimePlay, _date);
  select last_insert_id() as matchId, _advImg as adImageUrl, _advId as adId, 6 as row, 5 as col, _advTimePlay as limitTimeToPlay from dual;
END;
drop PROCEDURE finishEventSinglePlay;
CREATE PROCEDURE finishEventSinglePlay(IN _userId int(11), IN _matchId int(11), IN _timeActualPlay int(4), IN _currentDate DATETIME, IN _history text)
proc_label: BEGIN
  DECLARE _error int(1) default 0;
  DECLARE _rank int(11) default 0;

  -- check event is available
  SELECT id INTO _error FROM sp_event_advertisement WHERE status = 1 and dateStart <= _currentDate and _currentDate <= dateEnd;
  if _error = 0 THEN
    select 1 as error from dual; -- these are no event
    LEAVE proc_label;
  END IF;
  -- check _matchId is correct
  SELECT id INTO _error FROM sp_event_singleplay WHERE id = _matchId and userId = _userId and dateEnd is NULL;
  if _error = 0 THEN
      select 2 as error from dual; -- incorrect input data
      LEAVE proc_label;
  END IF;
  UPDATE sp_event_singleplay SET dateEnd = _currentDate, timePlayed = _timeActualPlay, history = _history WHERE id = _matchId and userId = _userId and dateEnd is NULL;

  -- update to leadboard
  INSERT INTO sp_event_leadboard (userId, fullname, dateUpdate, timePlayed) select id as userId, fullname, _currentDate as dateUpdate, _timeActualPlay as timePlayed from sp_user where id=_userId
    ON DUPLICATE KEY UPDATE
      timePlayed = if(timePlayed >= values(timePlayed), values(timePlayed), timePlayed),
      dateUpdate = IF(timePlayed >= VALUES(timePlayed), VALUES(dateUpdate), dateUpdate);
  -- get leader board
  SELECT rank into _rank  FROM
    (SELECT @rn:=@rn+1 AS rank, userId
     FROM (
            SELECT userId, fullname, timePlayed
            FROM sp_event_leadboard
            ORDER BY timePlayed, dateUpdate
          ) t1, (SELECT @rn:=0) t2) t3
  WHERE userId= _userId;

  (select 0 as rank, userId, fullname as fullName, timePlayed from sp_event_leadboard ORDER BY timePlayed, dateUpdate limit 0, 3)
  union all
  (select rank, userId, fullname as fullName, timePlayed from (SELECT @rn:=@rn+1 AS rank, userId, fullname, timePlayed
                                                   FROM (
                                                          SELECT userId, fullname, timePlayed
                                                          FROM sp_event_leadboard
                                                          ORDER BY timePlayed, dateUpdate
                                                        ) t1, (SELECT @rn:=0) t2) t3
  where rank between _rank-4 and _rank + 4)
    union ALL
  (SELECT
     -1 AS rank,
     (select case when win=0 then 0 when win=1 and received = 0 then 1 else 2 end as hasPrize from sp_event_leadboard where userId=_userId and publish=1) AS userId ,
      '' as fullName,
     (SELECT COUNT(*) FROM sp_event_share WHERE userId=_userId) AS timePlayed FROM DUAL);
END;

drop PROCEDURE getLeaderBoard;
CREATE PROCEDURE getLeaderBoard(IN _userId int(11))
proc_label: BEGIN
    declare _rank int(11) default 0;

    SELECT rank into _rank  FROM
      (SELECT @rn:=@rn+1 AS rank, userId
       FROM (
              SELECT userId, fullname, timePlayed
              FROM sp_event_leadboard
              ORDER BY timePlayed, dateUpdate
            ) t1, (SELECT @rn:=0) t2) t3
    WHERE userId= _userId;

    (select 0 as rank, userId, fullname as fullName, timePlayed from sp_event_leadboard ORDER BY timePlayed, dateUpdate limit 0, 3)
    union all
    (select rank, userId, fullname as fullName, timePlayed from (SELECT @rn:=@rn+1 AS rank, userId, fullname, timePlayed
                                                    FROM (
                                                           SELECT userId, fullname, timePlayed
                                                           FROM sp_event_leadboard
                                                           ORDER BY timePlayed, dateUpdate
                                                         ) t1, (SELECT @rn:=0) t2) t3
                                                    where rank between _rank-4 and _rank + 4)
    union ALL
    (SELECT
       -1 AS rank,
       (select case when win=0 then 0 when (win=1 or win=2) and received = 0 then 1 else 2 end as hasPrize from sp_event_leadboard where userId=_userId and publish=1) AS userId ,
       '' as fullName,
       (SELECT COUNT(*) FROM sp_event_share WHERE userId=_userId) AS timePlayed FROM DUAL);

END;

CREATE PROCEDURE getEventStatus(IN _currentDate DATETIME)
  proc_label: BEGIN
    DECLARE _error int(1) default 0;
    DECLARE _dateEnd DATETIME;
    -- check event is available
    SELECT id INTO _error FROM sp_event_advertisement WHERE status = 1;
    IF _error = 0 THEN
      select 0 as error from dual; -- không có
    ELSE
      set _error = 0;
      SELECT id, dateEnd INTO _error, _dateEnd  FROM sp_event_advertisement WHERE status = 1 and dateStart <= _currentDate and _currentDate <= dateEnd;
      IF _error <> 0 THEN
        select 1 as error, DATE_FORMAT(_dateEnd,'%d%b,%h:%i%p') as dateEnd from dual; -- đang diễn ra
      ELSE
        SELECT id INTO _error FROM sp_event_advertisement WHERE status = 1 and _currentDate > dateEnd and publish = 1;
        IF _error <> 0 THEN
          select 3 as error from dual; -- đang trao giải
        ELSE
          select 2 as error from dual; -- đang chờ xét giải
        END IF;
      END IF;
    END IF;
  END;

CREATE PROCEDURE insertShare(IN _userId int(11), IN _currentDate DATETIME)
  BEGIN
    DECLARE _error int(1) DEFAULT (select count(*) from sp_event_share WHERE userId = _userId);
    if _error >= 3 THEN
      select 1 as error from dual;
    ELSE
      SELECT id INTO _error FROM sp_event_advertisement WHERE status = 1 and dateStart <= _currentDate and _currentDate <= dateEnd;
      if _error = 0 THEN
        select 1 as error from dual;
      else
        insert INTO sp_event_share (userId, dateShared) VALUE (_userId, _currentDate);
        select _error + 1 as sharedFB FROM dual;
      END IF;
    END IF;
  END;

CREATE PROCEDURE confirmReceiveReward(IN _userId int(11), IN _currentDate DATETIME)
  BEGIN
    UPDATE sp_event_leadboard SET dateConfirmed = _currentDate, received = 1 WHERE userId = _userId and dateConfirmed is NULL;
  END;
DROP PROCEDURE getEventPrize;
CREATE PROCEDURE getEventPrize(IN _userId int(11))
  BEGIN
    SELECT winValue, win, dateConfirmed FROM sp_event_leadboard WHERE userId = _userId AND win > 0 and publish = 1 AND received = 0;
  END;