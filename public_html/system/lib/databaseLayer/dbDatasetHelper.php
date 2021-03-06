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
class dbDatasetHelper {
	public static function getAllDatasets() {
		global $pdo;
		$stmt = $pdo->prepare('SELECT id FROM datasets WHERE deleted = 0;');
		$stmt->execute();
		$datasets = array();
		while ($row = $stmt->fetch()) {
			$datasets[] = new dbDataset($row['id']);
		}
		return $datasets;
	}	

	public static function getRootDatasets() {

		global $pdo;
		$stmt = $pdo->prepare('SELECT id FROM datasets WHERE  parent_dataset_id IS NULL AND deleted  = 0;');
		$stmt->execute();
		$datasets = array();
		while ($row = $stmt->fetch()) {
			$datasets[] = new dbDataset($row['id']);
		}
		return $datasets;
	}

	public static function checkDatasetInputToActiveJob(dbDataset $dbDataset){
		global $pdo;

		$stmt = $pdo->prepare('SELECT jobs.id as id FROM jobs, job_states WHERE jobs.job_state_id = job_states.id AND  job_states.internal_name != :job_state_internal_name AND input_dataset_id = :input_dataset_id;');

		$stmt->bindValue(':input_dataset_id', $dbDataset->getId());
		$stmt->bindValue(':job_state_internal_name', 'complete');
		$stmt->execute();

		if ($row = $stmt->fetch()) {
			var_dump($row);
			return true;
		}
		return false;
	} 

	public static function getDatasetsByState($dbState) {
		global $pdo;
		$stmt = $pdo->prepare('SELECT id FROM datasets WHERE dataset_state_id = :dataset_state_id AND deleted = 0;');
		$stmt->bindValue(':dataset_state_id', $dbState->getId());
		$stmt->execute();

		$datasets = array();
		while ($row = $stmt->fetch()) {
			$datasets[] = new dbDataset($row['id']);
		}
		return $datasets;
	}

	public static function getDatasetsByParent($dbDataset) {
		global $pdo;
		$stmt = $pdo->prepare('SELECT id FROM datasets WHERE parent_dataset_id = :parent_dataset_id AND deleted = 0;');
		$stmt->bindValue(':parent_dataset_id', $dbDataset->getId());
		$stmt->execute();
		
		$datasets = array();
		while ($row = $stmt->fetch()) {
			$datasets[] = new dbDataset($row['id']);
		}
		return $datasets;
	}
	
	public static function createDataset($jobId, $parentDatasetId, $datasetStateId, $ownerUserId, $datasetProcessorId) {
		global $pdo;

		$stmt = $pdo->prepare('INSERT INTO datasets(job_id, parent_dataset_id, dataset_state_id, owner_user_id, dataset_processor_id) VALUES (:job_id, :parent_dataset_id, :dataset_state_id, :owner_user_id, :dataset_processor_id);');

		$stmt->bindValue(':job_id', $jobId);
		$stmt->bindValue(':parent_dataset_id',$parentDatasetId);
		$stmt->bindValue(':dataset_state_id', $datasetStateId);
		$stmt->bindValue(':owner_user_id', $ownerUserId);
		$stmt->bindValue(':dataset_processor_id', $datasetProcessorId);
		$stmt->execute();

		$id = $pdo->lastInsertId();
		return new dbDataset($id);
	}

}
