<?php
$uploaddir = 'uploaded/';
    $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
    $fn = 'uploaded/'.$_FILES['userfile']['name'];
    echo '<pre>';
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        echo "File has been uploaded.\n";
    } else {
        echo "Attack!\n";
    }

    echo 'Other info:';
    print_r($_FILES);

    print "</pre>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Bootstrap 101 Template</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <link href="css/bootstrap.css" rel="stylesheet">
    <!--<link href="assets/css/styles.css" rel="stylesheet">-->
    <!-- Bootstrap -->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
  <body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="msg"></div>
            <div id="main" class="row-fluid">
                <div class="table"></div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
         parser_local_btc();
         var file_name='<?= $fn ?>';
         var msg = 'parsed_successfully';
         function parser_local_btc(){
             $.ajax({
                 url:'processing.php',
                 type: 'post',
                 timeout: 30000,
                 data: {'file_name':msg},
                 success: function(data){
                     $(".table").html(data);
                 }
             });
        }
    </script>
    <script src="js/bootstrap.js"></script>
  </body>
</html>