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

class dbScript {
	private $id;
	private $internalName;
	private $filename;
	private $executionCommand;
	private $canBeCalledDirectly;
	private $dirty;

	public function __construct($id) {
		global $pdo;

		$stmt = $pdo->prepare('SELECT internal_name, filename, execution_command, can_be_called_directly FROM scripts WHERE id = :id;');
		$stmt->bindValue(':id', $id);
		$stmt->execute();

		if ($row = $stmt->fetch()) {
			$this->internalName = $row['internal_name'];
			$this->filename = $row['filename'];
			$this->executionCommand = $row['execution_command'];
			$this->canBeCalledDirectly = $row['can_be_called_directly'];
			$this->id = $id;

			$this->dirty = false;	
		}
	}

	public function getId() {
		return $this->id;
	}

	public function getExecutionCommand() {
		return $this->executionCommand;
	}

	public function setExecutionCommand($value) {
		$this->executionCommand = $value;
		$this->dirty = true;
	}

	public function getScriptFilename() {
		return $this->filename;
	}

	public function setScriptFilename($value) {
		return $this->scriptFilename = $value;
	}

	public function getCanBeCalledDirectly() {
		return $this->canBeCalledDirectly;
	}

	public function setCanBeCalledDirectly($value) {
		$this->canBeCalledDirectly = $value;	
		$this->dirty = true;
	}

	// In contrast to everything else this performs
	// a direct operation. Script bodies are expected to be long
	// and will should not be retained in memory any longer than 
 	// required
	public function getBody() {
		global $pdo;
		$stmt = $pdo->prepare('SELECT script_body FROM scripts_bodies WHERE script_id = :script_id;');
		$stmt->bindValue(':script_id', $this->id);
		$stmt->execute();
		
		if ($row = $stmt->fetch()) {
			return $row['script_body'];
		}
		return '';
	}

	public function setBody($scriptBody) {
		global $pdo;
		$stmt = $pdo->prepare('SELECT script_id FROM scripts_bodies WHERE script_id = :script_id;');
		$stmt->bindValue(':script_id', $this->id);
		$stmt->execute();
		if ($stmt->fetch()) {
			$stmt2 = $pdo->prepare('UPDATE script_bodies SET script_body = :script_body WHERE script_id = :script_id;');
			$stmt2->bindValue(':script_id', $scriptBody);
			$stmt2->execute();
		} else {
			$stmt3 = $pdo->prepare('INSERT INTO script_bodies(script_id, script_body) VALUES (:script_id, :script_body);');
			$stmt3->bindValue(':script_id', $this->id);
			$stmt3->bindValue(':script_body', $scriptBody);
			$stmt3->execute();
		}
	}


	public function save() {
		global $pdo;
		if ($this->dirty) {
			$stmt = $pdo->prepare('UPDATE scripts SET internal_name = :internal_name, filename = :filename, execution_command = :execution_command, can_be_called_directly = :can_be_called_directly WHERE id = :id;');
			$stmt->bindValue(':internal_name', $this->internalName);
			$stmt->bindValue(':filename', $this->filename);	
			$stmt->bindValue(':execution_command', $this->executionCommand);
			$stmt->bindValue(':can_be_called_directly', $this->canBeCalledDirectly);
			$stmt->bindValue(':id', $this->id);
			$stmt->execute();
			$this->dirty =  false;	
		}
	}	


}
