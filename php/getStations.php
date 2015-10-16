<?php
	try {
		$dbConnect = new PDO("mysql:host=localhost;dbname=vwfunlyg_trainsystem;","vwfunlyg_client","pancakeMix69");
		$dbConnect->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
		$stm = $dbConnect->prepare("SELECT * FROM trainstations");
		$stm->execute();

		$stationArray = array();
		$arr = array();
		foreach($stm as $val) {
			$location = explode(" ", $val[2]);
			$arr = array('ID' => $val[0], 'Name' => utf8_encode($val[1]), 'Lat' => $location[1], 'Lng' => $location[0], 'Country' => $val[3]);

			array_push($stationArray,$arr);
		}
		 echo json_encode($stationArray);
	} catch (Exception $e) {
		echo $e->getMessage();
	}
?>