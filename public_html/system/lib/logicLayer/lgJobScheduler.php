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
	private static $instance;

	private function __construct() {

	}

	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new lgJobScheduler();
		}
		return self::$instance;
	}		

	public function obtainLockOrExit() {
		//TODO: implement
	} 

	public function releaseLock() {
		//TODO: implement
	}

	public function updateJobStatii() {
		//TODO: implement
		//for each job with jobRUnning status check if
		// finished flag file on disk existas
		// update its status to toBeProcessed
	}

	public function runPendingJobsAsync() {
		$lgPendingJobs = lgJobHelper::getJobsToBeRun();
		if (!empty($lgPendingJobs)) {
			foreach ($lgPendingJobs as $job) {
				$job->beginRun();	
			}
		}
	}

	public function runPostProcessingJobs() {
		$lgJobsToPostProcess = lgJobHelper::getJobsToBePostProcessed();
		if (!empty($lgJobs)) {
			foreach ($lgJobs as $job) {
				$job->postProcess();
			}
		}
	}

}

