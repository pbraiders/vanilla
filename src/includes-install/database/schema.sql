-- -----------------------------------------------------
-- Table `_PBR_DB_DBN_`.`config`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `_PBR_DB_DBN_`.`config` ;

CREATE  TABLE IF NOT EXISTS `_PBR_DB_DBN_`.`config` (
  `name` VARCHAR(45) NOT NULL , -- COMMENT 'name of the option' ,
  `value` VARCHAR(255) NOT NULL , -- COMMENT 'value of the option' ,
  `role` TINYINT(1) UNSIGNED NOT NULL DEFAULT 10 , -- COMMENT 'minimun credential to modify the value' ,
  PRIMARY KEY (`name`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
-- COMMENT = 'Contains configuration options';


-- -----------------------------------------------------
-- Table `_PBR_DB_DBN_`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `_PBR_DB_DBN_`.`user` ;

CREATE  TABLE IF NOT EXISTS `_PBR_DB_DBN_`.`user` (
  `iduser` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT , -- COMMENT 'unique internal identifier' ,
  `login` VARCHAR(45) NOT NULL , -- COMMENT 'user name' ,
  `password` VARCHAR(40) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL , -- COMMENT 'encrypted user password' ,
  `registered` DATETIME NOT NULL , -- COMMENT 'date of registration. In server timezone.' ,
  `role` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 , -- COMMENT '0=user, 10=administrator' ,
  `last_visit` DATETIME NULL , -- COMMENT 'date of the last visit. In server time zone.' ,
  `state` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 , -- COMMENT '0:deleted, 1:activated' ,
  PRIMARY KEY (`iduser`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
-- COMMENT = 'Contains users';

CREATE UNIQUE INDEX `ui_user_name` ON `_PBR_DB_DBN_`.`user` (`login` ASC) ;

CREATE INDEX `ix_user_name` ON `_PBR_DB_DBN_`.`user` (`login` ASC) ;


-- -----------------------------------------------------
-- Table `_PBR_DB_DBN_`.`log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `_PBR_DB_DBN_`.`log` ;

CREATE  TABLE IF NOT EXISTS `_PBR_DB_DBN_`.`log` (
  `idlog` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , -- COMMENT 'unique internal identifier' ,
  `logged` DATETIME NOT NULL , -- COMMENT 'date when the error is raised' ,
  `username` VARCHAR(45) NOT NULL , -- COMMENT 'user name' ,
  `type` VARCHAR(15) NOT NULL , -- COMMENT 'log type (warn, error,...)' ,
  `title` TEXT NOT NULL , -- COMMENT 'title' ,
  `description` TEXT NOT NULL , -- COMMENT 'description' ,
  `mysqluser` VARCHAR(255) NULL , -- COMMENT 'mysql user authentication' ,
  `mysqlcurrentuser` VARCHAR(255) NULL , -- COMMENT 'mysql current user authentication' ,
  PRIMARY KEY (`idlog`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
-- COMMENT = 'Errors and logs';

CREATE INDEX `in_log_date` ON `_PBR_DB_DBN_`.`log` (`logged` ASC) ;


-- -----------------------------------------------------
-- Table `_PBR_DB_DBN_`.`session`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `_PBR_DB_DBN_`.`session` ;

CREATE  TABLE IF NOT EXISTS `_PBR_DB_DBN_`.`session` (
  `login` VARCHAR(45) NOT NULL , -- COMMENT 'user identifier' ,
  `session` VARCHAR(200) NOT NULL , -- COMMENT 'session identifier' ,
  `inet` INT UNSIGNED NOT NULL , -- COMMENT 'CRC32 checksum of the concatenation of IP and USER_AGENT' ,
  `create_date` INT UNSIGNED NOT NULL , -- COMMENT 'Date of creation' ,
  `expire_date` INT UNSIGNED NOT NULL , -- COMMENT 'Date of expiration' ,
  `logoff` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 , -- COMMENT '1: should be deleted. Updated when user ask for deconnection' ,
  PRIMARY KEY (`login`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
-- COMMENT = 'Session Table - store sessions in mysql so it matters not what server rpc request goes too';

CREATE INDEX `ix_session_session` ON `_PBR_DB_DBN_`.`session` (`session` ASC) ;


-- -----------------------------------------------------
-- Table `_PBR_DB_DBN_`.`contact`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `_PBR_DB_DBN_`.`contact` ;

CREATE  TABLE IF NOT EXISTS `_PBR_DB_DBN_`.`contact` (
  `idcontact` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT , -- COMMENT 'unique internal identifier' ,
  `lastname` VARCHAR(40) NOT NULL , -- COMMENT 'contact name' ,
  `firstname` VARCHAR(40) NOT NULL , -- COMMENT 'contact first name' ,
  `tel` VARCHAR(40) NOT NULL , -- COMMENT 'tel numbers' ,
  `email` VARCHAR(255) NULL , -- COMMENT 'emails' ,
  `address` VARCHAR(255) NULL , -- COMMENT 'address' ,
  `address_more` VARCHAR(255) NULL , -- COMMENT 'address more' ,
  `city` VARCHAR(255) NULL , -- COMMENT 'city' ,
  `zip` VARCHAR(8) NULL , -- COMMENT 'zip' ,
  `comment` TEXT NULL , -- COMMENT 'comment' ,
  `create_date` DATETIME NOT NULL , -- COMMENT 'creation date in server time zone' ,
  `create_iduser` SMALLINT UNSIGNED NOT NULL , -- COMMENT 'user who created the contact' ,
  `update_date` DATETIME NULL , -- COMMENT 'last update date in server time zone' ,
  `update_iduser` SMALLINT UNSIGNED NULL , -- COMMENT 'user who updated the contact' ,
  PRIMARY KEY (`idcontact`) ,
  CONSTRAINT `fk_contact_user_create`
    FOREIGN KEY (`create_iduser` )
    REFERENCES `_PBR_DB_DBN_`.`user` (`iduser` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_user_update`
    FOREIGN KEY (`update_iduser` )
    REFERENCES `_PBR_DB_DBN_`.`user` (`iduser` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
-- COMMENT = 'Contains the contacts';

CREATE INDEX `fk_contact_user_create` ON `_PBR_DB_DBN_`.`contact` (`create_iduser` ASC) ;

CREATE INDEX `fk_contact_user_update` ON `_PBR_DB_DBN_`.`contact` (`update_iduser` ASC) ;

CREATE INDEX `ix_contact_lastname` ON `_PBR_DB_DBN_`.`contact` (`lastname` ASC) ;

CREATE INDEX `ix_contact_createdate` ON `_PBR_DB_DBN_`.`contact` (`create_date` ASC) ;

-- -----------------------------------------------------
-- Table `_PBR_DB_DBN_`.`reservation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `_PBR_DB_DBN_`.`reservation` ;

CREATE  TABLE IF NOT EXISTS `_PBR_DB_DBN_`.`reservation` (
  `idreservation` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT , -- COMMENT 'unique internal identifier' ,
  `idcontact` MEDIUMINT UNSIGNED NOT NULL , -- COMMENT 'contact identifier' ,
  `year` SMALLINT UNSIGNED NOT NULL , -- COMMENT 'reservation date year' ,
  `month` TINYINT UNSIGNED NOT NULL , -- COMMENT 'reservation date month' ,
  `day` TINYINT UNSIGNED NOT NULL , -- COMMENT 'reservation date day' ,
  `rent_real` SMALLINT UNSIGNED NOT NULL DEFAULT 0 , -- COMMENT 'real rent count' ,
  `rent_planned` SMALLINT UNSIGNED NOT NULL DEFAULT 0 , -- COMMENT 'planned rent count' ,
  `rent_canceled` SMALLINT UNSIGNED NOT NULL DEFAULT 0 , -- COMMENT 'canceled rent count' ,
  `rent_max` SMALLINT UNSIGNED NOT NULL , -- COMMENT 'max rent allowed' ,
  `age` TINYINT UNSIGNED NULL , -- COMMENT '1:16-25, 2:26-35, 3:35 and more.' ,
  `arrhe` TINYINT UNSIGNED NULL , -- COMMENT '1:cash, 2:check, 3:credit card' ,
  `comment` TEXT NULL , -- COMMENT 'comment' ,
  `create_date` DATETIME NOT NULL , -- COMMENT 'creation date in server time zone' ,
  `create_iduser` SMALLINT UNSIGNED NOT NULL , -- COMMENT 'user who created the reservation' ,
  `update_date` DATETIME NULL , -- COMMENT 'last update date in server time zone' ,
  `update_iduser` SMALLINT UNSIGNED NULL , -- COMMENT 'user who updated the reservation' ,
  PRIMARY KEY (`idreservation`) ,
  CONSTRAINT `fk_reservation_contact`
    FOREIGN KEY (`idcontact` )
    REFERENCES `_PBR_DB_DBN_`.`contact` (`idcontact` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reservation_user_create`
    FOREIGN KEY (`create_iduser` )
    REFERENCES `_PBR_DB_DBN_`.`user` (`iduser` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reservation_user_update`
    FOREIGN KEY (`update_iduser` )
    REFERENCES `_PBR_DB_DBN_`.`user` (`iduser` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
-- COMMENT = 'Contains the reservations';

CREATE INDEX `fk_reservation_contact` ON `_PBR_DB_DBN_`.`reservation` (`idcontact` ASC) ;

CREATE INDEX `fk_reservation_user_create` ON `_PBR_DB_DBN_`.`reservation` (`create_iduser` ASC) ;

CREATE INDEX `fk_reservation_user_update` ON `_PBR_DB_DBN_`.`reservation` (`update_iduser` ASC) ;

CREATE INDEX `ix_reservation_year` ON `_PBR_DB_DBN_`.`reservation` (`year` ASC) ;

CREATE INDEX `ix_reservation_month` ON `_PBR_DB_DBN_`.`reservation` (`month` ASC) ;

CREATE INDEX `ix_reservation_day` ON `_PBR_DB_DBN_`.`reservation` (`day` ASC) ;

-- -----------------------------------------------------
-- Data for table `_PBR_DB_DBN_`.`config`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `_PBR_DB_DBN_`;
INSERT INTO `config` (`name`, `value`, `role`) VALUES ('schema_version', '1.2.0', 10);

COMMIT;
