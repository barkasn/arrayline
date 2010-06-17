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
abstract class lgHtmlFormField {
	protected $id;
	protected $name;
	protected $label;
	
	public function __construct($name, $label = NULL, $id = NULL ) {
		$this->name = $name;
		$this->label = $label;
		if (!empty($id)) {
			$this->id = $id;
		} else {
			$this->id = $name;
		}
	}

	public function getName() {
		return $this->name;
	}
	
	public function setName($value) {
		$this->name = $value;
	}

	public function getId() {
		return $this->id;
	}

	public function setId($value) {
		$this->id = $value;
	}	

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($value) {
		$this->label = $value;
	}

	protected function getRenderedLabel() {
		$renderedHtml;
		if ($this->label != NULL) {
			$renderedHtml = '<label for="'.$this->id.'">'.$this->label.'</label>';
		}
		return $renderedHtml;

	}	

	abstract protected function getRenderedHtml();
	
}
