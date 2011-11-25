<?php
ob_implicit_flush(1);

$row = 0;
$handle = fopen ("euronics.csv","r");
while ( ($data = fgetcsv ($handle, 1000, ";", '"')) !== FALSE ) {

    $row++;
	
	if( $row < 3 )
		continue;

	//echo '<pre>'.$row.'='.print_r($data,1).'</pre>';

	$lat = 0;
	$lon = 0;
	
	// find coordinates using google maps api
	$sQuery = sprintf(
		"%s %s %s %s"
	,	$data[3]
	,	$data[1]
	,	$data[2]
	,	'DE'
	);
	
	$sResponse = NULL;
	$sResponse = file_get_contents("http://maps.google.com/maps/geo?q=".rawurlencode($sQuery)."&output=json&oe=utf8&sensor=false&hl=de");
	
	if( !empty($sResponse) ) {
	
		$aResponse = array();
		$aResponse = json_decode($sResponse,1);

		if( !empty($aResponse['Status']) && $aResponse['Status']['code'] == '200' ) {
		
			$lon = $aResponse['Placemark'][0]['Point']['coordinates'][0];
			$lat = $aResponse['Placemark'][0]['Point']['coordinates'][1];
			
			// create mysql query
			$sQuery2 = sprintf(
				"INSERT INTO `tl_storelocator_stores` VALUES ('', '1', '%s', '%s', '', '%s', '%s', '%s', '%s', '%s', '%s', 'DE', '%s', '%s', null);"
			,	time()
			,	mysql_real_escape_string($data[0])
			,	mysql_real_escape_string($data[6])
			,	mysql_real_escape_string($data[4])
			,	mysql_real_escape_string($data[5])
			,	mysql_real_escape_string($data[3])
			,	mysql_real_escape_string($data[1])
			,	mysql_real_escape_string($data[2])
			, 	mysql_real_escape_string($lon)
			,	mysql_real_escape_string($lat)
			);
			
			echo $sQuery2."\n";

		} else {
			echo "ERROR: COULD NOT FIND COORDINATES FOR `".$sQuery."`\n";
		}
	} else {
		echo "ERROR: COULD NOT FIND COORDINATES FOR `".$sQuery."`\n";
	}
}
fclose ($handle);

echo "\n--------------------DONE---------------------";
?>
