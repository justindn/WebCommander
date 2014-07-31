<?php
header('Content-type:text/html;charset=utf-8');
include './lang/ru.php';
include 'config.php';
?>
<!doctype html>
<html>
<head>
<title>Web Commander</title>
<meta charset='utf-8'>
<link rel='stylesheet' href='<?='./themes/' . THEME . '/styles.css'?>'>
<style>
</style>

<script src='jquery-1.11.1.min.js'></script>
<script>
	$(document).ready(function(){
		//Текущее местоположение курсора. Объект DOM со строкой таблицы.
		var line;
		//Предыдущее местоположение курсора. Объект DOM со строкой таблицы.
		var prev_line;
		
		var sep = '/';
		//Активная панель
		init();
		//Данные панелей
		var panels = {
			'isActive' : 0, 
			0:{
				'folder' : '.', 
				'object' : $('.panel_list:eq(0)'),
				'header' : $('.panel_current_folder:eq(0)'),
			   },
			1:{
				'folder' : '.', 
				'object' : $('.panel_list:eq(1)'),
				'header' : $('.panel_current_folder:eq(1)'),
			},
			'getSibling' : function() {
				return Math.abs(this.isActive - 1);
			},
			'active' : function() {
				return this[this.isActive];
			}, 
			'another' : function() {
				return this[Math.abs(this.isActive - 1)];
			}, 

		}
		
		var progressWindow = {
			'title' : function (title){
				this.progress_title.html(title);
			}, 
			'value'   : function(val){
				if (typeof (val) == 'undefined' || val == 0){
					return;
				}
				var width = Number(val * parseInt(this.barContainer.css('width')) / 100).toFixed(0) + 'px';
				this.bar.css ({'width' : width});
			},  
			'show'  : function(){
				this.window.show();
			},
			'hide'  : function (){
				this.progress_title.html('');
				this.bar.css ({'width' : '0'});
				this.window.hide();
			},
			'window': $('#progress'), 
			
			'barContainer': $('.progressbar'),
			
			'bar'   : $('.progressbar div'),
			
			'progress_title' : $('#progress_description'),
		}
		
		//Функция инициализации
		function init(){
			$.ajax('init.php').done(function(data){
				var init = JSON.parse(data);
				panels[0].folder = init.folder;
				panels[1].folder = init.folder;
				renderPanel(panels[0]);
				renderPanel(panels[1]);
			});
		}
		
		//Service functions
		function getCurrentFolderName(){
			
			var foundPos = 0;
			
			while (true){
				var foundPos = panels[panels.isActive].folder.indexOf('\\', ++foundPos);
				//var foundPos = panels[panels.isActive].folder.indexOf(sep, ++foundPos);
				if (foundPos == -1) break;
				var lastPos = foundPos;

			}
			return panels[panels.isActive].folder.substr(lastPos+1);
			
		}
		
		//Функция отрисовки панели со списком файлов. Передается объект - панель из panels
		function renderPanel(panel, cursorPosition){
			if (typeof line != 'undefined'){
				var lineNumber = line.index();
			}
 			if (typeof (panel) === "undefined") return;
		
			$.ajax('filelist.php?folder=' + panel.folder).done(function(data) {
			
				panel.object.empty();
				
				var filelist = JSON.parse(data);
				
				panel.header.html(panel.folder);
				
				for (var i = 0; i < filelist.length; i++){
				
					panel.object.append('<div class="panel_row" data-folder="' 
						+ filelist[i].fullpath + 
						'" data-filename="' + 
						filelist[i].name +
						'" data-extension="'+ 
						filelist[i].extension +
						'" data-is-folder = "' 
						+ filelist[i].folder + 
						'"><div class="panel_cell" style="background-image:url(\'' + filelist[i].icon + '\');">' +filelist[i].name +  
						'</div> <div class="panel_cell">' + filelist[i].extension + 
						'</div> <div class="panel_cell">' + filelist[i].size + 
						'</div> <div class="panel_cell">' + filelist[i].datetime + 
						'</div></div>');
				}
				
				/* if (lineNumber === 'undefined') { */
				if (typeof cursorPosition === 'undefined'){
					line = panels[panels.isActive].object.children('.panel_row:eq(0)');
				}
				else{
					line = panels[panels.isActive].object.children('[data-filename=' + cursorPosition+ ']');
					if (line.length == 0){
						line = panels[panels.isActive].object.children('.panel_row:eq(0)');
					}
				}
				
				/* }
				else {
					line = panels[panels.isActive].object.children('.panel_row:eq(' + lineNumber + ')');
					if (line.length == 0){
						line = panels[panels.isActive].object.children('.panel_row:eq(0)');
					}
				} */
				
				drawCursor(line);

			});
		}
	
		function selectLine(line_to_select){
			line_to_select.toggleClass('selected');
		}
		
	//Рисование курсора. Функции передается объект, где надо нарисовать курсор
	//В объекте line - объект, где курсор был.
		function drawCursor(current_line){
			if (typeof current_line === 'undefined') {
				current_line = panels[panels.isActive].object.children('.panel_row:eq(0)');
			}
			if (typeof current_line.attr('class') === 'undefined') return;
			if (line != ''){
				line.removeClass('cursor');
			}
			
			line=$(current_line);
			$(current_line).addClass('cursor');
			
			panels.isActive = line.parent().parent().index();
			
			$('.panel_current_folder:eq(' + panels.isActive + ')').addClass('active')
			$('.panel_current_folder:eq(' + Math.abs(panels.isActive-1) + ')').removeClass('active');
			
		}
		/*Mouse Events*/
		$('.panel_list').click(function(event){
			event.preventDefault();
			if ($(event.target).attr('class') == 'panel_cell'){
				drawCursor($(event.target).parent());
			}
		});
		
		$('.panel_list').contextmenu(function(event){
			event.preventDefault();
			if ($(event.target).prop('class') == 'panel_cell'){
				selectLine($(event.target).parent());
				drawCursor($(event.target).parent());
			}
		});
		
		$('.panel_list').dblclick(function(event){
			var cursorPosition;
			event.preventDefault();
			if ($(event.target).attr('class') == 'panel_cell'){
				drawCursor($(event.target).parent());
				if (line.attr('data-filename') === '..'){
					cursorPosition = getCurrentFolderName();
				}
				if (line.attr('data-is-folder') === 'true'){
					panels[panels.isActive].folder = line.attr('data-folder');
					renderPanel(panels[panels.isActive]);
				}
			}


			
		});
		
		/*Interface Functions*/
		function panelScrollTop() {
			
			panels.active().object.animate({scrollTop: '0'}, 1);
			
		}
		function panelScrollBottom() {
			var height = panels.active().object.prop ('scrollHeight');
			panels.active().object.animate({scrollTop: height}, 1);
		}
		
		function panelScrollTo(height) {
			panels.active().object.animate({scrollTop: height}, 1);
		}
		function getPageScrollLine(lineNum){
			if (lineNum < 0) {
				lineNum = 0;
			}
			if ($(line.parent().children(':eq(' + lineNum + ')')).length == 0){
				var lastLine = $(line.parent().children()).length - 1;
				return $(line.parent().children(':eq(' + lastLine + ')'));
			}
			else{
				return $(line.parent().children(':eq(' + lineNum + ')'));
			}
		}
		
		function getSelectedFiles(){
			var filesList = [];
			var quantity = panels.active().object.children('.selected').length;
			if (quantity == 0){
				filesList[0] = {
						'fullname'   : line.attr('data-folder'), 
						'filename'  : line.attr('data-filename'),
						'extension' : line.attr('data-extension'),
						'is-folder' : line.attr('data-is-folder'),
					
				}
			}
			else {
				for (i=0;i<quantity; i++){
					var current_line = panels.active().object.children('.selected:eq(' + i + ')');
					filesList[i] = {
						'fullname'    : current_line.attr('data-folder'), 
						'filename'  : current_line.attr('data-filename'),
						'extension' : current_line.attr('data-extension'),
						'is-folder' : current_line.attr('data-is-folder'),
					}
				}
			}
			return filesList;
		}
		
		/*Files functions*/
		function rename(){
			if (line.attr('data-filename') == '..') return;
			if (line.attr('data-is-folder') == 'true'){
				var oldName = line.attr('data-filename');
			}
			else{
				var oldName = line.attr('data-filename') + '.' + line.attr('data-extension') ;
			}
			var newName = prompt('Enter the new name', oldName);

			var currentDir = panels.active().folder;
			
			if (newName !== null && newName != false && newName != oldName && line.attr('data-filename') != '..'){
					$.ajax({
						cache : false, 
						type  : 'POST',
						url   : 'operations.php',
						data  : 'action=ren&oldname=' + oldName + '&newname=' + newName + '&path=' + currentDir,
					}).done(function (){
						renderPanel(panels.active());
					});
			}
		}
		
		function getDirSize(dir, writeTo){
			writeTo.html('...');
			$.ajax({
					cache : false, 
					type  : 'POST',
					url   : 'operations.php',
					data  : 'action=calcsize&path=' + dir,
				}).done(function (data){
					writeTo.html(data);
			});	
			
		}
		
		function newdir(){
			var dirName = prompt('Enter the new directory name');
			if (dirName !== null && dirName != '' && dirName != false && dirName !='.' && dirName !='..')
			var currentDir = panels.active().folder;
								
			$.ajax({
						cache : false, 
						type  : 'POST',
						url   : 'operations.php',
						data  : 'action=newfolder&filename=' + dirName + '&path=' + currentDir,
					}).done(function (data){
						renderPanel(panels.active());
					});
			
		}
		
		function unlink(){
			var filesList = getSelectedFiles();
			var quantity = filesList.length;
			if (confirm ('Are you really want to delete ' + quantity + ' file(s)?')){
				progressWindow.show();

				for (i=0; i<quantity; i++){
					if (filesList[i].filename == '..'){
						continue;
					}
					else{
						progressWindow.title('Deleting: ' + filesList[i].fullname);
						$.ajax({
							cache : false, 
							type  : 'POST',
							url   : 'operations.php',
							data  : 'action=del&filename=' + filesList[i].fullname,
							async : false, 
						}).done(function (data){
							var result = JSON.parse(data);
							if (typeof(result.message) != "undefined"){
								alert(filesList[i].fullname + ': \n' + result.message);
							}
							progressWindow.value((i+1)*100/quantity);
						});
					}
				} 
			}
			progressWindow.hide();
			renderPanel(panels.active());
			/*if (line.attr('data-is-folder') == 'true'){
				var fileName = line.attr('data-filename');
			}
			else{
				var fileName = line.attr('data-filename') + '.' + line.attr('data-extension') ;
			}*/
			
		}
		function copy(){
			if (panels.another().folder == panels.active().folder){
					alert('Cannot copy files to itself!');
					return;
			} 
			
			var filesList = getSelectedFiles();
			var quantity = filesList.length;

			if (confirm ('Are you really want to copy ' + quantity + ' file(s)?')){
				progressWindow.show();
				for (i=0; i<quantity; i++){
					if (filesList[i].filename == '..'){
						continue;
					}
					else{
						var copy_to = panels.another().folder + sep + filesList[i].filename + '.' + filesList[i].extension;
						var copy_from = filesList[i].fullname;

						progressWindow.title('Copying: ' + filesList[i].fullname);
						$.ajax({
							cache : false, 
							type  : 'POST',
							url   : 'copy.php',
							data  : 'from=' + copy_from + '&to=' + copy_to + '&overwrite=0',
							async : false, 
						}).done(function (data){
							var result = JSON.parse(data);
							if (typeof(result.message) != "undefined"){
								alert(filesList[i].fullname + ': \n' + result.message);
							}
							if (data == ''){
								alert('Copy error');
							}
							if (data == '2'){
								alert('File already exist!');
							}
							
							progressWindow.value((i+1)*100/quantity);
						}); 
					}
				} 
			}
			progressWindow.hide();
			renderPanel(panels.active());
			renderPanel(panels.another());
		}
		function move(){
			if (panels.another().folder == panels.active().folder){
					alert('Cannot move files to itself!');
					progressWindow.hide();
					return;
			}
			var filesList = getSelectedFiles();
			var quantity = filesList.length;
			if (confirm ('Are you really want to move ' + quantity + ' file(s)?')){
				progressWindow.show();
				for (i=0; i<quantity; i++){
					if (filesList[i].filename == '..'){
						continue;
					}
					else{
						var copy_to = panels.another().folder + sep + filesList[i].filename + '.' + filesList[i].extension;
						var copy_from = filesList[i].fullname;

						progressWindow.title('Moving: ' + filesList[i].fullname);
						
						 $.ajax({
							cache : false, 
							type  : 'POST',
							url   : 'move.php',
							data  : 'from=' + copy_from + '&to=' + copy_to + '&overwrite=0',
							async : false, 
						}).done(function (data){
							var result = JSON.parse(data);
							if (typeof(result.message) != "undefined"){
								alert(filesList[i].fullname + ': \n' + result.message);
							}
							if (data == ''){
								alert('Copy error');
							}
							if (data == '2'){
								alert('File already exist!');
							}
							progressWindow.value((i+1)*100/quantity);
						}); 
					}
				} 
			}
			progressWindow.hide();
			renderPanel(panels.active());
			renderPanel(panels.another());
		}

		/*Viewer functions*/
		
		function show_viewer(){
			
			if (line.attr('data-is-folder') != 'true'){
				$('#viewer_header').html(line.attr('data-folder'));
				$('#viewer_content').attr('src', '');
				$('#viewer').toggle();
				$('#viewer_content').attr('src', 'operations.php?action=getfile&folder=' + line.attr('data-folder'));
				 /* $.ajax({
					cache : false,
					type  : 'POST',
					url   : 'operations.php',
					data  : 'action=getfile&folder=' + line.attr('data-folder'),
				}).done(function (data){
					$('#viewer_content').html('<pre>' + data + '</pre>');
				});	  */
			} 
			
			$('#viewer_content').focus();
		}
		
		function hide_viewer(){
			$('#viewer').hide();
		}
		
		$('#viewer_close').click(function(){
			hide_viewer();
		});

		
		/*Editor functions*/
		
		function show_editor(){
			
			if (line.attr('data-is-folder') != 'true'){
				$('#editor_header').html(line.attr('data-folder'));
				$('#editor_content').val('');
				$('#editor').toggle();
				//$('#editor_content').attr('src', 'operations.php?action=getfile&folder=' + line.attr('data-folder'));
				 $.ajax({
					cache : false,
					type  : 'POST',
					url   : 'editor.php',
					data  : 'action=get&file=' + line.attr('data-folder'),
				}).done(function (data){
					$('#editor_content').val(data);
				});	  
			} 
			
			$('#editor_content').focus();
		}
		function hide_editor(){
			$('#editor').hide();
		}
		
		$('#editor_close').click(function(){
			hide_editor();
		});
		
		function editorSaveFile(){
			var filename = $('#editor_header').html();
			$('#editor_header').html('Saving...');
			
			 $.ajax({
					cache : false,
					type  : 'POST',
					url   : 'editor.php',
					data  : 'action=save&file=' + line.attr('data-folder') + '&content=' + $('#editor_content').val(),
				}).done(function (data){
					$('#editor_header').html(filename);
					alert('Сохранено');
				});	
		}
		
		/*functional buttons functions*/
		$('#f2').click(function(){
			rename();
		});
		$('#f3').click(function(){
			show_viewer();
		});
		$('#f5').click(function(){
			copy();
		});
		$('#f6').click(function(){
			move();
		});
		$('#f7').click(function(){
			newdir();
		});
		$('#f8').click(function(){
			unlink();
		});
		
		/*Keyboard Shortcuts*/
		
		$('#editor').keydown(function(event){
			event.stopPropagation();
			if ($(event.target).attr('id') == 'editor_content'){
				switch (event.keyCode){
					case 115: //F4
					case 27: //Esc
						event.preventDefault();
						$('#editor').hide();
						$('#editor_content').blur();
						var cursorPosition = line.attr('data-filename');
						renderPanel(panels.active(), cursorPosition);
						/*panelScrollTop();*/
					case 83: //Ctrl+S
						
						if (event.ctrlKey){
							event.preventDefault();
							editorSaveFile();
							return false;
						}
						break;
						
				}
			}
					
		});
		$('body').keydown(function(event){
			//alert(event.keyCode);

				event.preventDefault();
				switch (event.keyCode){
					case 45: //Insert
						selectLine(line);
						drawCursor(line.next());
						break;
					case 33: //PageUp
						if ($('#viewer').css('display') == 'block'){
							var scroll = +$('#viewer_content').prop('scrollTop') - +$('#viewer_content').prop('clientHeight');
							$('#viewer_content').animate({scrollTop: scroll}, 1);
							return false;
						}
						var line_height = parseInt(line.css('height'));
						var jump_to = line.index() - Math.round((panels.active().object.prop('clientHeight')/ line_height) - 1);
						drawCursor(getPageScrollLine(jump_to, 'up'));
						panelScrollTo (jump_to * line_height);
						break;
						
					case 34: //PageDown
						if ($('#viewer').css('display') == 'block'){
							var scroll = +$('#viewer_content').prop('scrollTop') + +$('#viewer_content').prop('clientHeight');
							$('#viewer_content').animate({scrollTop: scroll}, 1);
							return false;
						}
						//Переменная, содержащая количество строк на панель + номер текущей строки
						var jump_to = line.index() + Math.round((panels.active().object.prop('clientHeight')/ parseInt(line.css('height'))) - 1);
						drawCursor(getPageScrollLine(jump_to, 'down'));
						panelScrollTo (jump_to * parseInt(line.css('height')));
						break;
						
					case 35: //End
						panelScrollBottom();
						drawCursor(line.parent().children().last());
						break;
						
					case 36: //Home
						drawCursor(line.parent().children().first());
						panelScrollTop();
						break;
						
					case 37: //Left
						/* if ($('#viewer').css('display') == 'block'){
							var scroll = +$('#viewer_content').prop('scrollLeft') - 10;
							$('#viewer_content').animate({scrollLeft: scroll}, 1);
							return false;
						} */
						break;
						
					case 39: //Right
						/* if ($('#viewer').css('display') == 'block'){
							var scroll = +$('#viewer_content').prop('scrollLeft') + 10;
							$('#viewer_content').animate({scrollLeft: scroll}, 1);
							return false;
						} */
						break;
						
					case 38:
						//Up
						
					/* 	if ($('#viewer').css('display') == 'block'){
							var scroll = +$('#viewer_content').prop('scrollTop') - 10;
							$('#viewer_content').animate({scrollTop: scroll}, 1);
							return false;
						} */
						if (event.shiftKey){
							line.toggleClass ('selected');
						}
						drawCursor(line.prev());
						break;
						
					case 40:
						//down
						/* if ($('#viewer').css('display') == 'block'){
							var scroll = +$('#viewer_content').prop('scrollTop') + 10;
							$('#viewer_content').animate({scrollTop: scroll}, 1);
							return false;
						} */
						if (event.shiftKey){
							line.toggleClass ('selected');
						}
						drawCursor(line.next());
						
						break;
						
					case 9: //Tab
						panels.isActive = panels.getSibling();
						
						drawCursor();
						break;
					case 13:
						drawCursor(line);
						var cursorPosition;
						if (line.attr('data-filename') === '..'){
							cursorPosition = getCurrentFolderName();
						}
						if (line.attr('data-is-folder') === 'true'){
							panels[panels.isActive].folder = line.attr('data-folder');
							renderPanel(panels[panels.isActive], cursorPosition);
						}
						
						break;
					case 27:
						$('#viewer').hide();
						break;
					case 113: //F2
						rename();
						break;
					case 114: //F3
						show_viewer();
						break;
					case 115: //F4
						show_editor();
						break;
					case 116: //F5
						copy();
						break;
					case 117: //F6
						move();
						break;
					case 118: //F7
						newdir();
						break;
					
					case 119: //F8
					case 46:  //Delete
						unlink();
						break;
					
					case 82:  //Ctrl + R
						if (event.ctrlKey){
							renderPanel(panels.active());
						}
						break;
					case 32:  //Space
						line.toggleClass('selected');
						if (isNaN(parseInt(line.children('div:eq(2)').html()))){
							if (line.attr('data-is-folder') == 'true'){
								getDirSize(line.attr('data-folder'), line.children('div:eq(2)'));
							}
						}
						break;
				}
				return false;
		});
	});
	
</script>
</head>

<body>
<div id='viewer'>
	<header>
		<span id='viewer_header'></span>
		<div id='viewer_close'>&times;</div>
	</header>
	<iframe id='viewer_content'>
		text
	</iframe>
</div>
<div id = 'container'>
	<div id = 'toolbar'></div>
	<div id='files'>
		<div class = 'panel'>
			<div class = 'panel_current_folder active'>Current folder</div>
			<div class='panel_header'>
				<div class='panel_header_cell'>Имя</div>
				<div class='panel_header_cell'>Тип</div>
				<div class='panel_header_cell'>Размер</div>
				<div class='panel_header_cell'>Дата</div>
			</div>
			<div class='panel_list'>
				<div class='panel_row cursor'>
					<div class='panel_cell'>file</div>
					<div class='panel_cell'>php</div>
					<div class='panel_cell'>1000</div>
					<div class='panel_cell'>01.01.01</div>
					
				</div>
			</div>
		</div>
		
		<div class = 'panel'>
			<div class = 'panel_current_folder'>Current folder</div>	
			<div class='panel_header'>
				<div class='panel_header_cell'>Имя</div>
				<div class='panel_header_cell'>Тип</div>
				<div class='panel_header_cell'>Размер</div>
				<div class='panel_header_cell'>Дата</div>
			</div>
			<div class='panel_list'>
				<div class='panel_row cursor'>
					<div class='panel_cell'>file</div>
					<div class='panel_cell'>php</div>
					<div class='panel_cell'>1000</div>
					<div class='panel_cell'>01.01.01</div>
				</div>
			</div>
		</div>
		
	</div>
	<div id = 'bottom_panel'>
		<button id='f2'>F2 Rename</button>
		<button id='f3'>F3 View</button>
		<button id='f4'>F4 Edit</button>
		<button id='f5'>F5 Copy</button>
		<button id='f6'>F6 Move</button>
		<button id='f7'>F7 NewDir</button>
		<button id='f8'>F8 Delete</button>
	</div>
</div>
<div id='progress'>
	<header></header>
	<span id='progress_description'></span>
	<div class='progressbar'>
		<span></span>
		<div>
			
		</div>
	</div>
	<!--<div class='progressbar'>
		<span></span>
		<div>

		</div>
	</div>-->
</div>
<div id='editor'>
	<header>
		<span id='editor_header'></span>
		<div id='editor_close'>&times;</div>
	</header>
	<textarea id='editor_content'>

	</textarea>
</div>
</body>
</html>