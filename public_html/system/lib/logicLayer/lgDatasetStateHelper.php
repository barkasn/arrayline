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

class lgDatasetStateHelper {

	public static function getAllDatasetStates() {
		$dbDatasetStates = dbDatasetStateHelper::getAllDatasetStates();
		$lgDatasetStates = self::getLogicFromDatabaseDatasetStates($dbDatasetStates);
		return $lgDatasetStates;
	}

	public static function getDatasetStateByInternalName($internalName) {
		$dbDatasetState = dbDatasetStateHelper::getDatasetStateByInternalName($internalName);
		return new lgDatasetState($dbDatasetState->getId());
	}

	private static function getLogicFromDatabaseDatasetStates($dbDatasetStates) {
		$lgDatasetStates = array();
		if (!empty($dbDatasetStates)) {
			foreach ($dbDatasetStates as $dbds) {
				$lgDatasetStates[] = new lgDatasetState($dbds->getId());
			} 
		}
		return $lgDatasetStates;
	}

}
