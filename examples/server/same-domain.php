<?php



/*
 * All of your application logic with $_FILES["file"] goes here.
 * It is important that nothing is outputted yet.
 */



// $output will be converted into JSON

if ($sucess) {
	$output = array("success" => true, "message" => "Success!");
} else {
	$output = array("success" => false, "error" => "Failure!");
}



if (($iframeId = (int)$_GET["_iframeUpload"]) > 0) { //old browser...

	header("Content-Type: text/html; charset=utf-8");

?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<script type="text/javascript">

var data = {
	id: <?php echo $iframeId; ?>,
	type: "json",
	data: <?php echo json_encode($output); ?>
};

parent.simpleUpload.iframeCallback(data);

</script>
</body>
</html>
<?php

} else { //new browser...

	header("Content-Type: application/json; charset=utf-8");
	echo json_encode($output);

}

?>