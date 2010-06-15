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

class lgReqHandlerDatasets implements iRequestHandler {
	public function __construct() {
		
	}

	public function processRequest(lgRequest $lgRequest) {
		switch($lgRequest->getRequestString()) {
			case 'viewdatasets':
				$this->processViewAllRequest($lgRequest);
				break;
			case 'deletedataset':
				$this->processDeleteDatasetRequest($lgRequest);
				break;
			case 'viewdataset':
				$this->processViewDatasetRequest($lgRequest);
				break;
			case 'createdataset':
				$this->processCreateDatasetRequest($lgRequest);
				break;
			case 'processdataset':
				$this->processProcessDatasetRequest($lgRequest);
				break;
			default:
				die('lgReqHandlerDatasets: Unknown Request');
		}
	}

	private function processProcessDatasetRequest(lgRequest $lgRequest) {
		$postData = $lgRequest->getPostArray();
		if (isset($postData['processorid'])) {
			$id = $postData['processorid'];
			$lgDatasetProcessor = new lgDatasetProcessor($id);
			$lgSpecialDatasetProcessor = $lgDatasetProcessor->getSpecificObject();
			// TODO: check permissions again
			$lgSpecialDatasetProcessor->processRequest($lgRequest);
		} else {
			$this->showDatasetProcessingSelection($lgRequest);
		}
	}

	private function showDatasetProcessingSelection(lgRequest $lgRequest) {
		$postData = $lgRequest->getPostArray();
		$lgDataset = new lgDataset($postData['datasetid']);
		$inputDatasetState = $lgDataset->getDatasetState();
		
		$availableProcessors = lgDatasetProcessorHelper::getDatasetProcessorsByAcceptState($inputDatasetState);
		$page = new lgCmsPage();
		$page->setTitle('Select Dataset Processor');
		$page->appendContent('<h2>Select Dataset Processor</h2>');
		
		if (empty($availableProcessors)) {
			$page->appendContent('No processors available for this dataset');
		} else {
			foreach ($availableProcessors as $procs) {
				$page->appendContent(
					'<a href="index.php?requeststring=' . $lgRequest->getRequestString() . 
						'&datasetid=' . $postData['datasetid'] . '&processorid=' . 
						$procs->getId().'">'.$procs->getName().'</a><br />');
			}
		}
		$page->render();	
			
	}

	private function processCreateDatasetRequest(lgRequest $lgRequest) {
		$postData = $lgRequest->getPostArray();
		if (isset($postData['processorid']) ) {
			$id = $postData['processorid'];
			$lgDatasetProcessor = new lgDatasetProcessor($id);
			$lgSpecialDatasetProcessor = $lgDatasetProcessor->getSpecificObject();
			// TODO: check permissions again
			$lgSpecialDatasetProcessor->processRequest($lgRequest);
		} else {
			$this->showDatasetCreationSelection();
		}
	}

	private function showDatasetCreationSelection() {
		$page = new lgCmsPage();
		$page->setTitle('Create New Dataset - Select Dataset Handler');
		$page->appendContent('<h2>Create New Dataset - Select Dataset Handler</h2>');

		$lgCreationProcessors = lgDatasetProcessorHelper::getDatasetCreationProcessors();
		if (empty($lgCreationProcessors)) {
			$page->appendContent('No processors for creating datasets were found. Contact the system administrator');
		} else {
			foreach($lgCreationProcessors as $pcs) {
				$page->appendContent('<a href="index.php?requeststring=createdataset&processorid='.$pcs->getId().'">'.$pcs->getName().'</a><br />');
			}	
		}
		$page->render();
	}


	private function processDeleteDatasetRequest(lgRequest $lgRequest) {
		die('Delete dataset not implemented');
	}

	private function processViewDatasetRequest(lgRequest $lgRequest) {
		$postArray = $lgRequest->getPostArray();
		$id = $postArray['datasetid'];

		if (!is_numeric($id)) {
			die('lgReqHanderDatasets: Non-numeric id');
		}

		$lgDataset = new lgDataset($id);
		$datasetProcessor = $lgDataset->getProcessor();
		$datasetProcessor = $datasetProcessor->getSpecificObject();
		$datasetProcessor->processRequest($lgRequest);
	}

	private function processViewAllRequest(lgRequest $lgRequest) {
		$postArray = $lgRequest->getPostArray();

		switch (isset($postArray['viewdisplay'])?$postArray['viewdisplay']:'simple') {
			case 'simple':
				$this->displayDatasetsSimple($lgRequest);
				break;
			case 'hierarchical':
				$this->displayDatasetsHierarchical($lgRequest);
				break;
		}
	}

	private function displayDatasetsSimple(lgRequest $lgRequest) {
		$page = new lgCmsPage();
		$page->setTitle('View Datasets');
		$page->appendContent('<h2>View Datasets</h2>');

		// TODO: Add Search Box

		$datasets = lgDatasetHelper::getAllDatasets();
		if (isset($datasets) && !empty($datasets)) {
			foreach($datasets as $dataset) {
				$page->appendContent($this->getRenderedDatasetEntry($dataset));
			}
		} else {
			$page->appendContent('No Datasets Found');
		}
		$page->render();
	}


	private function getRenderedDatasetEntry($ds) {
		$datasetEntry = '<div class="dataset-entry">';
		$datasetEntry .= '<div class="dataset-title">';
		$datasetEntry .= '<strong>'.$ds->getId().'</strong>';
		$datasetEntry .= '</div>';
		$datasetEntry .= '<div class="dataset-actions">';
		$datasetEntry .= '<a href="index.php?requeststring=viewdataset&datasetid='.$ds->getId().'">View</a> ';
		$datasetEntry .= '<a href="index.php?requeststring=processdataset&datasetid='.$ds->getId().'">Process</a> ';
		$datasetEntry .= '<a href="index.php?requeststring=deletedataset&datasetid='.$ds->getId().'">Delete</a> ';
		$datasetEntry .= '</div>';

		return $datasetEntry;
	}

	private function displayDatasetsHierarchical(lgRequest $lgRequest) {
		die('Not implemented');	
	}

	public function getRequiredPermissions() {
		return array();
	}
}
