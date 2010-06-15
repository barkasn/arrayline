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


	// Constructor Destructor
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
	
	// Getters and Setters

	public function getId() {
		return $this->id;
	}

	public function getDataCleared() {
		return $this->dbJob->getDataCleared();
	}

	public function getUser() {
		return new lgUser($this->dbJob->getUser()->getId());
	}

	public function setUser(lgUser $lgUser) {
		$this->dbJob->setUser(new dbUser($lgUser->getId()));
	}

	public function getDatasetProcessor() {
		return new lgDatasetProcessor($this->dbJob->getDatasetProcessor()->getId());	
	}

	public function setDatasetProcessor(lgDatasetProcessor $lgDatasetProcessor) {
		$this->dbJob->setDatasetProcessor(new dbDatasetProcessor($lgDatasetProcessor->getId()));
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

	public function checkRunComplete() 
	{
		if($this->currentJobStatusString() == 'processRunning' &&
				$this->jobCompleteFileExists()) {
			$this->setToBePostprocessed();
			return true;
		}
		return false;
	}

	public function postProcess() {
		//TODO: set state to post-processing at start (and force save)

		$lgNewDatasetState = new lgDatasetState($this->dbJob->getOutputDatasetProcessState()->getId());
		$lgOwnerUser = new lgUser($this->dbJob->getUser()->getId());
		$lgDatasetProcessor = new lgDatasetProcessor($this->dbJob->getDatasetProcessor()->getId());
		
		$lgNewDataset = lgDatasetHelper::createDataset(
			$this, // The Job which created the dataset
			$this->getInputDataset(), // The input dataset
			$lgNewDatasetState, // The new dataset state dictated by the module at job creation time 
			$lgOwnerUser, // The owner of this dataset
			$lgDatasetProcessor  // the dsp that set this job up
		);

		$lgNewDataset->copyDataFrom($this->getOutputDataDirectoryPath());
		$lgNewDataset->computeAllChecksums();

		$this->setComplete();
		$this->clearData();
	}

	// Private functions

	private function setDataCleared($value) {
		$this->dbJob->setDataCleared($value);
	}

	private function clearData() {
		if (! $this->getDataCleared() ) {
			$inputDirPath = $this->getInputDataDirectoryPath();	
			$outputDirPath = $this->getOutputDataDirectoryPath();
	
			$this->emptyDirectory($inputDirPath);
			$this->emptyDirectory($outputDirPath);

			$this->setDataCleared(true);
		} else {
			throw new Exception('The data of this job have already been cleared');
		}
	}

	private function emptyDirectory($dirPath) {
		if ($input_dir = opendir($dirPath)) {
       			while ($file = readdir($input_dir)) {
				$filePath = $dirPath.'/'.$file;
				if (is_file($filePath)) {
					unlink($filePath);
                              	} 
			}
		} else {
			throw new Exception('An error occured while attempting to read directory '.$input_dir);
		}
	}


	private function setComplete() {
		$dbCompleteJobState = dbJobStateHelper::getJobStateByInternalName('complete');
		$this->dbJob->setJobState($dbCompleteJobState);
	}

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
		$scriptFullPath = $this->getScriptFullPath($lgScript);

		$scriptBasePath = dirname($scriptFullPath).'/';
		if (!is_writable($scriptBasePath)) {
			throw new Exception('The webserver does not have permission to save the script in: ' . $scriptBasePath);
		} else {
			if ( $fp = fopen($scriptFullPath, 'w')) {
				$scriptBody = str_replace("\r\n","\n", $lgScript->getBody());
				fwrite($fp,$scriptBody);
				fclose($fp);

				// wrx for user, rx for others
				chmod($scriptFullPath, 0755);
			} else {
				throw new Exception('An error occured while attempting to open file '.$scriptFullPath.' for writing');
			}
		}
	}

	private function getScriptFullPath(lgScript $lgScript) {
		$scriptDir = $this->getScriptDirectoryPath();
		$filename = $lgScript->getScriptFilename();
		$scriptFullPath = $scriptDir.'/'.$filename;

		return $scriptFullPath;
	}
}

