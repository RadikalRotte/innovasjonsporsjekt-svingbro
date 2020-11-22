ok	- save file
<?php
// Decodes received JSON and dumps selected fields to datalog.txt
	$headers = getallheaders();
	if ($headers["Authorization"] == "mysecret") {
		$json_str = file_get_contents('php://input');
		$json_obj = json_decode($json_str, true);

		// load selected data fields into PHP array
		// look at the JSON file from the previous example to know decide fields to access
		$mydata = array(
			'bridge' => $json_obj['payload_raw'],
			'time' => $json_obj['metadata']['time'],
		);

		// write the data fields to a log file (in text format)
		$status = strval(base64_decode($mydata['bridge']));
		$date_time = substr($mydata['time'], 0, 10);
		$clock_time = substr($mydata['time'], 11, 8);

		$file = fopen("datalog.txt","w"); // opens datalog.txt and overwrites the old data with the new
		fwrite($file, 
			$mydata['time'].', '.
			$mydata['bridge'].', '
		);
		
		fwrite($file, PHP_EOL); // end of line
		fclose($file);
	}
?>
