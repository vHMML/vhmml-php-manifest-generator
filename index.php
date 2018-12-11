<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <title>PHP Manifest Generator</title>
  </head>
  <body>
    <div class="container">
<?php

	function test_input($data) {
	  $data = trim($data);
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  return $data;
	}
	
	$location = "";
	$system = "READING_ROOM";
	$environment = "https://www.vhmml.org/image/READING_ROOM/";
	
      if (isset($_GET['environment'])) {
		if( $_GET["environment"]) {
			//echo "environment ". $_GET['environment']. "<br />";
			$environment = test_input($_GET['environment']);		
		}
   }
    if (isset($_GET['location'])) {
		if( $_GET["location"]) {
			//echo "location ". $_GET['location']. "<br />";
			$location = test_input($_GET['location']);			
		}
   }
   if (isset($_GET['system'])) {
		if( $_GET["system"]) {
			//echo "system ". $_GET['system']. "<br />";
			$system = test_input($_GET['system']);			
		}
   }
?>

<h1>PHP Manifest Generator for vHMML</h1>
<p>Copy images to a location on your C: Drive. For example, C:/Manifest folder. Make sure you follow the folder structure "{Collection}/{Object}/{Image files}". For example, "HMML/HMML 451/HMML 00451_002r.JPG". 
Delete or Backup the data.json files within the folders. If you don't, the application will overwrite the file! Choose a Location, Environment, and System. Then click Generate.<p>

<form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method = "GET">
	  <div class="form-row">
		<div class="input-group col-md-12">
		  <div class="input-group-prepend">
			<span class="input-group-text" id="basic-addon1">Location (ex. "C:/Manifest")</span>
		  </div>
		  <input type="text" class="form-control" aria-label="location" aria-describedby="basic-addon1" id="location" placeholder="" name = "location" value="<?php echo $location; ?>">
		</div>
		</div>
	  <br>
	  <div class="form-row">
		  <div class="input-group col-md-8">
			  <div class="input-group-prepend">
				<label class="input-group-text" for="environment">Environment</label>
			  </div>
			  <select class="custom-select" id="environment" name="environment">
				<option value="https://www.vhmml.org/image/" <?php if($environment=="https://www.vhmml.org/image/") { echo "selected"; } ?>>PROD (https://www.vhmml.org/image/)</option>
				<option value="https://test.vhmml.org/image/" <?php if($environment=="https://test.vhmml.org/image/") { echo "selected"; } ?>>TEST (https://test.vhmml.org/image/)</option>
				<option value="http://hmmldev.vhmml.org:8080/vhmml/image/" <?php if($environment=="http://hmmldev.vhmml.org:8080/vhmml/image/") { echo "selected"; } ?>>DEV (http://hmmldev.vhmml.org:8080/vhmml/image/)</option>
			  </select>        
			</div>
			<div class="input-group col-md-4">
			  <div class="input-group-prepend">
				<label class="input-group-text" for="system">System</label>
			  </div>
			  <select class="custom-select" id="system" name="system">
				<option value="READING_ROOM" <?php if($system=="READING_ROOM") { echo "selected"; } ?>>READING_ROOM</option>
				<option value="MUSEUM" <?php if($system=="MUSEUM") { echo "selected"; } ?>>MUSEUM</option>
				<option value="FOLIO" <?php if($system=="FOLIO") { echo "selected"; } ?>>FOLIO</option>
			  </select>        
			</div>
	  </div>
	  <br>
	  <div class="form-row">
		<div class="form-group d-flex flex-row-reverse col-md-12">
			<button type="submit" class="btn btn-primary">Generate</button>&nbsp;<a class="btn btn-warning" href="index.php" role="button">Clear</a>
		</div>
	  </div>
	</form>
	<hr class="endsearch">
	
<?php
if ($environment and $location and $system) {
	
	$startTime = date("Y-m-d H:i:s");
	echo "Start: " . $startTime . "<br>";
	
	//$dir = "C:/Manifest";
	$dir = $location;
	$dirCount = strlen($dir)+1; //"12";// was 8 for Museum "12";//needs to be dynamic!!!!

	$contents = getDirContents($dir,$dirCount,$environment,$system);
	//var_dump($contents);

	$endTime =  date("Y-m-d H:i:s");
	$differenceInSeconds = strtotime($endTime) - strtotime($startTime);
	echo "End: " . $startTime;
	echo "<br><br><p><strong>Time to finish: " . $differenceInSeconds . " second(s).</strong></p>";
} //end if to run manifest generator

function getDirContents($dir, $dirCount,$environment,$system)
{
  $results_array = array();
  $handle = opendir($dir);
  if ( !$handle ) return array();
  $contents = array();
  while ( $entry = readdir($handle) )
  {
	if ( $entry=='.' || $entry=='..' ) continue;
	$filename = $entry;
	$entry = $dir."/".$entry; //was $entry = $dir.DIRECTORY_SEPARATOR.$entry; but DIRECTORY_SEPARATOR is "\" and we need "/"
	if ( is_file($entry) )
	{
		$contents[] = $entry;
		if (list($width, $height, $type, $attr) = @getimagesize($entry)) {
			//$results_array[] = "{\"format\":\"image/jpeg\",\"width\":". $width. ",\"id\":\"https://www.vhmml.org/image/READING_ROOM/". substr($dir, $dirCount) ."/" . $filename."\",\"label\":\"".$filename."\",\"height\":".$height."},";
			$results_array[] = "{\"format\":\"image/jpeg\",\"width\":". $width. ",\"id\":\"".$environment.$system."/". substr($dir, $dirCount) ."/" . $filename."\",\"label\":\"".$filename."\",\"height\":".$height."},";
		} else {
			$results_array[] = "";
			//echo "Error getting image size information for " .$dir . "/" . $file;
		}
							
	}
	else if ( is_dir($entry) )
	{
	  $contents = array_merge($contents, getDirContents($entry,$dirCount,$environment,$system));
	  $results_array[] = "";
	}
  }
  
    //Output findings
	$JSONStr = "]";
	if($results_array) {
		$JSONStr = "[";
		foreach($results_array as $value)
		{
			$JSONStr .=  $value;
		}
		//get rid of the last comma
		$JSONStr = substr($JSONStr, 0, -1);
		$JSONStr .= "]";
	}
	
	//put out the data.json
	if ($JSONStr !== "]" ) {
		echo "Writing ". $dir."/". 'data.json' ."<br>";
		$fp = fopen($dir.DIRECTORY_SEPARATOR. 'data.json', 'w');
		fwrite($fp, $JSONStr);
		fclose($fp);
	}
	
	closedir($handle);
	return $contents;
}

	/*
	//may have to use to get the format info rather than hard code it?!?
	$image_info = getImageSize($path);
	switch ($image_info['mime']) {
	case 'image/gif':
		$extension = '.gif';
		break;
	case 'image/jpeg':
		$extension = '.jpg';
		break;
	case 'image/png':        
		$extension = '.png';
		break;
	default:
		// handle errors
		break;
	}
	*/
?>
<br><br>
 <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</div>
	</body>
</html>
