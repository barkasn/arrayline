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

class lgScriptSet {
	private $id;
	private $dbScriptSet;

	public function __construct($id) {
		if (! $this->dbScriptSet = new dbScriptSet($id)) {
			die('lgScriptSet: Database record not found');
		}
		$this->id = $id;
	}

	public function __destruct() {
		$this->dbScriptSet->save();
	}

	public function forceSave() {
		$this->dbScriptSet->save();
	}

	public function getId() {
		return $this->id;
	}

	public function appendScript(lgScript $lgScript) {
		$this->dbScriptSet->appendScript(new dbScript($lgScript->getId()));
	}

	public function setEntryScript(lgScript $lgScript) {
		$this->dbScriptSet->setEntryScript(new dbScript($lgScript->getId()));
	}

	public function getAllScripts() {
		$dbScripts = $this->dbScriptSet->getScripts();
		$lgScripts  = array();
		if (!empty($dbScripts)) {
			foreach ($dbScripts as $script) {
				$lgScripts[] = new lgScript($dbScript->getId());
			}
		}
		return $lgScripts;
	}

}
