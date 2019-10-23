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

if ($_SERVER['REQUEST_METHOD'] == "PUT") {

	if(array_key_exists('listID', $_GET)){

		$listID = $_GET['listID'];

	}else {

        http_response_code(504);

        exit();
	}
    //Decoding the json body from the request.

    $task = json_decode(file_get_contents('php://input'), true);
    //IF the task == null, then exit, because there is nothing to POST

    if ($task === null) {

        http_response_code(400);
        exit(); //no data in body.

    }
    if (array_key_exists('completed', $task)) {

        $complete = $task["completed"];
	} else {
        http_response_code(400);

        exit();

	}

    if (array_key_exists('taskName', $task)) {
        $taskName = $task["taskName"];
    } else {
        http_response_code(400);
        exit();
    }
    if (array_key_exists('taskDate', $task)) {

        $taskDate = $task["taskDate"];

    } else {

        http_response_code(400);

        exit();
    }

	if (!$dbconnecterror) {

		try {

			$sql = "UPDATE doList SET complete=:complete, listItem=:listItem, finishDate=:finishDate WHERE listID=:listID";

			$stmt = $dbh->prepare($sql);

			$stmt->bindParam(":complete", $complete);

			$stmt->bindParam(":listItem", $taskName);
			$stmt->bindParam(":finishDate", $taskDate);
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

//make a task
else if ($_SERVER['REQUEST_METHOD'] == "POST") {


    //Decoding the json body from the request.

    $task = json_decode(file_get_contents('php://input'), true);
    //IF the task == null, then exit, because there is nothing to POST

    if ($task === null) {

        http_response_code(400);
        exit(); //no data in body.

    }

    if (array_key_exists('completed', $task)) {

        $complete = $task["completed"];
	} else {
        http_response_code(400);

        exit();

	}

    if (array_key_exists('taskName', $task)) {
        $taskName = $task["taskName"];
    } else {
        http_response_code(400);
        exit();
    }
    if (array_key_exists('taskDate', $task)) {

        $taskDate = $task["taskDate"];

    } else {

        http_response_code(400);

        exit();
    }

	if (!$dbconnecterror) {

		try {

			$sql = "INSERT INTO doList SET complete=:complete, listItem=:listItem, finishDate=:finishDate";

			$stmt = $dbh->prepare($sql);

			$stmt->bindParam(":complete", $complete);

			$stmt->bindParam(":listItem", $taskName);
			$stmt->bindParam(":finishDate", $taskDate);
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

//delete a task
else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {

	if(array_key_exists('listID', $_GET)){

		$listID = $_GET['listID'];

	}else {

        http_response_code(504);

        exit();
	}
    //Decoding the json body from the request.

    $task = json_decode(file_get_contents('php://input'), true);
    //IF the task == null, then exit, because there is nothing to POST

    if ($task === null) {

        http_response_code(400);
        exit(); //no data in body.

    }
    if (array_key_exists('deleteTask', $task)) {

        $deleteTask = $task["deleteTask"];
	} else {
        http_response_code(400);

        exit();

	}

  if ($deleteTask == 1){
    if (!$dbconnecterror) {

      try {

        $sql = "DELETE FROM doList where listID = :listID";

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":listID", $listID);
        $response = $stmt->execute();
                  http_response_code(204);
                  exit();
      } catch (PDOException $e) {
              http_response_code(505);

              echo "error running query";

              exit();
      }
    } else {

          http_response_code(504);
          echo "db connect error";
      exit();

  }
  } else {
    http_response_code(504);
    echo "deleteTask has to be 1 to delete";
    exit();
  }

}

else {

    http_response_code(405);//method not allowed

    echo "expected put or delete or post request";

    exit();

}

?>
