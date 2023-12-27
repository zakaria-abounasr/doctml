<?php

	function html() {
		if (!isset($GLOBALS['ghtml'])) {
			$GLOBALS['ghtml'] = new HTML();
		}
		return $GLOBALS['ghtml'];
	}

	function app() {
		if (!isset($GLOBALS['gapp'])) {
			$GLOBALS['gapp'] = new App();
		}
		return $GLOBALS['app'];
	}

	function dbSetUser($username, $password) {
		if (!isset($GLOBALS['gdb'])) {
			$GLOBALS['gdb'] = new Database();
		}
		if (!empty($username) && is_string($username) && isset($password)) {
			$GLOBALS['gdb']->setUser($username, $password);
		}
	}

	function dbConnect($dbname) {
		if (isset($dbname) && is_string($dbname)) {
			if (!isset($GLOBALS['gdb'])) {
				$GLOBALS['gdb'] = new Database();
			}
			if ($GLOBALS['gdb']->name != $dbname) {
				$GLOBALS['gdb']->dbConnect($dbname);
			}
		}
	}
	
	function db() {
		if (!isset($GLOBALS['gdb'])) {
			$GLOBALS['gdb'] = new Database();
		}
		return $GLOBALS['gdb'];
	}
	
	function index() {
		if (isset($_GET['idx'])) {
			$_SESSION['idx'] = $_GET['idx'];
			return $_GET['idx'];
		}
		else if (isset($_POST['idx'])) {
			return $_POST['idx'];
		}
		return 0;
	}
	
	function formatText($data_text) {
		$data_array = str_split($data_text, 1);
		$index = 0;
		while ($letter = current($data_array)) {
			if ($letter == '[') {
				$index++;
				while(next($data_array) == ' ');
				if (empty($marker_start)) {
					$marker_start = true;
					$string[$index]['marker'] = current($data_array);
				}
				else {
					$marker_start = false;
					$string[$index]['marker'] = 'p';
				}
				while(next($data_array) == ' ');
				next($data_array);
			}
			if (empty($string[$index]['marker'])) {
				$string[$index]['marker'] = 'p';
			}
			$string[$index]['text'][] = current($data_array);
			next($data_array);
		}
		if (isset($string)) {
			return $string;
		}
		return [];
	}
	
	function id() {
		if (isset($GLOBALS['gitem'])) {
			return $GLOBALS['gitem'];
		}
	}

	function nextId() {
		if (isset($GLOBALS['gitem'])) {
			return $GLOBALS['gitem']+1;
		}
	}
	
	function prevId() {
		if (isset($GLOBALS['gitem'])) {
			return $GLOBALS['gitem']-1;
		}
	}
	
	function stepId($step) {
		if (isset($GLOBALS['gitem'])) {
			(int) $step;
			return $GLOBALS['gitem']+$step;
		}
	}
	
	function item() {
		return 'item_'.id();
	}
	
	function nextItem() {
		return 'item_'.nextId();
	}
	
	function prevItem() {
		return 'item_'.prevId();
	}
	
	function stepItem($step) {
		(int) $step;
		return 'item_'.stepId($step);
	}
	
	function varset($name, $value) {
		if (isset($name) && is_string($name)) {
			if (isset($value)) {
				$GLOBALS['g'.$name] = $value;
			}
		}
	}

	function varget($name) {
		if (isset($name) && is_string($name)) {
			if (isset($GLOBALS['g'.$name])) {
				return $GLOBALS['g'.$name];
			}
		}
		return null;
	}
	
	function save($name, $value) {
		if (isset($name) && is_string($name)) {
			if (isset($value)) {
				$_SESSION[currentLocation()]['index_'.index()]['page_'.page_id()][$name] = $value;
			}
		}
	}
	
	function save_global($name, $value) {
		if (isset($name) && is_string($name)) {
			if (isset($value)) {
				$_SESSION[currentLocation()]['index_'.index()]['global'][$name] = $value;
			}
		}
	}
	
	function array_save($name, $key, $value) {
		if (isset($name) && is_string($name)) {
			if (isset($value)) {
				if (isset($key)) {
					$_SESSION[currentLocation()]['index_'.index()]['page_'.page_id()][$name][$key] = $value;
				}
				else {
					$_SESSION[currentLocation()]['index_'.index()]['page_'.page_id()][$name][] = $value;
				}
			}
		}
	}
	
	function array_save_global($name, $key, $value) {
		if (isset($name) && is_string($name)) {
			if (isset($value)) {
				if (isset($key)) {
					$_SESSION[currentLocation()]['index_'.index()]['global'][$name][$key] = $value;
				}
				else {
					$_SESSION[currentLocation()]['index_'.index()]['global'][$name][] = $value;
				}
			}
		}
	}
	
	function read($name) {
		if (isset($name) && is_string($name)) {
			if (isset($_SESSION[currentLocation()]['index_'.index()]['page_'.page_id()][$name])) {
				return $_SESSION[currentLocation()]['index_'.index()]['page_'.page_id()][$name];
			}
		}
		return null;
	}
	
	function read_global($name) {
		if (isset($name) && is_string($name)) {
			if (isset($_SESSION[currentLocation()]['index_'.index()]['global'][$name])) {
				return $_SESSION[currentLocation()]['index_'.index()]['global'][$name];
			}
		}
		return null;
	}
	
	function delete($name) {
		if (isset($name) && is_string($name)) {
			if (isset($_SESSION[currentLocation()]['index_'.index()]['page_'.page_id()][$name])) {
				unset ($_SESSION[currentLocation()]['index_'.index()]['page_'.page_id()][$name]);
			}
		}
		return null;
	}
	
	function array_delete($name, $key) {
		if (isset($name) && is_string($name)) {
			if (isset($_SESSION[currentLocation()]['index_'.index()]['page_'.page_id()][$name])) {
				if (isset($_SESSION[currentLocation()]['index_'.index()]['page_'.page_id()][$name][$key])) {
					unset ($_SESSION[currentLocation()]['index_'.index()]['page_'.page_id()][$name][$key]);
				}
			}
		}
		return null;
	}
	
	function delete_global($name) {
		if (isset($name) && is_string($name)) {
			if (isset($_SESSION[currentLocation()]['index_'.index()]['global'][$name])) {
				unset ($_SESSION[currentLocation()]['index_'.index()]['global'][$name]);
			}
		}
		return null;
	}
	
	function array_delete_global($name, $key) {
		if (isset($name) && is_string($name)) {
			if (isset($_SESSION[currentLocation()]['index_'.index()]['global'][$name])) {
				if (isset($_SESSION[currentLocation()]['index_'.index()]['global'][$name][$key])) {
					unset ($_SESSION[currentLocation()]['index_'.index()]['global'][$name][$key]);
				}
			}
		}
		return null;
	}
	
	function ToBeLoaded() {
		if (!isset($GLOBALS['gitem'])) {
			$GLOBALS['gitem'] = 0;
		}
		else {	
			$GLOBALS['gitem']++; 
		}
		if (!isset($_POST['load']) && !isset($_GET['load'])) {
			return true;
		}
		if ((isset($_POST['load']) && ($_POST['load'] == $GLOBALS['gitem'])) || (isset($_GET['load']) && ($_GET['load'] == $GLOBALS['gitem']))) {
			return true;
		}
		return null;
	}
	
	function loaded($id) {		
		if (!isset($_POST['load']) && !isset($_GET['load'])) {
			return true;
		}
		if ((isset($_POST['load']) && ($_POST['load'] == $id)) || (isset($_GET['load']) && ($_GET['load'] == $id))) {
			return true;
		}
		return null;
	}
	
	function startSession() {
		if (session_id() == null) {
			session_start();
		}
	}

	function closeSession() {
		if (session_id()) {
			session_destroy();
			session_unset();
		}
	}

	function labelling($label) {
		if (empty(varget('dict'))) {
			$dict = [
				'activite' => 'activité',
				'tache' => 'tâche',
				'telephone' => 'téléphone',
				'associee' => 'associée',
				'duree' => 'durée',
				'periode' => 'période',
				'frequence' => 'fréquence',
				'debut' => 'début',
				'eouvre' => 'œeuvre',
				'vehicule' => 'véhicule',
				'allee' => 'allée',
				'quantite' => 'quantité',
				'designation' => 'désignation',
				'reference' => 'référence',
				'realisation' => 'réalisation',
				'unite' => 'unité',
				'modele' => 'modèle',
				'reference' => 'référence'
			];
			varset('dict', $dict);
		}
		$words = explode("_", $label);
		foreach ($words as $word) {
			if (strlen($word) == 1) {
				if ($word == 'n') {
					$new_word[] = ucfirst($word).'° ';
				}
				else if (in_array($word, ['d', 'l'])) {
					$new_word[] = $word."\'";
				}
				else {
					$new_word[] = $word.' ';
				}
			} 
			else if (strlen($word) == 2) {
				if (in_array($word, ['de', 'du', 'et', 'la', 'on', 'by', 'of', 'or'])) {
					$new_word[] = $word.' ';
				}
				else {
					$new_word[] = strtoupper($word).' ';
				}
			} 
			else {
				if (in_array($word, array_keys(varget('dict')))) {
					$new_word[] = varget('dict')[$word].' ';
				}
				else {
					$new_word[] = $word.' ';
				}
			}
		}
		$new_label = implode("", $new_word);
		return $new_label;
	}
	
	function labelling_plural($label) {
		if (isset($label) && is_string($label)) {
			$new_label = trim(labelling($label));
			if (isset($new_label)) {
				if ($new_label == 'contrat de maintenance') {
					return 'contrats de maintenance';
				}
				else if ($new_label == 'installation contact') {
					return 'installation contacts';
				}
				else if ($new_label == 'Planning') {
					return $new_label;
				}
				if ($new_label == 'Offres de Prix') {
					return 'Offres de Prix';
				}
				else if (in_array($new_label, ['projets Anton Paar', 'historique de stock', 'stock', 'package applications', 'responsibility matrix'])) {	
					return  $new_label;
				}
				else {
					foreach (explode(' ', $new_label) as $word) {
						if (in_array($word, ['country', 'city'])) {
							$new_words[] = substr($word, 0, strlen($word)-1).'ies';
						}
						else if (string_ends_by($word, 's') || (strlen($word) <= 3)) {
							$new_words[] = $word;
						}
						else if (in_array($word, ['association', 'prix'])) {
							$new_words[] = $word;
						}
						else {
							$new_words[] = $word.'s';
						}
					}
					if (isset($new_words)) {
						return implode(' ', $new_words);
					}
				}
			}
		}
		return '';
	}
		
	
	function string_starts_by($string, $find) {
		if (isset($find) && is_string($find)) {
			$words[] = $find;
		}
		else if (isset($find) && is_array($find)) {
			$words = $find;
		}
		if (is_string($string) && !empty($words)) {
			foreach ($words as $word) {
				if (stripos($string, $word) !== false) {
					if (stripos($string, $word) == 0) {
						return true;
					}
				}
			}
		}
		return null;
	}
	
	function string_ends_by($string, $find) {
		if (isset($find) && is_string($find)) {
			$words[] = $find;
		}
		else if (isset($find) && is_array($find)) {
			$words = $find;
		}
		if (is_string($string) && !empty($words)) {
			foreach ($words as $word) {
				if (substr($string, strlen($string)-strlen($word)) == $word) {
					return true;
				}
			}
		}
		return null;
	}

	function is_dbl_array($array) {
		if (isset($array) && is_array($array)) {
			foreach ($array as $son) {
				if (is_array($son)) {
					return true;
				}
			}
		}
		return null;
	}

	function goToLink($link) {
		header('location: '.$link);
	}

	function currentPage() {
		return $_SERVER['PHP_SELF'];
	}

	function refreshPage() {
		goToLink(currentPage());
	}

	function currentLocation() {
		return basename(substr($_SERVER['PHP_SELF'], 1, strlen($_SERVER['PHP_SELF'])-strlen(basename($_SERVER['PHP_SELF']))-2));
	}
	
	function array_key($array, $index) {
		foreach ($array as $key => $value) {
			if ($value == $index) {
				return $key;
			}
		}
		return null;
	}

	function is_field($field) {
		if (isset($field)) {
			if (is_a($field, 'Field')) {
				return true;
			}
		}
		return null;
	}

	function is_static_field($field) {
		if (isset($field)) {
			if (is_a($field, 'StaticField')) {
				return true;
			}
		}
		return null;
	}

	function is_control($control) {
		if (isset($control)) {
			if (is_a($control, 'Control')) {
				return true;
			}
		}
		return null;
	}

	function is_submit($submit) {
		if (isset($submit)) {
			if (is_a($submit, 'Submit')) {
				return true;
			}
		}
		return null;
	}

	function is_link_button($link) {
		if (isset($link)) {
			if (is_a($link, 'Link')) {
				return true;
			}
		}
		return null;
	}

	function is_button($button) {
		if (isset($button)) {
			if (is_a($button, 'Button')) {
				return true;
			}
		}
		return null;
	}

	function is_primary_element($primary_element) {
		if (isset($primary_element)) {
			if (is_a($primary_element, 'PrimaryElement')) {
				return true;
			}
		}
		return null;
	}

	function is_controller($controller) {
		if (isset($controller)) {
			if (is_a($controller, 'Controller')) {
				return true;
			}
		}
		return null;
	}

	function is_form($form) {
		if (isset($form)) {
			if (is_a($form, 'Form')) {
				return true;
			}
		}
		return null;
	}

	function is_data_table($data_table) {
		if (isset($data_table)) {
			if (is_a($data_table, 'DataTable')) {
				return true;
			}
		}
		return null;
	}

	function in_string($string, $find) {
		if (isset($find) && is_string($find)) {
			$words[] = $find;
		}
		else if (isset($find) && is_array($find)) {
			$words = $find;
		}
		if (is_string($string) && !empty($words)) {
			foreach ($words as $word) {
				if (stripos($string, $word) !== false) {
					return true;
				}
			}
		}
		return null;
	}

	function string_in($string, $find) {
		if (isset($find) && is_string($find)) {
			$words[] = $find;
		}
		else if (isset($find) && is_array($find)) {
			$words = $find;
		}
		if (is_string($string) && !empty($words)) {
			foreach ($words as $word) {
				if (stripos($word, $string) !== false) {
					return true;
				}
			}
		}
		return null;
	}
	
	function build_sorter($keys_array) {
		return function ($a, $b) use ($keys_array) {
		// ----------------------------------------------
		foreach ($keys_array as $index => $key) {
		// ----------------------------------------------
		if (substr($key, 0, 1) == '!') { $k[$index] = substr($key, 1);	$direction[$index] = -1; }
		else { $k[$index] = $key; $direction[$index] = 1;	}}
		// ----------------------------------------------
		$current_key = sizeof($keys_array)-1;
		// ----------------------------------------------
		while (($a[$k[$current_key]] == $b[$k[$current_key]]) && ($current_key >= 1)) { $current_key--; }
		// ----------------------------------------------
		if ($direction[$current_key] == -1) {
		return strnatcasecmp($b[$k[$current_key]], $a[$k[$current_key]]); }
		return strnatcasecmp($a[$k[$current_key]], $b[$k[$current_key]]);	};
	}

	function onLoad($function) {
		$function();
	}

?>
