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

class lgJobHelper {
	const defaultJobState = 'toBeSetup';
	const autorunDefault = 0;
	const runStartDefault = 0;
	const runEndDefault = 0;
	const commentDefault = '';
	
	public static function createNewJob($description){
		$dbJobState = dbJobStateHelper::getJobStateByInternalName(self::defaultJobState);
		$dbJob = dbJobHelper::createNewJob($dbJobState, $description, self::autorunDefault,
			 0, self::runStartDefault, self::runEndDefault, self::commentDefault);

		//TODO: Create Directory Structure

		$lgJob = self::getLogicalFromDatabaseJob($dbJob);
		return $lgJob;	
	}


	private static function getLogicalFromDatabaseJob(dbJob $dbJob) {
		return new lgJob($dbJob->getId());
	}
}
