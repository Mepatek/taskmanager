/* ---------------------------------------------------- */
/*  Generated by Enterprise Architect Version 12.1 		*/
/*  Created On : 21-lis-2016 10:12:27 				*/
/*  DBMS       : MySql 						*/
/* ---------------------------------------------------- */

SET FOREIGN_KEY_CHECKS=0 
;

/* Drop Tables */

DROP TABLE IF EXISTS `Tasks` CASCADE
;

/* Create Tables */

CREATE TABLE `Tasks`
(
	`TaskID` INTEGER NOT NULL AUTO_INCREMENT,
	`Name` VARCHAR(150) NOT NULL,
	`Created` DATETIME NOT NULL,
	`Source` VARCHAR(255) 	 NULL,
	`Author` VARCHAR(100) 	 NULL,
	`Description` TEXT 	 NULL,
	`DeleteAfterRun` TINYINT NOT NULL DEFAULT 0,
	`MaxExecutionTimeInSecond` INTEGER 	 NULL,
	`Disabled` TINYINT NOT NULL DEFAULT 0,
	`State` INTEGER NOT NULL DEFAULT 0 COMMENT '0 = Idle 1 = Running 2 = Queued',
	`ExceedDateTime` DATETIME 	 NULL,
	`NextRun` DATETIME 	 NULL,
	`LastRun` DATETIME 	 NULL,
	`LastSuccess` INTEGER 	 NULL,
	`Deleted` TINYINT NOT NULL DEFAULT 0,
	CONSTRAINT `PK_Task` PRIMARY KEY (`TaskID` ASC)
)

;

/* Create Primary Keys, Indexes, Uniques, Checks */

ALTER TABLE `Tasks` 
 ADD INDEX `IDX_Task_Name` (`Name` ASC)
;

ALTER TABLE `Tasks` 
 ADD INDEX `IDX_Task_Deleted` (`Deleted` ASC)
;

ALTER TABLE `Tasks` 
 ADD INDEX `IDX_Task_DeletedAfterRun` (`DeleteAfterRun` ASC)
;

ALTER TABLE `Tasks` 
 ADD INDEX `IDX_Task_Running` (`State` ASC)
;

ALTER TABLE `Tasks` 
 ADD INDEX `IDX_Task_NextRun` (`NextRun` ASC)
;

ALTER TABLE `Tasks` 
 ADD INDEX `IDX_Task_Source` (`Source` ASC)
;

ALTER TABLE `Tasks` 
 ADD INDEX `IDX_Task_Disabled` (`Disabled` ASC)
;

ALTER TABLE `Tasks` 
 ADD INDEX `IDX_Task_LastSuccess` (`LastSuccess` ASC)
;

SET FOREIGN_KEY_CHECKS=1 
;