<!DOCTYPE html>

<html>

<head>
    <title>Sending a newsletter - <?php echo ENVIRONMENT_TYPE; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="assets/js/main.js"></script>
</head>

<body>
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-outline-dark">
                    <a style="text-decoration:none; color: inherit" href="logout.php">Logout</a>
                </button>
            </div>
        </div>
    </div>
    <div class="container mb-5">
        <div class="container">
            <div class="row d-flex justify-content-center text-center">
                <div class="col-md-4 mb-1">
                    <h3>Hello, <?php echo $_SESSION['user_name']; ?></h3>
                </div>
                <div class="col-md-4 mb-1">
                    <h3 style="color: red">
                        You are in a <?php echo ENVIRONMENT_TYPE; ?> enviroment.
                    </h3>
                </div>
                <div class="col-md-4 mb-1">
                    <button type="button" class="btn btn-primary">
                        <a href="mailinglist.php" style="color:white; text-decoration: none;" target="_blank">View mailing list</a>
                    </button>
                </div>
            </div>
        </div>
        <div class="text-center d-flex justify-content-center">
            <div id="loader"></div>
        </div>
        <div class="container mt-5">
            <h2 class="text-center">Send a Newsletter - <?php echo ENVIRONMENT_TYPE; ?></h2>
            