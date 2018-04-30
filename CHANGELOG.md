# Change Log
All changes to simpleUpload are listed here.

## 1.1 - 2018-04-30
- Added "xhr" property to error returned in error callback if upload happened over AJAX. Useful for getting server-defined error message when returning with HTTP status code other than 200.
- Changed request error messages from "Could not get response from server" to "Upload failed"
- Added beforeSend function in options that passes jqXHR and settings object to allow XHR to be modified before AJAX requests are sent. Useful for modifying headers for the request.

## 1.0 - 2015-03-05
simpleUpload.js JUST RELEASED! PARTAYYY!!!