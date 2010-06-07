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

class lgJob {
	private $dbJob;
	private $id;
	private $inputDataset;
	private $scriptSet;

	public function __construct($id) {
		$this->dbJob = new dbJob($id);
		$this->id = $id;
	}

	public function __destruct() {
		$this->dbJob->save();
	}

	public function getId() {
		return $this->id;
	}

	public function setInputDataset(lgDataset $lgDataset) {
		$this->inputDataset = $lgDataset;
	}

	public function setScriptSet(lgScriptset $lgScriptSet) {
		$this->scriptSet = $lgScriptSet;
	}

	public function schedule() {
		
	}
}

