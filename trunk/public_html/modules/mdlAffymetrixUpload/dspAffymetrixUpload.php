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

class dspAffymetrixUpload extends lgDatasetProcessor {
	private $datasetId; // The id of the newly created dataset

	public function __construct($id) {
		parent::__construct($id);
	}

	public function processRequest(lgRequest $lgRequest) {
		$postArray = $lgRequest->getPostArray();
		if (empty($postArray['processoraction'])) {
			$this->showIntroForm($lgRequest);
		} else  if ($postArray['processoraction'] == 'help') {
			$this->showHelpPage($lgRequest);
		} else if ($postArray['processoraction'] == 'createdataset') {
			$this->createNewDataset($lgRequest);	
		} else if ($postArray['processoraction'] == 'selectaction') {
			$this->processActionSelection($lgRequest);
		} else if ($postArray['processoraction'] == 'doFinalise') {
			$this->doFinalise($lgRequest);
		} else if ($postArray['processoraction'] == 'doUploadCel') {
			$this->doUploadCelFile($lgRequest);
		} else if ($postArray['processoraction'] == 'doUploadCov') {
			$this->doUploadCovFile($lgRequest);
		} else {
			die('Undefined processor action!');
		}
	}

	public function getSpecialised() {
		return $this;
	}

	private function showHelpPage(lgRequest $lgRequest) {
		$page = new lgCmsPage();
		$page->setTitle('Create Dataset - Affymetrix Upload - Help');
		$page->appendContent('<h2>Affymetrix Upload Help</h2>');
		$page->appendContent('<p>Affymetric Upload Help goes here</p>');
		$page->render();
	}

	private function showIntroForm(lgRequest $lgRequest) {
		$page = new lgCmsPage();
		$page->setTitle('Create Dataset - Affymetrix Upload');
		$page->appendContent('<h2>Affymetrix Upload</h2>');

		$page->appendContent('<p class="notice">To create a new Affymetrix
		 dataset you need to upload your experimental data in .CEL file format
		 as well as a covariates files which provides information on biological
 		and technical replicates. For more information on preparing a covariates
		 file see the <a href="index.php?requeststring=createdataset&processorid='.
		$this->getId().'&processoraction=help">help</a> page.</p>');


		$page->appendContent('<h4>Create Dataset</h4>');
		$page->appendContent('<p>To create a new dataset click the button bellow and proceed to upload the files</p>');

		$form = new lgHtmlForm();
		$form->addField(new lgHtmlSubmitButton('createdataset','Create Dataset'));

                $hiddenVals = array (
                        'requeststring' => 'createdataset',
                        'processorid' => $this->getId(),
			'processoraction' => 'createdataset'
                 );

		$form->addFields(lgHtmlFormHelper::getHiddenFieldsFromArray($hiddenVals));
		$page->appendContent($form->getRenderedHtml());
		$page->render();
	}

	private function createNewDataset(lgRequest $lgRequest) {
		$this->createNewDatasetStructures();
		$this->showMainSelectionForm($lgRequest);
	}

	private function createNewDatasetStructures() {
		// TODO: Implement error handling
		$lgDatasetState = lgDatasetStateHelper::getDatasetStateByInternalName('affymetrixCelDataIncomplete');
		$lgUser = lgUserHelper::getUserFromEnviroment();
		$lgDataset = lgDatasetHelper::createDataset(NULL, NULL, $lgDatasetState, $lgUser, $this);
		$this->datasetId = $lgDataset->getId();
	}

	private function showMainSelectionForm($lgRequest, $message = NULL) {
		$page = new lgCmsPage();
		$page->setTitle('Create Dataset - Affymetrix Upload - Main Menu');
		$page->appendContent('<h2>Affymetrix Upload</h2>');

		if ($message !== NULL) {
			$page->appendContent('<p class="notice">'.'</p>');
		}

		$page->appendContent('<h3>Select action</h3>');

		$form = new lgHtmlForm();
		
		// Using raw html field to built a radio button selection
		// TODO: implement proper radio buttons in system
		$optionsField = new lgHtmlRawHtmlField('options');
		$optionsField->setValue( '
			<input type="radio" name="actionname" value="uploadcel" />Upload .CEL file<br />
			<input type="radio" name="actionname" value="uploadcovar" />Upload covariates file<br />
			<input type="radio" name="actionname" value="finalise" />Finalise Dataset<br />
		');

		$form->addField($optionsField);
		$form->addField(new lgHtmlSubmitButton('submit', 'Go!'));
		
                $hiddenVals = array (
                        'requeststring' => 'createdataset',
                        'processorid' => $this->getId(),
			'processoraction' => 'selectaction',
			'datasetid' => $this->datasetId,
                 );
		$form->addFields(lgHtmlFormHelper::getHiddenFieldsFromArray($hiddenVals));

		$page->appendContent($form->getRenderedHtml());
		$page->render();
	}

	private function processActionSelection(lgRequest $lgRequest) {
		$post =  $lgRequest->getPostArray();
		$actionname = $post['actionname'];
		$this->datasetId = $post['datasetid'];

		switch($actionname) {
			case 'uploadcel':
				$this->showUploadCelFileForm($lgRequest);
				break;
			case 'uploadcovar':
				$this->showUploadCovarFileForm($lgRequest);
				break;
			case 'finalise':
				$this->showFinaliseForm($lgRequest);
				break;
		}
	}

	private function showUploadCelFileForm(lgRequest $lgRequest) {
		$page  = new lgCmsPage();
		$page->setTitle('Create Dataset - Affymetrix Upload - Upload .CEL File');
		$page->appendContent('<h3>Upload .CEL file</h3>');
		$page->appendContent('<p class="notice">Please use the form below to upload .CEL files one at a time.</p>');
		
		$form = new lgHtmlForm();
		$form->setEnctype('multipart/form-data');

		$form->addField(new lgHtmlFileField('file'));
		
                $hiddenVals = array (
                        'requeststring' => 'createdataset',
                        'processorid' => $this->getId(),
                        'processoraction' => 'doUploadCel',
                        'datasetid' => $this->datasetId,
                 );     
                $form->addFields(lgHtmlFormHelper::getHiddenFieldsFromArray($hiddenVals));

		$form->addField(new lgHtmlSubmitButton('submit','Start Upload'));
		$page->appendContent($form->getRenderedHtml());
		$page->render();
	}

	private function doUploadCelFile(lgRequest $lgRequest) {
		$post =  $lgRequest->getPostArray();
		$this->datasetId = $post['datasetid'];
		$lgDataset = new lgDataset($this->datasetId);
	
		// TODO: Add checks here
		if(is_uploaded_file($_FILES['file']['tmp_name']) ){
			$lgDataset->addFileFromUpload($_FILES['file']['tmp_name'],$_FILES['file']['name']);

			$message = 'Your file has been uploaded successfully';
			$this->showMainSelectionForm(NULL,$message);
		} else {
			$message = 'An error occured while attempting to upload your file. Please try again. If the 
				problem persists please contact ther system administrator';
			$this->showMainSelectionForm(NULL,$message);
		}
	}

	private function showUploadCovarFileForm(lgRequest $lgRequest) {
		$page = new lgCmsPage();
		$page->setTitle('Create Dataset - Affymetrix Upload - Upload covariates file');
		$page->appendContent('<h3>Upload covariates file</h3>');
		$page->appendContent('<p class="notice">Please use the form bellow to upload a covariates file</p>');

                $form = new lgHtmlForm();
                $form->setEnctype('multipart/form-data');

                $form->addField(new lgHtmlFileField('file'));

                $hiddenVals = array (
                        'requeststring' => 'createdataset',
                        'processorid' => $this->getId(),
                        'processoraction' => 'doUploadCov',
                        'datasetid' => $this->datasetId,
                 );
                $form->addFields(lgHtmlFormHelper::getHiddenFieldsFromArray($hiddenVals));

                $form->addField(new lgHtmlSubmitButton('submit','Start Upload'));
                $page->appendContent($form->getRenderedHtml());
                $page->render();
	}


        private function doUploadCovFile(lgRequest $lgRequest) {
                $post =  $lgRequest->getPostArray();
                $this->datasetId = $post['datasetid'];
                $lgDataset = new lgDataset($this->datasetId);

                // TODO: Add checks here
                if(is_uploaded_file($_FILES['file']['tmp_name']) ){
                        $lgDataset->addFileFromUpload($_FILES['file']['tmp_name'],'covariates.txt');

                        $message = 'Your file has been uploaded successfully';
                        $this->showMainSelectionForm(NULL,$message);
                } else {
                        $message = 'An error occured while attempting to upload your file. Please try again. If the
                                problem persists please contact ther system administrator';
                        $this->showMainSelectionForm(NULL,$message);
                }
        }


	private function showFinaliseForm(lgRequest $lgRequest) {
		$page = new lgCmsPage();
		$page->setTitle('Create Dataset - Affymetrix Upload - Confirm Finalise');
		$page->appendContent('<h3>Confirm Finalise</h3>');
		$page->appendContent('<p class="notice">Finalising an affymetrix dataset prevents further modification of the files and allows
			downstream processing. For more information about finalisation please see the see the
			 <a href="index.php?requeststring=createdataset&processorid='. $this->getId().'&processoraction=help">help</a> page.</p>');
		$page->appendContent('<h4>Are you sure you want to finalise the dataset?</h4>');

		$form = new lgHtmlForm();

                $hiddenVals = array ( 
                        'requeststring' => 'createdataset',
                        'processorid' => $this->getId(),
                        'processoraction' => 'doFinalise',
			'datasetid' => $this->datasetId,
	
                 );      
                $form->addFields(lgHtmlFormHelper::getHiddenFieldsFromArray($hiddenVals));

		$form->addField(new lgHtmlSubmitButton('submit','Finalise Dataset'));

		$page->appendContent($form->getRenderedHtml());
		$page->render();
	}

	private function doFinalise(lgRequest $lgRequest) {
                $post =  $lgRequest->getPostArray();
                $this->datasetId = $post['datasetid'];

		$lgDataset = new lgDataset($this->datasetId);
		$lgDatasetState = lgDatasetStateHelper::getDatasetStateByInternalName('affymetrixCelDataComplete');
		$lgDataset->setDatasetState($lgDatasetState);
		
		$this->showDataFinalisedSuccess();
	}

	private function showDataFinalisedSuccess() {
		$page = new lgCmsPage();
		$page->setTitle('Create Dataset - Affymetrix');
		$page->appendContent('<h2>Dataset sucessfully finalised</h2>');
		$page->appendContent('<p>The dataset you created has been finalised and no further changes to it are now possible. You can now proceed tpo process this dataset. You probably wish to run quality control on it and then proceed to normalise it.</p>');
		$page->render();
	}

}

