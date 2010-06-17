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
class lgModule {
	private $id;
	private $dbModule;

	public function __construct($id) {
		$dbModule = new dbModule($id);
		$this->id = $id;
	}

	public function __destruct() {
		$dbModule->save();
	}
	
	public function getId() { 
		return $this->id;
	}

	public function getInternalName() {
		return $this->dbModule->getInternalName();
	}

	public function setInternalName($value) {
		$this->dbModule->setInternalName($value);
	}

	public function getName() {
		return $this->getName();
	}
	
	public function setName($value) {
		$this->dbModule->setName($value);
	}

}

	
