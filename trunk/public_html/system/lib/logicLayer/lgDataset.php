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
class lgDataset {
	private $dbDataset;
	private $id;

	/*
         * Public functions
         */

	public function __construct($id) {
		$this->id = $id;
		$this->dbDataset = new dbDataset($id);
	}

	public function __destruct() {
		$this->dbDataset->save();
	}

	public function getId() {
		return $this->id;
	}

	public function getParent() {
		return lgDataset($dbDataset->getParentDatasetId());
	}

	public function getChildren() {
		$childrenLgDatasets = array();
		$childrenDbDatasets = dbDatasetHelper::getDatasetsByParent($this->dbDatatset);
		if ($childrenDbDatasets) {
			foreach($childrenDbDataset as $dbds) {
				$childrenLgDatasets[] = new lgDataset($dbds->getId());
			}
		}
		return $childrenLgDatasets;
	}

	public function getDatasetState() {
		return new lgDatasetState($this->dbDataset->getDatasetState()->getId());
	}

	public function getProcessor() {
		return new lgDatasetProcessor($dbDataset->getProcessor()->getId());
	}

	public function getMainDirectoryPath() {
		return lgDatasetHelper::getDatasetMainDirectoryPath($this);	
	}
	
	public function getMetaDirectoryPath() {
		return lgDatasetHelper::getDatasetDirectoryMetaPath($this);
	}

	public function getFilesDirectoryPath() {
		return lgDatasetHelper::getDatasetDirectoryFilesPath($this);
	}

	public function getFileList() {
		$files = scandir($this->getFilesDirectoryPath());
		return $files;	
	}

	public function addFileFromUpload($pathToFile,$newFileName) {
		// TODO: This needs to work to do proper checks before moving the file
		$newFileLocation = $this->getFilesDirectoryPath().'/'.$newFileName;
		move_uploaded_file($pathToFile,$newFileLocation);
	}

	public function computeAllChecksums() {
		$this->clearHashFile();


		$files = $this->getFileList();
		if (!empty($files)) {
			foreach($files as $f) {
				$filePath = $this->getFilesDirectoryPath().'/'.$f;
				if(is_file($filePath)) {
					$hash = $this->getFileHash($filePath);
					$this->appendHashFile($f,$hash);
				}
			}
		}
	}

	/*
	 * Private Functions
	 */

	private function getHashFilePath() {
		return $this->getMetaDirectoryPath().'/hashes.txt';	
	}

	private function clearHashFile() {
		$hashFilePath = $this->getHashFilePath();
		if (is_file($hashFilePath)) {
			unlink($hashFilePath);
		}
		touch($hashFilePath);
	}

	private function appendHashFile($filename, $hash) {
		$fp = fopen($this->getHashFilePath(), 'a');
		fwrite($fp,"$filename\t$hash\n");
		fclose($fp);
	}

	public function getFileHash($filePath) {
		return sha1_file($filePath);
	}
	
}