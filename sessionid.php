<?php
require_once "config.php";
// Funktion um eine neue JSESSIONID zu bekommen (benötigt um Login die ICS Datei runterladen zu können)
function getUntisSessionId($SCHOOL, $USERNAME, $PASSWORD, $schoolname){

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://$UNTIS_DOMAIN/WebUntis/j_spring_security_check");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "school=$SCHOOL&j_username=$USERNAME&j_password=$PASSWORD&token=");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HEADER, true);

$headers = array();
$headers[] = "Origin: https://$UNTIS_DOMAIN";
$headers[] = "Accept-Encoding: gzip, deflate, br";
$headers[] = "Accept-Language: de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7,cs;q=0.6,tr;q=0.5";
$headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36";
$headers[] = "Content-Type: application/x-www-form-urlencoded";
$headers[] = "Accept: application/json";
$headers[] = "Referer: https://$UNTIS_DOMAIN/WebUntis/index.do";
$headers[] = "Cookie: schoolname=$schoolname";
$headers[] = "Connection: keep-alive";
$headers[] = "Dnt: 1";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
$errors = curl_error($ch);
$response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close ($ch);
#echo $result;

$arr = explode('JSESSIONID=', $result);
$arr = explode('; Path=/WebUntis;', $arr[1]);
$important = $arr[0];
return $important;

}
?>
