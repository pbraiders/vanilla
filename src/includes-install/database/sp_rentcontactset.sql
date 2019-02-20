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
 * TITLE      : sp_RentContactSet                                        *
 * AUTHOR     : O.JULLIEN                                                *
 * CREATION   : 04/02/2010                                               *
 * DESCRIPTION: Create a contact and a rent                              *
 * COPYRIGHT  : Olivier JULLIEN, All rights reserved                     *
 *************************************************************************
 * Parameters:                                                           *
 * IN    sLogin: login identifier                                        *
 *     sSession: session identifier                                      *
 *        sInet: concatenation of  IP and USER_AGENT                     *
 *    sLastName: contact last name                                       *
 *   sFirstName: contact first name                                      *
 *         sTel: contact telephone                                       *
 *       sEmail: contact email                                           *
 *     sAddress: contact address                                         *
 * sAddressMore: contact address more                                    *
 *        sCity: contact address city                                    *
 *         sZip: contact address zip code                                *
 *        iReal: group real count                                        *
 *     iPlanned: group planned count                                     *
 *    iCanceled: group canceled count                                    *
 *         iAge: group age                                               *
 *      iArrhes: rent arrhes                                             *
 *         iDay: rent day                                                *
 *       iMonth: rent month                                              *
 *        iYear: rent year                                               *
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
DROP PROCEDURE IF EXISTS `_PBR_DB_DBN_`.`sp_RentContactSet`$$
CREATE PROCEDURE  `_PBR_DB_DBN_`.`sp_RentContactSet`(IN sLogin VARCHAR(45), IN sSession VARCHAR(200), IN sInet VARCHAR(255), IN sLastName VARCHAR(40), IN sFirstName VARCHAR(40), IN sTel VARCHAR(40), IN sEmail VARCHAR(255), IN sAddress VARCHAR(255), IN sAddressMore VARCHAR(255), IN sCity VARCHAR(255), IN sZip VARCHAR(8), IN iReal SMALLINT UNSIGNED,IN iPlanned SMALLINT UNSIGNED,IN iCanceled SMALLINT UNSIGNED,IN iAge TINYINT UNSIGNED,IN iArrhes TINYINT UNSIGNED,IN iDay TINYINT UNSIGNED, IN iMonth TINYINT UNSIGNED,IN iYear SMALLINT UNSIGNED)
    SQL SECURITY INVOKER
BEGIN
  -- ------ --
  -- Define --
  -- ------ --
  DECLARE iErrorCode INTEGER DEFAULT -1;
  DECLARE sErrorUsername VARCHAR(25) DEFAULT 'UNKNOWN';
  DECLARE sErrorTitle TEXT DEFAULT ' IN sp_RentContactSet';
  DECLARE sErrorDescription TEXT DEFAULT '';
  DECLARE iUser SMALLINT UNSIGNED DEFAULT 0;
  DECLARE iContact MEDIUMINT UNSIGNED DEFAULT 0;
  DECLARE bInTransaction TINYINT(1) DEFAULT 0;
  DECLARE iMaxRent INTEGER UNSIGNED DEFAULT 0;
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
  SET sErrorTitle = CONCAT(sErrorTitle,'(',IFNULL(sLogin,'NULL'),',',IFNULL(sSession,'NULL'),',',IFNULL(sInet,'NULL'),',',IFNULL(sLastName,'NULL'),',',IFNULL(sFirstName,'NULL'),',',IFNULL(sTel,'NULL'),',',IFNULL(iDay,'NULL'),',',IFNULL(iMonth,'NULL'),',',IFNULL(iYear,'NULL'),',...)');
  SET sErrorDescription = 'Check parameters';
  SET sLogin = TRIM(sLogin);
  SET sSession = TRIM(sSession);
  SET sInet=TRIM(sInet);
  SET sLastName=TRIM(sLastName);
  SET sFirstName=TRIM(sFirstName);
  SET sTel=TRIM(sTel);
  SET sEmail=TRIM(sEmail);
  SET sAddress=TRIM(sAddress);
  SET sAddressMore=TRIM(sAddressMore);
  SET sCity=TRIM(sCity);
  SET sZip=TRIM(sZip);
  -- ------- --
  -- Request --
  -- ------- --
  IF (sLogin IS NOT NULL) AND (LENGTH(sLogin)>0) AND (sSession IS NOT NULL) AND (LENGTH(sSession)>0) AND (sInet IS NOT NULL) AND (LENGTH(sInet)>0) AND (sLastName IS NOT NULL) AND (LENGTH(sLastName)>0) AND (sFirstName IS NOT NULL) AND (LENGTH(sFirstName)>0) AND (sTel IS NOT NULL) AND (LENGTH(sTel)>0) AND (iReal IS NOT NULL) AND (iPlanned IS NOT NULL) AND (iCanceled IS NOT NULL) AND (iDay IS NOT NULL) AND (iDay>0) AND (iDay<32) AND (iMonth IS NOT NULL) AND (iMonth>0) AND (iMonth<13) AND (iYear IS NOT NULL) THEN
  BEGIN
    -- Authentication --
    SET sErrorDescription = 'Check authentication';
    CALL sp_SessionValid(sLogin, sSession, 1, sInet, iUser);
    IF (iUser>0) THEN
    BEGIN
      -- Select default max rent --
      SET sErrorDescription = 'Select max rent';
      SELECT IFNULL(MAX(r.`rent_max`),0) INTO iMaxRent
      FROM `_PBR_DB_DBN_`.`reservation` AS r
      WHERE r.`year`=iYear
      AND r.`month`=iMonth
      AND r.`day`=iDay;
      IF iMaxRent<=0 THEN
      BEGIN
        -- Select default max rent --
        SET sErrorDescription = 'Select default max rent';
        CALL sp_ParameterGet(iMonth,iMaxRent);
      END;
      END IF;
      -- Start transaction --
      SET sErrorDescription = 'Start transaction';
      START TRANSACTION;
      SET bInTransaction = 1;
      -- Insert Contact --
      SET sErrorDescription = 'Insert contact';
      INSERT INTO `_PBR_DB_DBN_`.`contact`(`lastname`, `firstname`, `tel`, `email`, `address`, `address_more`, `city`, `zip`, `create_date`, `create_iduser`, `update_date`, `update_iduser`)
      VALUES (sLastName, sFirstName, sTel, sEmail, sAddress, sAddressMore, sCity, sZip, SYSDATE(), iUser, NULL, NULL);
      SELECT LAST_INSERT_ID() INTO iContact;
      -- Insert Rent --
      IF iContact>0 THEN
      BEGIN
        SET sErrorDescription = 'Insert rent';
        INSERT INTO `_PBR_DB_DBN_`.`reservation` (`idcontact`,`year`,`month`,`day`,`rent_real`,`rent_planned`,`rent_canceled`,`rent_max`,`age`,`arrhe`,`create_date`,`create_iduser`,`update_date`,`update_iduser`)
        VALUES (iContact, iYear, iMonth, iDay, iReal, iPlanned, iCanceled, iMaxRent, iAge, iArrhes, SYSDATE(), iUser, NULL, NULL);
        SELECT LAST_INSERT_ID() INTO iErrorCode;
      END;
      END IF;
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
        INSERT INTO `_PBR_DB_DBN_`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(), sErrorUsername, 'ERROR', CONCAT('TRANSACTION',sErrorTitle), CONCAT('An error occures while processing "','adding contact and rent','" step. Exit'),USER(),CURRENT_USER() );
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