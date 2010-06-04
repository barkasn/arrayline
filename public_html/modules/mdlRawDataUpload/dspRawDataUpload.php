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

class dspRawDataUpload extends lgDatasetProcessor {
	public function __construct($id) {
		parent::__construct($id);
	}

	public function processRequest(lgRequest $lgRequest) {
		$postArray = $lgRequest->getPostArray();
		if (empty($postArray['processoraction'])) {
			$this->showUploadForm();
		} else {
			$this->processFiles();
		}
	}

	public function getSpecialised() {
		return $this;
	}

	public function getRequiredPermissions() {
		return array();
	}

	private function processFiles() {
		//Create new dataset in the logic level

		// First we need to find the appropriate datasettype
		$lgDatasetState = lgDatasetStateHelper::getDatasetStateByInternalName('rawData');
		$lgUser = lgUserHelper::getUserFromEnviroment();
				
		$lgDataset = lgDatasetHelper::createDataset(NULL, NULL, $lgDatasetState, $lgUser, $this);

		$i = 1;
		while(is_uploaded_file($_FILES['file'.$i]['tmp_name'])) {
			$lgDataset->addFileFromUpload($_FILES['file'.$i]['tmp_name'],$_FILES['file'.$i]['name']);
			$i++;
		}
		$lgDataset->computeAllChecksums();

	}


	private function showUploadForm() {
		$page = new lgCmsPage();
		$page->setTitle('Create Dataset - Raw Upload');
		$page->appendContent('<h2>Raw Upload</h2>');

		$form = new lgHtmlForm();
		$form->setEnctype('multipart/form-data');

		$i = 0;
		while ($i++ < 6) {
			$fileField = new lgHtmlFileField('file'.$i);
			$form->addField($fileField);
		}

		$form->addField(new lgHtmlSubmitButton('submit','submit'));

		$hiddenVals = array (
			'requeststring' => 'processdataset',
			'processorid' => $this->getId(),
			'processoraction' => 'submitfiles'
		 );

		$form->addFields(lgHtmlFormHelper::getHiddenFieldsFromArray($hiddenVals));
		$page->appendContent($form->getRenderedHtml());
		$page->render();
	}


}
	
