<?php

	class HTML {

		public $id = 0;
		public $title = '';
		public $stylesheets = [];
		public $js_files = [];
		public $javascripts = [];
		public $items = [];
		public $elements = [];

		public function __construct() {}
		
		public function globalStyle() {
			return ['display' => 'flex', 'flex' => '1', 'flex-basis' => '30%', 'width' => '100%'];
		}
		
		public function source($file) {
			if (isset($file) && is_string($file)) {
				if (file_exists($file)) {
					$this->readFile($file);
					$this->constructElements();
				}
			}
		}
		
		public function readFile($file) {
			
			if (isset($file) && is_string($file)) {
				if (file_exists($file)) {
					$file_array = file($file);
				}
			}
			
			if (isset($file_array)) {
				foreach ($file_array as $line_index => $line) {

					// ------------------------------
					
					if (!empty($line) || ($line == 0)) {
				
						$line = trim($line);

						// ------------------------------
						
						if (!string_ends_by($line, ' ')) { 
							$line .= ' '; 
						}
						
						// ------------------------------------------------------------
						
						if (string_starts_by($line, ['@', '#', '.'])) {
							while (string_starts_by($line, ['@', '#', '.'])) {
	
								// ------------------------------------
								
								if (string_starts_by($line, '@')) {
									
									$element_settings = substr($line, 0, strpos($line, ' '));
									$line = substr($line, strpos($line, ' ')+1);
									
									// -------------------------------------
									
									foreach (explode(':', $element_settings) as $piece) {
										
										if (string_starts_by($piece, '@')) {
											$element = substr($piece, 1);
										}
										
										else {
											$settings[] = $piece;
										}
										
									}
									
								}
									
								// ------------------------------------
		
								else if (string_starts_by($line, '#')) {
									
									$id = substr($line, 1, strpos($line, ' ')-1);
									$line = substr($line, strpos($line, ' ')+1);
																
								}
								
								// ------------------------------------
								
								else if (string_starts_by($line, '.')) {
									
									$class = substr($line, 1, strpos($line, ' '-1));
									$line = substr($line, strpos($line, ' ')+1);
									
								}
								
								else {
									break;
								}
								
							}
						}
						
						// ------------------------------------
						
						if (!empty(trim($line))) {
							$content[] = $line;
						}
						
						// ------------------------------------
						
						if (isset($file_array[$line_index+1])) {
							
							$next_line = $file_array[$line_index+1];
							
							// ------------------------------------
							
							if (string_starts_by($next_line, ['@', '#', '.'])) {
								
								$this->elements[] = [ 'element' => @$element, 'settings' => @$settings, 'id' => @$id, 'class' => @$class, 'content' => @$content ];
								
								$element = null;
								$settings = [];
								$id = null;
								$class = null;
								$content = null;
								 
							}
						}
						
						// ------------------------------------
					
					}
				}
			}
			
		}
		
		public function constructElements() {
			foreach ($this->elements as $element) {
				
				if (!empty($element['element'])) {
					if (!empty($element['settings'])) {
				
						foreach ($element['settings'] as $setting_index => $setting) {
							switch ($setting) {
								
								case 'top':
									$style = ['align-self' => 'flex-start'];
									
								case 'bottom':
									$style = ['align-self' => 'flex-end'];
									
								case 'left':
									$style = ['justify-self' => 'flex-start'];
									
								case 'right':
									$style = ['justify-self' => 'flex-end'];
									
								case 'center':
									
									if (!in_array('top', $element['settings']) && !in_array('bottom', $element['settings'])) {
										$style = ['align-self' => 'center'];
									}
									
									if (!in_array('left', $element['settings']) && !in_array('right', $element['settings'])) {
										$style = ['justify-self' => 'center'];
									}
								
								case 'beginning':
									$position = 'beginning';
									break;
								
								case 'middle':
									$position = 'middle';
									break;
								
								case 'end':
									$position = 'end';
									break;
									
								case 'bloc':
									$position = 'bloc';
									
							}
						}
					}
										
					if (!isset($position)) {
						$position = 'bloc';
					}
					
					if (empty($style)) {
						$style = [];
					}

					$item = new Item([
						'id' => @$element['id'],
						'class' => [@$element['element'], @$element['class']],
						'content' => @$element['content'],
						'style' => array_merge($this->globalStyle(), $style)
					]);
					
					if ($position == 'bloc') {
						$items[] = new div([ $item, 'style' => $this->globalStyle() ]);
						$item_key = @sizeof($items);
					}
					
					else {
						if (!isset($items[$item_key])) {
							$items[$item_key] = new Item([ [null, null, null], 'style' => $this->globalStyle() ]);
						}
						
						if ($position == 'beginning') {
							$items[$item_key]->content[0] = $item;
						}
						
						else if ($position == 'middle') {
							$items[$item_key]->content[1] = $item;
						}
						
						else if ($position == 'end') {
							$items[$item_key]->content[2] = $item;
						}
						
					}
					
					unset ($content);
					unset ($style);
					unset ($position);
					
				}
				
				if (isset($items)) {
					$this->setItem('@main', div([ $items ]));
				}
				
			}
		}
		
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

		  $html = "\n\t".'</body>';
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
