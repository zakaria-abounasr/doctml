<?php

	class HTML {

		public $id = 0;
		public $title = '';
		public $stylesheets = [];
		public $js_files = [];
		public $javascripts = [];
		public $items = [];

		public function __construct() {}
		
		public function setItem($name, $data) {
			if (isset($name) && is_string($name)) {
				if (string_starts_by($name, '@')) {
					$uname = substr($name, 1);
				}
				else {
					$uname = $name;
				}
			}
			if (isset($data) && is_a($data, 'Item')) {
				$item = $data;
			}
			else if (is_callable($data)) {
				$new_data = $data();
				if (isset($new_data) && is_a($new_data, 'Item')) {
					$item = $new_data;
				}
			}
			if (isset($item)) {
				if (!isset($item->id)) {
					if (isset($uname) && is_string($uname)) {
						$item->setId($uname);
					}
				}
				if (isset($uname) && is_string($uname)) {
					$this->items[$uname] = $item;
				}
				else {
					$this->items[] = $item;
				}
			}
		}

		public function item($name) {
			if (isset($name)) {
				if (is_string($name) && string_starts_by($name, '@')) {
					$uname = substr($name, 1);
				}
				else {
					$uname = $name;
				}
				return $this->items[$uname];
			}
			return null;
		}
		
		public function out() {

			// --- check items ------------------------
			if (!isset($_POST['load']) && !isset($_GET['load'])) {

				$this->beginHTML();

				if (isset($this->items)) {
					foreach($this->items as $item) {
						if (is_a($item, 'item')) {
							$item->out();
						}
					}
				}

				$this->closeHTML();

			}

			else {

				foreach ($this->items as $item) {
					$item->outContent();
				}

			}

		}

		public function print() {

			$html = '';

			// --- check items ------------------------
			if (!isset($_POST['load']) && !isset($_GET['load'])) {

				$html .= $this->beginHTMLPrint();

				if (isset($this->items)) {
					foreach($this->items as $item) {
						if (is_a($item, 'item')) {
							$html .= $item->print();
						}
					}
				}

				$html .= $this->closeHTMLPrint();

			}

			else {

				foreach ($this->items as $item) {
					$html .= $item->printContent();
				}

			}

			return $html;

		}

		public function beginHTMLPrint() {

		  $html = '<!DOCTYPE html>';
		  $html .= "\n".'<html>';
		  $html .= "\n\t".'<head>';
		  $html .= "\n\t\t".'<meta charset="utf-8" />';
		  $html .= "\n\t\t".'<meta name="viewport" content="width=device-width, initial-scale=0.7">';
		  $html .= "\n\t\t".'<title>'.$this->title.'</title>';
		  
		  foreach ($this->stylesheets as $file) {
		    $html .= "\n\t\t".'<link href="'.$file.'" type="text/css" rel="stylesheet" />';
		  } 
		   
		  foreach ($this->js_files as $file) {
		    $html .= "\n\t\t".'<script src="'.$file.'" defer></script>';
		  }
		  
		  foreach ($this->javascripts as $file) {
		  	$html .= "\n\t\t".'<script src="'.$file.'"  defer></script>';
		  }
		                                                                                                            
		  if (file_exists('favicon.ico')) {
		    $html .= "\n\t\t".'<link rel="icon" type="image/x-icon" href="favicon.ico">';
		  } 

		  $html .= "\n\t".'</head>';
		  $html .= "\n\t".'<body>'; 

			return $html;

		} 
		                                                                                                            
		public function closeHTMLPrint() {

		  $html = "\n".'<div id="response"></div>';
		  $html .= "\n\t".'</body>';
		  $html .= "\n".'</html>';  
			
			return $html;

		} 
		                                                                                                            
		public function beginHTML() {
		  $html = $this->beginHTMLPrint();
		  echo $html;
		}
		
		public function closeHTML() {
		  $html = $this->closeHTMLPrint();
		  echo $html;
		}

	}

?>
