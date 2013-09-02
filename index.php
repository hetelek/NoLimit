<?php
require_once('./snaphax.php');
$key = 'somekey';

$useCookieInfo = false;
if (isset($_COOKIE['nl']))
{
	$userInfo = unserialize(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($_COOKIE['nl']), MCRYPT_MODE_ECB));
	$useCookieInfo = (isset($userInfo['u']) && isset($userInfo['p']));
	setcookie('nl', '', time() - 3600);
}

$logged = false;
$loginAttempted = false;
if (isset($_POST['username']) && isset($_POST['password']) || $useCookieInfo) {
	$loginAttempted = true;
	$opts = array();
	
	if ($useCookieInfo)
	{
		$opts['username'] = $userInfo['u'];
		$opts['password'] = $userInfo['p'];
	}
	else
	{
		$opts['username'] = $_POST['username'];
		$opts['password'] = $_POST['password'];
	}
	
	$s = new Snaphax($opts);
	$result = $s->login();
	if (!empty($result))
		$logged = $result['logged'];
		
	if ($logged)
	{
		$data = array();
		$data['u'] = $result['username'];
		$data['p'] = $opts['password'];
		
		setcookie('nl', base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, serialize($data), MCRYPT_MODE_ECB)), time() + 604800);
	}
}
?>

<!doctype html>
<html>
	<head>
		<script type="text/javascript">window.google_analytics_uacct = "UA-19117130-5";</script>
		<meta charset="UTF-8" />
		<meta name="viewport" content="initial-scale=1, maximum-scale=1">
		<title>NoLimit (for Snapchat)</title>
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
		<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			ga('create', 'UA-19117130-5', 'expetelek.com');
			ga('send', 'pageview');
		</script>
	</head>
	<body>
		<div id="tasksPage" data-role="page">
			<div data-role="header" data-position="fixed">
				<a href="about.html" data-icon="info" data-rel="dialog">About</a>
				<h1>NoLimit</h1>
				<?php if ($logged) { ?>
					<a data-icon="delete" data-rel="dialog" onclick="document.cookie = 'nl=; expires=Thu, 01 Jan 1970 00:00:01 GMT;'; location.reload();">Logout</a>
				<?php } ?>
			</div>
			
			<script type="text/javascript">
			if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
			<!--
				google_ad_client = "ca-pub-3936904007874715";
				/* NoLimit Mobile Ad */
				google_ad_slot = "8501268847";
				google_ad_width = 320;
				google_ad_height = 50;
				//-->
				}
			</script>
			<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
			
			<div data-role="content" style="max-width:100%;padding-top:0px;">
		<?php if (!$logged) { if (!$loginAttempted) { ?>
		<?php } ?>
				<form action="index.php" method="POST">
					<input type="text" name="username" id="username" placeholder="Snapchat Username">
					<input type="password" name="password" id="password" placeholder="Snapchat Password">
					<input type="submit" value="Login" data-icon="check" data-theme="a">
				</form>
		<?php } else if (!empty($result['snaps'])) { ?>
				<div data-role="collapsible-set" data-theme="c" data-content-theme="d">
		<?php
				foreach ($result['snaps'] as $snap) {
					if ($snap['st'] == SnapHax::STATUS_NEW && !empty($snap['sn']) && !isset($snap['cap_ori'])) {
		?>
						<div data-role="collapsible">
							<h3><?php echo $snap['sn']; ?></h3>
							<img style="max-width:99%" src="<?php
								$data = array();
								$data['u'] = $result['username'];
								$data['at'] = $result['auth_token'];
								$data['id'] = $snap['id'];
							      
								echo 'snap.php?d=' . urlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, serialize($data), MCRYPT_MODE_ECB)));
							?>"</p>
						</div>
		<?php } } ?>
				</div>
		<?php } else { ?>
				<ul data-role="listview">
					<li style="text-align:center;">You have no new snaps.</li>
				</ul>
		<?php } ?>
			</div>
		</div>
	</body>
</html>