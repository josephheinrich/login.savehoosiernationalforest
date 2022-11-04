<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once "vendor/autoload.php";
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
     include 'include.php';
     
     if (!$_POST) {
//haven't seen the form, so display it
$display_block = <<<END_OF_BLOCK
<form method="POST" action="$_SERVER[PHP_SELF]">

<p><label for="subject" style="color: white;">Subject:</label><br/>
<input type="text" id="subject" name="subject" size="40" /></p>

<p style="margin:10px;"><label style="color:white;" for="message">Mail Body:</label><br/>
<textarea style="border-radius: 0.25rem" id="message" name="message" cols="50"   rows="10"></textarea></p>
<input type="file" name="file_to_upload" id="file_to_upload" accept=".jpg, .png, .jpeg, .pdf">
<!--<h3 style="color:white; padding-left: 10px;">Drag & Drop a File</h3>
<div style="margin-left:10px;" id="drop_zone">
     DROP HERE
</div>-->
<p style="margin-left:10px;" id="file_name"></p>
<progress id="progress_bar" value="0" max="100" style="width:400px; margin-left: 10px;"></progress>
<p id="progress_status"></p>
<input style="float: left; width: auto; margin-left:10px;" type="button" value="Upload Attachment" id="upload_file_button"><br />
<button type="submit" name="submit" value="submit">Send Email</button>
</form>
END_OF_BLOCK;
} else if ($_POST) {

//want to send form, so check for required fields
if (($_POST['subject'] == "") || ($_POST['message'] == "")) {
header("Location: home.php");
echo "issue";
exit;
}

//connect to database
doDB();

if (mysqli_connect_errno()) {
//if connection fails, stop script execution
printf("Connect failed: %s\n", mysqli_connect_error());
exit();
} else {
//otherwise, get emails from subscribers list
$sql = "SELECT email FROM subscribers";
$result = mysqli_query($mysqli, $sql)
or die(mysqli_error($mysqli));

//create a From: mailheader
$headers = array("From: noreply@savehoosiernationalforest.com",
    "X-Mailer: PHP/" . PHP_VERSION
);
$headers = implode("\r\n", $headers);

//loop through results and send mail
while ($row = mysqli_fetch_array($result)) {
     set_time_limit(0);
     $email = $row['email'];

     // if(mail("$email", stripslashes($_POST['subject']), stripslashes($_POST['message']), $headers)) {
     //      echo "success";
     // }
     $mail = new PHPMailer();

     $mail->From     = "noreply@savehoosiernationalforest.com";
     $mail->FromName = "Save Hoosier National Forest";
     $mail->addAddress($email);
     $mail->isHTML(true);
     $mail->Subject  = stripslashes($_POST['subject']);
     $mail->Body     = "<p>".stripslashes($_POST['message'])."</p><p><a href='https://savehoosiernationalforest.com/unsubscribe.php' target='_blank'>Click here to unsubscribe.</a></p>";

     // Add Static Attachment
     if ($_POST['file_to_upload'] != "") {
          $attachment = 'uploads'.'/'.$_POST['file_to_upload'];;
          $mail->AddAttachment($attachment , $_POST['file_to_upload']);
     }

     try {
          $mail->send();
 
      } catch (Exception $e) {
          echo "Mailer Error: " . $mail->ErrorInfo;
      }

     $display_block .= "newsletter sent to: ".$email."<br/>";
}


mysqli_free_result($result);
mysqli_close($mysqli);
}
}

 ?>

<!DOCTYPE html>

<html>

<head>

    <title>Sending a newletters</title>

    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        html {
            font-family: sans-serif;
        }

        div#drop_zone {
            height: 100px;
            width: 400px;
            border: 2px dotted white;
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
            font-family: monospace;
        }
        .outer > * {
            display: inline-block;
            vertical-align: middle;
        }
    </style>

</head>

<body>

     <h1>Hello, <?php echo $_SESSION['user_name']; ?></h1>
    <div class="outer">
        <h3><a href="mailinglist.php" target="_blank">View mailing list</a></h3>
        <h3><a href="archive/postmessage.php" target="_blank">Make a post</a></h3>
        <h3><a href="archive/deletemessage.php" target="_blank">Delete a post</a></h3>
    </div>
     <h2 style="color: black">Send a newsletter</h2>
     <?php echo $display_block; ?>
     <a style="margin-top:10px; background-color: rgb(218, 171, 33);" href="logout.php">Logout</a>



     <script>
        document.getElementById('file_to_upload').addEventListener('change', (event) => {
            window.selectedFile = event.target.files[0];
            document.getElementById('file_name').innerHTML = window.selectedFile.name;
        });

        document.getElementById('upload_file_button').addEventListener('click', (event) => {
            uploadFile(window.selectedFile);
        });

        const dropZone = document.getElementById('drop_zone'); //Getting our drop zone by ID
        if (window.FileList && window.File) {
            dropZone.addEventListener('dragover', event => {
                event.stopPropagation();
                event.preventDefault();
                event.dataTransfer.dropEffect = 'copy'; //Adding a visual hint that the file is being copied to the window
            });
            dropZone.addEventListener('drop', event => {
                event.stopPropagation();
                event.preventDefault();
                const files = event.dataTransfer.files; //Accessing the files that are being dropped to the window
                window.selectedFile = files[0]; //Getting the file from uploaded files list (only one file in our case)
                document.getElementById('file_name').innerHTML = window.selectedFile.name; //Assigning the name of file to our "file_name" element
            });
        }

        function uploadFile(file) {
            var formData = new FormData();
            formData.append('file_to_upload', file);
            var ajax = new XMLHttpRequest();
            ajax.upload.addEventListener("progress", progressHandler, false);
            ajax.open('POST', 'upload.php');
            ajax.send(formData);
        }

        function progressHandler(event) {
            var percent = (event.loaded / event.total) * 100;
            document.getElementById("progress_bar").value = Math.round(percent);
            document.getElementById("progress_status").innerHTML = Math.round(percent) + "% uploaded";
        }
    </script>
</body>

</html>

<?php 

}else{

     header("Location: index.php");
     echo "issue bottom";
     exit();

}

 ?>