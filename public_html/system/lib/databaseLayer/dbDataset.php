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

class dbDataset {
	private $id;
	private $jobId;
	private $parentDatasetId;
	private $datasetStateId;
	private $ownerUserId;
	private $datasetProcessorId;
	private $dirty;
	
	public function __construct($id) {
		global $pdo;
		$stmt = $pdo->prepare('SELECT job_id, parent_dataset_id, dataset_state_id, owner_user_id, dataset_processor_id FROM datasets WHERE id = :id;');
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		
		if ($row = $stmt->fetch()) {
			$this->id = $id;
			$this->jobId = $row['job_id'];
			$this->parentDatasetId = $row['parent_dataset_id'];
			$this->datasetStateId = $row['dataset_state_id'];
			$this->ownerUserId = $row['owner_user_id'];
			$this->datasetProcessorId = $row['dataset_processor_id'];
			$this->dirty = false;
		}
	}

	public function getId() {
		return $this->id;
	}

	public function getJobId() {
		return $this->jobId;
	}

	public function getProcessor() {
		return new dbDatasetProcessor($this->datasetProcessorId);
	}

	public function setProcessor(dbDatasetProcessor $dbDatasetProcessor) {
		$this->dbDatasetProcessorId = $dbDataseProcessor->getId();
		$this->dirty = true;
	}

	public function setJobId($value) {
		$this->jobId = $value;
		$this->dirty = false;
	}

	public function getJob() {
		return new dbJob($this->jobId);
	}

	public function setJob($dbJob) {
		$this->setJobId($dbJob->getId());
	}

	public function getParentDatasetId() {
		return $this->parentDatasetId;
	}

	public function setParentDatasetId($value) {
		$this->parentDatasetId = $value;
		$this->dirty = true;
	}

	public function getParentDataset() {
		return new dbDatasetr($this->parentDatasetId);
	}

	public function setParentDataset($dbDataset) {
		$this->parentDatasetId = $dbDataset->getId();
		$this->dirty = true;
	}

	public function getUserId() {
		return $this->userId;
	}
	
	public function setUserId($value) {
		$this->userId = $value;
		$this->dirty = true;
	}

	public function getUser() {
		return new dbUser($this->userId);
	}

	public function setUser($dbUser){
		$this->userId = $dbUser->getId();
		$this->dirty = true;
	}

	public function getDatasetState() {
		return new dbDatasetState($this->datasetStateId);
	}

	public function setDatasetState(dbDatasetState $dbDatasetState) {
		$this->datasetStateId = $dbDatasetState->getId();
		$this->dirty = true;
	}

	public function save() {
		global $pdo;
		if ($this->dirty) {
			$stmt = $pdo->prepare('UPDATE datasets SET job_id = :job_id, parent_dataset_id = :parent_dataset_id, dataset_state_id = :dataset_state_id, owner_user_id = :owner_user_id, dataset_processor_id = :dataset_processor_id WHERE id =: id;');
			$stmt->bindValue(':job_id', $this->jobId);
			$stmt->bindValue(':parent_dataset_id', $this->parentDatasetId);
			$stmt->bindValue(':dataset_state_id', $this->datasetStateId);
			$stmt->bindValue(':owner_user_id', $this->ownerUserId);
			$stmt->bindValue(':dataset_processor_id', $this->datasetProcessorId);
			$stmt->bindValue(':id', $this->id);
			$stmt->execute();
			$this->dirty = false;
		}
	}
}



