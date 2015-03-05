# simpleUpload.js
Unlike many JavaScript upload libraries on the interwebs, simpleUpload is an extremely simple yet powerful jQuery file upload plugin designed to be non-intrusive, backwards-compatible, flexible, and very easy to understand.

## Features

- Multiple uploads
- File type and size filtering
- HTML5 Ajax upload with fallback to iframe
- Simultaneous uploads
- Plays nicely with drag-n-drop
- Cross-domain uploads
- Cancellable uploads
- Client-side hashing for integration with deduplication systems (premium feature only)

## Browser Support

Desktop:

- Google Chrome
- Firefox 3+
- IE 6+
- Safari 4+
- Opera 10.6+

Mobile:

- iOS 6+
- Android 2.2+
- Windows Phone 8.1+
- Opera Mobile 11.50+

*Note: IE8+ and Firefox 3.6+ required for cross-domain uploads*

## Basic Usage

```javascript

$('#file').simpleUpload("/ajax/upload.php", {

	start: function(file){
		//upload started
	},
	progress: function(progress){
		//received progress
	},
	success: function(data){
		//upload successful
	},
	error: function(error){
		//upload failed
	}

});

```

## Example #1: Single File Upload with Progress Bar

#### JavaScript

```javascript

$(document).ready(function(){

	$('input[type=file]').change(function(){

		$(this).simpleUpload("/ajax/upload.php", {

			start: function(file){
				//upload started
				$('#filename').html(file.name);
				$('#progress').html("");
				$('#progressBar').width(0);
			},

			progress: function(progress){
				//received progress
				$('#progress').html("Progress: " + Math.round(progress) + "%");
				$('#progressBar').width(progress + "%");
			},

			success: function(data){
				//upload successful
				$('#progress').html("Success!<br>Data: " + JSON.stringify(data));
			},

			error: function(error){
				//upload failed
				$('#progress').html("Failure!<br>" + error.name + ": " + error.message);
			}

		});

	});

});

```

#### HTML

```html

<div id="filename"></div>
<div id="progress"></div>
<div id="progressBar"></div>

<input type="file" name="file">

```

## Example #2: Multiple File Upload

#### JavaScript

```javascript

$(document).ready(function(){

	$('input[type=file]').change(function(){

		$(this).simpleUpload("/ajax/upload.php", {

			/*
			 * Each of these callbacks are executed for each file.
			 * To add callbacks that are executed only once, see init() and finish().
			 *
			 * "this" is an object that can carry data between callbacks for each file.
			 * Data related to the upload is stored in this.upload.
			 */

			start: function(file){
				//upload started
				this.block = $('<div class="block"></div>');
				this.progressBar = $('<div class="progressBar"></div>');
				this.block.append(this.progressBar);
				$('#uploads').append(this.block);
			},

			progress: function(progress){
				//received progress
				this.progressBar.width(progress + "%");
			},

			success: function(data){
				//upload successful

				this.progressBar.remove();

				/*
				 * Just because the success callback is called doesn't mean your
				 * application logic was successful, so check application success.
				 *
				 * Data as returned by the server on...
				 * success:	{"success":true,"format":"..."}
				 * error:	{"success":false,"error":{"code":1,"message":"..."}}
				 */

				if (data.success) {
					//now fill the block with the format of the uploaded file
					var format = data.format;
					var formatDiv = $('<div class="format"></div>').text(format);
					this.block.append(formatDiv);
				} else {
					//our application returned an error
					var error = data.error.message;
					var errorDiv = $('<div class="error"></div>').text(error);
					this.block.append(errorDiv);
				}

			},

			error: function(error){
				//upload failed
				this.progressBar.remove();
				var error = error.message;
				var errorDiv = $('<div class="error"></div>').text(error);
				this.block.append(errorDiv);
			}

		});

	});

});

```

#### HTML

```html

<div id="uploads"></div>

<input type="file" name="file" multiple>

```

## Example #3: Cancellable Uploads (with file type & size filtering)

#### JavaScript

```javascript

$(document).ready(function(){

	$('input[type=file]').change(function(){

		$(this).simpleUpload("/ajax/upload.php", {

			allowedExts: ["jpg", "jpeg", "jpe", "jif", "jfif", "jfi", "png", "gif"],
			allowedTypes: ["image/pjpeg", "image/jpeg", "image/png", "image/x-png", "image/gif", "image/x-gif"],
			maxFileSize: 5000000, //5MB in bytes

			start: function(file){
				//upload started

				this.block = $('<div class="block"></div>');
				this.progressBar = $('<div class="progressBar"></div>');
				this.cancelButton = $('<div class="cancelButton">x</div>');

				/*
				 * Since "this" differs depending on the function in which it is called,
				 * we need to assign "this" to a local variable to be able to access
				 * this.upload.cancel() inside another function call.
				 */

				var that = this;

				this.cancelButton.click(function(){
					that.upload.cancel();
					//now, the cancel callback will be called
				});

				this.block.append(this.progressBar).append(this.cancelButton);
				$('#uploads').append(this.block);

			},

			progress: function(progress){
				//received progress
				this.progressBar.width(progress + "%");
			},

			success: function(data){
				//upload successful

				this.progressBar.remove();
				this.cancelButton.remove();

				if (data.success) {
					//now fill the block with the format of the uploaded file
					var format = data.format;
					var formatDiv = $('<div class="format"></div>').text(format);
					this.block.append(formatDiv);
				} else {
					//our application returned an error
					var error = data.error.message;
					var errorDiv = $('<div class="error"></div>').text(error);
					this.block.append(errorDiv);
				}

			},

			error: function(error){
				//upload failed
				this.progressBar.remove();
				this.cancelButton.remove();
				var error = error.message;
				var errorDiv = $('<div class="error"></div>').text(error);
				this.block.append(errorDiv);
			},

			cancel: function(){
				//upload cancelled
				this.block.fadeOut(400, function(){
					$(this).remove();
				});
			}

		});

	});

});

```

#### HTML

```html

<div id="uploads"></div>

<input type="file" name="file" multiple>

```

# Server-side

No external scripts required. I have written these examples in PHP, but they can be written in any language.

### Same-domain Upload

```php

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

```

### Cross-domain Upload

```php

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

```

I created simpleUpload.js because I could not find a solution for uploading files that was simple, efficient, non-intrusive, and backwards-compatible. Now it exists.

#### simpleUpload.js is free to use under the MIT License

#### Full documentation and examples can be found here: http://simpleupload.michaelcbrook.com/
