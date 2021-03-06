/* ---------------------------------------------------- */
/*  Generated by Enterprise Architect Version 12.1 		*/
/*  Created On : 21-lis-2016 10:13:03 				*/
/*  DBMS       : SQL Server 2012 						*/
/* ---------------------------------------------------- */

/* Drop Foreign Key Constraints */

IF EXISTS (SELECT 1 FROM dbo.sysobjects WHERE id = object_id(N'[FK_TaskActions_Tasks]') AND OBJECTPROPERTY(id, N'IsForeignKey') = 1) 
ALTER TABLE [TaskActions] DROP CONSTRAINT [FK_TaskActions_Tasks]
GO

/* Drop Tables */

IF EXISTS (SELECT 1 FROM dbo.sysobjects WHERE id = object_id(N'[TaskActions]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1) 
DROP TABLE [TaskActions]
GO

/* Create Tables */

CREATE TABLE [TaskActions]
(
	[TaskActionID] int NOT NULL IDENTITY (1, 1),
	[TaskID] int NULL,
	[Type] varchar(30) NOT NULL,    -- Type of Action: iTask - run class with iTask interface
	[Data] text NULL,    -- Different from Type:  iTask - class name
	[Order] int NOT NULL DEFAULT 1
)
GO

/* Create Primary Keys, Indexes, Uniques, Checks */

ALTER TABLE [TaskActions] 
 ADD CONSTRAINT [PK_TaskActions]
	PRIMARY KEY CLUSTERED ([TaskActionID] ASC)
GO

CREATE NONCLUSTERED INDEX [IXFK_TaskActions_Tasks] 
 ON [TaskActions] ([TaskID] ASC)
GO

CREATE NONCLUSTERED INDEX [IDX_TaskActions_Order] 
 ON [TaskActions] ([Order] ASC)
GO

/* Create Foreign Key Constraints */

ALTER TABLE [TaskActions] ADD CONSTRAINT [FK_TaskActions_Tasks]
	FOREIGN KEY ([TaskID]) REFERENCES [Tasks] ([TaskID]) ON DELETE Cascade ON UPDATE Cascade
GO

/* Create Table Comments */

EXEC sp_addextendedproperty 'MS_Description', 'Type of Action: iTask - run class with iTask interface', 'Schema', [dbo], 'table', [TaskActions], 'column', [Type]
GO

EXEC sp_addextendedproperty 'MS_Description', 'Different from Type:  iTask - class name', 'Schema', [dbo], 'table', [TaskActions], 'column', [Data]
GO