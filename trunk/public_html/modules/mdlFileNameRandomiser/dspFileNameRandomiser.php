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


// Asynchronous randomisation of filename
// Used for developing asynchronous jobs classes

class dspFileNameRandomiser extends lgDatasetProcessor{
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

	private function scheduleJob(lgRequest $lgRequest) {
		$lgJob = lgJobHelper::createNewJob('mdlRandomizer Developement Job');

		$lgScriptSet = lgScriptSetHelper::createScriptSet('Filename randomizer job script set');

		$lgScript = lgScriptHelper::getScriptByInternalName('randomizer');
		$lgScript2 = lgScriptHelper::getScriptByInternalName('randomizerhelper');

		$lgScriptSet->appendScript($lgScript);
		$lgScriptSet->appendScript($lgScript2);
		$lgScriptSet->setEntryScript($lgScript);
		$lgScriptSet->forceSave(); // Required for adhoc created script sets

		// TODO: Add checks here
		$postarray = $lgRequest->getPostArray();
		$dataset = new lgDataset($postarray['datasetid']);
		$lgJob->setInputDataset($dataset);

		$lgJob->setScriptSet($lgScriptSet);
		$lgOutputDatasetState = lgDatasetStateHelper::getDatasetStateByInternalName('randomizedData');
		$lgJob->setOutputDatasetProcessState($lgOutputDatasetState);
		$lgJob->setUser(lgUserHelper::getUserFromEnviroment());
		$lgJob->setDatasetProcessor($this);

		$lgJob->schedule();
	}

	private function showConfirmationForm(lgRequest $lgRequest) {
		$postarray = $lgRequest->getPostArray();
		$page = new lgCmsPage();
		$page->setTitle('File Name Randomizer');
		$page->appendContent('<h2>File Name Randomizer</h2>');
		$page->appendContent('<p>Are you sure you want to proceed</p>');

		$form = new lgHtmlForm();
		$field = new lgHtmlSubmitButton('submit','Yes!');
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

}
