/*************************************************************************
 *                                                                       *
 * Copyright (C) 2010   Olivier JULLIEN - PBRAIDERS.COM                  *
 * Tous droits reserves - All rights reserved                            *
 *                                                                       *
 *************************************************************************
 *                                                                       *
 * Except if expressly provided in a dedicated License Agreement,you     *
 * are not authorized to:                                                *
 *                                                                       *
 * 1. Use,copy,modify or transfer this software component,module or      *
 * product,including any accompanying electronic or paper documentation  *
 * (together,the "Software"),.                                           *
 *                                                                       *
 * 2. Remove any product identification,copyright,proprietary notices    *
 * or labels from the Software.                                          *
 *                                                                       *
 * 3. Modify,reverse engineer,decompile,disassemble or otherwise         *
 * attempt to reconstruct or discover the source code,or any parts of    *
 * it,from the binaries of the Software.                                 *
 *                                                                       *
 * 4. Create derivative works based on the Software (e.g. incorporating  *
 * the Software in another software or commercial product or service     *
 * without a proper license).                                            *
 *                                                                       *
 * By installing or using the "Software",you confirm your acceptance     *
 * of the hereabove terms and conditions.                                *
 *                                                                       *
 *************************************************************************/
USE `_PBR_DB_DBN_`;
-- -----------------------------------------------------
-- Data for table `config`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `config` (`name`,`value`,`role`) VALUES
('data_version','1.0 - 2010-02-04',10),
('cookie_time_expire','36000',10),
('session_time_expire','36000',10),
('timezone','+1:00',10),
('max_rent_1','300',10),
('max_rent_2','300',10),
('max_rent_3','300',10),
('max_rent_4','300',10),
('max_rent_5','300',10),
('max_rent_6','300',10),
('max_rent_7','300',10),
('max_rent_8','300',10),
('max_rent_9','300',10),
('max_rent_10','300',10),
('max_rent_11','300',10),
('max_rent_12','300',10);
COMMIT;
