/*************************************************************************
 *                                                                       *
 * Copyright (C) 2009   JOT - PBRAIDERS.COM                              *
 * Tous droits réservés - All rights reserved                            *
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
 * TITLE      : sp_SessionValid                                          *
 * AUTHOR     : O.JULLIEN                                                *
 * CREATION   : 04/02/2010                                               *
 * DESCRIPTION: Check if the session is valid and return user            *
 * COPYRIGHT  : Olivier JULLIEN, All rights reserved                     *
 *************************************************************************
 * Parameters:                                                           *
 * IN   sLogin: login identifier                                         *
 *    sSession: session identifier                                       *
 *       iRole: minimum role level                                       *
 *       sInet: concatenation of IP and USER_AGENT                       *
 *                                                                       *
 * OUT  iUser: >0 if the session is valid                                *
 *************************************************************************
 * Date          * Author             * Changes                          *
 *************************************************************************
 * 04/02/2010    * O.JULLIEN          * Creation                         *
 *************************************************************************/
DROP PROCEDURE IF EXISTS `_PBR_DB_DBN_`.`sp_SessionValid` $$
CREATE PROCEDURE `_PBR_DB_DBN_`.`sp_SessionValid`(IN sLogin VARCHAR(45), IN sSession VARCHAR(200), IN iRole TINYINT(1) UNSIGNED, IN sInet VARCHAR(255), OUT iUser SMALLINT(5) UNSIGNED)
BEGIN
  -- ------ --
  -- Define --
  -- ------ --
  DECLARE sErrorUsername VARCHAR(25) DEFAULT 'UNKNOWN';
  DECLARE sErrorTitle TEXT DEFAULT ' IN sp_SessionValid';
  DECLARE sErrorDescription TEXT DEFAULT '';
  DECLARE iUnixTimestamp INTEGER UNSIGNED DEFAULT 0;
  DECLARE iCRC32 INTEGER UNSIGNED DEFAULT 0;
  -- --------------------- --
  -- Define Error Handlers --
  -- --------------------- --
  DECLARE EXIT HANDLER FOR 1061, 1062
  BEGIN
    INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'ERROR', CONCAT('SQLEXCEPTION',sErrorTitle), CONCAT('An ER_DUP_ occures while processing "',sErrorDescription,'" step. Exit'),USER(),CURRENT_USER() );
   SET iUser=0;
  END;
  DECLARE EXIT HANDLER FOR 1141, 1142, 1143, 1370
  BEGIN
    INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'ERROR', CONCAT('SQLEXCEPTION',sErrorTitle), CONCAT('An ACCESS_DENIED occures while processing "',sErrorDescription,'" step. Exit'),USER(),CURRENT_USER() );
   SET iUser=0;
  END;
  DECLARE CONTINUE HANDLER FOR NOT FOUND
  BEGIN
    SET iUser=0;
  END;
  -- ---------- --
  -- Initialize --
  -- ---------- --
  SET sErrorUsername = IFNULL(sLogin,'NULL');
  SET sErrorTitle = CONCAT(sErrorTitle,'(',IFNULL(sLogin,'NULL'),',',IFNULL(sSession,'NULL'),',',IFNULL(sInet,'NULL'),')');
  SET sErrorDescription = 'Check parameters';
  SELECT CONVERT( UNIX_TIMESTAMP(), UNSIGNED INTEGER) INTO iUnixTimestamp;
  SET sLogin = TRIM(sLogin);
  SET sSession = TRIM(sSession);
  SET sInet=TRIM(sInet);
  SET iUser=0;
  -- ------- --
  -- Request --
  -- ------- --
  IF (sLogin IS NOT NULL) AND (LENGTH(sLogin)>0) AND (sSession IS NOT NULL) AND (LENGTH(sSession)>0) AND (iRole IS NOT NULL) AND (sInet IS NOT NULL) AND (LENGTH(sInet)>0) THEN
  BEGIN
    -- CRC32 --
    SET sErrorDescription = 'CRC32';
    SELECT CRC32(sInet) INTO iCRC32;
    -- Request --
    SET sErrorDescription = 'SELECT';
    SELECT IFNULL(u.`iduser`,0) INTO iUser
    FROM `_PBR_DB_DBN_`.`user` AS u
    INNER JOIN `_PBR_DB_DBN_`.`session` AS s ON u.`login` = s.`login` AND s.`logoff`=0 AND s.`session`=sSession AND s.`inet`=iCRC32
    WHERE u.`login`=sLogin
    AND s.`expire_date` >= iUnixTimestamp
    AND u.`role`>=iRole
    AND u.`state` = 1;
  END;
  END IF;
END $$

DELIMITER ;
