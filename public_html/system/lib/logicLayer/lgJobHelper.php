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

class lgJobHelper {
	const defaultJobState = 'toBeSetup';
	const toBeRunJobState = 'toBeRun';
	const runningJobState = 'processRunning';
	const toBePostProcessedState = 'processComplete';
	const autorunDefault = 0;
	const runStartDefault = 0;
	const runEndDefault = 0;
	const commentDefault = '';
	
	// Public Functions
	public static function createNewJob($description){
		$dbJobState = dbJobStateHelper::getJobStateByInternalName(self::defaultJobState);
		$dbJob = dbJobHelper::createNewJob($dbJobState, $description, self::autorunDefault,
			 0, self::runStartDefault, self::runEndDefault, self::commentDefault);

		$lgJob = self::getLogicalFromDatabaseJob($dbJob);
		self::createDirectoryStructure($lgJob);

		return $lgJob;	
	}

	public static function getJobsToBeRun() {
		$dbJobState = dbJobStateHelper::getJobStateByInternalName(self::toBeRunJobState);
		return self::getLogicalJobsByDatabaseState($dbJobState);
	}

	public static function getJobsToBePostProcessed() {
		$dbJobState = dbJobStateHelper::getJobStateByInternalName(self::toBePostProcessedState);
		return self::getLogicalJobsByDatabaseState($dbJobState);
	}

	public static function getRunningJobs() {
		$dbJobState = dbJobStateHelper::getJobStateByInternalName(self::runningJobState);
		return self::getLogicalJobsByDatabaseState($dbJobState);
	}

	public static function getJobInputDataDirectoryPath(lgJob $lgJob) {
		return self::getJobMainDirectoryPath($lgJob).'/input_data';			
	}

	public static function getJobOutputDataDirectoryPath(lgJob $lgJob) {
		return self::getJobMainDirectoryPath($lgJob).'/output_data';			
	}

	public static function getJobScriptDirectoryPath(lgJob $lgJob) {
		return self::getJobMainDirectoryPath($lgJob).'/scripts';			
	}

	public static function getJobLogFilePath(lgJob $lgJob) {
		return self::getJobMainDirectoryPath($lgJob).'/job_log.txt';			
	} 

	public static function getJobMainDirectoryPath(lgJob $lgJob){
		global $basepath;
		global $jobroot;

		$jobAbsoluteStorage = $basepath.$jobroot;
		$jobDirectory = $jobAbsoluteStorage.($lgJob->getId());

		return $jobDirectory;
	}

	// Private Functions

	private static function getLogicalJobsByDatabaseState($dbJobState) {
		$dbJobs = dbJobHelper::getJobsByState($dbJobState);
		$lgJobs = self::getLogicalFromDatabaseJobs($dbJobs);
		return $lgJobs;

	}

	private static function createDirectoryStructure(lgJob $lgJob) {
		// TODO: Improve error handling
		// TODO: Add Checks
		try {
			umask();
			mkdir($lgJob->getMainDirectoryPath());
			mkdir($lgJob->getInputDataDirectoryPath());
			mkdir($lgJob->getOutputDataDirectoryPath());
			mkdir($lgJob->getScriptDirectoryPath());
		} catch (Exceptions $e) {
			die('An error occured while attempting to create a direcotry for the new Job: '. $e->getMessage());
		}
	}

	private static function getLogicalFromDatabaseJobs($dbJobs) {
		$lgJobs  = array();
		if (!empty($dbJobs)) {
			foreach ($dbJobs as $dbJob) {
				$lgJobs[] = self::getLogicalFromDatabaseJob($dbJob);
			}
		}	
		return $lgJobs;
	}

	private static function getLogicalFromDatabaseJob(dbJob $dbJob) {
		$id = $dbJob->getId();
		return new lgJob($id);
	}
}
