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
        <div class="justify-content-center">
        <form method="POST" action="$_SERVER[PHP_SELF]" onsubmit="if(confirm('Do you really want to send this newsletter?')) {showLoader()} else {return false;};">
            <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" class="form-control" id="subject" name="subject">
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Body</label>
                <textarea class="form-control" id="message" name="message" rows="10"></textarea>
            </div>
            <div class="mb-3">
                <input class="form-control" type="file" name="file_to_upload" id="file_to_upload" accept=".jpg, .png, .jpeg, .pdf">
            </div>
            <p style="margin-left:10px; display:none" id="file_name"></p>
            <progress id="progress_bar" value="0" max="100" style="width:400px; margin-left: 10px;"></progress>
            <p id="progress_status"></p>

            <button type="submit" class="btn btn-primary" name="submit" value="submit">Send Email</button>
        </form>
        </div>
        END_OF_BLOCK;
    } else if ($_POST) {
    //want to send form, so check for required fields
    if (($_POST['subject'] == "") || ($_POST['message'] == "")) {
        header("Location: home.php");
        echo "issue";
        exit;
    }
    //$script_tag = "";
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
    $sql = SUBSCRIBERS_TABLE;
    $result = mysqli_query($mysqli, $sql)
    or die(mysqli_error($mysqli));

    $listOfEmails = [];

    //add each email address to a list
    foreach ($result as $item) {
        array_push($listOfEmails, $item['email']);
    }

    //break up the list of emails into multiple lists
    $chunkedEmailList = array_chunk($listOfEmails, BATCH_SIZE);

    $mail_body = stripslashes($_POST['message']);
    $mail_subject = stripslashes($_POST['subject']);
    $count = 1;
    $display_block .= "<div class='text-center'>";
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
                    $display_block .= $count.". ".$emailaddress."<br/>";
                    $count += 1;
                }
            }

        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }

    }
    $display_block .= "</div>";
    $script_tag = "hideLoader();";


    mysqli_free_result($result);
    mysqli_close($mysqli);
    }
    }

 ?>

<?php include("home_top.php");?>

<?php echo $display_block; ?>

<?php include("home_bottom.php");?>

<?php 
} else{

     header("Location: index.php");
     echo "issue bottom";
     exit();

}
?>