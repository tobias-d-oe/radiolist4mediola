
<?php
// The radio.php page is used to create a webpage, which can be included in 
// mediola remote control to control kodi
// 
// For that you need to set the ip of kodi as a parameter:
// http://<myhost>/radio.php?ip=192.178.1.40



// Please define how to access the radiolist
$baseurl="http://localhost.localdomain/radio.php";



?>

<html>
<head>
<title>RADIO</title>
<style>
table {
  border-spacing: 0px;
  margin:0px;
  padding:0px;
}

<?php
if (isset($_GET['radio'])) {
    $switch=$_GET['radio'];
}

if (isset($_GET['q'])) {
    $q=$_GET['q'];
}

function sentenceTrim($string, $maxLength = 300) {
    $string = preg_replace('/\s+/', ' ', trim($string)); // Replace new lines (optional)

    if (mb_strlen($string) >= $maxLength) {
        $string = mb_substr($string, 0, $maxLength);

        $puncs  = array('. ', '! ', '? '); // Possible endings of sentence
        $maxPos = 0;

        foreach ($puncs as $punc) {
            $pos = mb_strrpos($string, $punc);

            if ($pos && $pos > $maxPos) {
                $maxPos = $pos;
            }
        }

        if ($maxPos) {
            return mb_substr($string, 0, $maxPos + 1);
        }

        return rtrim($string) . '&hellip;';
    } else {
        return $string;
    }           
}
?>


tbody tr[scope=row]:nth-child(odd) td { 
  background-color: #eee; 
  color: #000;
  width: 300px; 
}
tbody tr[scope=row]:nth-child(even) td { 
  background-color: #aaa; 
  color: #000;
  width: 300px; 
}

tbody tr[scope=current]:nth-child(odd) td { 
  background-color: #d28b8b; 
  color: #000;
  width: 300px; 
}
tbody tr[scope=current]:nth-child(even) td { 
  background-color: #d28b8b; 
  color: #000;
  width: 300px; 
}




tr[scope=row] { 
  color: #c32e04;
  text-align: right; 
  width: 300px; 
}

td {
  padding: 0px;
  font-size: 15px;
}


html { 
  font-size: 10px; /* font-size 1em = 10px bei normaler Browser-Einstellung */ 
  margin:0px;
  padding:0px;
  scroll-behavior: smooth; 
} 

body {
  overflow-x: hidden;
}

progress {
	display:inline-block;
	width:190px;
	height:11px;
	padding:0px 0 0 0;
	margin:0;
	background:none;
	border: 0;
	border-radius: 15px;
	text-align: left;
	position:relative;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 0.8em;
}
progress::-webkit-progress-bar {
	height:11px;
	width:150px;
	margin:0 auto;
	background-color: #CCC;
	border-radius: 15px;
	box-shadow:0px 0px 6px #777 inset;
}

progress::-webkit-progress-value {
	display:inline-block;
	float:left;
	height:11px;
	margin:0px -10px 0 0;
	background: #F70;
	border-radius: 15px;
	box-shadow:0px 0px 6px #777 inset;
}

a {
  color: black;
  text-decoration: none; /* no underline */
}
</style>


</head>




<?php


$ip = $_GET['ip'];
$url = 'http://'.$ip.'/jsonrpc';

$string = file_get_contents("radiosender.json");
$radiosender = json_decode($string, true);


//if (isset($switch)) {
//  $currentvid = $switch;
//}




// switch channel if necessary
if (isset($_GET['radio'])) {
    $switch=$_GET['radio'];
    print "<body onload=\"document.getElementById('".$switch."').scrollIntoView();\">";
    foreach ($radiosender['sender'] as $radio) {
        //print("B ".$radio['name']." B ".$switch." B ");
        if ( $radio['name'] == $switch ) {
            $streamurl=$radio['url'];
        }
    }
//print($_GET['radio']."-".$streamurl);


    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => '{"jsonrpc": "2.0", "method": "Player.Stop", "params": { "playerid": 0 }, "id": 1}'
        )
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => '{"jsonrpc":"2.0","id":1,"method":"Player.Open","params":{"item": {"file":"'.$streamurl.'"}}}'
        )
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

} else {
    print "<body>";
}

?>






<?php

/*
"sender": [
    {
      "name": "Kiss FM",
      "url": "http://80.86.106.136/listen.pls",
      "icon": "/logos/radio/kissfmro.png",
      "lang": "Romania"
    },
*/


        
        print ("<table id=main width=295px border=0>");
        
       
        // loop through channels
        foreach ($radiosender['sender'] as $radio) {
        
            print ("
             <tr scope=\"row\" border=0>
              <td border=0>
                  <a href=\"".$baseurl."?ip=".$ip."&radio=".$radio['name']."\"> 
                  <table border=0 width=100% >
                    <tr id=\"".$radio['name']."\" style=\"height:100px;\">
                      <td rowspan=3 style=\"width:70px;font-size:10px\"><center>");
            if (strlen($radio['icon']) >= 6) {
              print ("<img border=0 src=\"".$radio['icon']."\" width=\"50px\" heigh=\"50px\">");
            } else {
              print ("<img border=0 src=\"http://fhemnew.fritz.box/iptv/vid-unknown.png\" width=\"50px\" heigh=\"50px\">");
            }
            print ("</center></td>
                      <td style=\"width:350px;height:50px;font-size:12px;vertical-align:middle\"><b>".$radio['name']."</b><br><font style=\"font-size:10px \">(".$radio['lang'].")</font></td>
                      <!--<td style=\"width:5px;font-size:10px\">-</td>            
                      <td style=\"width:30px;font-size:10px\">".$radio['name']."</td> -->
                    </tr>
                  </table>
                  </a>
              </td>
            </tr>");
        }
        print ("</table>");
?>
    

</body>
<html>
