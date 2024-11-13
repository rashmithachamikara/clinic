<?php

function print_url_message(){
	//Check whether msg is set, otherwise break
	if (isset($_GET["msg"])) {
		$msg = urldecode($_GET["msg"]);
	} else {
		return 0;
	}
	//Check whether msgtype is set, otherwise set it to default "url-message-info"
	//Message type can be. info, warning, error. Sample css class - .url-message-info
	if (isset($_GET["msgtype"])) {
		$msgtype = "url-message-".$_GET["msgtype"];
	} else {
		$msgtype = "url-message-info";
	}
	//Print message as a span and add a css class based on msgtype.
	echo "<span class=$msgtype>$msg</span>";
}

//Date functions
date_default_timezone_set("Asia/Colombo");

function formatDateTime($date){
	return date("Y.m.d h:i a", strtotime($date));
}

function formatDate($date){
	return date("Y.m.d", strtotime($date));
}

function formatTime($date){
	return date("h:i:s a", strtotime($date));
}

?>