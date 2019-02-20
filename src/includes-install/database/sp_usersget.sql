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
 * TITLE      : sp_UsersGet                                              *
 * AUTHOR     : O.JULLIEN                                                *
 * CREATION   : 04/02/2010                                               *
 * DESCRIPTION: Get User(s)                                              *
 * COPYRIGHT  : Olivier JULLIEN, All rights reserved                     *
 *************************************************************************
 * Parameters:                                                           *
 * IN    sLogin: login identifier                                        *
 *     sSession: session identifier                                      *
 *        sInet: concatenation of  IP and USER_AGENT                     *
 *                                                                       *
 * Returns:                                                              *
 *         none, one or more records (user_id, user_name, user_lastvisit,*
 *         user_state)                                                   *
 *         or                                                            *
 *         ErrorCode:  -1 when a private error occures                   *
 *                     -2 when an authentication error occures           *
 *                     -3 when an access denied error occures            *
 *                     -4 when a duplicate error occures                 *
 *************************************************************************
 * Date          * Author             * Changes                          *
 *************************************************************************
 * 04/02/2010    * O.JULLIEN          * Creation                         *
 *************************************************************************/
DROP PROCEDURE IF EXISTS `_PBR_DB_DBN_`.`sp_UsersGet`$$
CREATE PROCEDURE  `_PBR_DB_DBN_`.`sp_UsersGet`(IN sLogin VARCHAR(45), IN sSession VARCHAR(200), IN sInet VARCHAR(255))
    SQL SECURITY INVOKER
BEGIN
  -- ------ --
  -- Define --
  -- ------ --
  DECLARE sErrorUsername VARCHAR(25) DEFAULT 'UNKNOWN';
  DECLARE sErrorTitle TEXT DEFAULT ' IN sp_UsersGet';
  DECLARE sErrorDescription TEXT DEFAULT '';
  DECLARE iUser SMALLINT UNSIGNED DEFAULT 0;
  -- --------------------- --
  -- Define Error Handlers --
  -- --------------------- --
  DECLARE EXIT HANDLER FOR 1061, 1062
  BEGIN
    INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'ERROR', CONCAT('SQLEXCEPTION',sErrorTitle), CONCAT('An ER_DUP_ occures while processing "',sErrorDescription,'" step. Exit'),USER(),CURRENT_USER() );
    SELECT -4 AS 'ErrorCode';
  END;
  DECLARE EXIT HANDLER FOR 1141, 1142, 1143, 1370
  BEGIN
    INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'ERROR', CONCAT('SQLEXCEPTION',sErrorTitle), CONCAT('An ACCESS_DENIED occures while processing "',sErrorDescription,'" step. Exit'),USER(),CURRENT_USER() );
    SELECT -3 AS 'ErrorCode';
  END;
  -- ---------- --
  -- Initialize --
  -- ---------- --
  SET sErrorUsername = IFNULL(sLogin,'NULL');
  SET sErrorTitle = CONCAT(sErrorTitle,'(',IFNULL(sLogin,'NULL'),',',IFNULL(sSession,'NULL'),',',IFNULL(sInet,'NULL'),')');
  SET sErrorDescription = 'Check parameters';
  SET sLogin = TRIM(sLogin);
  SET sSession = TRIM(sSession);
  SET sInet=TRIM(sInet);
  -- ------- --
  -- Request --
  -- ------- --
  IF (sLogin IS NOT NULL) AND (LENGTH(sLogin)>0) AND (sSession IS NOT NULL) AND (LENGTH(sSession)>0) AND (sInet IS NOT NULL) AND (LENGTH(sInet)>0) THEN
  BEGIN
    -- Authentication --
    SET sErrorDescription = 'Check authentication';
    CALL sp_SessionValid(sLogin, sSession, 10, sInet, iUser);
    IF (iUser>0) THEN
    BEGIN
      -- Select --
      SET sErrorDescription = 'Select';
      SELECT u.`iduser` AS "user_id", u.`login` AS "user_name", u.`last_visit` AS "user_lastvisit", u.`state` AS "user_state" FROM `_PBR_DB_DBN_`.`user` AS u ORDER BY u.`login`;
    END;
    ELSE
    BEGIN
      -- User, session and/or inet is/are not valid --
      INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'WARNING', CONCAT('SESSION CHECK',sErrorTitle), 'Not allowed.',USER(),CURRENT_USER() );
      SELECT -2 AS 'ErrorCode';
    END;
    END IF;
  END;
  ELSE
  BEGIN
    -- Parameters are not valid --
    INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'ERROR', CONCAT('PARAMETER CHECK',sErrorTitle), 'Bad parameters.',USER(),CURRENT_USER() );
      SELECT -1 AS 'ErrorCode';
  END;
  END IF;
END $$

DELIMITER ;
