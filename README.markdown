# phpBackupS3

This program will backup file paths and [MySQL](http://www.mysql.com) databases to [Amazon's S3](http://www.amazonaws.com/) storage cloud service.

I've used this program in production for about a year with no problems. But, your results may vary.

## Features

* Simple to use
* Backups all databases on a MySQL server
* Backups and compresses (using bzip2) directories and files
* Uses URL type storage keys, so it's easy to browse the backup bucket
* (optionally) Removes old backups according to a [grandfather-father-son](http://en.wikipedia.org/wiki/Grandfather-Father-Son_Backup) based schedule

## About this script

This set of scripts ships with three files:

* backup.php--This is an example of how to call the backup functions
* include/
  * backup.inc.php--All the backup functions are in here
  * S3.php--This is the library to access Amazon S3

### (optional) Backup deletion schedule

A unique feature of this script is the way it will store (and eventually delete) old backups to conserve space, and yet maintain significant backup history. In this method you will have the following full backups:

* Everyday for the past two weeks (14 backups)
* Every saturday for the past 2 months (8 or 9 backups)
* First day of the month going back forever

This allows you to keep a very detailed history of your files during the most recent time, but progressively remove backups as time goes on to save space. This feature can be turned off if you want to store all your backups, all the time.

To disable this feature, comment out the following line from backup.inc.php:

    deleteBackups($BACKUP_BUCKET);

## About the author

I wrote this small program to help backup my virtual machines hosted at various places. I figured someone might be able to make use of it, so I've published it as open source.

## ToDo

* Add configuration documentation
* Comment code