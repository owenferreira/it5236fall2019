<?php
// Declare the credentials to the database
$dbh = NULL;
require_once 'credentials.php';
try{

    $conn_string = "mysql:host=".$dbserver.";dbname=".$db;

	$dbh= new PDO($conn_string, $dbusername, $dbpassword);

	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}catch(Exception $e){
	//Database issues were encountered.
    http_response_code(504);
    echo "Database timeout";
    exit();
}

// get all tasks
if ($_SERVER['REQUEST_METHOD'] == "GET") {
	try {
		$sql = "SELECT * FROM doList";

		$stmt = $dbh->prepare($sql);
		$response = $stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$final =[];
		
		
		foreach($result as $task){
		//rename keys
		
		$task['taskName'] = $task['listItem'];
		$task['taskDate'] = $task['finishDate'];
		$task['completed'] = $task['complete'];
		
		//delete old keys
		unset($task['listItem'], $task['finishDate'], $task['complete']);
		
		//store updated task
		
		$final[] = $task;
		
		
		}
		

		
		http_response_code(200);
		echo json_encode($final);
		exit();
	} catch (PDOException $e) {
		http_response_code(504);
		echo "error running query";
		exit();
	}
} else {
    http_response_code(405);//method not allowed
    echo "expected get request";
    exit();
}

