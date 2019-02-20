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
 * TITLE      : sp_ContactRentsGet                                       *
 * AUTHOR     : O.JULLIEN                                                *
 * CREATION   : 04/02/2010                                               *
 * DESCRIPTION: Get rent(s) contact                                      *
 * COPYRIGHT  : Olivier JULLIEN, All rights reserved                     *
 *************************************************************************
 * Parameters:                                                           *
 * IN    sLogin: login identifier                                        *
 *     sSession: session identifier                                      *
 *        sInet: concatenation of  IP and USER_AGENT                     *
 *     iContact: contact identifier                                      *
 *      iOffset: offset of the first row to return                       *
 *       iLimit: maximum number of rows to return                        *
 *                                                                       *
 * NOTE: In MYSQL 5.x the LIMIT clause do not accept variables.          *
 *       So we use a less performant request to simulate it.             *
 *       This procedure should be updated for MYSQL > 6.0.               *
 *                                                                       *
 * Returns:                                                              *
 *         none or one record (reservation_id, reservation_year,         *
 *         reservation_month, reservation_day, reservation_real,         *
 *         reservation_planned, reservation_canceled, reservation_arrhes,*
 *         reservation_age, reservation_comment, reservation_max)        *
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
DROP PROCEDURE IF EXISTS `_PBR_DB_DBN_`.`sp_ContactRentsGet`$$
CREATE PROCEDURE  `_PBR_DB_DBN_`.`sp_ContactRentsGet`(IN sLogin VARCHAR(45), IN sSession VARCHAR(200), IN sInet VARCHAR(255), IN iContact MEDIUMINT UNSIGNED, IN iOffset MEDIUMINT UNSIGNED, IN iLimit MEDIUMINT UNSIGNED )
    SQL SECURITY INVOKER
BEGIN
  -- ------ --
  -- Define --
  -- ------ --
  DECLARE sErrorUsername VARCHAR(25) DEFAULT 'UNKNOWN';
  DECLARE sErrorTitle TEXT DEFAULT ' IN sp_ContactRentsGet';
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
  SET sErrorTitle = CONCAT(sErrorTitle,'(',IFNULL(sLogin,'NULL'),',',IFNULL(sSession,'NULL'),',',IFNULL(sInet,'NULL'),',',IFNULL(iContact,'NULL'),',',IFNULL(iOffset,'NULL'),',',IFNULL(iLimit,'NULL'),')');
  SET sErrorDescription = 'Check parameters';
  SET sLogin = TRIM(sLogin);
  SET sSession = TRIM(sSession);
  SET sInet=TRIM(sInet);
  -- ------- --
  -- Request --
  -- ------- --
  IF (sLogin IS NOT NULL) AND (LENGTH(sLogin)>0) AND (sSession IS NOT NULL) AND (LENGTH(sSession)>0) AND (sInet IS NOT NULL) AND (LENGTH(sInet)>0) AND (iContact IS NOT NULL) AND (iContact>0) AND (iOffset IS NOT NULL) AND (LENGTH(iOffset)>=0)  AND (iLimit IS NOT NULL)  AND (LENGTH(iLimit)>0) THEN
  BEGIN
    -- Authentication --
    SET sErrorDescription = 'Check authentication';
    CALL sp_SessionValid(sLogin, sSession, 1, sInet, iUser);
    IF (iUser>0) THEN
    BEGIN
      -- Select --
      SET @iRowNum:=0;
      SET sErrorDescription = 'Select';
      SELECT t.`reservation_id`
           , t.`reservation_year`
           , t.`reservation_month`
           , t.`reservation_day`
           , t.`reservation_real`
           , t.`reservation_planned`
           , t.`reservation_canceled`
           , t.`reservation_age`
           , t.`reservation_arrhes`
           , t.`reservation_comment`
           , t.`reservation_max`
      FROM ( SELECT (@iRowNum:=@iRowNum+1) AS RowNumber
                  , r.`idreservation` AS 'reservation_id'
                  , r.`year` AS 'reservation_year'
                  , r.`month` AS 'reservation_month'
                  , r.`day` AS 'reservation_day'
                  , r.`rent_real` AS 'reservation_real'
                  , r.`rent_planned` AS 'reservation_planned'
                  , r.`rent_canceled` AS 'reservation_canceled'
                  , r.`age` AS 'reservation_age'
                  , r.`arrhe` AS 'reservation_arrhes'
                  , r.`comment` AS 'reservation_comment'
                  , r.`rent_max` AS 'reservation_max'
             FROM `_PBR_DB_DBN_`.`reservation` AS r
             WHERE r.`idcontact`=iContact
             ORDER BY r.`year` DESC, r.`month` DESC, r.`day` DESC) AS t
      WHERE t.RowNumber > iOffset
      AND t.RowNumber <= (iOffset + iLimit);
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
