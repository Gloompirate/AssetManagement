<?php
	include('software.php');
	include('computer.php');
	$software = New Software("name","0");
	//$software = unserialize(file_get_contents('test.txt'));
	echo json_encode($software) .'<br>';
	$software->update();
	echo '<br>';
	echo json_encode($software) .'<br>';
	$file = fopen('test.txt', 'w') or die('Unable to open file!');
	fwrite($file, serialize($software)); 
	fclose($file);
	unset($software);
?>


