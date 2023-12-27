<?php 

class Dir {
	
	public $path;
	
	public function __construct($root, $dir_name, $reset) {
		$this->resetDir($root, $dir_name, $reset);
	}
	
	public function resetDir($root, $dir_name, $reset) {
		$dir = $root.'/'.$dir_name;
		if (is_dir($root)) {
			if (is_dir($dir)) {
				if (!empty($reset)) {
					$files = scandir($dir);
					foreach($files as $file) {
						if ($file != "." && $file != "..") { 
							unlink($dir."/".$file);
						}
					}
					rmdir($dir);
				}
			}
		} 
		else {
			mkdir($root, 0777, true);
		}
		mkdir($dir, 0777, true);
		$this->path = $dir; 
	}
	
	
}

?>