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

class dbJobHelper {
	private static function getAllJobs() {
		global $pdo;

		$stmt = $pdo->prepare('SELECT id FROM jobs;');
		$stmt->execute();
		$jobObjects = array();
		while($row = $stmt->fetch() ) {
			$jobObjects[] = new dbJobObject($row['id']);
		}

		return $jobObjects;
	}

	private static function getJobsByState($dbJobState) {
		global $pdo;

		$stmt = $pdo->prepare('SELECT id FROM jobs WHERE job_state_id = :job_state_id;');
		$stmt->bindValue(':job_state_id', $dbJobState->getId() );
		$stmt->execute();
		$jobObjects = array();
		while($row = $stmt->fetch() ) {
			$jobObjects[] = new dbJobObject($row['id']);
		}

		return $jobObjects;
	}

	public static function createNewJob($dbJobState, $description, $autorun, $runStart, $runEnd, $comment ) {
		global $pdo;

		$stmt = $pdo->prepare('INSERT INTO jobs(job_state_id, description, autorun, run_start, run_end, comment) VALUES (:job_state_id, :description, :autorun, :run_start, :run_end, :comment);');

		$stmt->bindValue(':job_state_id', $dbJobState->getId());
		$stmt->bindValue(':description', $description);
		$stmt->bindValue(':autorun', $autorun);
		$stmt->bindValue(':run_start', $runStart);
		$stmt->bindValue(':run_end', $runEnd);
		$stmt->bindValue(':comment', $comment);
	
		$stmt->execute();

		$id = $pdo->lastInsertId();
		$dbJob = new dbJob($id);

		return $dbJob;
	}

}
