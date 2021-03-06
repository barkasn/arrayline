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

class dspAffymetrixRawQC extends lgDatasetProcessor {
	public function __construct($id) {
		parent::__construct($id);
	}

	public function processRequest(lgRequest $lgRequest) {
		$requestString = $lgRequest->getRequestString();
		switch ($requestString) {
			case 'processdataset':
				$this->handleProcessRequest($lgRequest);
				break;
			case 'viewdataset': 
				$this->handleViewRequest($lgRequest);
				break;
			default:
				throw new Exception('Unknown request');
				break;
		}
	}
		
	private function handleViewRequest($lgRequest){
		echo 'View Dataset not implemented';

	}

	private function handleProcessRequest(lgRequest $lgRequest) {
		$postArray = $lgRequest->getPostArray();
		if (isset($postArray['processoraction']) &&
				$postArray['processoraction'] == 'execute') {
			$this->doExecute($lgRequest);
		} else {
			$this->showConfirmationForm($lgRequest);
		}
	}

	public function getSpecialised() {
		return $this;
	}

	public function getRequiredPermissions() {
		return array();
	}

	private function showConfirmationForm(lgRequest $lgRequest) {
		$postarray = $lgRequest->getPostArray();
		$page = new lgCmsPage();
		$page->setTitle('Affymetrix Raw QC');
		$page->appendContent('<h2>Affymetrix Raw QC</h2>');
		$page->appendContent('<p>Are you sure you want to continue?</p>');

		$form = new lgHtmlForm();
		$field = new lgHtmlSubmitButton('submit','Continue >');
		$field->setValue('submit');
		$form->addField($field);

		$hiddenVals = array (
			'requeststring' => 'processdataset',
			'processorid' => $this->getId(),
			'processoraction' => 'execute',
			'datasetid' => $postarray['datasetid'],
		);
		$form->addFields(lgHtmlFormHelper::getHiddenFieldsFromArray($hiddenVals));

		$page->appendContent($form->getRenderedHTML());
		$page->render();

	}

	private function scheduleJob(lgRequest $lgRequest) {
		$lgJob = lgJobHelper::createNewJob('Affymetrix Raw QC Background Job');

		$lgScriptSet = lgScriptSetHelper::createScriptSet('Temporary Scriptset');

		// Get the scripts here
		$lgScript = lgScriptHelper::getScriptByInternalName('affyRawQCRscript');
		$lgScriptInit = lgScriptHelper::getScriptByInternalName('affyRawQCInit');

		$lgScriptSet->appendScript($lgScript);
		$lgScriptSet->appendScript($lgScriptInit);
		$lgScriptSet->setEntryScript($lgScriptInit);
		$lgScriptSet->forceSave(); // Required for adhoc created script sets

		$postarray = $lgRequest->getPostArray();
		$dataset = new lgDataset($postarray['datasetid']);

		$lgJob->setInputDataset($dataset);
		$lgJob->setScriptSet($lgScriptSet);

		$lgOutputDatasetState = lgDatasetStateHelper::getDatasetStateByInternalName('affymetrixRawQC');
		$lgJob->setOutputDatasetProcessState($lgOutputDatasetState);

		$lgJob->setUser(lgUserHelper::getUserFromEnviroment());
		$lgJob->setDatasetProcessor($this);

		$lgJob->schedule();
		return $lgJob;
	}

	private function doExecute($lgRequest) {
		$lgJob = $this->schedulejob($lgRequest);
		$this->showJobScheduled($lgJob);
	}

	private function showJobScheduled(lgJob $lgJob) {
		$page = new lgCmsPage();
		$page->setTitle('Affymetrix Raw Data QC');
		$page->appendContent('<h2>Raw Data QC - Job Scheduled</h2>');
		$page->appendContent('<p>A background job has been scheduled. The new dataset will appear in the dataset listing when ready</p>');
		$page->render();
	}

}
	
