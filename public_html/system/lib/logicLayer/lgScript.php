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

class lgScript {

	private $dbScript;
	private $id;
	
	public function __construct($id) {
		$this->dbScript = new dbScript($id);
		if ($this->dbScript === NULL) {
			die('lgScript: Invalid Script Id');
		} 
		$this->id = $id;
	}

	public function __destruct() {
		$this->dbScript->save();
	}

	public function getId() {
		return $this->id;
	}

	public function getExecutionCommand() {
		return $this->dbScript->getExecutionCommand();
	}

	public function getScriptFilename() {
		return $this->dbScript->getScriptFilename();
	}

	public function getCanBeCalledDirectly() {
		return $this->dbScript->getrCanBeCalledDirectly();
	}

	public function getBody() {
		return $this->dbScript->getBody();
	}	
		
}
