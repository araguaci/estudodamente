
<?php
if (session_id() == "")
    session_start();
ob_start();
?>
<?php
// NOTICE THAT THERE IS NO WHITE-SPACE OR OUTPUT BEFORE <?php
// AND ALSO; NO "echo" STATEMENT AT ALL BEFORE THE if(isset()){} BLOCK.

if (isset($_GET['file'])) {
    $file = htmlspecialchars(trim($_GET['file']));
    //echo $file;
    //die();
    processDownload($file);
}

function processDownload($fileName)
{
    if ($fileName) {
        $dldFile    = $fileName;
        if (file_exists($fileName)) {
            $size       = @filesize($fileName);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $fileName);
            header('Content-Transfer-Encoding: binary');
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . $size);
            return TRUE;
        }
    }
    return FALSE;
}

?>
<?php
// Page créé par Shepard [Fabian Pijcke] <Shepard8@laposte.net>
// et Romain Bourdon <romain@anaska.com>
// pour WAMP5
//afficher phpinfo
if (isset($_GET['phpinfo'])) {
    phpinfo();
    exit();
}

//Verifica o Proxy e retorna o ip real
function getRealIPAddress()
{

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

//retorna nome da máquina remota pelo IP
function NomeMaquinaRem()
{

    $Nome = gethostbyaddr(getRealIPAddress());
    return $Nome;
}

//Mac da Máquina remota conectada
function MacAdressByWindows()
{

    $ipAddress = getRealIPAddress();

    #run the external command, break output into lines
    exec("arp -a $ipAddress", $output);
    $IpMac = explode(" ", trim($output[3]));
    return $IpMac[11];
}

function getMacAddress()
{
    ob_start();
    system('getmac /NH');
    $mycom = ob_get_contents();
    $mycom = str_replace("N/A", "", $mycom);
    $mycom = str_replace("Hardware ausente", "", $mycom);
    $mycom = cleanString($mycom);
    ob_clean();
    $pmac = strpos($mycom, "\Device");
    $mac = substr($mycom, $pmac - 20, 20);
    $mac = trim(ew_RemoveCrLf($mac));
    return trim(ew_RemoveCrLf($mac));
}

function getCPUInfo()
{
    ob_start(); // Turn on output buffering
    system('VOL'); //Execute external program to display output
    $mycom = ob_get_contents(); // Capture the output into a variable
    ob_clean(); // Clean (erase) the output buffer
    $findme = "-";
    $pmac = strpos($mycom, $findme); // Find the position of Physical text
    $mac = trim(substr($mycom, -12, $pmac)); // Get Physical Address
    return str_replace("-", "", $mac);
}

// encrypt
function TEAencrypt($str, $key = '6oL5scK4Muiy9h64')
{
    if ($str == "") {
        return "";
    }
    $v = str2long($str, true);
    $k = str2long($key, false);
    $cntk = count($k);
    if ($cntk < 4) {
        for ($i = $cntk; $i < 4; $i++) {
            $k[$i] = 0;
        }
    }
    $n = count($v) - 1;
    $z = $v[$n];
    $y = $v[0];
    $delta = 0x9E3779B9;
    $q = floor(6 + 52 / ($n + 1));
    $sum = 0;
    while (0 < $q--) {
        $sum = int32($sum + $delta);
        $e = $sum >> 2 & 3;
        for ($p = 0; $p < $n; $p++) {
            $y = $v[$p + 1];
            $mx = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $z = $v[$p] = int32($v[$p] + $mx);
        }
        $y = $v[0];
        $mx = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
        $z = $v[$n] = int32($v[$n] + $mx);
    }
    return ew_UrlEncode(long2str($v, false));
}

// decrypt
function TEAdecrypt($str, $key = '6oL5scK4Muiy9h64')
{
    $str = ew_UrlDecode($str);
    if ($str == "") {
        return "";
    }
    $v = str2long($str, false);
    $k = str2long($key, false);
    $cntk = count($k);
    if ($cntk < 4) {
        for ($i = $cntk; $i < 4; $i++) {
            $k[$i] = 0;
        }
    }
    $n = count($v) - 1;
    $z = $v[$n];
    $y = $v[0];
    $delta = 0x9E3779B9;
    $q = floor(6 + 52 / ($n + 1));
    $sum = int32($q * $delta);
    while ($sum != 0) {
        $e = $sum >> 2 & 3;
        for ($p = $n; $p > 0; $p--) {
            $z = $v[$p - 1];
            $mx = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $y = $v[$p] = int32($v[$p] - $mx);
        }
        $z = $v[$n];
        $mx = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
        $y = $v[0] = int32($v[0] - $mx);
        $sum = int32($sum - $delta);
    }
    return long2str($v, true);
}

function ew_UrlEncode($string)
{
    $data = base64_encode($string);
    return str_replace(array('+', '/', '='), array('-', '_', '.'), $data);
}

function str2long($s, $w)
{
    $v = unpack("V*", $s . str_repeat("\0", (4 - strlen($s) % 4) & 3));
    $v = array_values($v);
    if ($w) {
        $v[count($v)] = strlen($s);
    }
    return $v;
}

function int32($n)
{
    while ($n >= 2147483648)
        $n -= 4294967296;
    while ($n <= -2147483649)
        $n += 4294967296;
    return (int) $n;
}

function long2str($v, $w)
{
    $len = count($v);
    $s = array();
    for ($i = 0; $i < $len; $i++) {
        $s[$i] = pack("V", $v[$i]);
    }
    if ($w) {
        return substr(join('', $s), 0, $v[$len - 1]);
    } else {
        return join('', $s);
    }
}

function ew_UrlDecode($string)
{
    $data = str_replace(array('-', '_', '.'), array('+', '/', '='), $string);
    return base64_decode($data);
}

/*
 * clearstring
 */
function cleanString($str, $charlist = '')
{
    $result = '';
    /* list of forbidden chars to be trimmed */
    $forbidden_list = array("\t", "\r", "\n", "\0", "\x0B");

    if (empty($charlist)) {
        for ($i = 0; $i < strlen($str); $i++) {
            if (($str[$i] != $forbidden_list[0]) &&
                ($str[$i] != $forbidden_list[1]) &&
                ($str[$i] != $forbidden_list[2]) &&
                ($str[$i] != $forbidden_list[3]) &&
                ($str[$i] != $forbidden_list[4]) &&
                ($str[$i] != $forbidden_list[5])
            ) {
                $result .= $str[$i];
            }
        }
    } else if (!empty($charlist)) {
        $is_not_same = true;

        for ($i = 0; $i < strlen($str); $i++) {
            for ($j = 0; $j < strlen($charlist); $j++) {
                if ($str[$i] != $charlist[$j]) {
                    $is_not_same = true;
                } else if ($str[$i] == $charlist[$j]) {
                    $is_not_same = false;
                    break;
                }
            }

            if ($is_not_same == true) {
                $result .= $str[$i];
            }
        }
    }

    return ($result);
}

function ew_RemoveCrLf($s)
{
    if (strlen($s) > 0) {
        $s = str_replace("\n", " ", $s);
        $s = str_replace("\r", " ", $s);
        $s = str_replace("\l", " ", $s);
        $s = str_replace("  ", " ", $s);
    }
    return $s;
}

function createPath($title)
{
    if ($title == '') {
        $title = "TOP";
    }
    $path = $_SERVER['SCRIPT_FILENAME'];
    $path = str_replace('/', '\\', $path);
    $path = str_replace('\\_', '\\', $path);
    $path = str_replace('index.php', '', strtolower($path));
    $path = str_replace('_index.php', '', strtolower($path));
    return '&nbsp;' . strtolower($path) . '&nbsp;&nbsp;<a href="#' . $title . '" title="' . $title . '"><img src="http://localhost/apps/images/network.png" align="absmiddle"/></a>';
}
/*
$stats = getServerStatistics($url);
if ($stats !== false) {
    print $stats->players->online;
}
*/
function getServerStatistics($url)
{
    $statisticsJson = file_get_contents($url);
    if ($statisticsJson === false) {
        return false;
    }

    $statisticsObj = json_decode($statisticsJson);
    if ($statisticsObj !== null) {
        return false;
    }

    return $statisticsObj;
}
function getUrlContent($url)
{
    #run the external command, break output into lines
    exec("curl $url", $output);
    //$result = json_decode($output);

    return $output;
}
function lcwords($words) {
    $words = str_replace('-', ' ', $words);
    $words = ucwords($words);
    return $words;
    return implode(' ', array_map(function($e) { return lcfirst($e); }, explode(' ', $words)));
}
?>
<?php
//echo "<pre><code>";
//print_r ($_SERVER);
//echo "</code></pre>";
/*
https://api.github.com/repos/araguaci/timeline-surf-santa-catarina
gh auth login
*/
$handle = opendir(".");
$list_ignore = array('desktop.ini', 'makeindex.php', '.', '..', '.htaccess', 'proccess.php');
$cnt = 0;
$json = "";
while ($file = readdir($handle)) {
    if (!in_array($file, $list_ignore)) {
        $strproject = $file;
        $strproject = str_replace('.netlify.app.png', '', $strproject);
        $strproject = str_replace('.netlify.app.jpeg', '', $strproject);
        $strproject = str_replace('.vercel.app.png', '', $strproject);
        $strproject = str_replace('.vercel.app.jpeg', '', $strproject);
        $strproject = str_replace('sereiscomodeuses', 'serei-como-deuses', $strproject);
        if ($strproject != "") {
            $title = lcwords($strproject);
            echo chr(13) . $title . chr(13);
            
            $url = "https://api.github.com/repos/araguaci/" . $strproject;
            //$content = file_get_contents("https://api.github.com/repos/araguaci/" . $strproject);
            echo '[' . $url . ']' . chr(13);
            echo '[' . $file . '][' . $strproject . ']' . chr(13);
            $response = getUrlContent($url);
            //print_r($response);
            $description = "";
            if (count($response)>77) {
                $description = $response[27];
                $created_at = $response[66];            
                $created_at = trim(str_replace('"created_at": "', '', $created_at));
                $created_at = substr($created_at, 0, 10);            
                $language = $response[77];
                $language = trim(str_replace('"language": "', '', $language));
                $language = trim(str_replace('",', '', $language));
                $homepage = $response[73];
                $homepage = trim(str_replace('"homepage": "', '', $homepage));
                $homepage = trim(str_replace('",', '', $homepage));
            } else {
                echo chr(13) . 'NÃO LOCALIZADO' . chr(13);
            }
            //print_r($response);
            //die();
        }   
        /*
        echo chr(13) . $title . chr(13);
        print_r($title);
        die();
        */
        $textbody = str_replace('"description": "', '', $description);
        $textbody = trim(str_replace('"description": ', '', $textbody)); 
        $textbody = str_replace('",', '', $textbody); 
        $description = trim(str_replace('",', '"', $description)); 
        $description = str_replace('"description": ', 'description: ', $description); 
        echo '[' . $created_at . '][' . $language . '][' . $description . ']' . chr(13);
        if ($textbody != "") {
            $json .= "{
    title: '" . $title . "',
    description: '" . $textbody . "',
    preview: ('/assets/img/projects/" . $file . "'),
    website: '" . $homepage . "',
    source: '',
    tags: ['" . $language . "'],
},";            
            //die($created_at);
            $strcontent = '---
layout: showcase
title: ' . $title . '
subtitle: "' . $textbody . '"
category: showcase
image: 
  path: /assets/img/projects/' . $file . '
links:
  - title: ' . $title . '
    url: ' . $homepage . '
tags: [showcase, artesdosul, ' . $language . ']
' . trim($description) . '
---

' . $textbody . '
';
            $myfile = fopen("./doc/" . $created_at . '-' . $strproject . ".md", "w");
            fwrite($myfile, $strcontent);
            fclose($myfile);
        }
    }
}
closedir($handle);
$myfile = fopen("./doc/projects.json", "w");
fwrite($myfile, $json);
fclose($myfile);
/*
	echo "<pre>";
	print_r($alias);
	echo "</pre>";
	die();
*/
?>
