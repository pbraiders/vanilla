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
 * TITLE      : sp_RentsGet                                              *
 * AUTHOR     : O.JULLIEN                                                *
 * CREATION   : 04/02/2010                                               *
 * DESCRIPTION: Get rent(s) for a specific date                          *
 *              the first line (reservation_id=0) is the sum of real     *
 *              planned and canceled rents + max of rents allowed        *
 *              ( reservation_id=0                                       *
 *                reservation_real=<sum real>                            *
 *                reservation_planned=<sum planned>                      *
 *                reservation_planned=<sum canceled>                     *
 *                reservation_arrhes=<max of rent allowed> )             *
 * COPYRIGHT  : Olivier JULLIEN, All rights reserved                     *
 *************************************************************************
 * Parameters:                                                           *
 * IN    sLogin: login identifier                                        *
 *     sSession: session identifier                                      *
 *        sInet: concatenation of  IP and USER_AGENT                     *
 *         iDay: requested day                                           *
 *       iMonth: requested month                                         *
 *        iYear: requested year                                          *
 *      iOffset: offset of the first row to return                       *
 *       iLimit: maximum number of rows to return                        *
 *                                                                       *
 * NOTE: In MYSQL 5.x the LIMIT clause do not accept variables.          *
 *       So we use a less performant request to simulate it.             *
 *       This procedure should be updated for MYSQL > 6.0.               *
 *                                                                       *
 * Returns:                                                              *
 *         none, one or more records (reservation_id,reservation_real    *
 *         reservation_planned,reservation_canceled,reservation_arrhes,  *
 *         contact_lastname,contact_firstname,contact_phone,             *
 *         reservation_comment)                                          *
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
DROP PROCEDURE IF EXISTS `_PBR_DB_DBN_`.`sp_RentsGet`$$
CREATE PROCEDURE  `_PBR_DB_DBN_`.`sp_RentsGet`(IN sLogin VARCHAR(45), IN sSession VARCHAR(200), IN sInet VARCHAR(255), IN iDay TINYINT UNSIGNED, IN iMonth TINYINT UNSIGNED, IN iYear SMALLINT UNSIGNED, IN iOffset MEDIUMINT UNSIGNED, IN iLimit MEDIUMINT UNSIGNED )
    SQL SECURITY INVOKER
BEGIN
  -- ------ --
  -- Define --
  -- ------ --
  DECLARE sErrorUsername VARCHAR(25) DEFAULT 'UNKNOWN';
  DECLARE sErrorTitle TEXT DEFAULT ' IN sp_RentsGet';
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
  SET sErrorTitle = CONCAT(sErrorTitle,'(',IFNULL(sLogin,'NULL'),',',IFNULL(sSession,'NULL'),',',IFNULL(sInet,'NULL'),',',IFNULL(iDay,'NULL'),',',IFNULL(iMonth,'NULL'),',',IFNULL(iYear,'NULL'),',',IFNULL(iOffset,'NULL'),',',IFNULL(iLimit,'NULL'),')');
  SET sErrorDescription = 'Check parameters';
  SET sLogin = TRIM(sLogin);
  SET sSession = TRIM(sSession);
  SET sInet=TRIM(sInet);
  -- ------- --
  -- Request --
  -- ------- --
  IF (sLogin IS NOT NULL) AND (LENGTH(sLogin)>0) AND (sSession IS NOT NULL) AND (LENGTH(sSession)>0) AND (sInet IS NOT NULL) AND (LENGTH(sInet)>0) AND (iDay IS NOT NULL) AND (iDay>0) AND (iDay<32) AND (iMonth IS NOT NULL) AND (iMonth>0) AND (iMonth<13) AND (iYear IS NOT NULL) AND (iOffset IS NOT NULL) AND (LENGTH(iOffset)>=0)  AND (iLimit IS NOT NULL)  AND (LENGTH(iLimit)>0) THEN
  BEGIN
    -- Authentication --
    SET sErrorDescription = 'Check authentication';
    CALL sp_SessionValid(sLogin, sSession, 1, sInet, iUser);
    IF (iUser>0) THEN
    BEGIN
      -- Select --
      SET @iRowNum:=0;
      SET sErrorDescription = 'Select';
      SELECT 0 AS 'reservation_id'
        , IFNULL(SUM(r.`rent_real`),0) AS 'reservation_real'
        , IFNULL(SUM(r.`rent_planned`),0) AS 'reservation_planned'
        , IFNULL(SUM(r.`rent_canceled`),0) AS 'reservation_canceled'
        , IFNULL(MAX(r.`rent_max`),0) AS 'reservation_arrhes'
        , NULL AS 'contact_lastname'
        , NULL AS 'contact_firstname'
        , NULL AS 'contact_phone'
        , NULL AS 'reservation_comment'
      FROM `_PBR_DB_DBN_`.`reservation` AS r
      INNER JOIN `_PBR_DB_DBN_`.`contact` AS c ON r.`idcontact`=c.`idcontact`
      WHERE r.`year`=iYear
      AND r.`month`=iMonth
      AND r.`day`=iDay
      UNION
      SELECT t.`reservation_id`
           , t.`reservation_real`
           , t.`reservation_planned`
           , t.`reservation_canceled`
           , t.`reservation_arrhes`
           , t.`contact_lastname`
           , t.`contact_firstname`
           , t.`contact_phone`
           , t.`reservation_comment`
      FROM ( SELECT (@iRowNum:=@iRowNum+1) AS RowNumber
                  , r.`idreservation` AS 'reservation_id'
                  , r.`rent_real` AS 'reservation_real'
                  , r.`rent_planned` AS 'reservation_planned'
                  , r.`rent_canceled` AS 'reservation_canceled'
                  , r.`arrhe` AS 'reservation_arrhes'
                  , c.`lastname` AS 'contact_lastname'
                  , c.`firstname` AS 'contact_firstname'
                  , c.`tel` AS 'contact_phone'
                  , r.`comment` AS 'reservation_comment'
             FROM `_PBR_DB_DBN_`.`reservation` AS r
             INNER JOIN `_PBR_DB_DBN_`.`contact` AS c ON r.`idcontact`=c.`idcontact`
             WHERE r.`year`=iYear
             AND r.`month`=iMonth
             AND r.`day`=iDay) AS t
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
