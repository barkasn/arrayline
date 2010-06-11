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
		$this->dbJob->setScriptSet(new dbScriptSet($lgScriptSet->getId()));
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
		// 1. Find Job Entry Script

		$dbScriptSet = $this->dbJob->getScriptSet();	
		$dbEntryScript = $dbScriptSet->getEntryScript();
		$lgEntryScript = new lgScript($dbEntryScript->getId());
		$entryPath = $this->getScriptFullPath($lgEntryScript);

		// 2. Change to the scripts directory and start job
		$scriptsDir = $this->getScriptDirectoryPath();
		chdir($scriptsDir);
		$command = '. '.$entryPath.' > ../joblog.txt &';
		exec($command);
	
		// 3. Update Job state
		$this->setRunning();
	}

	public function checkRunComplete() {
		// TODO: Implement
	}

	public function postProcess() {
		//TODO: Implement
	}

	// Private functions
	private function saveInputDataset() {
		$inputDir = $this->getInputDataDirectoryPath();
		$this->inputDataset->copyData($inputDir);
	}

	private function setRunning() {
		$dbSetRunningJobState = dbJobStateHelper::getJobStateByInternalName('processRunning');
		$this->dbJob->setJobState($dbSetRunningJobState);
	}

	private function setToRun() {
		$dbSetToRunJobState = dbJobStateHelper::getJobStateByInternalName('toBeRun');
		$this->dbJob->setJobState($dbSetToRunJobState);
	}

	private function saveScripts() {
		$dbScriptSet = $this->dbJob->getScriptSet();
		$dbScripts = $dbScriptSet->getScripts();

		if (!empty($dbScripts)) {
			foreach ($dbScripts as $dbScript) {
				$lgScript = new lgScript($dbScript->getId());
				$this->saveScript($lgScript);			
			}
		}
	}

	private function saveScript(lgScript $lgScript) {
		//TODO: Implement proper error handling

		$scriptFullPath = self::getScriptFullPath($lgScript);
		$scriptBody = str_replace("\r\n","\n", $lgScript->getBody());

		$fp = fopen($scriptFullPath, 'w');
		fwrite($fp,$scriptBody);
		fclose($fp);

		// wrx for user, rx for others
		chmod($scriptFullPath, 0755);
	}

	private function getScriptFullPath(lgScript $lgScript) {
		$scriptDir = $this->getScriptDirectoryPath();
		$filename = $lgScript->getScriptFilename();
		$scriptFullPath = $scriptDir.'/'.$filename;

		return $scriptFullPath;
	}
}

