<?php

//database_connection.php

$connect = new PDO("mysql:host=localhost;dbname=chat;charset=utf8mb4", "root", "");

date_default_timezone_set('Asia/Kolkata');

function fetch_user_last_activity($isd, $connect)
{
	$query = "
	SELECT * FROM login_details 
	WHERE isd = '$isd' 
	ORDER BY last_activity DESC 
	LIMIT 1
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return $row['last_activity'];
	}
}

function fetch_user_chat_history($from_isd, $to_isd, $connect)
{
	$query = "
	SELECT * FROM chat_message 
	WHERE (from_isd = '".$from_isd."' 
	AND to_isd = '".$to_isd."') 
	OR (from_isd = '".$to_isd."' 
	AND to_isd = '".$from_isd."') 
	ORDER BY timestamp DESC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '<ul class="list-unstyled">';
	foreach($result as $row)
	{
		$user_name = '';
		if($row["from_isd"] == $from_isd)
		{
			$user_name = '<b class="text-success">You</b>';
		}
		else
		{
			$user_name = '<b class="text-danger">'.get_user_name($row['from_isd'], $connect).'</b>';
		}
		$output .= '
		<li style="border-bottom:1px dotted #ccc">
			<p>'.$user_name.' - '.$row["chat_message"].'
				<div align="right">
					- <small><em>'.$row['timestamp'].'</em></small>
				</div>
			</p>
		</li>
		';
	}
	$output .= '</ul>';
	$query = "
	UPDATE chat_message 
	SET status = '0' 
	WHERE from_isd = '".$to_isd."' 
	AND to_isd = '".$from_isd."' 
	AND status = '1'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $output;
}

function get_user_name($isd, $connect)
{
	$query = "SELECT username FROM login WHERE isd = '$isd'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return $row['username'];
	}
}

function count_unseen_message($from_isd, $to_isd, $connect)
{
	$query = "
	SELECT * FROM chat_message 
	WHERE from_isd = '$from_isd' 
	AND to_isd = '$to_isd' 
	AND status = '1'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$count = $statement->rowCount();
	$output = '';
	if($count > 0)
	{
		$output = '<span class="label label-success">'.$count.'</span>';
	}
	return $output;
}

function fetch_is_type_status($isd, $connect)
{
	$query = "
	SELECT is_type FROM login_details 
	WHERE isd = '".$isd."' 
	ORDER BY last_activity DESC 
	LIMIT 1
	";	
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		if($row["is_type"] == 'yes')
		{
			$output = ' - <small><em><span class="text-muted">Typing...</span></em></small>';
		}
	}
	return $output;
}

function fetch_group_chat_history($connect)
{
	$query = "
	SELECT * FROM chat_message 
	WHERE to_isd = '0'  
	ORDER BY timestamp DESC
	";

	$statement = $connect->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll();

	$output = '<ul class="list-unstyled">';
	foreach($result as $row)
	{
		$user_name = '';
		if($row["from_isd"] == $_SESSION["isd"])
		{
			$user_name = '<b class="text-success">You</b>';
		}
		else
		{
			$user_name = '<b class="text-danger">'.get_user_name($row['from_isd'], $connect).'</b>';
		}

		$output .= '

		<li style="border-bottom:1px dotted #ccc">
			<p>'.$user_name.' - '.$row['chat_message'].' 
				<div align="right">
					- <small><em>'.$row['timestamp'].'</em></small>
				</div>
			</p>
		</li>
		';
	}
	$output .= '</ul>';
	return $output;
}


?>