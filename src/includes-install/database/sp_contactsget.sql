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
 * TITLE      : sp_ContactsGet                                           *
 * AUTHOR     : O.JULLIEN                                                *
 * CREATION   : 04/02/2010                                               *
 * DESCRIPTION: Get contact(s)                                           *
 * COPYRIGHT  : Olivier JULLIEN, All rights reserved                     *
 *************************************************************************
 * Parameters:                                                           *
 * IN    sLogin: login identifier                                        *
 *     sSession: session identifier                                      *
 *        sInet: concatenation of  IP and USER_AGENT                     *
 *    sSearch: contact last name                                         *
 *      iOffset: offset of the first row to return                       *
 *       iLimit: maximum number of rows to return                        *
 *                                                                       *
 * NOTE: In MYSQL 5.x the LIMIT clause do not accept variables.          *
 *       So we use a less performant request to simulate it.             *
 *       This procedure should be updated for MYSQL > 6.0.               *
 *                                                                       *
 * Returns:                                                              *
 *         none, one or more records (contact_id,contact_lastname,       *
 *         contact_firstname,contact_tel,contact_email,contact_address   *
 *         contact_addressmore,contact_addresscity,contact_addresszip    *
 *         contact_comment,creation_date,creation_username,update_date   *
 *         update_username)                                              *
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
DROP PROCEDURE IF EXISTS `_PBR_DB_DBN_`.`sp_ContactsGet`$$
CREATE PROCEDURE  `_PBR_DB_DBN_`.`sp_ContactsGet`(IN sLogin VARCHAR(45), IN sSession VARCHAR(200), IN sInet VARCHAR(255), IN sSearch VARCHAR(40), IN iOffset MEDIUMINT UNSIGNED, IN iLimit MEDIUMINT UNSIGNED)
    SQL SECURITY INVOKER
BEGIN
  -- ------ --
  -- Define --
  -- ------ --
  DECLARE sErrorUsername VARCHAR(25) DEFAULT 'UNKNOWN';
  DECLARE sErrorTitle TEXT DEFAULT ' IN sp_ContactsGet';
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
  SET sErrorTitle = CONCAT(sErrorTitle,'(',IFNULL(sLogin,'NULL'),',',IFNULL(sSession,'NULL'),',',IFNULL(sInet,'NULL'),',',IFNULL(sSearch,'NULL'),',',IFNULL(iOffset,'NULL'),',',IFNULL(iLimit,'NULL'),')');
  SET sErrorDescription = 'Check parameters';
  SET sLogin = TRIM(sLogin);
  SET sSession = TRIM(sSession);
  SET sInet=TRIM(sInet);
  SET sSearch=TRIM(sSearch);
  -- ------- --
  -- Request --
  -- ------- --
  IF (sLogin IS NOT NULL) AND (LENGTH(sLogin)>0) AND (sSession IS NOT NULL) AND (LENGTH(sSession)>0) AND (sInet IS NOT NULL) AND (LENGTH(sInet)>0) AND (iOffset IS NOT NULL) AND (LENGTH(iOffset)>=0)  AND (iLimit IS NOT NULL)  AND (LENGTH(iLimit)>0) THEN
  BEGIN
    -- Authentication --
    SET sErrorDescription = 'Check authentication';
    CALL sp_SessionValid(sLogin, sSession, 1, sInet, iUser);
    IF (iUser>0) THEN
    BEGIN
      SET @iRowNum:=0;
      IF (sSearch IS NOT NULL) AND (LENGTH(sSearch)>0) THEN
      BEGIN
        -- Looking for --
        SET sErrorDescription = 'Looking for';
        SELECT t.`contact_id`,t.`contact_lastname`
        	  ,t.`contact_firstname`,t.`contact_tel`
        	  ,t.`contact_email`,t.`contact_address`
        	  ,t.`contact_addressmore`,t.`contact_addresscity`
        	  ,t.`contact_addresszip`,t.`contact_comment`
        	  ,t.`creation_date`,t.`creation_username`
        	  ,t.`update_date`,t.`update_username`
        FROM ( SELECT (@iRowNum:=@iRowNum+1) AS RowNumber
                     ,c.`idcontact` AS 'contact_id',c.`lastname` AS 'contact_lastname'
                     ,c.`firstname` AS 'contact_firstname',c.`tel` AS 'contact_tel'
                     ,c.`email` AS 'contact_email',c.`address` AS 'contact_address'
                     ,c.`address_more` AS 'contact_addressmore',c.`city` AS 'contact_addresscity'
                     ,c.`zip` AS 'contact_addresszip',c.`comment` AS 'contact_comment'
                     ,c.`create_date` AS 'creation_date',u.`login` AS 'creation_username'
                     ,c.`update_date` AS 'update_date',v.`login` AS 'update_username'
               FROM `_PBR_DB_DBN_`.`contact` AS c
               INNER JOIN `_PBR_DB_DBN_`.`user` AS u ON c.`create_iduser`=u.`iduser`
               LEFT JOIN `_PBR_DB_DBN_`.`user` AS v ON c.`update_iduser`=v.`iduser`
               WHERE c.`lastname` LIKE sSearch
               ORDER BY c.`lastname`) AS t
        WHERE t.RowNumber > iOffset
        AND t.RowNumber <= (iOffset + iLimit);
      END;
      ELSE
      BEGIN
        -- Select --
        SET sErrorDescription = 'Select';
        SELECT t.`contact_id`,t.`contact_lastname`
        	  ,t.`contact_firstname`,t.`contact_tel`
        	  ,t.`contact_email`,t.`contact_address`
        	  ,t.`contact_addressmore`,t.`contact_addresscity`
        	  ,t.`contact_addresszip`,t.`contact_comment`
        	  ,t.`creation_date`,t.`creation_username`
        	  ,t.`update_date`,t.`update_username`
        FROM ( SELECT (@iRowNum:=@iRowNum+1) AS RowNumber
                     ,c.`idcontact` AS 'contact_id',c.`lastname` AS 'contact_lastname'
                     ,c.`firstname` AS 'contact_firstname',c.`tel` AS 'contact_tel'
                     ,c.`email` AS 'contact_email',c.`address` AS 'contact_address'
                     ,c.`address_more` AS 'contact_addressmore',c.`city` AS 'contact_addresscity'
                     ,c.`zip` AS 'contact_addresszip',c.`comment` AS 'contact_comment'
                     ,c.`create_date` AS 'creation_date',u.`login` AS 'creation_username'
                     ,c.`update_date` AS 'update_date',v.`login` AS 'update_username'
               FROM `_PBR_DB_DBN_`.`contact` AS c
               INNER JOIN `_PBR_DB_DBN_`.`user` AS u ON c.`create_iduser`=u.`iduser`
               LEFT JOIN `_PBR_DB_DBN_`.`user` AS v ON c.`update_iduser`=v.`iduser`
               ORDER BY c.`lastname`) AS t
        WHERE t.RowNumber > iOffset
        AND t.RowNumber <= (iOffset + iLimit);
      END;
      END IF;
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
