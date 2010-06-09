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


// Asynchronous randomisation of filename
// Used for developing asynchronous jobs classes

class dspFileNameRandomiser extends lgDatasetProcessor{
	public function __construct($id) {
		parent::__construct($id);
	}

	public function processRequest(lgRequest $lgRequest) {
		$postArray = $lgRequest->getPostArray();
		if ($postArray['processoraction'] == 'execute') {
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
		$postarray = $lgRequest->getPostArray();
		$dataset = new lgDataset($postarray['datasetid']);

		// Job scripts
		$lgScript = lgScriptHelper::getScriptByInternalName('randomizer');
		$lgScriptHelper = lgScriptHelper::getScriptByInternalName('randomizerhelper');

		$lgScriptSet = lgScriptSetHelper::createNewScriptSet('Filename randomizer job script set');
		$lgScriptSet->appendScript($lgScript);
		$lgScriptSet->appendScript($lgScriptHelper); // Just a second script that does nothing
		$lgScriptSet->setEntryScript($lgScript); // the command to be called at the command line is specified by the object

		$lgJob = lgJobHelper::createNewJob('mdlRandomizer Developement Job');
		$lgJob->setInputDataset($dataset);
		$lgJob->setScriptSet($lgScriptSet);
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
			'requeststring' => 'createdataset',
			'processorid' => $this->getId(),
			'processoraction' => 'execute',
			'datasetid' => $postarray['datasetid'],
		);
		$form->addFields(lgHtmlFormHelper::getHiddenFieldsFromArray($hiddenVals));

		$page->appendContent($form->getRenderedHTML());
		$page->render();
	}

}
