<?php
if (isset($_POST['hummel']) and $_POST['hummel'] == 'yes'){
	global $site;
	foreach($_POST as &$post){
		$post = htmlspecialchars($post, ENT_QUOTES);
	}
	$subj = 'Mail from site ' . Core::$config['sites'][$site]['name'];
	$headers = 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
	$mes="
<b>Name:</b> {$_POST['name']}<br>
<b>E-mail:</b> {$_POST['email']}<br>
<b>Text:</b> {$_POST['comment']}
";
	$mes = wordwrap($mes, 70);
	unset($_POST['hummel']);
	mail($page['meta:form_mail'], $subj, $mes, $headers);
}
?>
<form action="" method="post">
	<div><input type="text" name="name" placeholder="Name"></div>
	<div><input type="text" name="email" placeholder="E-mail" required></div>
	<div><textarea name="comment" placeholder="Text"></textarea></div>
	<div><input type="submit" class="btn btn-primary" value="Send"></div>
</form>

$headers   = array();
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-type: text/plain; charset=utf-8";
$headers[] = "From: Promo <robot@promo.ru>";
$headers[] = "Subject: {$subject}";
$headers[] = "X-Mailer: PHP/".phpversion();
$headers = implode("\r\n", $headers);