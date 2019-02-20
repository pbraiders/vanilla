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
 * TITLE      : sp_RentUpdate                                            *
 * AUTHOR     : O.JULLIEN                                                *
 * CREATION   : 04/02/2010                                               *
 * DESCRIPTION: Update a rent                                            *
* COPYRIGHT  : Olivier JULLIEN, All rights reserved                     *
 *************************************************************************
 * Parameters:                                                           *
 * IN    sLogin: login identifier                                        *
 *     sSession: session identifier                                      *
 *        sInet: concatenation of  IP and USER_AGENT                     *
 *  iIdentifier: rent identifier                                         *
 *        iReal: real count                                              *
 *     iPlanned: planned count                                           *
 *    iCanceled: canceled count                                          *
 *         iMax: max count for the day                                   *
 *         iAge: group age                                               *
 *      iArrhes: rent arrhes                                             *
 *     sComment: comment                                                 *
 *                                                                       *
 * Returns:                                                              *
 *    ErrorCode: >0 is OK. Number of row updated.                        *
 *               -1 when a private error occures                         *
 *               -2 when an authentication error occures                 *
 *               -3 when an access denied error occures                  *
 *               -4 when a duplicate error occures                       *
 *************************************************************************
 * Date          * Author             * Changes                          *
 *************************************************************************
 * 04/02/2010    * O.JULLIEN          * Creation                         *
 *************************************************************************/
DROP PROCEDURE IF EXISTS `_PBR_DB_DBN_`.`sp_RentUpdate`$$
CREATE PROCEDURE  `_PBR_DB_DBN_`.`sp_RentUpdate`(IN sLogin VARCHAR(45), IN sSession VARCHAR(200), IN sInet VARCHAR(255), IN iRent MEDIUMINT UNSIGNED, IN iReal SMALLINT UNSIGNED,IN iPlanned SMALLINT UNSIGNED,IN iCanceled SMALLINT UNSIGNED,IN iMax SMALLINT UNSIGNED,IN iAge TINYINT UNSIGNED,IN iArrhes TINYINT UNSIGNED, IN sComment TEXT)
    SQL SECURITY INVOKER
BEGIN
  -- ------ --
  -- Define --
  -- ------ --
  DECLARE iErrorCode TINYINT(1) DEFAULT -1;
  DECLARE sErrorUsername VARCHAR(25) DEFAULT 'UNKNOWN';
  DECLARE sErrorTitle TEXT DEFAULT ' IN sp_RentUpdate';
  DECLARE sErrorDescription TEXT DEFAULT '';
  DECLARE iUser SMALLINT UNSIGNED DEFAULT 0;
  DECLARE bInTransaction TINYINT(1) DEFAULT 0;
  -- --------------------- --
  -- Define Error Handlers --
  -- --------------------- --
  DECLARE EXIT HANDLER FOR 1061, 1062
  BEGIN
    IF 1=bInTransaction THEN
      ROLLBACK;
    END IF;
    INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'ERROR', CONCAT('SQLEXCEPTION',sErrorTitle), CONCAT('An ER_DUP_ occures while processing "',sErrorDescription,'" step. Exit'),USER(),CURRENT_USER() );
    SELECT -4 AS 'ErrorCode';
  END;
  DECLARE EXIT HANDLER FOR 1141, 1142, 1143, 1370
  BEGIN
    IF 1=bInTransaction THEN
      ROLLBACK;
    END IF;
    INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'ERROR', CONCAT('SQLEXCEPTION',sErrorTitle), CONCAT('An ACCESS_DENIED occures while processing "',sErrorDescription,'" step. Exit'),USER(),CURRENT_USER() );
    SELECT -3 AS 'ErrorCode';
  END;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    IF 1=bInTransaction THEN
      ROLLBACK;
    END IF;
    INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'ERROR', CONCAT('SQLEXCEPTION',sErrorTitle), CONCAT('An SQLEXCEPTION occures while processing "',sErrorDescription,'" step. Exit'),USER(),CURRENT_USER() );
    SET iErrorCode = -1;
    SELECT iErrorCode AS 'ErrorCode';
  END;
  -- ---------- --
  -- Initialize --
  -- ---------- --
  SET sErrorUsername = IFNULL(sLogin,'NULL');
  SET sErrorTitle = CONCAT(sErrorTitle,'(',IFNULL(sLogin,'NULL'),',',IFNULL(sSession,'NULL'),',',IFNULL(sInet,'NULL'),',',IFNULL(iRent,'NULL'),',',IFNULL(iReal,'NULL'),',',IFNULL(iPlanned,'NULL'),',',IFNULL(iCanceled,'NULL'),',',IFNULL(iMax,'NULL'),')');
  SET sErrorDescription = 'Check parameters';
  SET sLogin = TRIM(sLogin);
  SET sSession = TRIM(sSession);
  SET sInet=TRIM(sInet);
  SET sComment=TRIM(sComment);
  -- ------- --
  -- Request --
  -- ------- --
  IF (sLogin IS NOT NULL) AND (LENGTH(sLogin)>0) AND (sSession IS NOT NULL) AND (LENGTH(sSession)>0) AND (sInet IS NOT NULL) AND (LENGTH(sInet)>0) AND (iRent IS NOT NULL) AND (iRent>0) AND (iReal IS NOT NULL) AND (iPlanned IS NOT NULL) AND (iCanceled IS NOT NULL) AND (iMax IS NOT NULL) THEN
  BEGIN
    -- Authentication --
    SET sErrorDescription = 'Check authentication';
    CALL sp_SessionValid(sLogin, sSession, 1, sInet, iUser);
    IF (iUser>0) THEN
    BEGIN
      -- Start transaction --
      SET sErrorDescription = 'Start transaction';
      START TRANSACTION;
      SET bInTransaction = 1;
      -- Update max --
      SET sErrorDescription = 'Update max';
      UPDATE `_PBR_DB_DBN_`.`reservation` AS r INNER JOIN `_PBR_DB_DBN_`.`reservation` AS s USING(`year`,`month`,`day`) SET r.`rent_max`=iMax WHERE s.`idreservation`=iRent;
      -- Update rent --
      SET sErrorDescription = 'Update rent';
      UPDATE `_PBR_DB_DBN_`.`reservation` SET `rent_real`=iReal, `rent_planned`=iPlanned, `rent_canceled`=iCanceled, `age`=iAge, `arrhe`=iArrhes, `comment`=sComment, `update_date`=SYSDATE(), `update_iduser`=iUser
      WHERE `idreservation`=iRent;
      SELECT ROW_COUNT() INTO iErrorCode;
      -- Stop transaction --
      IF iErrorCode>0 THEN
      BEGIN
        -- No error --
        SET sErrorDescription = 'COMMIT transaction';
        COMMIT;
      END;
      ELSE
      BEGIN
        -- Error --
        SET sErrorDescription = 'ROLLBACK transaction';
        ROLLBACK;
        INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'ERROR', CONCAT('TRANSACTION',sErrorTitle), CONCAT('An error occures while processing "','updating rent','" step. Exit'),USER(),CURRENT_USER() );
      END;
      END IF;
      SET bInTransaction = 0;
    END;
    ELSE
    BEGIN
      -- User, session and/or inet is/are not valid --
      SET iErrorCode=-2;
      INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'WARNING', CONCAT('SESSION CHECK',sErrorTitle), 'Not allowed.',USER(),CURRENT_USER() );
    END;
    END IF;
  END;
  ELSE
  BEGIN
    -- Parameters are not valid --
    INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'ERROR', CONCAT('PARAMETER CHECK',sErrorTitle), 'Bad parameters.',USER(),CURRENT_USER() );
  END;
  END IF;
  -- ------ --
  -- Return --
  -- ------ --
  SELECT iErrorCode AS 'ErrorCode';
END $$

DELIMITER ;
