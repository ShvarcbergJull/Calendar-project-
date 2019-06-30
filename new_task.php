<?php
spl_autoload_register();

use App\Task;
$task = new Task;
if (!empty($_POST) and empty($_GET))
{
	$task->insert();
}

if (!empty($_GET) and empty($_POST))
{
	$task->read_for_id($_GET['id']);
}

if (!empty($_GET) and !empty($_POST))
{
	if ($task->validate_two())
		$task->update_db();
}

function selected($key, $i)
{
	if (empty($_POST))
		return null;
	if ($_POST[$key] == $i)
	{
		return 'selected';
	}
	return null;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Новая задача</title>
	<h1><font face="Palatino Linotype"><i>Мой календарь</i></font></h1>
	<style>
		* {box-sizing: border-box;}
		.form-inner {padding: 50px;}
		.form-inner input,
		.form-inner textarea {
			display: block;
			width: 50%;
			padding: 0 20px;
			margin-left: auto;
			margin-right: auto;
			margin-bottom: 10px;
			background: #E9EFF6;
			line-height: 40px;
			border-width: 0;
			border-radius: 20px;
			font-family: 'Roboto', sans-serif;
		}
		.form-inner input[type="submit"] {
			width: 20%;
			margin-top: 30px;
			background: #FFC0CB;
			border-bottom: 4px solid #FFB6C1;
			font-size: 14px;
		}
		.form-inner input[type="date"]{
			color: #707981;
		}
		.form-inner input[type="time"]{
			color: #707981;
		}
		.form-inner select{
			margin-left: auto;
			margin-right: auto;
			display: block;
			width: 50%;
			height: 30px;
			padding: 0 20px;
			margin-bottom: 10px;
			background: #E9EFF6;
			line-height: 40px;
			border-width: 0;
			border-radius: 20px;
			font-family: 'Roboto', sans-serif;
			color: #707981;
		}
		.form-inner textarea {resize: none;}
		.form-inner h3 {
			margin-top: 0;
			font-family: 'Roboto', sans-serif;
			font-weight: 500;
			font-size: 24px;
			color: #707981;
		}
	</style>
</head>
<body background="https://img3.badfon.ru/original/1600x900/3/9e/cvetok-vetka-vaza-boke-belyy.jpg">	
	<form action="<?= $_SERVER['REQUEST_URI'];?>" method="POST" align="center" class="form-inner">
		    <h3><?php echo empty($_GET) ? 'Новая задача':'Изменение задачи' ?></h3>
		    <input placeholder="Название задачи" name="name" value="<?= isset($_POST['name']) ? $_POST['name']:''?>" required>
		    <select name='type'>
				<optgroup label="Тип задачи">
					<option value=1 <?=selected('type', 1)?> name="meeting">Встреча</option>
					<option value=2 <?=selected('type', 2)?> name="call">Звонок</option>
					<option value=3 <?=selected('type', 3)?> name="caucus">Совещание</option>
					<option value=4 <?=selected('type', 4)?> name="case">Дело</option>
				</optgroup>
			</select>
		    <input placeholder="Место" name="place" value="<?= isset($_POST['place']) ? $_POST['place']:''?>">
		    <input type="date" name="cdate" value="<?= isset($_POST['cdate']) ? $_POST['cdate']:''?>" required>
		    <input type="time" name="ctime" value="<?= isset($_POST['ctime']) ? $_POST['ctime']:''?>" required>
		    <select name="duration" value="<?= isset($_POST['duration']) ? $_POST['duration']:''?>">>
				<optgroup label="Продолжительность">
					<option value=1 <?=selected('duration', 1)?> name="min5">5 минут</option>
					<option value=2 <?=selected('duration', 2)?> name="min15">15 минут</option>
					<option value=3 <?=selected('duration', 3)?> name="min30">30 минут</option>
					<option value=4 <?=selected('duration', 4)?> name="hour1">1 час</option>
					<option value=5 <?=selected('duration', 5)?> name="hour2">2 часа</option>
					<option value=6 <?=selected('duration', 6)?> name="hour3">3 часа</option>
					<option value=7 <?=selected('duration', 7)?> name="day">Весь день</option>
				</optgroup>
			</select>
		    <textarea placeholder="Комментарий..." rows="3" name='comment'><?= isset($_POST['comment']) ? $_POST['comment']:''?></textarea>
		    <input type="submit" value="<?= empty($_GET) ? 'Добавить задачу':'Изменить' ?>">
	</form>
</body>
</html>
