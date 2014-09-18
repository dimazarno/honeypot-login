<?php
/*
Honeypot Login v.1.0
Author : Dimaz Arno
Email  : dimazarno@gmail.com
Date   : 09-09-2014
/////////////////////////////////////
*/
session_start();

//Your log folder name
$logFolderName = "335467";

if (!is_dir($logFolderName)) {
    @mkdir($logFolderName, 0755, true);
}

function getIP(){
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
		$ip=$_SERVER['HTTP_CLIENT_IP'];
    } 
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
		$ip=$_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

function getData($ip){
	$json = file_get_contents('http://ipinfo.io/'.$ip.'/json');
	$links = json_decode($json,true);
	return $links;
}

function getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'Windows';
        if (preg_match('/NT 6.2/i', $u_agent)) { $platform .= ' 8'; }
            elseif (preg_match('/NT 6.3/i', $u_agent)) { $platform .= ' 8.1'; }
            elseif (preg_match('/NT 6.1/i', $u_agent)) { $platform .= ' 7'; }
            elseif (preg_match('/NT 6.0/i', $u_agent)) { $platform .= ' Vista'; }
            elseif (preg_match('/NT 5.1/i', $u_agent)) { $platform .= ' XP'; }
            elseif (preg_match('/NT 5.0/i', $u_agent)) { $platform .= ' 2000'; }
        if (preg_match('/WOW64/i', $u_agent) || preg_match('/x64/i', $u_agent)) { $platform .= ' (x64)'; }
    }
   
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
        $bname = 'Apple Safari';
        $ub = "Safari";
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    }
    elseif(preg_match('/Netscape/i',$u_agent))
    {
        $bname = 'Netscape';
        $ub = "Netscape";
    }
   
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
    }
   
    $i = count($matches['browser']);
    if ($i != 1) {
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
   
    if ($version==null || $version=="") {$version="?";}
   
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}

function logger($logFolderName){
	$ip   = getIP();
	$data = getData($ip);
	$ua   = getBrowser();
	$file = fopen(dirname(__FILE__). "/" . $logFolderName . "/" . $ip . ".log", "a");
	$txt .= "\nTIMESTAMP : " . date('d-m-Y H:i:s');
	$txt .= "\nUSERNAME : " . $_POST['username'];
	$txt .= "\nPASSWORD : " . $_POST['password'];
	$txt .= "\nIP : " . $ip;
	$txt .= "\nHOSTNAME : " . $data['hostname'];
	$txt .= "\nCITY : " . $data['city'];
	$txt .= "\nREGION : " . $data['region'];
	$txt .= "\nCOUNTRY : " . $data['country'];
	$txt .= "\nLOCATION : " . $data['loc'];
	$txt .= "\nORG : ".$data['org'];
	$txt .= "\nBROWSER : " . $ua['name'] . " " . $ua['version'];
	$txt .= "\nOS : " . $ua['platform'];
	$txt .= "\nUSERAGENT : " . $ua['userAgent'];
	foreach ($_COOKIE as $key=>$val){$txt .= "\nCOOKIE : " . $key . " : " . $val;}
	
	//OPTIONAL - you can add your session here
	//$txt .= "\nSESSION : " . $_SESSION['userlogin'];
	
	$txt .= "\n";
	fwrite($file, $txt);
	fclose($file);
}

if (isset($_POST['do'])){
	if ($_POST['do'] == 'login'){		
		if ($_POST['answer'] == $_SESSION['result']){			
			$msg = 'Invalid username and password';                       
                        logger($logFolderName);
		} else {
			$msg = 'Wrong Answer';                        
		}                
	} else {
		$msg = 'Invalid Request';
 	}
}

$captcha_1 = rand(1,9);
$captcha_2 = rand(1,9);
$_SESSION['result'] = $captcha_1+$captcha_2;

?>
<!doctype html>
<html>
<head>
<title>Admin Login Area</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<style>
body {
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #eee;
}

.form-signin {
  max-width: 330px;
  padding: 15px;
  margin: 0 auto;
}
.form-signin .form-signin-heading,
.form-signin .checkbox {
  margin-bottom: 10px;
}
.form-signin .checkbox {
  font-weight: normal;
}
.form-signin .form-control {
  position: relative;
  height: auto;
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
  padding: 10px;
  font-size: 16px;
}
.form-signin .form-control:focus {
  z-index: 2;
}
.form-signin input[type="email"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
.captcha {
  font-size:18px;
  float:left;
  margin-right:10px;
  font-weight:bold
}
.answer {
  float:left;
  width:18%;
}
.checkbox {
  margin-left:20px
}
</style>
</head>
<body>
<div class="container">
    <form class="form-signin" role="form" method="POST">
	<h2 class="form-signin-heading">Please sign in</h2>
        <?php if (isset($msg)) { ?><h4 style="color:red"><?php echo $msg; ?></h4><?php } ?>
        <input type="username" name="username" class="form-control" placeholder="Username" required autofocus>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
		<input type="hidden" name="do" value="login"/>
                <div class="captcha"><?php echo $captcha_1 ?> + <?php echo $captcha_2 ?> =</div>
		<input type="answer" class="answer" name="answer" required>
                <div style="clear:both;"></div>
		<label class="checkbox">
		<input type="checkbox" value="remember-me"> Remember me
		</label>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
    </form>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>