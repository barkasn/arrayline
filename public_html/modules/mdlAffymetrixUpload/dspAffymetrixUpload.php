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
		$requestString = $lgRequest->getRequestString();
		switch($requestString) {
			case 'createdataset':
				$this->handleDatasetCreation($lgRequest);
				break;
			case 'viewdataset':
				$this->handleDatasetViewing($lgRequest);
				break;
			default:
				throw new Exception('Unknown request');
		}
	}

	private function handleDatasetViewing(lgRequest $lgRequest) {
		echo 'Viewing dataset';
	}

	private function handleDatasetCreation(lgRequest $lgRequest) {
		$postArray = $lgRequest->getPostArray();

		$processorAction = empty($postArray['processoraction'])?'':$postArray['processoraction'];
		switch($processorAction) {
			case 'help':
				$this->showHelpPage($lgRequest);
				break;
			case 'createdataset':
				$this->createNewDataset($lgRequest);
				break;
			case 'selectaction':
				$this->processActionSelection($lgRequest);
				break;
			case 'doFinalise':
				$this->doFinalise($lgRequest);
				break;
			case 'doUploadCel':
				$this->doUploadCelFile($lgRequest);
				break;
			case 'doUploadCov':
				$this->doUploadCovFile($lgRequest);
				break;
			default:
				$this->showIntroForm($lgRequest);
				break;
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
			$page->appendContent('<p class="notice">'.$message.'</p>');
		}

		$page->appendContent('<h3>Select action</h3>');

		$form = new lgHtmlForm();
		
	// Using raw html field to built a radio button selection
	// TODO: implement proper radio buttons in system
	$optionsField = new lgHtmlRawHtmlField('options');
	$optionsField->setValue( '
		<input type="radio" name="actionname" value="uploadcel" />Upload .CEL files<br />
		<input type="radio" name="actionname" value="uploadcovar" />Upload covariates file<br />
		<input type="radio" name="actionname" value="createcovar" />Create covariates file wizard<br />
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
		case 'createcovar': 
			$this->showCreateCovariates($lgRequest);
			break;
		default:
			throw new Exception('Unknown selection');
	}
}


private function showCreateCovariates(lgRequest $lgRequest) {
	$postArray = $lgRequest->getPostArray();
	$step = empty($postArray['step'])?'1':$postArray['step'];
	switch($step) {
		case '1':
			$this->createCovarNumberOfVars($lgRequest);
			break;
		case '2':
			$this->createCovarVarAllowedValues($lgRequest);
			break;
		case '3':
			$this->createCovarSelectNumberOfFiles($lgRequest);
			break;
		case '4':
			$this->createCovarFileNamesAndInfo($lgRequest);
			break;
		case '5':
			$this->createCovarFileDoFileCreate($lgRequest);
			break;
		default:
			throw new Exception('Unknown step');
			break;
	}
}

private function createCovarFileDoFileCreate(lgRequest $lgRequest) {
	$postArray = $lgRequest->getPostArray();
	$datasetId = $postArray['datasetid'];

	$lgDataset = new lgDataset($datasetId);
	$dirPath = $lgDataset->getFilesDirectoryPath();
	$variablesInfo = json_decode(urldecode($postArray['varallowedvalues']));

	// Open file
	$fh = fopen($dirPath.'/covariates.csv','w');

	// Prepare header line
	$headerLineArray = array (
		'Filename',
		'Unique Sample Identifier',
		'Variable Value Identifier',
		'Sample Comment',
		'Replicate Identifier',
	);
	foreach ($variablesInfo as $var) {
		$headerLineArray[] = $var->name;
	}
	fputcsv($fh, $headerLineArray,',','"');

	// Loop over files
	for ( $i = 0; $i < $postArray['nofiles']; $i++) {
		$filename = $postArray['filename_'.$i];
		$comment = $postArray['comment_'.$i];
		$replicateId = $postArray['replicateid_'.$i];

		$vars = array();
		for ($j = 0; $j < $postArray['novariables']; $j++) {
			$vars[] = $postArray['varval_'.$i.'_'.$j];
		}
	
		// Computed columns
		$variableValueId = implode('_',$vars);
		$uniqueSampleId = $variableValueId.'_'.$replicateId;

		$rowArray = array (
			$filename,
			$uniqueSampleId,
			$variableValueId,
			$comment,
			$replicateId,
		);

		foreach ($vars as $var) {
			$rowArray[] = $var;
		}
		fputcsv($fh, $rowArray,',','"');
	}

	fclose($fh);

	$message = 'The covariates file was sucessfuly created';
	$this->showMainSelectionForm(NULL,$message);
}

private function createCovarFileNamesAndInfo(lgRequest $lgRequest) {
	$postArray = $lgRequest->getPostArray();

	$variablesInfo = json_decode(urldecode($postArray['varallowedvalues']));
	$numberOfFiles = $postArray['nofiles'];

	$page = new lgCmsPage();
	$page->setTitle('Create Covariates File - File Information');
	$page->appendContent('<h2>Create Covariates File - File Information</h2>');

	$page->appendContent('<p class="notice">Use the table below to specify the names and variable values for each of your microarray experiments. Filenames are case-sensitive (i.e. capitalisation matters). Please place particular importance in specifying unique replicate identifiers. Replicate identifiers MUST be unique among experiments with the exact same variable conditions. Unless you are trying to replicate a numbering scheme used in the labelling of the physical arrays it is recommended you enter a sequence of numbers: 1,2, ..., n </p>');
	
	$form = new lgHtmlForm();
	
	$filesData = new lgHtmlRawHtmlField('raw','raw');
	$filesData->setValue($this->getFileVariableTable($numberOfFiles, $variablesInfo));
	
	$form->addField($filesData);

	//TODO: Add hidden vars
	$hiddenVals = array (
		'requeststring' => 'createdataset',
		'processorid' => $this->getId(),
		'processoraction' => 'selectaction',
		'actionname' => 'createcovar',
		'datasetid' => $postArray['datasetid'],
		'novariables' => $postArray['novariables'],
		'maxallowedvalues' => $postArray['maxallowedvalues'],
		'varallowedvalues' => $postArray['varallowedvalues'],
		'nofiles' => $postArray['nofiles'],
		'step' => '5',
	 );

	$form->addFields(lgHtmlFormHelper::getHiddenFieldsFromArray($hiddenVals));
	$form->addField(new lgHtmlSubmitButton('submit', 'Next >'));
	$page->appendContent($form->getRenderedHtml());
	$page->render();
}

private function getFileVariableTable($numberOfFiles, $variablesInfo) {
	$html = '';

	$html .=<<<EOE
<table
<tr>
	<td>Filename</td>
	<td>Comment</td>
	<td>Replicate Identifier</td>
EOE;

	foreach ($variablesInfo as $variable) {
		$html .= '<td>'.$variable->name.'</td>';
	}
	$html .= '</tr>';

	for ($i = 0; $i < $numberOfFiles; $i++) {
		$html .= '<tr>';
		$html .= '<td><input type="text" name="filename_'.$i.'" /></td>';
		$html .= '<td><input type="text" name="comment_'.$i.'" /></td>';
		$html .= '<td><input type="text" name="replicateid_'.$i.'" value="'.($i+1).'" /></td>';
		
		$j = 0;
		foreach ($variablesInfo as $variable) {
			$html .= '<td><select name="varval_'.$i.'_'.$j.'">';
			foreach($variable->values as $value) {
				$html .= '<option>'.$value.'</option>';
			}
			$html .= '</select></td>';	
			$j++;
		}
		$html .= '</tr>';
	}
	$html .= '</table>';
	return $html;
}

private function createCovarSelectNumberOfFiles(lgRequest $lgRequest) {
	// Process Input and create serialisable array
	$postArray = $lgRequest->getPostArray();
	$variableCount = $postArray['novariables'];
	$maxAllowedValues = $postArray['maxallowedvalues'];

	$variablesInfo = array();
	for ($i = 0; $i < $variableCount; $i++ ) {
		$varInfo = array();
		$varInfo['name'] = $postArray['name_'.$i];

		$varInfoValues = array();
		for ($j = 0; $j < $maxAllowedValues; $j++) {
			if (!empty($postArray['val_'.$i.'_'.$j])) {
				$varInfoValues[] = $postArray['val_'.$i.'_'.$j];
			}
		}
		$varInfo['values'] = $varInfoValues;

		$variablesInfo[] = $varInfo;
	}

	// Show selection page for number of files
	$page = new lgCmsPage();
	$page->setTitle('Create Covariates File');
	$page->appendContent('<h2>Create covariates File - Number of Files</h2>');
	
	$page->appendContent('<p class="notice">Here you can select the number of CEL files in your experiment. This is essentially the number of microarray experiments you have performed (and which to process). In the next screen you will be presented with a table that will allow you to specify the values of the variables in your experiment for each microarray</p>');


	$page->appendContent('<p>Number of files</p>');
	
	$form = new lgHtmlForm();
	
	$dpdNumberOfFiles = new lgHtmlRawHtmlField('raw','raw');
	$selectContent = '';
	for ($i = 1; $i <= 50; $i++) {
		$selectContent .= '<option value="'.$i.'">'.$i.'</option>';
		}
		$dpdNumberOfFiles->setValue('<select name="nofiles">'.$selectContent.'</select>');

		$form->addField($dpdNumberOfFiles);
		$form->addField(new lgHtmlSubmitButton('submit','Next >'));

		$varAllowedValuesJson = urlencode(json_encode($variablesInfo));

                $hiddenVals = array (
                        'requeststring' => 'createdataset',
                        'processorid' => $this->getId(),
			'processoraction' => 'selectaction',
			'actionname' => 'createcovar',
			'datasetid' => $postArray['datasetid'],
			'novariables' => $postArray['novariables'],
			'maxallowedvalues' => $maxAllowedValues,
			'varallowedvalues' => $varAllowedValuesJson,
			'step' => '4',
                 );
		$form->addFields(lgHtmlFormHelper::getHiddenFieldsFromArray($hiddenVals));
		$page->appendContent($form->getRenderedHtml());
		$page->render();

	}

	private function createCovarVarAllowedValues(lgRequest $lgRequest) {
		$postArray = $lgRequest->getPostArray();
		$variableCount = $postArray['novariables'];

		// Maximum number of possible values any variable can have
		// TODO: Allow the uset to change this
		$maxAllowedValues = 10;

		$page = new lgCmsPage();
		$page->setTitle('Create Covariates File');
		$page->appendContent('<h2>Create covariates File - Variable Allowed Values</h2>');

		$page->appendContent('<p class="notice">Use the table below to enter the allowed values for each variable in your experiment. Rows in the table represent variables in the experiment, for example gene mutation and temperature. The first column on the table allows you to name your variable. The columns at the left allow you to enter allowed values. For a gene mutation and three temperature points you would enter here wt, mutant in the first row and the three temperatures at the second one. Please avoid funny characters and especially avoid values which differ only by a special character. These characters will be stripped and you this cause problems in downstream processing.</p>');
	
		$dataInput = new lgHtmlRawHtmlField('raw','raw');
		$dataInput->setValue($this->getVariableValuesInputTable($variableCount, $maxAllowedValues));
		
		$form = new lgHtmlForm();
		$form->addField($dataInput);
		$form->addField(new lgHtmlSubmitButton('submit','Next >'));

                $hiddenVals = array (
                        'requeststring' => 'createdataset',
                        'processorid' => $this->getId(),
			'processoraction' => 'selectaction',
			'actionname' => 'createcovar',
			'datasetid' => $this->datasetId,
			'novariables' => $variableCount,
			'maxallowedvalues' => $maxAllowedValues,
			'step' => '3',
                 );

		$form->addFields(lgHtmlFormHelper::getHiddenFieldsFromArray($hiddenVals));
		$page->appendContent($form->getRenderedHtml());
		$page->render();
	}

	private function getVariableValuesInputTable($variableCount, $maxValueCount) {
		$dataInputValue = '';
		
		$dataInputValue .= '<table>';
		$dataInputValue .= '<tr><td></td><td></td><td colspan="'.$maxValueCount.'">Allowed Variable Values</td></tr>';

		$dataInputValue .= '<tr><td rowspan="'.($variableCount+1) .'">Variables</td><td>Variable Name</td>';
		for ($i = 0; $i < $maxValueCount; $i++) {
			$dataInputValue .= '<td>Value '.($i + 1).'</td>';
		}
		$dataInputValue .= '</tr>';

		for ($i = 0; $i < $variableCount; $i++) {
			$dataInputValue .= '<tr>';
			$dataInputValue .= '<td><input type="text" name="name_'.$i.'" value="variable_'.($i+1).'" /></td>';
			for ($j = 0; $j < $maxValueCount; $j++) {
				$dataInputValue .= '<td><input type="text" name="val_'.$i.'_'.$j.'" /></td>';	
			}
			$dataInputValue .= '</tr>';
		}

		$dataInputValue .= '</table>';

		return $dataInputValue;
	}

	private function createCovarNumberOfVars(lgRequest $lgRequest) {
		$page = new lgCmsPage();
		$page->setTitle('Create Covariates File');
		$page->appendContent('<h2>Create covariates File - Select Number of Variables</h2>');
		$page->appendContent('<p class="notice">Here you can select the number of independent variables you have in your experiment. This is the total number of experimental conditions that you are changing and can include gene mutations and/or other parameters. For example if you are testing a gene mutant and its wild type in three different temperatures your would select two here. One for the mutant (wt vs mut) and one for the temperature (x,y and z degrees C).</p>');
		$page->appendContent('Number of variables: ');

		$form = new lgHtmlForm();

		$dpdNoVars = new lgHtmlRawHtmlField('rawField','rawField');
		$selectContent = '';
		for ($i = 1; $i <= 10; $i++) {
			$selectContent .= '<option value="'.$i.'">'.$i.'</option>';
		}

		$dpdNoVars->setValue('<select name="novariables">'.$selectContent.'</select>');
		$form->addField($dpdNoVars);

		$form->addField(new lgHtmlSubmitButton('submit','submit'));

                $hiddenVals = array (
                        'requeststring' => 'createdataset',
                        'processorid' => $this->getId(),
			'processoraction' => 'selectaction',
			'actionname' => 'createcovar',
			'datasetid' => $this->datasetId,
			'step' => '2'
                 );
		$form->addFields(lgHtmlFormHelper::getHiddenFieldsFromArray($hiddenVals));
		$page->appendContent($form->getRenderedHtml());
		$page->render();
	}

	private function showUploadCelFileForm(lgRequest $lgRequest) {
		$page  = new lgCmsPage();
		$page->setTitle('Create Dataset - Affymetrix Upload - Upload .CEL File');
		$page->appendContent('<h3>Upload .CEL files</h3>');
		$page->appendContent('<p class="notice">Please use the form below to upload .CEL files. You can upload a maximum of 10 files from this page, but you can repeat the procedure to upload larger datasets.</p>');
		
		$form = new lgHtmlForm();
		$form->setEnctype('multipart/form-data');

		for ($i=0; $i <10;  $i++) {
			$form->addField(new lgHtmlFileField('file_'.$i));
		}
		
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
	
		$flag = false;
		for ($i = 0; $i < 10; $i++) {
			if(is_uploaded_file($_FILES['file_'.$i]['tmp_name']) ){
				$lgDataset->addFileFromUpload($_FILES['file_'.$i]['tmp_name'],$_FILES['file_'.$i]['name']);
			} else {
				$flag = true;
			}
		}

		if ($flag) {
			$message = 'An error occured while attempting to upload your file. Please try again. If the 
				problem persists please contact ther system administrator';
		} else {
			$message = 'Your files have been uploaded sucessfully';
		}
		$this->showMainSelectionForm(NULL,$message);

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
                        $lgDataset->addFileFromUpload($_FILES['file']['tmp_name'],'covariates.csv');

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

