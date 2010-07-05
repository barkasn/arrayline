<?php
// $Id: dspAffymetrixImporter.php 138 2010-06-30 15:50:13Z barkasn $

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

class dspAffymetrixNormalisation extends lgDatasetProcessor {
	public function __construct($id) {
		parent::__construct($id);
	}

	public function getSpecialised() {
		return $this;
	}

	public function getRequiredPermissions() {
		return array();	
	}

	public function processRequest(lgRequest $lgRequest)  {
		$requestString = $lgRequest->getRequestString();
		switch($requestString) {
			case 'processdataset':
				$this->handleDatasetProcessing($lgRequest);
				break;
			case 'viewdataset':
				$this->handleDatasetViewing($lgRequest);
				break;
			default:
				throw new Exception('Unknown request string');
		}
	}

	private function handleDatasetProcessing(lgRequest $lgRequest) {

		$postArray = $lgRequest->getPostArray();
		$processorAction = empty($postArray['processoraction'])?'':$postArray['processoraction'];
		switch ($processorAction) {
			case 'execute':
				$this->doExecute($lgRequest);
				break;
			default:
				$this->showConfirmationForm($lgRequest);
				break;
		} 
	}

	private function showConfirmationForm(lgRequest $lgRequest) {
		$postarray = $lgRequest->getPostArray();


		$page = new lgCmsPage();
		$page->setTitle('Affymetrix Normalisation');
		$page->appendContent('<h2>Affymetrix Normalisation</h2>');
		$page->appendContent('<p>You are about to normalise your dataset. To proceed select the algorithm you wish to use and
select continue. This will schedule a new job for background running</p>');
		
		$form = new lgHtmlForm();
		$algorithmDropDown = new lgHtmlRawHtmlField('t','t');
		$algorithmDropDown->setValue('
			<select name="algorithm">
				<option value="gcrma">GCRMA</option>
				<option value="rma">RMA</option>
				<option value="vsnrma">VSNRMA</option>
			</select>');
		$form->addField($algorithmDropDown);
		//TODO: Add Hidden Fields

		$hiddenVals = array (
			'requeststring' => 'processdataset',
			'processorid' => $this->getId(),
			'processoraction' => 'execute',
			'datasetid' => $postarray['datasetid'],
		);
		$form->addFields(lgHtmlFormHelper::getHiddenFieldsFromArray($hiddenVals));

		$form->addField(new lgHtmlSubmitButton('submit','Normalise > '));
		$page->appendContent($form->getRenderedHtml());
		$page->render();
	}

	private function scheduleJob(lgRequest $lgRequest) {
		$postArray = $lgRequest->getPostArray();
		
		$lgJob = lgJobHelper::createNewJob('Affymetrix Normalisation');
		$lgScriptSet = lgScriptSetHelper::createScriptSet('Temporary Scriptset');

		// TODO: Select script based on algorithm specified in options
		$algorithmString = $postArray['algorithm'];
		$scriptName = $this->getNormalisationScriptByAlgorithm($algorithmString);
		$lgScript = lgScriptHelper::getScriptByInternalName($scriptName);

		$lgScriptInit = lgScriptHelper::getScriptByInternalName('affyNormaliseInit');

		$lgScriptSet->appendScript($lgScript);
		$lgScriptSet->appendScript($lgScriptInit);
		$lgScriptSet->setEntryScript($lgScriptInit);
		$lgScriptSet->forceSave(); // Required for adhoc created script sets

		$postarray = $lgRequest->getPostArray();
		$dataset = new lgDataset($postarray['datasetid']);

		$lgJob->setInputDataset($dataset);
		$lgJob->setScriptSet($lgScriptSet);

		$lgOutputDatasetState = lgDatasetStateHelper::getDatasetStateByInternalName('affymetrixNormalised');
		$lgJob->setOutputDatasetProcessState($lgOutputDatasetState);

		$lgJob->setUser(lgUserHelper::getUserFromEnviroment());
		$lgJob->setDatasetProcessor($this);

		$lgJob->schedule();
		return $lgJob;
	}

	private function getNormalisationScriptByAlgorithm($algorithmName) {
		switch($algorithmName) {
			case 'gcrma':
				return 'norm_gcrma';
				break;
			case 'rma':
				return 'norm_rma';
				break;
			case 'vsnrma':
				return 'norm_rma';
				break;
			case 'mas5':
				return 'norm_mas5';
				break;
			default:
				throw new Exception('Invalid Normalisation parameter');
		}
	}


	private function showJobScheduled(lgJob $lgJob) {
		//TODO: Fix this
		echo 'Job Scheduled';
	}

	private function doExecute(lgRequest $lgRequest) {
		$lgJob = $this->schedulejob($lgRequest);
		$this->showJobScheduled($lgJob);
	}

	private function handleDatasetViewing(lgRequest $lgRequest) {
		echo 'Dataset Viewing not implemented';
	}
}
