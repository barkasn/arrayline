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

/* Represents an individual user request
 * it is essentially an encapsulation of the 
 * post array, with checking for the request_action
 * string validity and some other goodies
 */

class lgRequest {
	private $post;
	private $requestString;	
	private $timeCreated;

	public function __construct($postData) {
		$this->timeCreated = getdate();	
		$this->post = $postData;

		$this->requestString = isset($postData['requeststring'])?$postData['requeststring']:'default';
	}
	
	public function getRequestString() {
		return $this->requestString;
	}

	public function getPostArray() {
		// Aparently Arrays in PHP have different semantics than objects
		// and on assignment they get copied. Anyhow, avoid changing the 
		// post array you get from this function in case it does affect the
		// object
		$ret = $this->post;
		return $ret;
	}


}

