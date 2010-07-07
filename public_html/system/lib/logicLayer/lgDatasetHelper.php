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

class lgDatasetHelper {
	public static function getAllDatasets() {
		$lgDatasets = array();
		$dbDatasets = dbDatasetHelper::getAllDatasets();
		return self::getLgFromDbDatasets($dbDatasets);
	}

	public static function getRootDatasets() {
		$lgDatasets = array();
		$dbDatasets = dbDatasetHelper::getRootDatasets();
		return self::getLgFromDbDatasets($dbDatasets);
	}

	private static function getLgFromDbDatasets($dbDatasets) {
		$lgDatasets = array();
		if ($dbDatasets) { 
			foreach ($dbDatasets as $dbds) {
				$lgDatasets[] = new lgDataset($dbds->getId());
			}
		}
		return $lgDatasets;
	}


	public static function createDataset($lgJob, $lgParentDataset, $lgDatasetState, $lgOwner, $lgDatasetProcessor) {

		$jobId = ($lgJob != NULL)?$lgJob->getId():NULL;
		$parentDatasetId = ($lgParentDataset != NULL)?$lgParentDataset->getId():NULL;
		$datasetStateId = ($lgDatasetState != NULL)?$lgDatasetState->getId():NULL;
		$ownerId = $lgOwner->getId();
		$datasetProcessorId = $lgDatasetProcessor->getId();
	
		// Create the database record
		$dbDataset = dbDatasetHelper::createDataset($jobId, $parentDatasetId, $datasetStateId, $ownerId, $datasetProcessorId);
		$lgDataset = new lgDataset($dbDataset->getId());
		$now = date('Y-m-d H:i');
		$lgDataset->setCreated($now);

		// And the corresponding directory
		self::createDatasetDirectoryStructure($lgDataset);
		
		return $lgDataset;
	}

        public static function getDatasetMainDirectoryPath(lgDataset $lgDataset) {
                global $basepath;
                global $datastoreroot;

                $datastoreAbsoluteRoot = $basepath.$datastoreroot;
                $datasetDirectory = $datastoreAbsoluteRoot.$lgDataset->getId();

                return $datasetDirectory;
        }

	public static function getDatasetDirectoryFilesPath(lgDataset $lgDataset) {
		$datasetDirectoryFilesPath = self::getDatasetMainDirectoryPath($lgDataset).'/files';
		return $datasetDirectoryFilesPath;
	}

	public static function getDatasetDirectoryMetaPath(lgDataset $lgDataset) {
		$datasetDirectoryMetaPath = self::getDatasetMainDirectoryPath($lgDataset).'/meta';
		return $datasetDirectoryMetaPath;
	}

	public static function createDatasetDirectoryStructure(lgDataset $lgDataset) {
		// TODO: Improve error handling
		try {
			mkdir($lgDataset->getMainDirectoryPath());
			mkdir($lgDataset->getFilesDirectoryPath());
			mkdir($lgDataset->getMetaDirectoryPath());
		} catch (Exception $e) {
			die('An error occured while attempting to create directory for new dataset: '. $e->getMessage());
		}
	}



}


