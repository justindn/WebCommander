<?php
include './lang/ru.php';
include 'config.php';
$folder = dirname(__FILE__);

$theme_path_files = '.' . SEP . 'images' . SEP . 'themes' . SEP . THEME .  SEP . 'icons' . SEP . 'files' . SEP;

$folder_icon = $theme_path_files . 'folder.png';

$up_icon = $theme_path_files . 'up.png';

$file_icon = $theme_path_files . 'file.png';


if (isset($_GET['folder']) OR $_GET['folder']!='') {
	$folder = $_GET['folder'];
}

chdir($folder);
$folder .= '/';
$files = getFiles($folder);

echo json_encode($files);

/* Functions */
function getFiles($folder) {
	global $lang;
	global $lang;
	global $theme_path_files;
	global $folder_icon;
	global $up_icon;
	global $file_icon;

	$list = scandir($folder);
	
	$dir_array = array();
	$files_array = array();
	
	foreach ($list as $filename){
		/*$dir_array[]['name'] = '..';
		$dir_array[]['extension'] = '';
		$dir_array[]['size'] = $lang['folder'];;
		$dir_array[]['folder'] =  'true';*/
		if ($filename != '.' /*&& $filename != '..'*/) {

			$file_array = array();
			$file_array['name'] = removeExtension($filename);
			$file_array['fullpath'] = realpath($filename);
			$file_array['datetime'] = date('d.m.Y h:m:s', filectime($folder . $filename));

			if (is_dir($filename)) {
				
				$file_array['icon'] = $folder_icon;
			
				if ($filename == '..'){
					$file_array['icon'] = $up_icon;
				}
				
				$file_array['name'] = $filename;
				$file_array['extension'] = '';
				$file_array['size'] = $lang['folder'];
				$file_array['folder'] = 'true';
				$dir_array[] = $file_array;
			} else {
				$file_array['extension'] = getExtension($filename);
				$icon_name = $theme_path_files . $file_array['extension'] . '.png';
				
					if (file_exists(dirname(__FILE__) . $icon_name)) {
						$file_array['icon'] = $icon_name;
					} else {
						$file_array['icon'] = $file_icon;
					}
				
				$file_array['size'] = filesize($folder . $filename);
				$file_array['folder'] = 'false';
				$files_array[] = $file_array;
			}
			
		}
	}
	
	$dir_array = array_merge($dir_array, $files_array);
	return $dir_array;
	
}

function getExtension($filename){
	
	if (substr($filename, 0, 1) == '.'){
		return '';
	}
	return array_pop(explode('.', $filename));
}

function removeExtension($filename){
	if (substr($filename, 0, 1) == '.'){
		return $filename;
	}
	$arr = explode('.', $filename);
	array_pop($arr);
	return implode('.', $arr);
	
}

?>