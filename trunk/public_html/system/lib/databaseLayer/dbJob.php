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

class dbJob {
	private $id;
	private $jobStateId;
	private $description;
	private $autorun;
	private $runStart;
	private $runEnd;
	private $comment;

	private $dirty;


	public function __construct($id) {
		global $pdo;

		$stmt = $pdo->prepare('SELECT job_state_id, description, autorun, run_start, run_end, comment FROM jobs WHERE id = :id;');
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		
		if ($row = $stmt->fetch()) {
			$this->jobStateId = $row['job_state_id'];
			$this->description = $row['description'];
			$this->autorun = $row['autorun'];
			$this->runStart = $row['run_start'];
			$this->runEnd = $row['run_end'];
			$this->comment = $row['comment'];
			$this->id = $id;

			$this->dirty = false;
		}

	}

	public function getId() {
		return $this->id;
	}

	public function getJobState() {
		return new dbJobState($this->jobStateId);
	}

	public function setJobState( $dbState ) {
		$this->jobStateId = $dbState->getId();
		$this->dirty = true;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($value) {
		$this->description = $value;
		$this->dirty = true;
	}

	public function getAutorun() {
		return $this->autorun;
	}

	public function setAutorun($value) {
		$this->autorun = $value;
		$this->dirty = true;
	}

	public function getRunStart() {
		return $this->runStart;
	}

	public function setRunStart($value) {
		$this->runStart = $value;
		$this->dirty = true;
	}

	public function getComment() {
		return $this->comment;
	}

	public function setCommnent($value) {
		$this->comment = $value;
		$this->dirty = true;
	}	

	public function save() {
		global $pdo;
		if ($this->dirty) {
			$stmt = $pdo->prepare('UPDATE jobs SET job_state_id = :job_state_id, description = :description, autorun = :autorun, run_start = :run_start, run_end = :run_end, comment = :comment WHERE id = :id;');
			$stmt->bindValue(':job_state_id', $this->jobStateId);
			$stmt->bindValue(':description', $this->description);
			$stmt->bindValue(':autorun', $this->autorun);
			$stmt->bindValue(':run_start', $this->runStart);
			$stmt->bindValue(':run_end', $this->runEnd);
			$stmt->bindValue(':comment', $this->comment);
			$stmt->bindValue(':id', $this->id);
			$stmt->execute();
			$this->dirty = false;
		}
	}
	
}
