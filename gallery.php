<?php
//error_reporting(NULL);

// class for storing information temporally
class imageInfor
{
	public $f_path = NULL;
	public $p_name = NULL;
	public $d_taken = NULL;
	public $p_grapher = NULL;
	public $location_name = NULL;
}

// Store image in /uploads
$target_path = "uploads/";
//for avoiding undefined index: fileToUpload
$tmp_file_name = isset($_FILES['fileToUpload']['tmp_name']) ? $_FILES['fileToUpload']['tmp_name'] : '';
$file_name = isset($_FILES['fileToUpload']['name']) ? $_FILES['fileToUpload']['name'] : '';
$target_file = $target_path . basename($file_name);
move_uploaded_file($tmp_file_name, $target_file);

// For checking image name repeat purpose
$readImageFile = fopen("photoInfo.txt", "a+") or die("Unable to open file!");
$contents = fread($readImageFile, filesize("photoInfo.txt"));
$info = explode(';', $contents);
fclose($readImageFile);
$repeatSubmit = false;
for ($i = 0; $i < sizeof($info); $i++) {
	if ($info[$i] == $target_file) {
		$repeatSubmit = true;
	}
}
//default sorting method 
if (!$repeatSubmit) {
	// Store information from form in variables
	$photo_name = isset($_POST['photo_name']) ? $_POST['photo_name'] : '';
	$date_taken = isset($_POST['date_taken']) ? $_POST['date_taken'] : '';
	$photographer = isset($_POST['photographer']) ? $_POST['photographer'] : '';
	$location = isset($_POST['location']) ? $_POST['location'] : '';
}
$sort_method = isset($_GET['selection']) ? $_GET['selection'] : '';


// Store variables in local txt file
// content format: image path; photo_name; date_taken; photographer; locationl;
if (!$repeatSubmit and $target_file != $target_path) {
	$myfile = fopen("photoInfo.txt", "a") or die("Unable to open file!");
	$writeString = $target_file . ";" . $photo_name . ";" . $date_taken . ";" . $photographer . ";" . $location . ";";
	fwrite($myfile, $writeString);
	fclose($myfile);

	$contents .= $target_file . ";" . $photo_namphpe . ";" . $date_taken . ";" . $photographer . ";" . $location . ";";
	$temp_array = array($target_file, $photo_name, $date_taken, $photographer, $location);
	$info = array_merge($info, $temp_array);
}

// store all images information in a class array for display purpose
$total_num_of_images = (sizeof($info) - 1) / 5;
$images[] = new imageInfor();

for ($i = 0; $i < $total_num_of_images; $i++) {
	$images[$i] = new stdClass;
	$images[$i]->f_path = $info[$i * 5];
	$images[$i]->p_name = $info[$i * 5 + 1];
	$images[$i]->d_taken = $info[$i * 5 + 2];
	$images[$i]->p_grapher = $info[$i * 5 + 3];
	$images[$i]->location_name = $info[$i * 5 + 4];
}

?>

<!DOCTYPE html>
<html>

<head>
	<title>Photo Album Web App</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Bootstrap -->
	<link href="css/bootstrap.css" rel="stylesheet">
	<!-- jQuery (Bootstrap's JavaScript plugin need to import jQuery) -->
	<script src="https://code.jquery.com/jquery.js"></script>
	<!-- include all complied plugin -->
	<script src="js/bootstrap.js"></script>

	<!-- refresh the page after first load -->
	<script type='text/javascript'>
		(function() {
			if (window.localStorage) {
				if (!localStorage.getItem('firstLoad')) {
					localStorage['firstLoad'] = true;
					window.location.reload();
				} else
					localStorage.removeItem('firstLoad');
			}
		})();
	</script>
</head>


<body>


	<div>
		<ul class="nav nav-pills justify-content-center">
			<li class="nav-item">
				<a class="nav-link" href="index.html">Home</a>
			</li>
			<li class="nav-item">
				<a class="nav-link active" href="error_check.php">Gallery</a>
			</li>
		</ul>
		<br><br>
	</div>

	<div>
		<h2>View All Photos</h2><br>
	</div>


	<div>
		<h3>
			<form>
				<table>
					<th>Sort By: </th>
					<th>
						<form method="get" action="gallery.php">
							<select name='selection' onchange='this.form.submit()' class="btn btn-secondary dropdown-toggle">
								<option value="SortingMethod">...</option>
								<option value="PhotoName">Photo Name</option>
								<option value="DateTaken">Date Taken</option>
								<option value="Photographer">Photographer</option>
								<option value="Location">Location</option>
							</select>
							<noscript><input type="submit" value="Submit"></noscript>
						</form>
					</th>
					<br>
					<a href="index.html" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Upload</a>
					</tr>
				</table>
			</form>
		</h3>
		<br>
	</div>

	<table>
		<tr>
			<?php
			//sorting array using usort($array, comparator_function)
			// functions used for comparator scores of two object/students 

			function sort_by_name($object1, $object2)
			{
				return $object1->p_name > $object2->p_name;
			}
			function sort_by_date($object1, $object2)
			{
				return $object1->d_taken > $object2->d_taken;
			}
			function sort_by_photographer($object1, $object2)
			{
				return $object1->p_grapher > $object2->p_grapher;
			}
			function sort_by_location($object1, $object2)
			{
				return $object1->location_name > $object2->location_name;
			}
			// set sort by name as default
			$sort_function_select = "sort_by_name";
			if ($sort_method == "PhotoName") {
				$sort_function_select = 'sort_by_name';
			} elseif ($sort_method == "DateTaken") {
				$sort_function_select = 'sort_by_date';
			} elseif ($sort_method == "Photographer") {
				$sort_function_select = 'sort_by_photographer';
			} elseif ($sort_method == "Location") {
				$sort_function_select = 'sort_by_location';
			}
			usort($images, $sort_function_select);

			// print table with image and information
			$row_count = 1;
			for ($i = 0; $i < $total_num_of_images; $i++) {
				if ($i >= 3 * $row_count) {
					$row_count++;
					echo  "</tr><tr>";
				}
				
				echo "<td><img src= " . $images[$i]->f_path . ">";
				echo "<h5>" . "Photo Name: " . $images[$i]->p_name . "<br>" .
					"Date Taken: " . $images[$i]->d_taken . "<br>" .
					"Photographer: " . $images[$i]->p_grapher . "<br>" .
					"Location: " . $images[$i]->location_name . "</h5></td>";
			}
			echo "</tr>";
			?>
	</table>

</body>

</html>