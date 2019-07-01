<?php
namespace App;

use PDO;
class Task
{
	public $id=null;
	private $name;
	private $type=[
		1 => "Встреча",
		2 => "Звонок",
		3 => "Совещание",
		4 => "Дело"
	];
	private $place;
	private $date;
	private $time;
	private $duration=[
		1 => "5_минут",
		2 => "15_минут",
		3 => "30_минут",
		4 => "1_час",
		5 => "2_часа",
		6 => "3_часа",
		7 => "Весь_день"
	];
	private $comment;
	private $error = array();
	public $table = 'tasks';

	private function validate()
	{
		foreach (['name', 'type', 'date', 'time', 'duration'] as $key) 
		{
			if(empty($this->$key))
			{
				echo $key;
				$this->error[$key] = "Это поле обязательно для ввода";
			}
		}

		if (!empty($this->error))
		{
			return false;
		}

		return true;
	}

	public static function get_pdo(){
		$_pdo;
        if (empty($_pdo))
        {
            $_pdo = new PDO('mysql:host=localhost;dbname=calendar','root',''); 
        }

        return $_pdo;
    }

    public function save_to_db()
    {
    	$sql = static::get_pdo()->prepare('INSERT INTO `'.$this->table.'` (`name`,`type`,`place`,`date`,`time`,`duration`,`comment`) VALUES (?,?,?,?,?,?,?);');

    	$sql->execute(array($this->name, $this->type, $this->place, $this->date, $this->time, $this->duration, $this->comment));

		return $sql->rowCount() === 1;
    }

    public function update_db()
    {
    	$this->name = isset($_POST['name']) ? trim($_POST['name']) : null;
		$this->type = isset($_POST['type']) ? trim($_POST['type']) : null;
		$this->place = isset($_POST['place']) ? trim($_POST['place']) : null;
		$this->date = isset($_POST['cdate']) ? trim($_POST['cdate']) : null;
		$this->time = isset($_POST['ctime']) ? trim($_POST['ctime']) : null;
		$this->duration = isset($_POST['duration']) ? trim($_POST['duration']) : null;
		$this->comment = isset($_POST['comment']) ? trim($_POST['comment']) : null;

    	$sql = static::get_pdo()->prepare('UPDATE `'.$this->table.'` SET `name`= ?, `type`= ?, `place`= ?, `date`= ?, `time`= ?, `duration`= ?, `comment`= ? where `id`= ? limit 1;');
    	$sql->execute(array($this->name, $this->type, $this->place, $this->date, $this->time, $this->duration, $this->comment, $_GET['id']));

    	header('Location: /SHU/calendar');
    }

    public function validate_two()
    {
    	if ($this->name != trim($_POST['name']) or $this->type != trim($_POST['type']) or $this->place != trim($_POST['place']) or $this->date != trim($_POST['cdate']) or $this->time != trim($_POST['ctime']) or $this->duration != trim($_POST['duration']) or $this->comment != trim($_POST['comment']))
    	{
    		return true;
    	}

    	return false;
    }

    public function read_spec($td)
    {
    	switch ($td) {
    		case 'today':
    			$sql = static::get_pdo()->prepare('SELECT * FROM `' . $this->table . '` where `date` = date_format(now(), "%Y-%m-%d");');
    			break;
    		
    		case 'tom':
    			$sql = static::get_pdo()->prepare('SELECT * FROM `' . $this->table . '` where `date` = (date_format(now(), "%Y-%m-%d") + INTERVAL 1 DAY);');
    			break;
    		case 'thweek':
				$sql = static::get_pdo()->prepare('SELECT * FROM `' . $this->table . '` where year(date) = year(now()) and week(date, 1) = week(now(), 1);');
				break;
			case 'neweek':
				$sql = static::get_pdo()->prepare('SELECT * FROM `' . $this->table . '` where year(date) = year(now() + INTERVAL 1 WEEK) and week(`date`, 1) = week(now() + INTERVAL 1 WEEK, 1);');
				break;
    	}
        $sql->execute();

    	$objects = [];

        while ($object = $sql->fetchObject(static::class))
        {
            $str .= "<tr><td align='center'>".$this->type[$object->type]."</td><td align='center'><a href='new_task.php?id=$object->id'>".$object->name."</a></td><td align='center'>".$object->place."</td><td align='center'>".$object->date." ".$object->time."</td><td align='center'>".($object->done == "no" ? "<input type='checkbox' id='test' name='test[]'>" : "<input type='checkbox' id='test' name='test[]' checked>")."</td><td align='center'>".$object->comment."</td></tr>";
    	}

	    	return $str;
    }

    public function read_scroll($td)
    {
    	switch ($td) {
    		case 'cur':
    			$sql = $this->get_pdo()->prepare('SELECT * FROM `'.$this->table.'` WHERE (curdate()<`date` and done=?) or (curdate()=`date` and curtime()<`time` and done=?);');
    			$sql->execute(array('no','no'));
    			break;
    		case 'ov':
    			$sql = $this->get_pdo()->prepare('SELECT * FROM `'.$this->table.'` WHERE (curdate()>`date`) or (curtime()>`time` and curdate()=`date`) and done="no";');
        		$sql->execute();
    			break;
    		case 'ma':
    			$sql = $this->get_pdo()->prepare('SELECT * FROM `'.$this->table.'` WHERE done=?;');
        		$sql->execute(array('yes'));
        		break;
    	}

    	while ($object = $sql->fetchObject(static::class))
        {
            $str .= "<tr><td align='center'>".$this->type[$object->type]."</td><td align='center'><a href='new_task.php?id=$object->id'>".$object->name."</a></td><td align='center'>".$object->place."</td><td align='center'>".$object->date." ".$object->time."</td><td align='center'>".($object->done == "no" ? "<input type='checkbox' id='test' name='test[]'>" : "<input type='checkbox' id='test' name='test[]' checked>")."</td><td align='center'>".$object->comment."</td></tr>";
        }
        return $str;
    }

    public function datetime($d)
    {
		$sql = $this->get_pdo()->prepare('SELECT * FROM `'.$this->table.'` WHERE `date` =?;');
		$sql->execute(array($d));
    	while ($object = $sql->fetchObject(static::class))
        {
            $str .= "<tr><td align='center'>".$this->type[$object->type]."</td><td align='center'><a href='new_task.php?id=$object->id'>".$object->name."</a></td><td align='center'>".$object->place."</td><td align='center'>".$object->date." ".$object->time."</td><td align='center'>".($object->done == "no" ? "<input type='checkbox' id='test' name='test[]'>" : "<input type='checkbox' id='test' name='test[]' checked>")."</td><td align='center'>".$object->comment."</td></tr>";
        }
        return $str;
    }

    public function read_to_db()
    {
    	"<script src = 'test.js'></script>";
    	$sql = static::get_pdo()->prepare('SELECT * FROM `' . $this->table . '`;');
        $sql->execute();

        $objects = [];
        $str = '';
        while ($object = $sql->fetchObject(static::class))
        {
            $str .= "<tr><td align='center'>".$this->type[$object->type]."</td><td align='center'><a href='new_task.php?id=$object->id'>".$object->name."</a></td><td align='center'>".$object->place."</td><td align='center'>".$object->date." ".$object->time."</td><td align='center'>".($object->done == "no" ? "<input type='checkbox' id='test' name='test[]'>" : "<input type='checkbox' id='test' name='test[]' checked>")."</td><td align='center'>".$object->comment."</td></tr>";
            $objects[] = $object;
        }

        return $str;
    }

    public function read_for_id($gid)
    {
    	$sql = static::get_pdo()->prepare('SELECT * FROM `' . $this->table . '` where `id`='.$gid.';');
    	$sql->execute();

    	$object = $sql->fetchObject(static::class);
    	$this->id = $gid;
    	$this->name = $object->name;
    	$this->type = $object->type;
    	$this->place = $object->place;
    	$this->date = $object->date;
    	$this->time = $object->time;
    	$this->duration = $object->duration;
    	$this->comment = $object->comment;

    	$_POST['name'] = $object->name;
    	$_POST['type'] = $object->type;
    	$_POST['place'] = $object->place;
    	$_POST['cdate'] = $object->date;
    	$_POST['ctime'] = $object->time;
    	$_POST['duration'] = $object->duration;
    	$_POST['comment'] = $object->comment;
    }

	public function insert()
	{
		$this->name = isset($_POST['name']) ? trim($_POST['name']) : null;
		$this->type = isset($_POST['type']) ? trim($_POST['type']) : null;
		$this->place = isset($_POST['place']) ? trim($_POST['place']) : null;
		$this->date = isset($_POST['cdate']) ? trim($_POST['cdate']) : null;
		$this->time = isset($_POST['ctime']) ? trim($_POST['ctime']) : null;
		$this->duration = isset($_POST['duration']) ? trim($_POST['duration']) : null;
		$this->comment = isset($_POST['comment']) ? trim($_POST['comment']) : null;

		var_dump($this->duration);

		if ($this->validate())
		{
			$this->save_to_db();
			header('Location: /SHU/calendar');
		}
	}
}
