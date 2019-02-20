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
 * TITLE      : sp_ParameterGet                                          *
 * AUTHOR     : O.JULLIEN                                                *
 * CREATION   : 04/02/2010                                               *
 * DESCRIPTION: Return the max size group                                *
 * COPYRIGHT  : Olivier JULLIEN, All rights reserved                     *
 *************************************************************************
 * Parameters:                                                           *
 * IN sMonth: rent month                                                 *
 *                                                                       *
 * OUT  iMax: max of the month                                           *
 *************************************************************************
 * Date          * Author             * Changes                          *
 *************************************************************************
 * 04/02/2010    * O.JULLIEN          * Creation                         *
 *************************************************************************/
DROP PROCEDURE IF EXISTS `_PBR_DB_DBN_`.`sp_ParameterGet` $$
CREATE PROCEDURE `_PBR_DB_DBN_`.`sp_ParameterGet`(IN sMonth VARCHAR(2), OUT iMax INTEGER)
BEGIN
  SELECT IFNULL(c.`value`,0) INTO iMax
  FROM `_PBR_DB_DBN_`.`config` AS c
  WHERE c.`name` LIKE CONCAT_WS('_','max_rent',sMonth);
END $$

DELIMITER ;
