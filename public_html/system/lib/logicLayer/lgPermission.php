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
class lgPermission {
	private $dbPermission;
	private $id;

	public function __construct($id) {
		$this->id = $id;
		$this->dbPermission = new dbPermission($id);
		if (!$this->dbPermission) {
			throw new Exception('Permission not found!');
		}
	}

	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->dbPermission->getName();
	}

	public function getInternalName() {

		return $this->dbPermission->getInternalName();
	}

	public function _destruct() {
		$this->dbPermission->save();
	}
		
}

