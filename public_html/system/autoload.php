<?php
// $Id$

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

// Project autoloader, automagically loads all other project dependencies
// uses prefixes to differenctiate between diffent project layers
function __autoload($class_name) {
	global $basepath, $classroot, $moduleroot;


	if (preg_match('/^lg/',$class_name)) {
		if(preg_match('/^lgHtml/', $class_name)) {
			$location = $basepath.$classroot.'logicLayer/html/'.$class_name.'.php';
		} else if (preg_match('/^lgReqHandler/', $class_name)) {
			$location = $basepath.$classroot.'logicLayer/requestHandlers/'.$class_name.'.php';
		} else {
			$location = $basepath.$classroot.'logicLayer/'.$class_name.'.php';
		}
		autoload_require_file($location);
	} else if (preg_match('/^db/', $class_name)) {
		$location = $basepath.$classroot.'databaseLayer/'.$class_name.'.php';
		autoload_require_file($location);
	} else if (preg_match('/^i/', $class_name)) {
		$location = $basepath.$classroot.'interfaces/'.$class_name.'.php';
		autoload_require_file($location);
	} else if (preg_match('/^dsp/', $class_name)) {
		// A data processor
		$processorName = getProcessorNameFromClass($class_name);
		$moduleName = getModuleNameFromProcessor($processorName);
		$location = $basepath.$moduleroot.'mdl'.$moduleName.'/dsp'.$processorName.'.php';
		autoload_require_file($location);
	} else {
		die('Autoloader: A fatal error has occured: Invalid class prefix for class '.$class_name);
	}	
	return;
}

function getProcessorNameFromClass($class_name) {
	return preg_replace('/^dsp/','',$class_name);
}

function getModuleNameFromProcessor($processor_name) {
	// TODO: This is heavy and result should be cached on the disk
	// or at least loaded at init time
	$moduleName = dbModuleHelper::getModuleNameByProcessorName($processor_name);
	return $moduleName;
}

function autoload_require_file($filename) {
	require($filename);
}
	


