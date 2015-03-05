<?php

$remoteOrigin = "http://www.remote-domain.com"; //change to the origin of your webpage



/* FOR AJAX CORS REQUESTS */

if ($_SERVER["HTTP_ORIGIN"]===$remoteOrigin) {

	header("Access-Control-Allow-Origin: " . $_SERVER["HTTP_ORIGIN"]);

	/* Uncomment to allow cookies across domains */
	//header("Access-Control-Allow-Credentials: true");

	/* Uncomment to improve performance after testing */
	//header("Access-Control-Max-Age: 86400"); // cache for 1 day

}

if ($_SERVER["REQUEST_METHOD"]==="OPTIONS") {

	if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

	if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
		header("Access-Control-Allow-Headers: " . $_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]);

	exit(0);

}

/* END AJAX CORS CONFIGURATION */



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
	namespace: "simpleUpload",
	id: <?php echo $iframeId; ?>,
	type: "json",
	data: <?php echo json_encode($output); ?>,
	xDomain: "<?php echo $remoteOrigin; ?>"
};

try {
	parent.simpleUpload.iframeCallback(data);
} catch(e) {
	parent.postMessage(JSON.stringify(data), data.xDomain);
}

</script>
</body>
</html>
<?php

} else { //new browser...

	header("Content-Type: application/json; charset=utf-8");
	echo json_encode($output);

}

?>