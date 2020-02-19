<?php
	//check if information file exist
	$file_exist = file_exists("photoInfo.txt");
	if ($file_exist){
		//check if information file is not empty
		$file_size = filesize("photoInfo.txt");
		if($file_size > 0){
			header("Location: gallery.php");
		}
	}

	//if fail check, go back to index.html and display error message
	echo '<script language="javascript">';
	echo 'if(confirm("No image uploaded, please upload a image first!")) window.location.href="index.html"';
	echo '</script>';
	
?>