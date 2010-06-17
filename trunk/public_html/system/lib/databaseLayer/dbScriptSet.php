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

class dbScriptSet {
	private $id;
	private $description;
	private $scriptIds;
	private $entryScriptId;
	private $dirty;

	public function __construct($id) {
		global $pdo;
		
		$stmt = $pdo->prepare('SELECT description, entry_script_id FROM script_sets WHERE id = :id;');
		$stmt->bindValue(':id', $id);
		$stmt->execute();

		if ($row = $stmt->fetch()) {
			$this->id = $id;
			$this->description = $row['description'];
			$this->entryScriptId = $row['entry_script_id'];


			$stmt2 = $pdo->prepare('SELECT script_id FROM script_sets_scripts WHERE script_set_id = :script_set_id;');
			$stmt2->bindValue(':script_set_id', $this->id);
			$stmt2->execute();
			
			$this->scriptIds = array();
			while ( $row2 = $stmt2->fetch() ) {
				$this->scriptIds[] = $row2['script_id'];
			}
			
			$this->dirty = true;
		}
	}

	public function getId() {
		return $this->id;
	}

	public function getEntryScript() {
		return new dbScript($this->entryScriptId);
	}

	public function setEntryScript(dbScript $dbScript) {
		$this->entryScriptId = $dbScript->getId();
		$this->dirty = true;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($value) {
		$this->description = $value;
		$this->dirty = true;
	}

	public function getScripts() {
		$dbScripts = array();
		if (!empty($this->scriptIds)) {
			foreach ($this->scriptIds as $scriptId) {
				$dbScripts[] = new dbScript($scriptId);
			}
		}	
		return $dbScripts;
	}

	public function appendScript(dbScript $dbScript) {
		// TODO: duplicate checking
		$this->scriptIds[] = $dbScript->getId();
		$this->dirty = true;
	}

	public function setScripts($dbScripts) {
		$this->dbScripts = array();
		if (!empty($dbScripts)) {
			foreach ($dbScript as $script) {
				$this->dbScripts[] = $script->getId();
			}
		}
		$this->dirty = true;
	}

	public function save() {
		global $pdo;

		if ($this->dirty) {
			$stmt = $pdo->prepare('UPDATE script_sets SET description = :description, entry_script_id = :entry_script_id WHERE id = :id;');
			$stmt->bindValue(':description', $this->description);
			$stmt->bindValue(':entry_script_id', $this->entryScriptId);
			$stmt->bindValue(':id', $this->id);
			$stmt->execute();

			$stmt2 = $pdo->prepare('DELETE FROM script_sets_scripts WHERE script_set_id = :script_set_id;');
			$stmt2->bindValue(':script_set_id', $this->id);
			$stmt2->execute();

			if (!empty($this->scriptIds)) {
				foreach ($this->scriptIds as $scriptId) {
					$stmt3 = $pdo->prepare('INSERT INTO script_sets_scripts(script_id, script_set_id) VALUES (:script_id, :script_set_id);');
					$stmt3->bindValue(':script_id', $scriptId);
					$stmt3->bindValue(':script_set_id', $this->id);

					$stmt3->execute();
				}
			}
			$this->dirty =  false;
		}
	}

}

