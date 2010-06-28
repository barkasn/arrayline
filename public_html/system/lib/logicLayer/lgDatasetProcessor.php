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
class lgDatasetProcessor implements iRequestHandler {
	protected $id;
	private $dbDatasetProcessor;
	
	
	public function __construct($id) {
		$this->id = $id;
		$this->dbDatasetProcessor = new dbDatasetProcessor($id);
	}
	
	public function __destruct() {
		$this->dbDatasetProcessor->save();
	}
	
	public function getId() {
		return $this->id;
	}

	public function getInternalName() {
		return $this->dbDatasetProcessor->getInternalName();
	}
	
	public function setInternalName($value) {
		$this->dbDatasetProcessor->setInternalName($value);
	}

	public function getName() {
		return $this->dbDatasetProcessor->getName();
	}

	public function setName($value) {
		$this->dbDatasetProcessor->setName($value);
	}

	public function getRequiredPermissions() {
		die('Fatal Error: Generic Dataset Processor getRequiredPermissions called.');
	}

	public function processRequest(lgRequest $lgRequest) {
		die('Fatal Error: Generic Dataset Processor processRequest called.');
	}

	// This is a critical function it returns a new object, a subclass of the lgDatasetProcessor
	// that is defined in the module and implements all the custom functionality
	public function getSpecificObject() {
		$internalName = $this->dbDatasetProcessor->getInternalName();
		$moduleClassName = 'dsp'.$internalName;
		return new $moduleClassName($this->id);
	}

	
}
