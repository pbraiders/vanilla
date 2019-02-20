/*************************************************************************
 *                                                                       *
 * Copyright (C) 2010   Olivier JULLIEN - PBRAIDERS.COM                  *
 * Tous droits reserves - All rights reserved                            *
 *                                                                       *
 *************************************************************************
 *                                                                       *
 * Except if expressly provided in a dedicated License Agreement, you    *
 * are not authorized to:                                                *
 *                                                                       *
 * 1. Use, copy, modify or transfer this software component, module or   *
 * product, including any accompanying electronic or paper documentation *
 * (together, the "Software"),.                                          *
 *                                                                       *
 * 2. Remove any product identification, copyright, proprietary notices  *
 * or labels from the Software.                                          *
 *                                                                       *
 * 3. Modify, reverse engineer, decompile, disassemble or otherwise      *
 * attempt to reconstruct or discover the source code, or any parts of   *
 * it, from the binaries of the Software.                                *
 *                                                                       *
 * 4. Create derivative works based on the Software (e.g. incorporating  *
 * the Software in another software or commercial product or service     *
 * without a proper license).                                            *
 *                                                                       *
 * By installing or using the "Software", you confirm your acceptance    *
 * of the hereabove terms and conditions.                                *
 *                                                                       *
 *************************************************************************/

USE `_PBR_DB_DBN_`;
DELIMITER $$

/*************************************************************************
 *              PBRAIDERS.COM                                            *
 * TITLE      : sp_SessionSet                                            *
 * AUTHOR     : O.JULLIEN                                                *
 * CREATION   : 04/02/2010                                               *
 * DESCRIPTION: Create a session if the login and password are           *
 *              valid.                                                   *
 *      ASSUME: The columns of the tables are case insentive.            *
 *              Except user.password                                     *
 * COPYRIGHT  : Olivier JULLIEN, All rights reserved                     *
 *************************************************************************
 * Parameters:                                                           *
 * IN   sLogin: login identifier                                         *
 *    sSession: session identifier                                       *
 *   sPassword: password                                                 *
 *       sInet: concatenation of  IP and USER_AGENT                      *
 *                                                                       *
 * Returns:                                                              *
 *    ErrorCode: >0 is OK. Number of row inserted.                       *
 *               -1 when a private error occures                         *
 *               -2 when an authentication error occures                 *
 *               -3 when an access denied error occures                  *
 *               -4 when a duplicate error occures                       *
 *************************************************************************
 * Date          * Author             * Changes                          *
 *************************************************************************
 * 04/02/2010    * O.JULLIEN          * Creation                         *
 *************************************************************************/
DROP PROCEDURE IF EXISTS `_PBR_DB_DBN_`.`sp_SessionSet`$$
CREATE PROCEDURE  `_PBR_DB_DBN_`.`sp_SessionSet`(IN sLogin VARCHAR(45), IN sSession VARCHAR(200), IN sPassword VARCHAR(40), IN sInet VARCHAR(255))
    SQL SECURITY INVOKER
BEGIN
  -- ------ --
  -- Define --
  -- ------ --
  DECLARE iErrorCode TINYINT(1) DEFAULT -1;
  DECLARE sErrorUsername VARCHAR(25) DEFAULT 'UNKNOWN';
  DECLARE sErrorTitle TEXT DEFAULT ' IN sp_SessionSet';
  DECLARE sErrorDescription TEXT DEFAULT '';
  DECLARE iSessionTimeExpire INTEGER UNSIGNED DEFAULT 3600;
  DECLARE iUnixTimestamp INTEGER UNSIGNED DEFAULT 0;
  DECLARE iUserId INTEGER UNSIGNED DEFAULT 0;
  -- --------------------- --
  -- Define Error Handlers --
  -- --------------------- --
  DECLARE EXIT HANDLER FOR 1061, 1062
  BEGIN
    INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'ERROR', CONCAT('SQLEXCEPTION',sErrorTitle), CONCAT('An ER_DUP_ occures while processing "',sErrorDescription,'" step. Exit'),USER(),CURRENT_USER() );
    SET iErrorCode = -4;
    SELECT iErrorCode AS 'ErrorCode';
  END;
  DECLARE EXIT HANDLER FOR 1141, 1142, 1143, 1370
  BEGIN
    INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'ERROR', CONCAT('SQLEXCEPTION',sErrorTitle), CONCAT('An ACCESS_DENIED occures while processing "',sErrorDescription,'" step. Exit'),USER(),CURRENT_USER() );
    SET iErrorCode = -3;
    SELECT iErrorCode AS 'ErrorCode';
  END;
  -- ---------- --
  -- Initialize --
  -- ---------- --
  SET sErrorUsername = IFNULL(sLogin,'NULL');
  SET sErrorTitle = CONCAT(sErrorTitle,'(',IFNULL(sLogin,'NULL'),',',IFNULL(sSession,'NULL'),',',IFNULL(sPassword,'NULL'),',',IFNULL(sInet,'NULL'),')');
  SET sErrorDescription = 'Check user';
  SET sLogin = TRIM(sLogin);
  SET sSession = TRIM(sSession);
  SET sPassword = TRIM(sPassword);
  SET sInet=TRIM(sInet);
  -- ------- --
  -- Request --
  -- ------- --
  IF (sLogin IS NOT NULL) AND (LENGTH(sLogin)>0) AND (sSession IS NOT NULL) AND (LENGTH(sSession)>0) AND (sPassword IS NOT NULL) AND (LENGTH(sPassword)>0) AND (sInet IS NOT NULL) AND (LENGTH(sInet)>0) THEN
  BEGIN
    -- Check user --
    SELECT u.`iduser` INTO iUserId FROM `_PBR_DB_DBN_`.`user`AS u WHERE u.`login` = sLogin AND u.`state`=1 AND u.`password`=sPassword;
    IF (iUserId IS NOT NULL) AND (iUserId>0) THEN
    BEGIN
      -- Get config values --
      SET sErrorDescription = 'Get config values';
      SELECT CONVERT(c.`value`, UNSIGNED INTEGER) INTO iSessionTimeExpire FROM `_PBR_DB_DBN_`.`config` AS c WHERE c.`name` LIKE 'session_time_expire';
      SELECT CONVERT( UNIX_TIMESTAMP(), UNSIGNED INTEGER) INTO iUnixTimestamp;
      -- Insert session --
      SET sErrorDescription = 'Insert session';
      REPLACE INTO `_PBR_DB_DBN_`.`session` (`login`,`session`,`create_date`,`expire_date`,`logoff`,`inet`)
		VALUES ( sLogin, sSession, iUnixTimestamp, iUnixTimestamp+iSessionTimeExpire, 0, CRC32(sInet) );
      SELECT ROW_COUNT() INTO iErrorCode;
      -- Update user --
      SET sErrorDescription = 'Update user';
      UPDATE `_PBR_DB_DBN_`.`user` SET `last_visit`=SYSDATE() WHERE `iduser`=iUserId;
      SELECT ROW_COUNT()+iErrorCode INTO iErrorCode;
    END;
    ELSE
    BEGIN
      -- User, session and/or inet is/are not valid --
      SET iErrorCode = -2;
      INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'WARNING', CONCAT('USER CHECK',sErrorTitle), 'Unknown or not active user.',USER(),CURRENT_USER() );
    END;
    END IF;
  END;
  ELSE
    -- Parameters are not valid --
    INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'ERROR', CONCAT('PARAMETER CHECK',sErrorTitle), 'Bad parameters.',USER(),CURRENT_USER() );
  END IF;
  -- ------ --
  -- Return --
  -- ------ --
  SELECT iErrorCode AS 'ErrorCode';
END $$

DELIMITER ;
