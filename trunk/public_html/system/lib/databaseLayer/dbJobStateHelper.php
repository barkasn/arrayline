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

class dbJobStateHelper {
	public static function getAllJobStates() {
		global $pdo;
		$stmt = $pdo->prepare('SELECT id FROM job_states;');
		$stmt->execute();

		$dbJobStates = array();
		while ($row = $stmt->fetch()) {
			$dbJobStates[] = new dbJobState($row['id']);
		}

		return $dbJobStates;
	}

	public static function getJobStateByInternalName($internalName) {
		global $pdo;
		$dbJobState;
		$stmt = $pdo->prepare('SELECT id FROM job_states WHERE internal_name = :internal_name;');
		$stmt->bindValue(':internal_name', $internalName);
		$stmt->execute();
		if ($row = $stmt->fetch()) {
			$dbJobState = new dbJobState($row['id']);
		}
		return $dbJobState;
	}
}
