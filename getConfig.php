<?php
	include_once('software.php');
	//computers should be scraped from domain
	$everymanAssets = ['Everyman',['CATERING','EV01','EV02','EV03','EV04','EV05','EV06','EV07','EV08','EV09','EV10','EV11','EV12','EV13','EV14','EV15','EV16','EV17','EV18','EV19','EV20','EV21','EV22','EV23','EV24','EV25','EV26','EV27','EV28','EV29','EV30','EV31','EV32','EV33','EV34','EV35','EV36','EV37','EV38','EV41','EV42','EV43','EV44','EV45','EV46','EV47','EV48','EV49','EV50','EV51','EV52','EV53','EV54','EV55','EV57','EV58','EV59','EV60']];
	$playhouseAssets = ['Playhouse',['PHCCTV','PH01','PH02','PH03','PH04','PH05','PH06','PH07','PH08','PH09','PH10','PH11','PH12','PH13','PH14','PH15','PH16','PH17','PH18','PH19','PH20','PH21','PH22','PH25','PH26','PH27','PH28','PH29','PH30','PH31']];
	$laptopAssets = ['Laptops',['LAP01','LAP02','LAP03','LAP04','LAP05','LAP06','LAP07','LAP08','LAP09','LAP10','LAP11','LAP12','LAP13','LAP14','LAP15','LAP16','LAP17','LAP18','LAP19','LAP20','LAP21','LAP22','LAP23','LAP24','LAP25','LAP26','LAP27','LAP28','LAP29','LAP30','LAP31','LAP32','LAP33','LAP34','LAP35','LAP36','LAP37','LAP38','LAP39','LAP40','LAP41','LAP42','LAP43','LAP44','LAP45']];
	$tabletAssets = ['Tablets',['TAB01','TAB02','TAB03','TAB04','TAB05','TAB06','TAB07','TAB08','TAB09','TAB10','TAB11','TAB12','TAB13','TAB14','TAB15']];
	$pdqsAssets = ['PDQs',['PDQEV39','PDQEV40','PH23', 'PH24']];
	$yepAssets = ['YEPLaptops',['LAP-YEP1','LAP-YEP3','LAP-YEP4','LAP-YEP6']];
	$computers = [$everymanAssets, $playhouseAssets, $pdqsAssets, $laptopAssets, $tabletAssets, $yepAssets];

	//should be an array of arrays each containing the type and which bootstrap button type to use
	$softwareTypes = [['Standard','success'],['Non-Standard','info'],['Removal Required','danger'],['Out of Date','warning']];
	//should be an array of arrays each containing the type and which bootstrap button type to use
	$visibilityTypes = [['Visible','success'],['Hidden','danger']];

	$software = New Software('name','0');
	$softwareDetails = [];//$software->getProperties();
	unset($software);

	$config = ['computers' =>$computers,'softwareDetails'=>$softwareDetails, 'softwareTypes'=>$softwareTypes, 'softwareVisibility'=>$visibilityTypes];
	$jsonConfig =  json_encode($config);
	$file = fopen('config.json', 'w') or die('Unable to open file!');
	fwrite($file, $jsonConfig); 
	fclose($file);
	echo $jsonConfig;


?>

