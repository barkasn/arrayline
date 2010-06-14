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

	public function getOutputDatasetProcessState() {
		return new lgDatasetState($this->dbJob->getOutputDatasetProcessState()->getId());
	}

	public function setOutputDatasetProcessState(lgDatasetState $lgDatasetState) {
		$this->dbJob->setOutputDatasetProcessState(new dbDatasetState($lgDatasetState->getId()));
	}

	// Dataset getters and setters
	public function setInputDataset(lgDataset $lgDataset) {
		$this->dbJob->setInputDataset(new dbDataset($lgDataset->getId()));
	}

	public function getInputDataset() {
		return new lgDataset($this->dbJob->getInputDataset()->getId());
	}

	public function setOutputDatase(lgDataset $lgDataset) {
		$this->dbJob->setInputDataset(new dbDataset($lgDataset->getId()));
	}

	public function getOutputDataset() {
		return new lgDataset($this->dbJob->getOutputDataset()->getId());
	}

	// Script set setter
	public function setScriptSet(lgScriptset $lgScriptSet) {
		$this->dbJob->setScriptSet(new dbScriptSet($lgScriptSet->getId()));
	}


	// Directory path getters
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


	// Misc public fucntions
	public function schedule() {
		$this->saveScripts();
		$this->saveInputDataset();
		$this->setToRun();
	}

	public function beginRun() {
		$dbScriptSet = $this->dbJob->getScriptSet();	
		$dbEntryScript = $dbScriptSet->getEntryScript();
		$lgEntryScript = new lgScript($dbEntryScript->getId());
		$entryPath = $this->getScriptFullPath($lgEntryScript);

		$scriptsDir = $this->getScriptDirectoryPath();
		if (!chdir($scriptsDir) ) {
			//TODO: Manage the error
		} else {
			$command = '. '.$entryPath.' > ../joblog.txt &';
			exec($command);
			$this->setRunning();
		}
	}

	// This is called by the job schedulaer
	// The function checks if the process is in the running state
	// and if so, whether the scripts have terminated (by checking for
	// the existence of the lock file) if so, it sets it to be
	// post processed
	public function checkRunComplete() 
	{
		if($this->currentJobStatusString() == 'processRunning' &&
				$this->jobCompleteFileExists()) {
			$this->setToBePostprocessed();
			return true;
		}
		return false;
	}

	// This is called by the job scheduler
	// It executes the postprocessing steps which	
	// are common for all functions
	public function postProcess() {
		// TODO: Implement
		// What do we need to do here?

		// 1. Create a new dataset

		// TODO: Allow module to dictate the output dataset state at job building time
		// it is important to allow the datasetprocessor to do this because some processors
		// may potentially be able to output more than one type of dataset
		$lgDatasetState = new lgDatasetState(1);
		
		//$lgNewDataset = lgDatasetHelper::createDataset($this, $this->getInputDataset(), 

		// 2. Save the data in it
		// 3. Set Job status to complete
		// 4. Delete all the data in this set	(recoverable through references)

	}

	// Private functions
	private function currentJobStatusString() {
		return $this->dbJob->getJobState()->getInternalName();
	}

	private function jobCompleteFileExists() {
		$completeJobFilePath = $this->getMainDirectoryPath().'/JOB_COMPLETE';
		if (is_file($completeJobFilePath)) {
			return true;
		}
		return false;
	}

	private function saveInputDataset() {
		$inputDir = $this->getInputDataDirectoryPath();
		$lgInputDataset = new lgDataset($this->dbJob->getInputDataset()->getId());
		$lgInputDataset->copyData($inputDir);
	}

	private function setToBePostprocessed() {
		$dbRunningCompleteJobState = dbJobStateHelper::getJobStateByInternalName('processComplete');
		$this->dbJob->setJobState($dbRunningCompleteJobState);

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

