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

class dspAffymetrixImporter extends lgDatasetProcessor {
	public function __construct($id) {
		parent::__construct($id);
	}

	public function processRequest(lgRequest $lgRequest) {
		$postArray = $lgRequest->getPostArray();
		if (isset($postArray['processoraction']) &&
				$postArray['processoraction'] == 'execute') {
			$this->scheduleJob($lgRequest);
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
		$page->setTitle('Affymetrix Importer');
		$page->appendContent('<h2>Affymetrix Importer</h2>');
		$page->appendContent('<p>You are about to schedule a background job that will convert your input dataset to an Biovconductor object. Are you sure you want to continue?</p>');

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
		$lgJob = lgJobHelper::createNewJob('Affymetrix Importer Background Job');

		$lgScriptSet = lgScriptSetHelper::createScriptSet('Temporary Scriptset');

		// Get the scripts here
		$lgScript = lgScriptHelper::getScriptByInternalName('');

		$lgScriptSet->appendScript($lgScript);
		$lgScriptSet->setEntryScript($lgScript);
		$lgScriptSet->forceSave(); // Required for adhoc created script sets

		$postarray = $lgRequest->getPostArray();
		$dataset = new lgDataset($postarray['datasetid']);

		$lgJob->setInputDataset($dataset);
		$lgJob->setScriptSet($lgScriptSet);

		$lgOutputDatasetState = lgDatasetStateHelper::getDatasetStateByInternalName('AffymetrixImportedData');
		$lgJob->setOutputDatasetProcessState($lgOutputDatasetState);

		$lgJob->setUser(lgUserHelper::getUserFromEnviroment());
		$lgJob->setDatasetProcessor($this);

		$lgJob->schedule();
	}

}
	
