<?php

	require 'lib/loader.php';

	html()->stylesheets = ['doctml.css'];
	html()->source('doctml.file');
	html()->out();

?>