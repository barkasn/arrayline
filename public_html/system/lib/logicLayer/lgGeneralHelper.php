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

class lgGeneralHelper {
	public static function formatFileSize($sizeValue) {
		$units = array("bytes","kB","MB","GB","TB");
	
		$newSizeValue = 0;
		$newOrderOfMagnitude = 0;
	
		foreach($units as $orderOfMagnitude => $unitLabel) {
			//TODO: This could be faster
			if( ($sizeValue / pow(1024,$orderOfMagnitude)) >= 1 && 
				($sizeValue / pow(1024,$orderOfMagnitude + 1)) < 1) {
				$newSizeValue = $sizeValue / pow(1024,$orderOfMagnitude);
				$newOrderOfMagnitude = $orderOfMagnitude;	
			}
		}

		return number_format($newSizeValue,0) . " " . $units[$newOrderOfMagnitude];
	}

}
