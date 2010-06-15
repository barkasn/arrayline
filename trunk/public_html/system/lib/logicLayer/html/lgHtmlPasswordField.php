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
class lgHtmlPasswordField extends lgHtmlFormField {
	private $value;
	
	public function __construct( $name, $label = NULL, $id = NULL ) {
		parent::__construct($name, $label, $id);	
	}


	public function getRenderedHtml() {
		$html = $this->getRenderedLabel();
		$html .= '<input type="password" name="'.$this->name.'" id="'.$this->id.'" value="'.$this->value.'" />';
		return $html;
	}

	public function getValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value; 
	}


}

