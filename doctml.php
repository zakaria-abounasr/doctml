<?php

	require 'lib/loader.php';
	
	$class = null;
	$id = null;
	$content = [];
	$items = [];
	
	if (file_exists('file')) {
		foreach (file('file') as $line) {
			
			$line = trim($line);
			if (!strpos($line, ' ')) { $line .= ' '; }
			
			if (string_starts_by($line, '@')) {
			
				if (!empty($content)) {
					$items[] = ['class' => $class, 'id' => $id, 'content' => $content]; 
				}
				
				$class = null;
				$id = null;
				$content = [];
				
				$tags = substr($line, 0, strpos($line, ' '));
				$content[] = substr($line, strpos($line, ' ')+1);
				
				foreach (explode(':', $tags) as $tag) {
	
					if (string_starts_by($tag, '@')) {
						$class = substr($tag, 1);
					}
					
					else if (string_starts_by($tag, '#')) {
						$id = substr($tag, 1);
					}
					
				}
				
			}
			
			else {
				$content[] = $line;
			}
				
		}
	}
	
	if (!empty($content)) {
		$items[] = ['class' => $class, 'id' => $id, 'content' => $content];
	}
	
		// ------------------------------------------------------
	
	html()->setItem('@main', div([
		function() use ($items) {
			if (!empty($items)) {
				foreach ($items as $item) {
					
					$class = $item['class'];
					$id = $item['id'];
					$content = $item['content'];
					
					foreach ($content as $key => $c) {
						if (in_array($c, ['', ' ', PHP_EOL])) {
							unset ($content[$key]);
						}
					}
					
					switch ($class) {
						
						case 'title':
							$tag = 'div';
							$formatted_content = implode(' ', $content);
							break;
						
						case 'label':
							$tag = 'label';
							$formatted_content = implode(' ', $content);
							break;
					
							
						case 'table':
							$tag = 'table';
							$formatted_content = $content;
							break;
							
						default:
							$tag = 'span';
							$formatted_content = implode('<br>', $content);
					}
					
					$div[] = new Item([
						'tag' => $tag,
						'class' => $class,
						'id' => $id,
						'content' => $formatted_content
					]);
				}
			}
			
			if (!empty($div)) {
				return $div;
			}
			
		}
	]));
	
	html()->stylesheets = ['doctml.css'];
	html()->out();
	
	
	
	

?>