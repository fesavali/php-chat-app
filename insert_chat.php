<?php

//insert_chat.php

include('database_connection.php');

session_start();

$data = array(
	':to_isd'		=>	$_POST['to_isd'],
	':from_isd'		=>	$_SESSION['isd'],
	':chat_message'		=>	$_POST['chat_message'],
	':status'			=>	'1'
);

$query = "
INSERT INTO chat_message 
(to_isd, from_isd, chat_message, status) 
VALUES (:to_isd, :from_isd, :chat_message, :status)
";

$statement = $connect->prepare($query);

if($statement->execute($data))
{
	echo fetch_user_chat_history($_SESSION['isd'], $_POST['to_isd'], $connect);
}

?>