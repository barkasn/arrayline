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
class dbDatasetProcessor {
	private $id;
	private $internalName;
	private $name;
	private $dirty;

	private $acceptStates;
	private $produceStates;

	public function __construct($id) {
		global $pdo;

		$stmt = $pdo->prepare('SELECT internal_name, name FROM dataset_processors WHERE id = :id;');
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		
		if ($row = $stmt->fetch() ) {
			$this->id = $id;
			$this->internalName = $row['internal_name'];
			$this->name = $row['name'];

			$stmt2 = $pdo->prepare('SELECT dataset_state_id FROM module_accept_states WHERE dataset_processor_id = :dataset_processor_id;');
			$stmt2->bindValue(':dataset_processor_id', $this->id);
			$stmt2->execute();

			$acceptStates = array();
			while( $row = $stmt2->fetch() ) {
				$acceptStates[] = $row['dataset_state_id'];
			}

			$produceStates = array();
			while( $row = $stmt2->fetch() ) {
				$produceStates[] = $row['dataset_state_id'];
			}

			$this->dirty = false;
		} else {
			throw new Exception('Dataset processor not found!');
		}
	}

	

	public function getAcceptStates() {
		$dbAcceptStates = array();
		if (!empty($this->acceptStates)) {
			foreach ($this->acceptStates as $state) {
				$dbAcceptStates[] = new dbDatasetState($state);
			}
		}
		return $dbAcceptStates;
	}

	public function getProduceStates() {
		$dbProduceStates = array();
		if (!empty($this->produceStates)) {
			foreach ($this->produceStates as $state) {
				$dbProduceStates[] = new dbDatasetState($state);
			}
		}
		return $dbProduceStates;
	}

	public function getId() {
		return $this->id;
	}

	public function getInternalName() {
		return $this->internalName;
	}

	public function setInternalName($value) {
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

	public function save() {
		global $pdo;
		if ($this->dirty) {
			$stmt = $pdo->prepare('UPDATE dataset_processors SET internal_name = :internal_name, name = :name WHERE id = :id;');
			$stmt->bindValue(':internal_name', $this->internalName);
			$stmt->bindValue(':name', $this->name);
			$stmt->bindValue(':id', $this->id);
			$stmt->execute();
			$this->dirty = false;
		}
	}
}	
	
