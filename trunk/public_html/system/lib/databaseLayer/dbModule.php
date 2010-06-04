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
class dbModule {
	private $id;
	private $internalName;
	private $name;
	private $datasetProcessorsIds;
	private $dirty;

	public class __construct($id) { 
		global $pdo;
		$stmt = $pdo->prepare('SELECT internal_name, name FROM modules WHERE id =:id;');
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		if ($row = $stmt->fetch()) {
			$this->id = $id;
			$this->internalName = $row['internal_name'];
			$this->name = $row['name'];

			$this->datasetProcessorsIds = array();
			$stmt2 = $pdo->prepare('SELECT dataset_processor_id FROM modules_dataset_processors WHERE module_id = :module_id;');
			$stmt2->bindValue(':module_id', $this->id);
			$stmt2->execute();
			while ($row = $stmt2->fetch()) {
				$this->datasetProcessorsIds[] = $row['dataset_processor_id'];
			}

			$this->dirty = false;
		}
	}


	public function getId() {
		return $this->id;	

	}

	public function getInternalName() {
		return $this->internalName;
	}

	public function setInternalName ($value) {
		$this->internalName = $value;
		$this->dirty = true;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($value) {
		$this->name = $value;
		$this->dirty = true;
	}

	public function getDatasetProcessors() {
		$dbProcessors = array();
		if (!empty($this->datasetProcessorsIds)) {
			foreach($this->datasetProcessorsIds as $pid) {
				$dbProcessors = new dbProcessor($pid);
			}
		}
		return $dbProcessors;
	}

	public function setDatasetProcessors($dbProcessors) {
		$this->datasetProcessosIds = array();
		if(!empty($dbProcessors)) {
			foreach ($dbProcessors as $pcs) {
				$this->datasetProcessorsIds[] = $pcs->getId();
			}
		}
		$this->dirty = true;
	}
				
	public function save() {
		global $pdo;
		if ($this->dirty) {
			$stmt = $pdo->prepare('UPDATE modules SET internal_name = :internal_name, name = :name WHERE id = :id;');
			$stmt->bindValue(':internal_name', $this->internalName);
			$stmt->bindValue(':name', $this->name);
			$stmt->bindValue(':id', $this->id);
			$stmt->execute();

			// Save processors
			$stmt2 = $pdo->prepare('DELETE FROM modules_dataset_processors WHERE module_id = :module_id');
			$stmt2->bindValue(':module_id', $this->id);
			$stmt2->execute();
			
			if (!empty($this->datasetProcessors)) {
				foreach ($this->datasetProcessors as $pcs) {
					$stmt3 = $pdo->prepare('INSERT INTO modules_dataset_processors(module_id, dataset_processor_id) VALUES (:module_id, :dataset_processor_id);'):
					$stmt3->bindValue(':module_id', $this->id);
					$stmt3->bindValue(':dataset_processor_id', $pcs->getId();
					$stmt3->execute();
				}	

			}


			$this->dirty = false;
		}
	}
	
}
	

