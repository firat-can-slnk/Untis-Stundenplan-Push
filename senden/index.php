<?php
$alert = "0";
if (isset($_GET["submit"])) {


  $chatID = $_GET["chatid"];
    $parsemode = $_GET["parsemode"];
    if ($parsemode !== "image") {
      $url = "https://api.telegram.org/" . $token . "/sendMessage?parse_mode=".$parsemode."&chat_id=" . $chatID;
      $url = $url . "&text=" . urlencode($_GET["nachricht"]);
    }
    else {
      $url = "https://api.telegram.org/" . $token . "/sendPhoto?chat_id=" . $chatID;
      $url = $url . "&photo=" . $_GET["nachricht"];
    }
    $ch = curl_init();
    $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
    );
    curl_setopt_array($ch, $optArray);
    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (isset($httpcode)) {
      if ($httpcode == "200") {
        $alert = 1;
        $alertws = "success";
        $alertmsg = "<strong>".$_GET["nachricht"]."</strong> an <strong>".$chatID."</strong> gesendet.";
      }else {
        $alert = 1;
        $alertws = "danger";
        $alertmsg = "<strong>Fehler!</strong> $result";
      }
    }


    curl_close($ch);

}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title></title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" integrity="sha384-y3tfxAZXuh4HwSYylfB+J125MxIs6mR5FOHamPBG064zB+AFeWH94NdvaCBm8qnd" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="/untis/tether.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js" integrity="sha384-vZ2WRJMwsjRMW/8U7i6PWi6AlO1L79snBrmgiDpgIWJ82z8eA5lenwvxbMV1PAh7" crossorigin="anonymous"></script>

  </head>
  <body>
    <div class="container">
<div class="jumbotron">
  <h1 class="display-3">Untis Bot - Nachricht senden</h1>
  <p class="lead">Mit dieser Seite kannst du Nachrichten mit dem Untis-Bot senden.</p>
</div>
<?php if ($alert == 1){
  echo "<div class=\"alert alert-".$alertws." alert-dismissible show\" role=\"alert\">
    ".$alertmsg."
    <a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\" title=\"close\">×</a>
  </div>";
 } ?>


    <form>
      <fieldset class="form-group">
        <label for="formGroupExampleInput">Nachricht</label>
        <textarea class="form-control" name="nachricht" rows="6" cols="60" id="formGroupExampleInput"></textarea>
      </fieldset>
      <fieldset class="form-group">
        <label for="formGroupExampleInput2">Chat ID</label>
        <input type="text" name="chatid">
      </fieldset>
      <fieldset class="form-group">
        <label for="formGroupExampleInput3">Parsemode</label>
        <select class="form-control" name="parsemode" id="formGroupExampleInput3">
          <option value="HTML">HTML</option>
          <option value="Markdown">Markdown</option>
        </select>
      </fieldset>
      <input type="submit" class="btn btn-primary" name="submit" value="Senden">
    </form>
        </div>

  </body>
</html>
