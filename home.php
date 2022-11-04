<?php 
require_once "vendor/autoload.php";
require_once 'config.php';

use SendGrid\Mail\Personalization;
use SendGrid\Mail\To;
use SendGrid\Mail\ReplyTo;
use SendGrid\Helper\Assert;
use SendGrid\Mail\Substitution;

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
        <textarea style="border-radius: 0.25rem; width: 100%; height: 13rem;" id="message" name="message" cols="50"   rows="10"></textarea></p>
        <input type="file" name="file_to_upload" id="file_to_upload" accept=".jpg, .png, .jpeg, .pdf">
        <p style="margin-left:10px;" id="file_name"></p>
        <progress id="progress_bar" value="0" max="100" style="width:400px; margin-left: 10px;"></progress>
        <p id="progress_status"></p>
        <!--<input style="float: left; width: auto; margin-left:10px;" type="button" value="Upload Attachment" id="upload_file_button"><br />-->
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
        function array_push_assoc(&$array, $key, $value){
            $array[$key] = $value;
            return $array;
        }
    //otherwise, get emails from subscribers list
    $sql = "SELECT email FROM subscribers_test";
    $result = mysqli_query($mysqli, $sql)
    or die(mysqli_error($mysqli));

    $listOfEmails = [];

    //add each email address to a list
    foreach ($result as $item) {
        array_push($listOfEmails, $item['email']);
    }

    //break up the list of emails into multiple lists
    $chunkedEmailList = array_chunk($listOfEmails, 2);

    $mail_body = stripslashes($_POST['message']);
    $mail_subject = stripslashes($_POST['subject']);

    foreach ($chunkedEmailList as $chunk) {   
        $email = new \SendGrid\Mail\Mail(); //1
        $email->setFrom("noreply@savehoosiernationalforest.com", "Save Hoosier National Forest"); //2
        $email->setReplyTo("savehoosiernationalforest@gmail.com", "Save Hoosier National Forest");
        $email->setTemplateId(
            new \SendGrid\Mail\TemplateId( TEMPLATE_ID )
        );
        
        if ($_POST['file_to_upload'] != "") {
            $file_encoded = base64_encode(file_get_contents('uploads'.'/'.$_POST['file_to_upload']));
            $email->addAttachment(
                $file_encoded,
                "application/pdf",
                $_POST['file_to_upload'],
                "attachment"
            );
        }

        foreach ($chunk as $individualEmail) {
            $personalization = new Personalization();
            $personalization->addTo( new To( $individualEmail ) ); //3
            $personalization->addDynamicTemplateData("mail_body", $mail_body);
            $personalization->addDynamicTemplateData("mail_subject", $mail_subject);
            $email->addPersonalization( $personalization );
        }

        $sendgrid = new \SendGrid(SENDGRID_API_KEY);
        $emailResponse = "";

        try {
            $response = $sendgrid->send($email);
            $emailResponse = strval($response->statusCode());
            if (strpos($emailResponse, '202') !== false) {
                $display_block .= "<p>Success! Newsletter sent to:</p>";
                foreach($chunk as $emailaddress){
                    $display_block .= $emailaddress."<br/>";
                }
            }

        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }

}


    mysqli_free_result($result);
    mysqli_close($mysqli);
    }
    }

 ?>

<!DOCTYPE html>

<html>
    

<head>
    <title>Sending a newletters - Staging</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        html {
            font-family: arial, sans-serif;
        }

        .outer > * {
            display: inline-block;
            vertical-align: middle;
        }
    </style>

</head>

<body style="margin: 5rem; padding-bottom: 3rem;">
        <h1 style="margin-bottom: 0.2rem; margin-top: 6rem;">
            Hello, <?php echo $_SESSION['user_name']; ?>
        </h1>

        <h4 style="color:red; margin-top: 0.3rem;">
            You are in a testing enviroment.
        </h4>

        <div class="outer">
            <h3>
                <a href="mailinglist.php" target="_blank">View mailing list</a>
            </h3>
        </div>

        <h2 style="color: black">Send a newsletter</h2>

        <?php echo $display_block; ?>

        <a style="margin-top:10px; background-color: rgb(218, 171, 33);" href="logout.php">Logout</a>

     <script>
        document.getElementById('file_to_upload').addEventListener('change', (event) => {
            window.selectedFile = event.target.files[0];
            document.getElementById('file_name').innerHTML = window.selectedFile.name;
            uploadFile(window.selectedFile);
        });

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

} else{

     header("Location: index.php");
     echo "issue bottom";
     exit();

}

 ?>