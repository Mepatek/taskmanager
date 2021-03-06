/* ---------------------------------------------------- */
/*  Generated by Enterprise Architect Version 12.0 		*/
/*  Created On : 17-�no-2016 12:09:10 				*/
/*  DBMS       : SQLite 								*/
/* ---------------------------------------------------- */

/* Drop Tables */

DROP TABLE IF EXISTS 'Tasks'
;

DROP TABLE IF EXISTS 'TaskHistory'
;

DROP TABLE IF EXISTS 'TaskConditions'
;

DROP TABLE IF EXISTS 'TaskActions'
;

/* Create Tables with Primary and Foreign Keys, Check and Unique Constraints */

CREATE TABLE 'Tasks'
(
	'TaskID' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	'Name' TEXT NOT NULL,
	'Created' TEXT NOT NULL,
	'Source' TEXT,
	'Author' TEXT,
	'Description' TEXT,
	'DeleteAfterRun' INTEGER NOT NULL DEFAULT 0,
	'MaxExecutionTimeInSecond' INTEGER, 
  'Disabled' INTEGER NOT NULL DEFAULT 0,
	'State' INTEGER NOT NULL DEFAULT 0, -- 0 = Idle 1 = Running 2 = Queued
  'ExceedDateTime' TEXT,
	'NextRun' TEXT,
	'LastRun' TEXT,
	'LastSuccess' INTEGER,
	'Deleted' INTEGER NOT NULL DEFAULT 0
)
;

CREATE TABLE 'TaskHistory'
(
	'TaskHistoryID' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	'TaskID' INTEGER NOT NULL,
	'Started' TEXT NOT NULL,
	'Finished' TEXT,
	'ResultCode' INTEGER,
	'User' TEXT,
	'Output' TEXT,
	CONSTRAINT 'FK_TaskHistory_Tasks' FOREIGN KEY ('TaskID') REFERENCES 'Tasks' ('TaskID') ON DELETE No Action ON UPDATE No Action
)
;

CREATE TABLE 'TaskConditions'
(
	'TaskConditionID' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	'TaskID' INTEGER NOT NULL,
	'Type' TEXT NOT NULL,
	'Data' TEXT,
	'Created' TEXT,
	'Expired' TEXT,
	'Active' INTEGER NOT NULL DEFAULT 1,
	CONSTRAINT 'FK_TaskConditions_Tasks' FOREIGN KEY ('TaskID') REFERENCES 'Tasks' ('TaskID') ON DELETE Cascade ON UPDATE Cascade
)
;

CREATE TABLE 'TaskActions'
(
	'TaskActionID' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	'TaskID' INTEGER,
	'Type' TEXT NOT NULL, -- Type of Action: iTask - run class with iTask interface
	'Data' TEXT, -- Different from Type:  iTask - class name
	'Order' INTEGER NOT NULL DEFAULT 1,
	CONSTRAINT 'FK_TaskActions_Tasks' FOREIGN KEY ('TaskID') REFERENCES 'Tasks' ('TaskID') ON DELETE Cascade ON UPDATE Cascade
)
;

/* Create Indexes and Triggers */

CREATE INDEX 'IDX_Task_Name'
 ON 'Tasks' ('Name' ASC)
;

CREATE INDEX 'IDX_Task_Deleted'
 ON 'Tasks' ('Deleted' ASC)
;

CREATE INDEX 'IDX_Task_DeletedAfterRun'
 ON 'Tasks' ('DeleteAfterRun' ASC)
;

CREATE INDEX 'IDX_Task_Running'
 ON 'Tasks' ('State' ASC)
;

CREATE INDEX 'IDX_Task_NextRun'
 ON 'Tasks' ('NextRun' ASC)
;

CREATE INDEX 'IDX_Task_Source'
 ON 'Tasks' ('Source' ASC)
;

CREATE INDEX 'IDX_Task_Disabled'
 ON 'Tasks' ('Disabled' ASC)
;

CREATE INDEX 'IDX_Task_LastSuccess'
 ON 'Tasks' ('LastSuccess' ASC)
;

CREATE INDEX 'IXFK_TaskHistory_Tasks'
 ON 'TaskHistory' ('TaskID' ASC)
;

CREATE INDEX 'IDX_ResultCode'
 ON 'TaskHistory' ('ResultCode' ASC)
;

CREATE INDEX 'IXFK_TaskConditions_Tasks'
 ON 'TaskConditions' ('TaskID' ASC)
;

CREATE INDEX 'IDX_TaskConditions_Type'
 ON 'TaskConditions' ('Type' ASC)
;

CREATE INDEX 'IDX_TaskConditions_Active'
 ON 'TaskConditions' ('Active' ASC)
;

CREATE INDEX 'IXFK_TaskActions_Tasks'
 ON 'TaskActions' ('TaskID' ASC)
;

CREATE INDEX 'IDX_TaskActions_Order'
 ON 'TaskActions' ('Order' ASC)
;
