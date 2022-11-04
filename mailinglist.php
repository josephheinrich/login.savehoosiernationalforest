<?php
session_start();
if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
    include 'include.php';

    //connect to database
    doDB();

    if (mysqli_connect_errno()) {
        //if connection fails, stop script execution
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    } 
    else {
        //otherwise, get emails from subscribers list
        $sql = "SELECT email FROM subscribers_test";
        $result = mysqli_query($mysqli, $sql)
        or die(mysqli_error($mysqli));
        $count = 0;
        while ($row = mysqli_fetch_array($result)) {
            set_time_limit(0);
            $email = $row['email'];
            $count += 1;
            echo $count . " - " . $email . "<br>";
        }
    }

} else {
    header("Location: index.php");
     echo "issue bottom";
     exit();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>View Mailing List</title>
	</head>
	<body>
        <br>
        <br>
		<a href="home_staging.php">Go back</a>
	</body>
</html>