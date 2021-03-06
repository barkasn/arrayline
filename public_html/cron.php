<?php
// $Id$

/*

    Arrayline, an extensible bioinfomatics data processing platform
    Copyright (C) 2010 Nikolaos Barkas

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.
 
    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


require_once('system/includeall.php');


lgSystemLog::logMessage('Cron.php running');

$lgJobScheduler = lgJobScheduler::getInstance();

if (DEBUG) { $lgJobScheduler->releaseLock(); } // DEBUG ONLY

if ( $lgJobScheduler->obtainLock() ) {
	// Commence asynchronous running of jobs that are waiting to run
	// do not wait for jobs to complete return immediately
	$lgJobScheduler->runPendingJobsAsync();

	// Sleep for 1 second to allow jobs that take less than that to
	// execute processing and preprocessing all in one pass
	sleep(1);

	// Check if jobs marked as running, either from the ones just started,
	// or from previous runs are complete and update their status to toBePostProcessed
	$lgJobScheduler->updateStatusOfJobs();

	// Commence Asynchrous post processing of jobs
	$lgJobScheduler->runPostProcessingJobsAsync();

	// Release the lock
	$lgJobScheduler->releaseLock();
}	

lgSystemLog::logMessage('Cron.php running complete');

