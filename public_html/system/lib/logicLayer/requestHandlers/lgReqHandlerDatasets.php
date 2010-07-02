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
			case 'renamedataset':
				$this->processRenameDatasetRequest($lgRequest);
				break;
			default:
				throw new Exceptions('Unknown request string');
		}
	}


	private function processRenameDatasetRequest(lgRequest $lgRequest) {
		$postArray = $lgRequest->getPostArray();
		if (!empty($postArray['execute'])) {
			$this->doRename($lgRequest);
		} else {
			$this->showRenameForm($lgRequest);
		}
	}

	private function doRename(lgRequest $lgRequest) {
		$postArray = $lgRequest->getPostArray();
		$lgDataset = new lgDataset($postArray['datasetid']);
		
		$newName = $postArray['newname'];	
		$lgDataset->setName($newName);

                $postLogoutPage = new lgPage();
                $postLogoutPage->setRedirect('index.php?requeststring=viewdatasets', 0);
                $postLogoutPage->render();


	}

	private function showRenameForm($lgRequest) {
		$postArray = $lgRequest->getPostArray();
		$lgDataset = new lgDataset($postArray['datasetid']);

		$page = new lgCmsPage();
		$page->setTitle('Rename Dataset');
		$page->appendContent('<h2>Rename Dataset</h2>');
		// TODO: Show some info about dataset

		$form = new lgHtmlForm();
		$form->addField(new lgHtmlTextField('newname','New Name:'));
		$form->addField(new lgHtmlSubmitButton('submit','Change name >'));



		$requestStringField = new lgHtmlHiddenField('requeststring','requeststring');
		$requestStringField->setValue('renamedataset');
		$form->addField($requestStringField);

		$datasetIdField = new lgHtmlHiddenField('datasetid','datasetid');
		$datasetIdField->setValue($lgDataset->getId());
		$form->addField($datasetIdField);

		$formExecField = new lgHtmlHiddenField('execute','execute');
		$formExecField->setValue('1');
		$form->addField($formExecField);

		$page->appendContent($form->getRenderedHtml());
		
		$page->render();
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
		$page->appendContent('<h2>Create New Dataset: Select Dataset Handler</h2>');
		$page->appendContent('<p class="notice">Creating a new dataset requires that you use a specific handler
			particular to the dataset type you wish to create. Below you can find the available dataset
			handlers in this installation of arrayline. If you cannot find the handler you required,
			please contact your system administrator.</p>');

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
		$page = new lgCmsPage();
		$page->setTitle('Delete Dataset');
		$page->appendContent('Delete Dataset Not implemented');
		$page->render();
	}

	private function processViewDatasetRequest(lgRequest $lgRequest) {
		$postArray = $lgRequest->getPostArray();
		$id = $postArray['datasetid'];
		$showSpecific = empty($postArray['showspecific'])?false:true;

		$lgDataset = new lgDataset($id);
		if ($showSpecific) {
			$datasetProcessor = $lgDataset->getProcessor();
			$datasetProcessor = $datasetProcessor->getSpecificObject();
			$datasetProcessor->processRequest($lgRequest);
		} else {
			$this->showDatasetGeneric($lgDataset);
		}
	}

	private function showDatasetGeneric(lgDataset $lgDataset) {
		$page = new lgCmsPage();
		$page->setTitle('Show Dataset');
		$page->appendContent('<h2>Show Dataset</h2>');
		
		$page->appendContent($this->getGeneralInfoHtml($lgDataset));
		$page->appendContent($this->getFileListingHtml($lgDataset));
			
		$page->render();
	}

	private function getGeneralInfoHtml(lgDataset $lgDataset) {
		$id = $lgDataset->getId();
		$name = $lgDataset->getName();
		$owner = $lgDataset->getUser()->getUsername();
		$date = $lgDataset->getCreated();

		$html=<<<EOE
			<div class="general-info">
				<h3>General Dataset Information</h3>
				<p>Unique Id: $id</p>
				<p>Name: $name</p>
				<p>Owner user: $owner</p>
				<p>Date created: $date</p>
			</div>
EOE;
		return $html;
	}

	private function getFileListingHtml(lgDataset $lgDataset) {
		$filesInfo = $lgDataset->getFilesInfo();

		$html = '<div class="file-listing"> ';
		$html .= '<h3>File Listing</h3>';
		
		if (!empty($filesInfo)) {
			$i = 0;
			foreach ($filesInfo as $fileRecord) {
				$zebra = $i++%2?'odd':'even';
				$html .= '<div class="file-record '.$zebra.'">';
				$html .= '<span class="filename"><a href="'.$fileRecord['url'].'">'.$fileRecord['filename'].'</a></span>';
				$html .= '<span class="size">'.lgGeneralHelper::formatFileSize($fileRecord['filesize']).'</span>';
				$html .= '</div>';
			}
		} else {
			$html = '<p>No files found</p>';
		}
		$html .= '</div>';

		return $html;
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
		$page->appendContent($this->getSearchBoxHtml());
		
		$datasets = lgDatasetHelper::getAllDatasets();
		if (isset($datasets) && !empty($datasets)) {
			$page->appendContent('<div class="dataset-listing">');
			$i = 1;
			foreach($datasets as $dataset) {
				$page->appendContent($this->getRenderedDatasetEntry($dataset, ($i++%2?'odd':'even')));
 
			}
			$page->appendContent('</div>');
		} else {
			$page->appendContent('No Datasets Found');
		}
		$page->render();
	}

	private function getSearchBoxHtml() {
		$searchBoxHtml=<<<EOE
			<div class="search-box">
				<h3>View Settings</h3>
				<form action="index.php" method="post">
					<p>View Style</p>
					Simple<input type="radio" name="viewdisplay" value="simple" /><br />
					Hierarchical<input type="radio" name="viewdisplay" value="hierarchical" /><br />
					<input type="hidden" name="requeststring" value="viewdatasets" />
					<input type="submit" value="Apply"/>
				</form>
			</div>
		
EOE;
		return $searchBoxHtml;
	}

	private function getRenderedDatasetEntry($lgDataset, $class = '', $childrenHtml='') {
		$lgDatasetState = $lgDataset->getDatasetState();
		$lgUser = $lgDataset->getUser();

		$datasetId = $lgDataset->getId();
		$datasetName = $lgDataset->getName();
		$datasetStateName = $lgDatasetState->getName();
		$datasetCreated = $lgDataset->getCreated();
		$userId = $lgUser->getid();
		$userRealName = $lgUser->getRealName();

		$datasetEntry =<<<EOE
			<div class="dataset-entry $class">
				<div class="dataset-entry-main" >
					<div class="dataset-title">$datasetId <span class="name">$datasetName</span></div>
					<div class="dataset-info">
						<ul>
							<li>Dataset type: $datasetStateName</li>
							<li>Created by: <a href="index.php?requeststring=viewuser&userid=$userId">$userRealName</a></li>
							<li>Created on: $datasetCreated</li>
						</ul>
					</div>
					<div class="dataset-actions">
						<ul>
							<li><a href="index.php?requeststring=renamedataset&datasetid=$datasetId">Rename</a></li>
							<li><a href="index.php?requeststring=viewdataset&datasetid=$datasetId">View</a></li>
							<li><a href="index.php?requeststring=processdataset&datasetid=$datasetId">Process</a></li>
							<li><a href="index.php?requeststring=deletedataset&datasetid=$datasetId">Delete</a></li> 
						</ul>
					</div>
				</div>
				<div class="children">
					$childrenHtml
				</div>
			</div>
EOE;

		return $datasetEntry;
	}

	private function displayDatasetsHierarchical(lgRequest $lgRequest) {
		$outputHtml = ' ';

		$datasets = lgDatasetHelper::getRootDatasets();
                if (isset($datasets) && !empty($datasets)) {
			$i = 1;
                        foreach($datasets as $lgDataset) {
				$outputHtml .= $this->getDatasetHtmlHierarchical($lgDataset,($i++%2?'odd':'even'));
                        }
                } else {
                        $outputHtml = '<p>No datasets found</p>';
                }
		
                $page = new lgCmsPage();
                $page->setTitle('View Datasets');
                $page->appendContent('<h2>View Datasets</h2>');
                $page->appendContent($this->getSearchBoxHtml());
		$page->appendContent('<div class="dataset-listing">'.$outputHtml.'</div>');
		$page->render();
	}
	
	private function getDatasetHtmlHierarchical(lgDataset $lgDataset, $zebra= '') {
		$childrenDatasets = $lgDataset->getChildren();
		$childrenHtml = ' ';

		if (isset($childrenDatasets) && !empty($childrenDatasets)) {
			$i = 1;
			foreach($childrenDatasets as $lgDatasetChild) {
				$childrenHtml .= $this->getDatasetHtmlHierarchical($lgDatasetChild,($i++%2?'odd':'even'));
			}
		}

		return $this->getRenderedDatasetEntry($lgDataset, $zebra, $childrenHtml);
				
	}

	public function getRequiredPermissions() {
		return array();
	}
}
