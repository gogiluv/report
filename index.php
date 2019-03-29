<?php
	include "lib/db.php";
	$link = DBConnect();

	if(SessionCheck() == false) {
		header("Location: /login.php");
		exit;
	}
	else{
		header("Location: /layout.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>ECO 업무보고 사이트 </title>
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
</head>
<body>

<p>로그인 후 업무보고 사이트 메인  <a href="login.php?logout">로그아웃</a></p>

</body>
</html>

<?php @mysqli_close($link) ?>
