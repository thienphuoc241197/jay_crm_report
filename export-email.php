
<?php
        session_start();



    $to = $_POST["email"];
    $subject = $_COOKIE['myFullName'] ." - ". $_POST["subject"];
    $ccEmail = $_COOKIE["myEmail"];

    $message = "
    <html>
    <head>
    <title>Email from CRM report</title>
    </head>
    <body>

    <p>From: {$_COOKIE['myFullName']}</p>
    <p>    <a href='https://crm.jaybranding.com/jayreport/?report={$_POST["report"]}'><strong>{$_POST["subject"]}</strong></a>
    </p>
    <p>{$_POST["message"]}</p>
    <hr />

    {$_POST["tablefield"]}
    
    </body>
    </html>
    ";

    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "Cc: ". $ccEmail . "\r\n";
    


    // More headers
    $headers .= "From: CRM Report <report-noreply@jaybranding.com>" . "\r\n";
    $headers .= "Reply-To: ". $ccEmail . "\r\n";





    if (
        ( $_SESSION['myRoleSales'] == 'sales-level-report' )  || ( $_SESSION['myRole'] == 'admin-level-report' )
        ){

        echo 'Email sent to:'. $to .'<br>';
        echo '<button onclick="history.back()">Go Back</button>        ';
        mail($to,$subject,$message,$headers);

        //header( "refresh:3;url=https://crm.jaybranding.com/export-html.php?report=welcome&mail=sent" );
        
    }else{
        echo "Access Dennied";
        exit;
    }



?>