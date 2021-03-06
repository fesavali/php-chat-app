<?php

//group_chat.php

include('database_connection.php');

session_start();

if($_POST["action"] == "insert_data")
{
	$data = array(
		':from_isd'		=>	$_SESSION["isd"],
		':chat_message'		=>	$_POST['chat_message'],
		':status'			=>	'1'
	);

	$query = "
	INSERT INTO chat_message 
	(from_isd, chat_message, status) 
	VALUES (:from_isd, :chat_message, :status)
	";

	$statement = $connect->prepare($query);

	if($statement->execute($data))
	{
		echo fetch_group_chat_history($connect);
	}

}

if($_POST["action"] == "fetch_data")
{
	echo fetch_group_chat_history($connect);
}

?>