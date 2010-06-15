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
class dbProcessState {
	private $id;
	private $internalName;	
	private $name;
	private $description;
	private $dirty;

	public function __construct($id) {
		global $pdo;
		$stmt = $pdo->prepare('SELECT internal_name, name, description FROM process_states WHERE id = :id;');
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		if ($row = $stmt->fetch() ) {
			$this->id  = $id;
			$this->internalName = $row['internal_name'];
			$this->name = $row['name'];
			$this->description = $row['description'];
			$this->dirty = false;
		}
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

	public function getDescription() {
		return $this->description;
	} 

	public function setDescription($value) {
		$this->description = $value;
		$this->dirty = true;
	}

	public function save() {
		global $pdo;
		if ($this->dirty) {
			$stmt = $pdo->prepare('UPDATE process_states SET internal_name = :internal_name, name = :name, description = :description WHERE id = :id;');
			$stmt->bindValue(':internal_name', $this->internal_name);
			$stmt->bindValue(':name', $this->name);
			$stmt->bindValue(':description', $this->description);
			$stmt->bindValue(':id', $this->id);
			$stmt->execute();
			$stmt->dirty = true;
		}

	}

}
			
	
		
