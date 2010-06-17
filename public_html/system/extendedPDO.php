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

class ExtendedPDO extends PDO {
	public function __construct($dsn,$username, $password, $driver_options = array()) {
		parent::__construct($dsn, $username, $password, $driver_options);
		if ( DEBUG ) {
			parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} else {
			parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
		}
	}

}	
