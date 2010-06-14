<?php
/*

Copyright 2010 Nikolaos Barkas

This is part of Arrayline

Arrayline is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Arrayline is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details.

You should have received a copy of the GNU General Public License 
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

class lgJobScheduler {
	const databaseLockKey = 'jobSchedulerLock';

	private static $instance;

	private function __construct() {

	}

	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new lgJobScheduler();
		}
		return self::$instance;
	}		

	// This is not exactly atomic, but it should suffice given than
	// the cron job only runs every couple of minutes
	// TODO: Improve implementation 
	public function obtainLock() {
		$dbLockAttribute = new dbAttribute(self::databaseLockKey);
		if ($dbLockAttribute->getValue() == '1') {
			return false;
		} else {
			$dbLockAttribute->setValue('1');
			$dbLockAttribute->save();
		}
		return true;
	} 

	// This is not exactly atomic, but it should suffice given than
	// the cron job only runs every couple of minutes
	// TODO: Improve implementation 
	public function releaseLock() {
		$dbLockAttribute = new dbAttribute(self::databaseLockKey);
		$dbLockAttribute->setValue('0');
		$dbLockAttribute->save();
	}

	// "status" apparently does not have stattii as plural (OED)
	public function updateStatusOfJobs() {
		$lgRunningJobs  = lgJobHelper::getRunningJobs();
		if (!empty($lgRunningJobs)) {
			foreach ($lgRunningJobs as $job) {
				$job->checkRunComplete();
			}
		}
	}

	public function runPendingJobsAsync() {
		$lgPendingJobs = lgJobHelper::getJobsToBeRun();

		if (!empty($lgPendingJobs)) {
			foreach ($lgPendingJobs as $job) {
				$job->beginRun();	
			}
		}
	}

	public function runPostProcessingJobsAsync() {
		$lgJobsToPostProcess = lgJobHelper::getJobsToBePostProcessed();
		if (!empty($lgJobsToPostProcess)) {
			foreach ($lgJobsToPostProcess as $job) {
				$job->postProcess();
			}
		}
	}

}

