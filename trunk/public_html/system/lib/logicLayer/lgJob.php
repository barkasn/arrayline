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
		if ( $id === NULL ) {
			die('lgJob: invalid id');
		} else {
			$this->dbJob = new dbJob($id);
			$this->id = $id;
		}
	
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

	public function getMainDirectoryPath() {
		return lgJobHelper::getJobMainDirectoryPath($this);
	}

	public function getInputDataDirectoryPath() {
		return lgJobHelper::getJobInputDataDirectoryPath($this);
	}

	public function getOutputDataDirectoryPath() {
		return lgJobHelper::getJobOutputDataDirectoryPath($this);
	}

	public function getScriptDirectoryPath() {
		return lgJobHelper::getJobScriptDirectoryPath($this);
	}

	public function getLogFilePath() {
		return lgJobHelper::getJobLogFilePath($this);
	}

	public function schedule() {
		$this->saveScripts();
		$this->saveInputDataset();
		$this->setToRun();
	}

	public function beginRun() {
		//TODO: Implement this
	}

	public function checkRunComplete() {
		//TODO: Implement this
		// check the lock file
	}

	public function postProcess() {
		// Create new dataset
		// and place resulting data there
		// then delete input and output data
	}

	// Private functions
	private function saveInputDataset() {
		$inputDir = $this->getInputDataDirectoryPath();
		$this->inputDataset->copyData($inputDir);
	}

	private function setToRun() {
		$dbSetToRunJobState = dbJobStateHelper::getJobStateByInternalName('toBeRun');
		$this->dbJob->setJobState($dbSetToRunJobState);
	}

	private function saveScripts() {
		$lgScripts = $this->scriptSet->getAllScripts();

		if (!empty($lgScripts)) {
			foreach ($lgScripts as $lgScript) {
				$this->saveScript($lgScript);			
			}
		}
	}

	private function saveScript(lgScript $lgScript) {
		$scriptDir = $this->getScriptDirectoryPath();
		$filename = $lgScript->getScriptFilename();

		$scriptFullPath = $scriptDir.'/'.$filename;
		$scriptBody = $lgScript->getBody();

		// Remove carriage returns from script body
		// when followed by new line
		$scriptBody = str_replace("\r\n","\n", $scriptBody);

		$fp = fopen($scriptFullPath, 'w');
		fwrite($fp,$scriptBody);
		fclose($fp);
	}
}

