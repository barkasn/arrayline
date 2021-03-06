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


// Master include file, includes all other core project files
// and the autoloader

// General configuration file - non security related parameters
// Security related, like database credentials should go into
// the file speficified in this file and should reside outside the
// public directories
require_once('configuration.php');

// Initialise datbase connection
require_once('databaseConnection.php');

// Initialise PHP session, this must be  done before
// anything else
require_once('session.php');

// The autoloader which automatically loads any other class dependencies
require_once('autoload.php');

