<?php
// $Id$

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
class lgDatasetProcessorHelper {
	public static function getAllDatasetProcessors() {
		$dbDatasetProcessors  = dbDatasetProcessorHelper::getAllDatasetProcessorts();
		return self::getLogicalFromDatabaseProcessors($dbDatasetProcessors);
	}

	public static function getDatasetCreationProcessors() {
		$dbDatasetProcessors = dbDatasetProcessorHelper::getDatasetCreationProcessors();
		return self::getLogicalFromDatabaseProcessors($dbDatasetProcessors);
	}

	public static function getDatasetProcessorsByAcceptState(lgDatasetState $lgDatasetState) {
		$datasetStateId = $lgDatasetState->getId();
		$dbDatasetState = new dbDatasetState($lgDatasetState->getId());

		$dbDatasetProcessors = dbDatasetProcessorHelper::getProcessorsByAcceptState($dbDatasetState);
		$lgDatasetProcessors = self::getLogicalFromDatabaseProcessors($dbDatasetProcessors);
		return $lgDatasetProcessors;
	}	

	private static function getLogicalFromDatabaseProcessors($dbDatasetProcessors) {
		$lgDatasetProcessors = array();
		if (!empty($dbDatasetProcessors)) {
			foreach($dbDatasetProcessors as $dbp) {
				$lgDatasetProcessors[] = new lgDatasetProcessor($dbp->getId());	
			}
		}
		return $lgDatasetProcessors;
	}
}
