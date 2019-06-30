<?php 
spl_autoload_register();
error_reporting(0);

use App\Task;

function select($i)
{
	if (empty($_GET) || !isset($_GET['tr']))
		return false;
	if ($_GET['tr'] == $i)
	{
		return 'selected';
	}
	return null;
}

function spsel()
{
	if (empty($_GET) || !isset($_GET['tr']))
		return 'selected';
	return false;
}

if (!empty($_POST))

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/> 
	<title>Calendar</title>
	<h1><font face="Palatino Linotype"><i>Мой календарь</i></font></h1>
	<style>
		table{
			background-color: rgba(255,255,255,0.7);
		}

		a{
			color: black;
			text-decoration: none;
		}
		a:hover{
			text-decoration: underline;
		}
	</style>
</head>
<body background="https://img3.badfon.ru/original/1600x900/3/9e/cvetok-vetka-vaza-boke-belyy.jpg">
	<h2 align="center"><font face="CALIBRI"><i>Список задач</i></font></h2>
	<form > 
 		<button type="Button" onclick=""><font face="CALIBRI"><a href="/SHU/calendar?time=today">Сегодня</a></font></button>
 		<a>&nbsp;&nbsp;</a>
 		<button type="Button" onclick=""><font face="CALIBRI"><a href="/SHU/calendar?time=tom">Завтра</a></font></button>
 		<a>&nbsp;&nbsp;</a>
 		<button type="Button" onclick=""><font face="CALIBRI"><a href="/SHU/calendar?time=thweek">На эту неделю</a></font></button>
 		<a>&nbsp;&nbsp;</a>
 		<button type="Button" onclick=""><font face="CALIBRI"><a href="/SHU/calendar?time=neweek">На след. неделю</a></font></button>
 		<a>&nbsp;&nbsp;</a>
		<select name='stat' onchange="document.location=this.options[this.selectedIndex].value"  action="<?= $_SERVER['REQUEST_URI'];?>" method="GET">
			<optgroup label="Срок задач">
				<option value="/SHU/calendar?tr=cur" <?=select('cur')?> name="current">Текущие задачи</option>
				<option value="/SHU/calendar?tr=ov" <?=select('ov')?> name="overdue">Просроченые задачи</option>
				<option value="/SHU/calendar?tr=ma" <?=select('ma')?> name="made">Выполненые задачи</option>
				<option value="/SHU/calendar" <?=spsel('')?> name="special">Конкретное число</option>
			</optgroup>
		</select>
		<a>&nbsp;&nbsp;</a>
 		<input type="date" name="calendar" value="<?= isset($_POST['calendar']) ? $_POST['calendar']:''?>">
 		<a>&nbsp;&nbsp;</a>
 		<input type="submit" value="Посмотреть задачи">
 	</form>
 	<br>
 	<table border = 1 align="center" width=100%>
 		<tr>
 			<th><font face="CALIBRI">Тип</font></th>
 			<th><font face="CALIBRI">Задача</font></th>
 			<th><font face="CALIBRI">Место</font></th>
 			<th width=200px><font face="CALIBRI">Дата и время</font></th>
 			<th width=2px><font face="CALIBRI">Отметка о выполнение</font></th>
 			<th><font face="CALIBRI">Комментарий</font></th>
 		</tr>
 		<?php
 			$task = new Task;
 			if (empty($_GET))
 				echo $task->read_to_db();
 			else
 			{
 				if (isset($_GET['time']))
 				{
 					echo $task->read_spec($_GET['time']);
 					exit;
 				}
 				if (isset($_GET['tr']))
 				{
 					echo $task->read_scroll($_GET['tr']);
 					exit;
 				}
 				if (isset($_GET['calendar']))
 				{
 					echo $task->datetime($_GET['calendar']);
 				}
 			}
 		?>
 	</table>
 	<form action="new_task.php">
 		<p align="right"><input type="submit" value="Добавить задачу"></p>
 	</form>
</body>
</html>
