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
class lgHtmlForm {
	private $fields; 
	private $enctype;

	public function __construct() {
		$fields = array();
	}

	public function __destruct() {

	}

	public function addField(lgHtmlFormField $field) {
		$this->fields[] = $field;
	}

	public function addFields($fields) {
		if (is_array($fields) && !empty($fields)) {
			foreach($fields as $f) {
				$this->addField($f);
			}
		}
	}
	
	public function setEncType($value) {
		$this->enctype = $value;
	}

	public function getRenderedHTML() {
		$renderedHTML;

		$renderedHTML .= '<form method="post" action="index.php" '.$this->getEnctypeClause().'  />';
		if (!empty($this->fields)) {
			foreach ($this->fields as $field) {
				$renderedHTML .= $field->getRenderedHtml();
				$renderedHTML .= '<br />';
			}
		}
		$renderedHTML .= '</form>';
		return $renderedHTML;
	}

	private function getEnctypeClause() {
		if (!empty($this->enctype)) {
			return ' enctype="multipart/form-data" ';
		} else {
			return ' ';
		}
	}

}


