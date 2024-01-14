<?php

	class Item {

		public $id;
		public $name;
		public $tag = '';
		public $attributes = [];
		public $data = [];
		public $content;

		public function __construct($data) {
			
			// --- check data ------------------------
			if (!empty($data)) {
				
				// --- if data is array ------------------------
				if (is_array($data)) {
					$args = $data;
				}
				
				// --- if data is function ------------------------
				else if (is_callable($data)) {
					$args = $data();
				}
				
			}
			
			
			// --- set tag ------------------------
			if (isset($args) && (is_array($args))) {
				
				if (isset($args['tag']) && is_string($args['tag'])) {
					$this->setTag($args['tag']);
					unset ($args['tag']);
				}
				
			}
			
			// --- set default tag ------------------------
			if (empty($this->tag)) {
				$this->setTag('div');
			}
			
			// --- construct item ------------------------
			if (isset($args) && (is_array($args))) {
				$this->construct($args);
			}
			
		}
		
		public function construct($data) {

			// --- check data ------------------------
			if (!empty($data)) {

				// --- if data is array ------------------------
				if (is_array($data)) {
					$args = $data;
				}

				// --- if data is function ------------------------
				else if (is_callable($data)) {
					$args = $data();
				}

			}

			// --- construct ------------------------
			if (isset($args) && (is_array($args))) {
				
				if (isset($args['tag']) && is_string($args['tag'])) {
					$this->setTag($args['tag']);
					unset ($args['tag']);
				}
				
				// --- set args ------------------------
				foreach ($args as $arg => $value) {

					if (is_int($arg)) {
						
						if (is_string($value) || is_a($value, 'Item') || is_array($value) || is_int($value)) {
							if (is_string($value) && string_starts_by($value, '@')) {
								$this->setId(substr($value, 1));
							}
							else if (is_string($value) && string_starts_by($value, '&')) {
								$this->addClass(str_replace('&', '', $value));
							}
							else {
								$this->setContent($value);
							}
						}
						
						else if (is_callable($value)) {
							$this->setContent($value());
						}
						
					}
					
					else {
					
						// --- set method name ------------------------
						$method = 'set';
						foreach (explode('_', $arg) as $word) {;
							$method .= ucfirst($word);
						}
	
						// --- call method ------------------------
						if (method_exists($this, $method) && isset($value)) {
							
							if (is_string($value) || is_a($value, 'Item') || is_array($value) || is_int($value)) {
								$this->$method($value);
							}
	
							else if (is_callable($value)) {
								$this->$method($value());
							}
	
						}
						
						else if (is_string($arg)) {
							
							if (is_string($value) || is_int($value)) {
								$this->setAttribute([$arg => $value]);
							}
							
							else if (is_callable($value)) {
								$this->setAttribute([$arg => $value()]);
							}
							
						}
					}
					
				}
			}
		}

		public function setId($id) {
			if (isset($id) && is_string($id)) {
				$this->id = $id;
				$this->attributes['id'] = $id;
			}
		}

		public function setName($name) {
			if (isset($name) && is_string($name)) {
				$this->name = $name;
				$this->attributes['name'] = $name;
			}
		}
		
		public function setTag($tag) {
			if (isset($tag) && is_string($tag)) {
				if (in_array($tag, ['p', 'a', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'address',  'span', 'ul', 'ol', 'li', 'b', 'u', 'i', 'strong', 'table', 'tr', 'td', 'th', 'tbody', 'thead', 'tfoot', 'img', 'label', 'input', 'select', 'option', 'optgroup', 'textarea', 'button', 'form', 'datalist', 'div', 'nav', 'section', 'header', 'footer', 'script', 'style', 'hr', 'frame', 'pre'])) {
					$this->tag = $tag;
				}
			}
		}

		public function setClass($class) {
			if (isset($class) && (is_array($class) || is_string($class))) {
				$this->attributes['class'] = '';
				$this->addClass($class);
			}
		}
		
		public function addClass($class) {
			if (isset($class)) {
				if (is_array($class)) {
					foreach ($class as $class_name) {
						if (!empty($class_name) && is_string($class_name)) {
							$class_list[] = $class_name;
						}
					}
				}
				else if (!empty($class) && is_string($class)) {
					$class_list[] = $class;
				}
				if (isset($class_list)) {
					if (empty($this->attributes['class'])) {
						$this->attributes['class'] = implode(' ', $class_list);
					}
					else {
						$this->attributes['class'] .= ' '.implode(' ', $class_list);
					}
				}
			}
		}
		
		public function setContent($content) {
			if (isset($content)) {
				if (is_string($content) || is_int($content) || is_a($content, 'Item') || is_array($content)) {
					$this->content = $content;
				}
				else if (is_callable($content)) {
					$this->content = $content();
				}
			}
		}

		public function setAttribute($attributes) {
			if (isset($attributes)) {
				if (is_string($attributes) && in_array($attributes, ['selected', 'required', 'checked', 'autofocus', 'multiple', 'disabled', 'contenteditable'])) {
					$this->attributes[$attributes] = '';
				}
			 	else if (is_array($attributes)) {
			 		foreach ($attributes as $attr => $val) {
			 			if (is_string($val) && in_array($val, ['selected', 'required', 'checked', 'autofocus', 'multiple', 'disabled', 'contenteditable'])) {
			 				$this->attributes[$val] = '';
			 			}
						else if (isset($attr) && is_string($attr)) {
							if (isset($val) && (is_string($val) || is_int($val))) {
								$this->attributes[$attr] = $val;
							}
						}
			 		}
				}
			}
		}

		public function setData($data) {
			if (isset($data) && is_array($data)) {
				$this->data = $data;
			}
		}
		
		public function printContent() {
			$print = '';
			if (isset($this->content)) {
				if (is_string($this->content)) {
					$print = $this->content;
				}
				else if (is_a($this->content, 'Item')) {
					$print = $this->content->print();
				}
				else if (is_array($this->content)) {
					foreach ($this->content as $content) {
						if (is_string($content)) {
							$print .= $content;
						}
						else if (is_a($content, 'Item')) {
							$print .= $content->print();
						}
					}
				}
			}
			return $print;
		}
		
		public function print() {
			$print = '';
			if (!empty($this->tag)) {
				if (!in_array($this->tag, ['a'])) {
					$print .= "\n";
				}
				$print .= '<'.$this->tag;
				if (!empty($this->attributes)) {
					foreach ($this->attributes as $attribute => $value) {
						if (in_array($attribute, ['selected', 'required', 'checked', 'autofocus', 'multiple', 'disabled', 'contenteditable'])) {
							$print .= ' '.$attribute;
						}
						else {
							$print .= ' '.$attribute.'="'.$value.'"';
						}
					}
				}
				$print .=  '>';
				if (!in_array($this->tag, ['a', 'h1', 'h2', 'h3', 'textarea'])) {
					$print .= "\n";
				}
				$print .= $this->printContent();
				if (!in_array($this->tag, ['input', 'img'])) {
					if (!in_array($this->tag, ['a', 'h1', 'h2', 'h3', 'textarea'])) {
						$print .= "\n";
					}
					$print .= '</'.$this->tag.'>';
				}
			}
			return $print;
		}
		
		public function out() {
			echo $this->print();
		}
		
		public function outContent() {
			echo $this->printContent();
		}
		
		public function setValue($value) {
			if (isset($value)) {
				if (is_string($value)) {
					$this->attributes['value'] = $value;
				}
				if (is_int($value)) {
					$this->attributes['value'] = strval($value);
				}
			}
			
		}
		
		public function setStyle($style) {
			if (isset($style) && is_array($style)) {
				$this->attributes['style'] = '';
				$this->addStyle($style);
			}
		}
		
		public function addStyle($style) {
			if (isset($style) && is_array($style)) {
				if (!isset($this->attributes['style'])) { $this->setStyle($style); }
				else {
					foreach ($style as $style_tag => $value) {
						$this->attributes['style'] .= $style_tag.': '.$value.'; ';
					}
				}
			}
		}
		
		public function setLink($link) {
			if (isset($link) && is_string($link)) {
				if ($this->tag == 'a') {
					$this->attributes['href'] = $link;
				}
			}
		}
		
		public function setType($type) {
			if ($this->tag == 'input') {
				if (isset($type) && in_array($type, ['text', 'password', 'submit', 'date', 'datetime-local', 'checkbox', 'hidden', 'file'])) {
					$this->attributes['type'] = $type;
				}
				else {
					$this->attributes['type'] = 'text';
				}
			}
			else if ($this->tag == 'button') {
				if (isset($type) && ($type ==  'submit')) {
					$this->attributes['type'] = $type;
				}
			}
		}
		
		public function setForm($form) {
			if (isset($form) && is_string($form)) {
				if (in_array($this->tag, ['button', 'input', 'select', 'textarea'])) {
					$this->attributes['form'] = $form;
				}
			}
		}
		
		public function setMethod($method) {
			if (isset($method) && in_array($method, ['get', 'post'])) {
				if ($this->tag == 'form') {
					$this->attributes['method'] = $method;
				}
			}
		}
		
		public function setList($list_id) {
			if (isset($list_id) && is_string($list_id)) {
				if ($this->tag == 'input') {
					$this->attributes['list'] = $list_id;
				}
			}
		}
		
		public function setSize($size) {
			if (isset($size)) {
				(int) $size;
				$px_size = $size*6;
				if ($px_size < 120) {
					$px_size = 120;
				}
				if ($this->tag == 'textarea') {
					// $this->addStyle(['height' => '150px']);
				}
				else {
					$this->addStyle(['width' => $px_size.'px']);
				}
			}
		}
		
		public function setLength($length) {
			if (isset($length)) {
				if (in_array($this->tag, ['input', 'textarea'])) {
					$this->attributes['maxlength'] = $length;
				}
			}
		}
		
		public function setEvents($data) {
			if (isset($data)) {
				if (is_array($data)) {
					$events = $data;
				}
				else if (is_callable($data)) {
					$events = $data();
				}
			}
			if (isset($events) && is_array($events)) {
				foreach ($events as $event => $actions) {
					if (in_array($event, ['load', 'mouseover', 'mousemove', 'mousedown', 'mouseup', 'mouseout', 'keydown', 'click', 'submit', 'change', 'select', 'focus', 'blur', 'input', 'keypress'])) {
						$this->attributes['on'.$event] = '';
						if (!empty($actions) && is_array($actions)) {
							$actions_list = $actions;
						}
						else if (is_string($actions)) {
							$actions_list[] = $actions;
						}
						if (isset($actions_list)) {
							foreach ($actions_list as $action) {
								$this->addEvent($event, $action);
							}
							unset ($actions_list);
						}
					}
				}
			}
		}
		
		public function addEvent($event, $action) {
			if (isset($event) && in_array($event, ['load', 'mouseover', 'mousemove', 'mousedown', 'mouseup', 'mouseout', 'keydown', 'click', 'submit', 'change', 'select', 'focus', 'blur', 'input', 'keypress'])) {
				if (isset($action) && is_string($action)) {
					if (!isset($this->attributes['on'.$event])) {
						$this->attributes['on'.$event] = '';
					}
					$this->attributes['on'.$event] .= $action.'; ';
				}
			}
		}
		
		public function setOnsubmit($function) {
			if (isset($function)) {
				if ($this->tag == 'form') {
					$this->attributes['onsubmit'] = $function;
				}
			}
		}
		
	}
	
	
	class pTag extends Item {
		
		public function __construct($data) {
			
			$this->setTag('p');
			parent::__construct($data);
			
		}
		
	}
	
	class aTag extends Item {
		
		public function __construct($data) {
			
			$this->setTag('a');
			parent::__construct($data);
			
		}
		
	}
	
	class H1 extends Item {
		
		public function __construct($data) {
			
			$this->setTag('h1');
			parent::__construct($data);
			
		}
		
	}
	
	class H2 extends Item {
		
		public function __construct($data) {
			
			$this->setTag('h2');
			parent::__construct($data);
			
		}
		
	}
	
	class H3 extends Item {
		
		public function __construct($data) {
			
			$this->setTag('h3');
			parent::__construct($data);
			
		}
		
	}
	
	class H4 extends Item {
		
		public function __construct($data) {
			
			$this->setTag('h4');
			parent::__construct($data);
			
		}
		
	}
	
	class H5 extends Item {
		
		public function __construct($data) {
			
			$this->setTag('h5');
			parent::__construct($data);
			
		}
		
	}
	
	class H6 extends Item {
		
		public function __construct($data) {
			
			$this->setTag('h6');
			parent::__construct($data);
			
		}
		
	}
	
	class Address extends Item {
		
		public function __construct($data) {
			
			$this->setTag('address');
			parent::__construct($data);
			
		}
		
	}
	
	class Span extends Item {
		
		public function __construct($data) {
			
			$this->setTag('span');
			parent::__construct($data);
			
		}
		
	}
	
	class UL extends Item {
		
		public function __construct($data) {
			
			$this->setTag('ul');
			parent::__construct($data);
			
		}
		
	}
	
	class OL extends Item {
		
		public function __construct($data) {
			
			$this->setTag('ol');
			parent::__construct($data);
			
		}
		
	}
	
	class LI extends Item {
		
		public function __construct($data) {
			
			$this->setTag('li');
			parent::__construct($data);
			
		}
		
	}
	
	class bTag extends Item {
		
		public function __construct($data) {
			
			$this->setTag('b');
			parent::__construct($data);
			
		}
		
	}
	
	class uTag extends Item {
		
		public function __construct($data) {
			
			$this->setTag('u');
			parent::__construct($data);
			
		}
		
	}
	
	class iTag extends Item {
		
		public function __construct($data) {
			
			$this->setTag('i');
			parent::__construct($data);
			
		}
		
	}
	
	class Strong extends Item {
		
		public function __construct($data) {
			
			$this->setTag('strong');
			parent::__construct($data);
			
		}
		
	}
	
	class Table extends Item {
		
		public function __construct($data) {
			
			$this->setTag('table');
			parent::__construct($data);
			
		}
		
	}
	
	class TR extends Item {
		
		public function __construct($data) {
			
			$this->setTag('tr');
			parent::__construct($data);
			
		}
		
	}
	
	class TD extends Item {
		
		public function __construct($data) {
			
			$this->setTag('td');
			parent::__construct($data);
			
		}
		
	}
	
	class TH extends Item {
		
		public function __construct($data) {
			
			$this->setTag('th');
			parent::__construct($data);
			
		}
		
	}
	
	class tBody extends Item {
		
		public function __construct($data) {
			
			$this->setTag('tbody');
			parent::__construct($data);
			
		}
		
	}
	
	class tHead extends Item {
		
		public function __construct($data) {
			
			$this->setTag('thead');
			parent::__construct($data);
			
		}
		
	}
	
	class tFoot extends Item {
		
		public function __construct($data) {
			
			$this->setTag('tfoot');
			parent::__construct($data);
			
		}
		
	}
	
	class Img extends Item {
		
		public function __construct($data) {
			
			$this->setTag('img');
			parent::__construct($data);
			
		}
		
	}
	
	class Label extends Item {
		
		public function __construct($data) {
			
			$this->setTag('label');
			parent::__construct($data);
			
		}
		
	}
	
	class Input extends Item {
		
		public function __construct($data) {
			
			$this->setTag('input');
			parent::__construct($data);
			
		}
		
	}
	
	class Select extends Item {
		
		public function __construct($data) {
			
			$this->setTag('select');
			parent::__construct($data);
			
		}
		
	}
	
	class Option extends Item {
		
		public function __construct($data) {
			
			$this->setTag('option');
			parent::__construct($data);
			
		}
		
	}
	
	class OptGrp extends Item {
		
		public function __construct($data) {
			
			$this->setTag('optgroup');
			parent::__construct($data);
			
		}
		
	}
	
	class TextArea extends Item {
		
		public function __construct($data) {
			
			$this->setTag('textarea');
			parent::__construct($data);
			
		}
		
	}
	
	class ButtonTag extends Item {
		
		public function __construct($data) {
			
			$this->setTag('button');
			parent::__construct($data);
			
		}
		
	}
	
	class FormTag extends Item {
		
		public function __construct($data) {
			
			$this->setTag('form');
			parent::__construct($data);
			
		}
		
	}
	
	class Datalist extends Item {
		
		public function __construct($data) {
			
			$this->setTag('datalist');
			parent::__construct($data);
			
		}
		
	}
	
	class Div extends Item {
		
		public function __construct($data) {
			
			$this->setTag('div');
			parent::__construct($data);
			
		}
		
	}
	
	class Element extends Item {
		
		public function __construct($data) {
			
			$this->setTag('div');
			parent::__construct($data);
			
		}
		
	}
	
	class Container extends Item {
		
		public function __construct($data) {
			
			$this->setTag('div');
			parent::__construct($data);
			
		}
		
	}
	
	class Wrapper extends Item {
		
		public function __construct($data) {
			
			$this->setTag('div');
			parent::__construct($data);
			
		}
		
	}
	
	class Content extends Item {
		
		public function __construct($data) {
			
			$this->setTag('div');
			parent::__construct($data);
			
		}
		
	}
	
	class Nav extends Item {
		
		public function __construct($data) {
			
			$this->setTag('nav');
			parent::__construct($data);
			
		}
		
	}
	
	class Section extends Item {
		
		public function __construct($data) {
			
			$this->setTag('section');
			parent::__construct($data);
			
		}
		
	}
	
	class Header extends Item {
		
		public function __construct($data) {
			
			$this->setTag('header');
			parent::__construct($data);
			
		}
		
	}
	
	class Footer extends Item {
		
		public function __construct($data) {
			
			$this->setTag('footer');
			parent::__construct($data);
			
		}
		
	}
	
	class Script extends Item {
		
		public function __construct($data) {
			
			$this->setTag('script');
			parent::__construct($data);
			
		}
		
	}
	
	class Style extends Item {
		
		public function __construct($data) {
			
			$this->setTag('style');
			parent::__construct($data);
			
		}
		
	}
	
	class HR extends Item {
		
		public function __construct($data) {
			
			$this->setTag('hr');
			parent::__construct($data);
			
		}
		
	}
	
	class Frame extends Item {
		
		public function __construct($data) {
			
			$this->setTag('frame');
			parent::__construct($data);
			
		}
		
	}
	
	class Pre extends Item {
		
		public function __construct($data) {
			
			$this->setTag('pre');
			parent::__construct($data);
			
		}
		
	}

?>
