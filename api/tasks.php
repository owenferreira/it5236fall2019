<?php



ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);



    // Declare the credentials to the database

$dbconnecterror = false;

$dbh = NULL;
require_once '/var/www/html/credentials.php';
try{

    $conn_string = "mysql:host=".$dbserver.";dbname=".$db;

	$dbh= new PDO($conn_string, $dbusername, $dbpassword);

	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}catch(Exception $e){
	//Database issues were encountered.

    //$dbconnecterror = TRUE;
    http_response_code(504);
    echo "Database timeout";
    exit();
}
//Update a task

if ($_SERVER['REQUEST_METHOD'] == "GET") {

	if(array_key_exists('listID', $_GET)){

		$listID = $_GET['listID'];

	}else {

        http_response_code(504);

        exit();
	}
	if (!$dbconnecterror) {

		try {

			$sql = "SELECT * FROM doList WHERE listID=:listID";

			$stmt = $dbh->prepare($sql);

			$stmt->bindParam(":listID", $listID);
			$response = $stmt->execute();
                http_response_code(204);
                exit();
		} catch (PDOException $e) {
            http_response_code(504);

            echo "error running query";

            exit();
		}
	} else {

        http_response_code(504);
        echo "db connect error";
    exit();

}
}
else {

    http_response_code(405);//method not allowed

    echo "expected get request";

    exit();

}

?>
