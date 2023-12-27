<?php

class File {

	private $dir;
	private $file_name;

	public function __construct($dir, $file_name) {
		$this->dir = $dir;
		$this->file_name = $file_name;
	}

	public function read() {
		$file_name = $this->dir.'/'.$this->file_name;
		$file = file_get_contents($filename);
		return $file;
	}

	public function write($text) {
		$file = $this->dir.'/'.$this->file_name;
		$handle = fopen($file, "a");
		fwrite($handle, $text);
		fclose($handle);
	}

	public function create($text) {
		$file = $this->dir.'/'.$this->file_name;
		if (!$handle = fopen($file, "x")) {
			echo '[erreur: fichier existant !!]';
		} else {
			fwrite($handle, $text);
		}
		fclose($handle);
		chmod($file, 0777);
	}

	public function override($text) {
		$file = $this->dir.'/'.$this->file_name;
		$handle = fopen($file, "w");
		fwrite($handle, $text);
		fclose($handle);
		chmod($file, 0777);
	}

	public function replace($search, $replace) {
		$file = $this->dir.'/'.$this->file_name;
		$handle = fopen($file, "r");
		while (!feof($handle)) {
			$line[] = fgets($handle);
		}
		fclose($handle);
		while (!in_string($search, current($line))) {	next($line); }
		$key = key($line);
		$line[$key] = str_replace($search, $replace, current($line));
		$this->override(implode('', $line));
	}

}

?>
