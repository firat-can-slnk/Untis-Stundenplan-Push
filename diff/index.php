
 <head>
   <!-- Required meta tags -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
 </head>

 <body style="width: 100%;height: auto;" id="mySelect" onload="lolo()">
   <?php
   // include the Diff class
   require_once './class.Diff.php';

   // output the result of comparing two files as a table
   $output = Diff::toHTML(Diff::compareFiles('old.txt', 'new.txt'));

    ?>

 <style media="screen">
   del{
     background-color: rgba(238, 29, 8, 0.34);
   }
   ins{
     background-color: rgba(8, 238, 31, 0.34);
     text-decoration: none;
   }
 </style>
<?php
$output = str_replace("Fach: *","Fach: <b>",$output);
$output = str_replace("Raum: *","Raum: <b>",$output);
$output = str_replace("*", "</b>",$output);
echo $output;
?>
 <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>

</body>
