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
 * TITLE      : sp_RentGet                                               *
 * AUTHOR     : O.JULLIEN                                                *
 * CREATION   : 04/02/2010                                               *
 * DESCRIPTION: Get a rent                                               *
 * COPYRIGHT  : Olivier JULLIEN, All rights reserved                     *
 *************************************************************************
 * Parameters:                                                           *
 * IN    sLogin: login identifier                                        *
 *     sSession: session identifier                                      *
 *        sInet: concatenation of  IP and USER_AGENT                     *
 *        iRent: rent identifier                                         *
 *                                                                       *
 * Returns:                                                              *
 *         none or one record (reservation_year, reservation_month,      *
 *         reservation_day, reservation_real, reservation_planned,       *
 *         reservation_canceled, reservation_max, reservation_age,       *
 *         reservation_arrhes, reservation_comment, creation_date,       *
 *         creation_username, update_date, update_username,              *
 *         contact_lastname, contact_firstname, contact_tel,             *
 *         contact_email,contact_address, contact_addressmore,           *
 *         contact_addresscity, contact_addresszip, contact_id,          *
 *         reservation_id)                                               *
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
DROP PROCEDURE IF EXISTS `_PBR_DB_DBN_`.`sp_RentGet`$$
CREATE PROCEDURE  `_PBR_DB_DBN_`.`sp_RentGet`(IN sLogin VARCHAR(45), IN sSession VARCHAR(200), IN sInet VARCHAR(255), IN iRent MEDIUMINT UNSIGNED )
    SQL SECURITY INVOKER
BEGIN
  -- ------ --
  -- Define --
  -- ------ --
  DECLARE sErrorUsername VARCHAR(25) DEFAULT 'UNKNOWN';
  DECLARE sErrorTitle TEXT DEFAULT ' IN sp_RentGet';
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
  SET sErrorTitle = CONCAT(sErrorTitle,'(',IFNULL(sLogin,'NULL'),',',IFNULL(sSession,'NULL'),',',IFNULL(sInet,'NULL'),',',IFNULL(iRent,'NULL'),')');
  SET sErrorDescription = 'Check parameters';
  SET sLogin = TRIM(sLogin);
  SET sSession = TRIM(sSession);
  SET sInet=TRIM(sInet);
  -- ------- --
  -- Request --
  -- ------- --
  IF (sLogin IS NOT NULL) AND (LENGTH(sLogin)>0) AND (sSession IS NOT NULL) AND (LENGTH(sSession)>0) AND (sInet IS NOT NULL) AND (LENGTH(sInet)>0) AND (iRent IS NOT NULL) AND (iRent>0) THEN
  BEGIN
    -- Authentication --
    SET sErrorDescription = 'Check authentication';
    CALL sp_SessionValid(sLogin, sSession, 1, sInet, iUser);
    IF (iUser>0) THEN
    BEGIN
      -- Select --
      SET sErrorDescription = 'Request';
      SELECT r.`idreservation` AS 'reservation_id'
		   , r.`year` AS 'reservation_year'
		   , r.`month` AS 'reservation_month'
		   , r.`day` AS 'reservation_day'
		   , r.`rent_real` AS 'reservation_real'
		   , r.`rent_planned` AS 'reservation_planned'
		   , r.`rent_canceled` AS 'reservation_canceled'
		   , r.`rent_max` AS 'reservation_max'
		   , r.`age` AS 'reservation_age'
		   , r.`arrhe` AS 'reservation_arrhes'
		   , r.`comment` AS 'reservation_comment'
		   , r.`create_date` AS 'creation_date'
		   , u.`login` AS 'creation_username'
		   , r.`update_date` AS 'update_date'
		   , v.`login` AS 'update_username'
		   , c.`lastname` AS 'contact_lastname'
		   , c.`firstname` AS 'contact_firstname'
		   , c.`tel` AS 'contact_tel'
		   , c.`email` AS 'contact_email'
		   , c.`address` AS 'contact_address'
		   , c.`address_more` AS 'contact_addressmore'
		   , c.`city` AS 'contact_addresscity'
		   , c.`zip` AS 'contact_addresszip'
		   , c.`idcontact` AS 'contact_id'
      FROM `_PBR_DB_DBN_`.`reservation` AS r
      INNER JOIN `_PBR_DB_DBN_`.`contact` AS c ON r.`idcontact`=c.`idcontact`
      INNER JOIN `_PBR_DB_DBN_`.`user` AS u ON r.`create_iduser`=u.`iduser`
      LEFT JOIN `_PBR_DB_DBN_`.`user` AS v ON r.`update_iduser`=v.`iduser`
      WHERE r.`idreservation`=iRent;
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
