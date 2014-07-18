<?
header('content-type:text/html;charset=utf-8');
include './lang/ru.php';
include 'config.php';
?>
<!doctype html>
<html>
<head>
<title> </title>
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
			'getActivePanel' : function() {
				return this[this.isActive];
			}

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
		
		function renderPanel(panel){
 			if (typeof (panel) === "undefined") return;
		
			$.ajax('filelist.php?folder=' + panel.folder).done(function(data) {
			
				panel.object.empty();
				
				var filelist = JSON.parse(data);
				
				//var currentFolder = panel.folder;
				
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
				

				
				/* if (typeof (line) === 'undefined'){
					line = $('.panel_row:eq(0)');
				}
				
				else{ */
					line = panels[panels.isActive].object.children('.panel_row:eq(0)');
					drawCursor(line);
				/* } */
				
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
			drawCursor($(event.target).parent());
		});
		
		$('.panel_list').contextmenu(function(event){
			event.preventDefault();
			selectLine($(event.target).parent());
			drawCursor($(event.target).parent());
		});
		
		$('.panel_list').dblclick(function(event){
			event.preventDefault();
			drawCursor($(event.target).parent());
			if (line.attr('data-is-folder') === 'true'){

				panels[panels.isActive].folder = line.attr('data-folder');
				renderPanel(panels[panels.isActive]);
			}
		});
		
		/*Interface Functions*/
		function panelScrollTop(){
			
			panels[panels.isActive].object.animate({scrollTop: 0}, 1);
		}
		function panelScrollBottom(){
			var height = panels[panels.isActive].object.height();
			panels[panels.isActive].object.animate({scrollTop: height}, 1);
		}
		
		/*Files functions*/
		
		/*Viewer functions*/
		
		
		/*Keyboard Shortcuts*/
		$('body').keydown(function(){
			
			
			/*
			F3-114
			4-115
			5-116
			6-117
			7-118
			R - 82
			*/
			///alert(event.keyCode);
			event.preventDefault();
			switch (event.keyCode){
				case 45:
					selectLine(line);
					drawCursor(line.next());
					break;
				case 35: //End
					/*panelScrollBottom();
					var quant = line.parent().children().length;
					*/
					break;
				case 36: //Home
					/*drawCursor(line.prev());
					panelScrollTop();*/
					break;
				case 38:
					//Up
					if (event.shiftKey){
						line.toggleClass ('selected');
					}
					drawCursor(line.prev());
					break;
					
				case 40:
					//down
					if (event.shiftKey){
						line.toggleClass ('selected');
					}
					drawCursor(line.next());
					
					break;
				case 9:
					panels.isActive = panels.getSibling();
					
					drawCursor();
					break;
				case 13:
					drawCursor(line);
					if (line.attr('data-is-folder') === 'true'){
						panels[panels.isActive].folder = line.attr('data-folder');
						renderPanel(panels[panels.isActive]);
					}
					/*TODO*/
					/*
					Выход из папки с установкой курсора на ней самой
					*/
					break;
				case 27:
					$('#viewer').hide();
					break;
				/*case 113: //F2
					rename();
					break;*/
				case 114: //F3
					show_viewer();
					break;
				/*case 116: //F5
					show_progress('Copy');
					break;
				case 117: //F6
					show_progress('Move');
					break;
				case 118: //F7
					newdir();
					break;
				case 119: //F8
				case 46:  //Delete
					unlink();
					break;
				*/
				case 82:  //Ctrl + R
					if (event.ctrlKey){
						renderPanel(panels.getActivePanel());
					}
					break;
				/*case 32:  //Space
					line.toggleClass('selected');
					if (line.attr('data-is-folder') == 'true'){
						getDirSize(line.attr('data-folder'), line.children('td:eq(2)'));
					}
					break;
				case 27:
					hide_viewer();
					break;
				*/
			}
			
			
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
	<div id='viewer_content'>
		text
	</div>
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
	<div class='progressbar'>
		<span></span>
		<div>

		</div>
	</div>
	
</div>
</body>
</html>