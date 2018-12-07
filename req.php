<?php
$dbhost = '192.168.1.160';
$dbuser = 'root';
$dbpass = '';
$dbname = 'warehouse';

//получаем значение баркода
if(isset($_POST['barcode'])) $barcode = $_POST['barcode'];
//получаем значение переключателя на выдачу или загрузку
if(isset($_POST['course']))
{
    $course = $_POST['course'];
}
// где-то здесь было бы хорошо проверить, готов ли контроллер

//подключаемся к базе
$connect = mysql_connect($dbhost, $dbuser, $dbpass);
if(! $connect )
{
  die('Could not connect: ' . mysql_error());
}
mysql_select_db ($dbname, $connect);

//1:запихиваем на склад  0:вынимаем
if($course == '1')
{
	//защита от дурака
	if(mysql_query("SELECT id FROM warehouse WHERE barcode = '$barcode'"))
	{
		echo "Ошибка! Баркод: ".$barcode." уже имеется в базе.";
		//валим отсюда
	}
    //ищем первое свободное место в базе (где нет баркода)
	$query = "SELECT TOP 1 id FROM warehouse WHERE barcode is NULL";
	$res = mysql_query($query);
	//записываем в это место баркод
	$query = "UPDATE warehouse SET barcode = '$barcode' WHERE id = '$res'";
	mysql_query($query);
	$query = "SELECT code FROM warehouse WHERE id = '$res'";
	//code: код полки для контроллера
	$res = mysql_query($query);
	// дальше тьма по передаче полки контроллеру
	// вместе с полкой нужно передать 1 или $course, что говорит о загрузке
	
}
else //иначе 0-вынимаем
{
	//защита от дурака
	if(!mysql_query("SELECT id FROM warehouse WHERE barcode = '$barcode'"))
	{
		echo "Ошибка! Баркода: ".$barcode." в базе не существует.";
		//валим отсюда
	}
    //вытаскиваем код полки
	$query = "SELECT code FROM warehouse WHERE barcode = '$barcode'";
	$res = mysql_query($query);
	//удаляем баркод
	$query = "UPDATE warehouse SET barcode = NULL WHERE code = '$res'";
	mysql_query($query);
	// дальше тьма по передаче полки контроллеру
	// вместе с полкой нужно передать 0 или $course, что говорит о выгрузке
}

//закрывать тут или в if'е?
mysql_close($connect);
?>