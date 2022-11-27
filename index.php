<!DOCTYPE html>

<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="mt-5 d-flex justify-content-center">
            <form action="login.php" method="post" class="p-3">
                <div class="mb-5">
                    <h2 class="text-center">LOGIN</h2>
                    <h4 class="text-center" style="color:red">You are in a <?php require_once 'config.php'; echo ENVIRONMENT_TYPE; ?> environment.</h5>
                </div>
                <?php if (isset($_GET['error'])) { ?>
                    <div class="mb-3">
                        <p class="error"><?php echo $_GET['error']; ?></p>
                    </div>
                <?php } ?>
                <div class="mb-3">
                <label for="uname" class="form-label">User Name</label>
                <input type="text" class="form-control" name="uname">
                </div>
                <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>