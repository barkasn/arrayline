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
	private $scriptSetId;
	private $inputDatasetId;
	private $outputDatasetId;
	private $outputDatasetProcessStateId;
	private $userId;
	private $datasetProcessorId;
	private $dataCleared;
	private $dirty;

	public function __construct($id) {
		global $pdo;

		$stmt = $pdo->prepare('SELECT job_state_id, description, autorun, run_start, run_end, comment, script_set_id, input_dataset_id, output_dataset_id, output_dataset_process_state_id, user_id, dataset_processor_id, data_cleared FROM jobs WHERE id = :id;');
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		
		if ($row = $stmt->fetch()) {
			$this->jobStateId = $row['job_state_id'];
			$this->description = $row['description'];
			$this->autorun = $row['autorun'];
			$this->runStart = $row['run_start'];
			$this->runEnd = $row['run_end'];
			$this->comment = $row['comment'];
			$this->scriptSetId = $row['script_set_id'];
			$this->inputDatasetId = $row['input_dataset_id'];
			$this->outputDatasetId = $row['output_dataset_id'];
			$this->outputDatasetProcessStateId = $row['output_dataset_process_state_id'];
			$this->userId = $row['user_id'];
			$this->datasetProcessorId = $row['dataset_processor_id'];
			$this->dataCleared = $row['data_cleared'];
			$this->id = $id;
			$this->dirty = false;
		}
	}

	public function getDataCleared() {
		return $this->dataCleared;
	}

	public function setDataCleared($value) {
		if (is_bool($value)) {
			$this->dataCleared = $value;
			$this->dirty = true;
		} else {
			throw new Exception('Invalid parameter type:  boolean Required.');
		}
	}

	public function getUser() {
		return new dbUser($this->userId);
	}

	public function setUser(dbUser $dbUser) {
		$this->userId = $dbUser->getId();
		$this->dirty = true;
	}

	public function setDatasetProcessor(dbDatasetProcessor $dbDatasetProcessor) {
		$this->datasetProcessorId = $dbDatasetProcessor->getId();
		$this->dirty = true;
	}

	public function getDatasetProcessor() {
		return new dbDatasetProcessor($this->datasetProcessorId);
	}

	public function getOutputDatasetProcessState() {
		return new dbDatasetState($this->outputDatasetProcessStateId);
	}

	public function setOutputDatasetProcessState(dbDatasetState $dbDatasetState) {
		$this->outputDatasetProcessStateId = $dbDatasetState->getId();
		$this->dirty = true;
	}

	public function getInputDataset() {
		if ($this->inputDatasetId) {
			return new dbDataset($this->inputDatasetId);
		}
		return NULL;
	}

	public function setInputDataset(dbDataset $dbDataset) {
		if ($dbDataset) {
			$this->inputDatasetId = $dbDataset->getId();
		} else {
			$this->inputDatasetId = NULL;
		}
		$this->dirty = true;
	}

	public function getOutputDataset() {
		if ($this->outputDatasetId) {	
			return new dbDataset($this->outputDatasetId);	
		}
		return NULL;
	}

	public function setOutputDataset(dbDataset $dbDataset) {
		if ($dbDataset) {
			$this->outputDatasetId = $dbDataset->getId();
		} else {
			$this->outputDatasetId = NULL;
		}
		$this->dirty = true;
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

	public function getRunEnd() {
		return $this->runEnd;
	}

	public function setRunEnd($value) {
		$this->runEnd = $value;
		$this->dirty = true;
	}


	public function getComment() {
		return $this->comment;
	}

	public function setCommnent($value) {
		$this->comment = $value;
		$this->dirty = true;
	}	

	public function getScriptSet() {
		return new dbScriptSet($this->scriptSetId);
	}

	public function setScriptSet($dbScriptSet) {
		$this->scriptSetId = $dbScriptSet->getId();
		$this->dirty = true;
	}

	public function save() {
		global $pdo;

		if ($this->dirty) {
			$stmt = $pdo->prepare('UPDATE jobs SET job_state_id = :job_state_id, description = :description, autorun = :autorun, run_start = :run_start, run_end = :run_end, comment = :comment, script_set_id = :script_set_id, input_dataset_id = :input_dataset_id, output_dataset_id = :output_dataset_id, output_dataset_process_state_id = :output_dataset_process_state_id, dataset_processor_id = :dataset_processor_id, user_id = :user_id, data_cleared = :data_cleared  WHERE id = :id;');

			$stmt->bindValue(':job_state_id', $this->jobStateId);
			$stmt->bindValue(':description', $this->description);
			$stmt->bindValue(':autorun', $this->autorun);
			$stmt->bindValue(':run_start', $this->runStart);
			$stmt->bindValue(':run_end', $this->runEnd);
			$stmt->bindValue(':comment', $this->comment);
			$stmt->bindValue(':script_set_id', $this->scriptSetId);
			$stmt->bindValue(':input_dataset_id', $this->inputDatasetId);
			$stmt->bindValue(':output_dataset_id', $this->outputDatasetId);
			$stmt->bindValue(':output_dataset_process_state_id', $this->outputDatasetProcessStateId);
			$stmt->bindValue(':dataset_processor_id', $this->datasetProcessorId);
			$stmt->bindValue(':user_id', $this->userId);
			$stmt->bindValue(':data_cleared', $this->dataCleared);
			
			$stmt->bindValue(':id', $this->id);
			$stmt->execute();
			$this->dirty = false;
		}
	}
	
}
