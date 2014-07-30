<?php
	switch ($_POST['action']){
		case 'get':
			getFile($_POST['file']);
			break;
			
		case 'save':
			saveFile($_POST['file'], $_POST['content']);
			break;
	}
	function getFile($file){
		echo file_get_contents($file);
	}
	function saveFile($file, $info){
		file_put_contents($file, $info);
	}
	
	
?>