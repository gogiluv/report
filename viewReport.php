<?php
	include "db.php";
	$link = DBConnect();
	$reportIdx = @addslashes($_GET['reportIdx']);
	$reportDate = @addslashes($_GET['selectedDate']);
	$memberName="";
	$projectName="";
	$platformName="";
	$marketName="";
	$ver="";
	$spendTime="";
	$spendPercent="";
	$report="";
	$nextReport="";


	if($reportIdx)
	{
		$result = @mysqli_query($link, "SELECT * FROM ECO_Reports WHERE ReportIdx = '$reportIdx'");



		while($row = mysqli_fetch_array($result))
		{

			$meberNameResult = @mysqli_query($link, "SELECT MemberName FROM ECO_Member WHERE MemberIdx = '$row[MemberIdx]'");
			$projectNameResult = @mysqli_query($link, "SELECT ProjectName FROM ECO_Project WHERE ProjectIdx = '$row[ProjectIdx]'");
			$marketNameResult = @mysqli_query($link, "SELECT MarketName FROM ECO_Market WHERE MarketIdx = '$row[MarketIdx]'");
			$platformNameResult = @mysqli_query($link, "SELECT PlatformName FROM ECO_Platform
														WHERE PlatformIdx =(SELECT PlatformIdx FROM ECO_Market 
														WHERE MarketIdx = '$row[MarketIdx]')");

			$memberName = @mysqli_result($meberNameResult,0,0);
			$projectName = @mysqli_result($projectNameResult,0,0);
			$marketName = @mysqli_result($marketNameResult,0,0);
			$platformName = @mysqli_result($platformNameResult,0,0);

			$ver = $row['Ver']; 
			$spendTime = $row['SpendTime'];
			$spendPercent = $row['SpendPercent'];
			$report = $row['Report'];
			$nextReport= $row['NextReport'];

			@mysqli_free_result($meberNameResult);
			@mysqli_free_result($projectNameResult);
			@mysqli_free_result($marketNameResult);
			@mysqli_free_result($platformNameResult);

		}
		@mysqli_free_result($result);

		
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1" runat="server">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/base.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
    <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />

    <script type="text/javascript">

       
        function resize() {
            parent.fncResizeHeight(document);
        }
    </script>

    <title>ECO팀 업무보고 - 이번주 보고서</title>
</head>
<body onload="return resize()">
    <div class="C2Scontent">
        <h1 class="location">
            관리메뉴 &gt; 보고서 세부내용
        </h1>
        <p class="t2">
            * 보고된 내용은 파트장/팀장만 확인가능합니다</p>
        <div class="divide">
        </div>
    </div>
    <form id="FormViewReport" name="formSignup" runat="server" method="post" enctype="multipart/form-data">
    <div class="C2Scontent">
        <div class="boardtype2">
		<table>
			<tr>
			<td align="center" bgColor="#eeeeee" >보고날짜</td>
			<td colspan="3"><font color="#dd0000">
			<?php 
				if($reportDate == "")  
					echo GetReportSummaryDate();
				else
					 echo $reportDate; 
			?></font></td>		
			</tr>
			<tr style="height:10px"></tr>
			<tr  bgColor="#eeeeee" >
			<td align="center" colspan="4">업무내용</td>
			</tr>
			<tr>
			<td style="width:20%"  bgColor="#eeeeee">개발자</td>
			<td colspan="3"><input type="text"  value="<?php echo $memberName ?>" style="background-color:#eeeeee" readonly/></td>
			</tr>
			<tr>
			<td style="width:20%" bgColor="#eeeeee">프로젝트</td>
			<td colspan="3"><input type="text" value="<?php echo $projectName ?>" style="background-color:#eeeeee" readonly/></td>
			</tr>
			<tr>
			<td style="width:20%" bgColor="#eeeeee">플랫폼</td>
			<td colspan="3"><input type="text" value="<?php echo $platformName ?>" style="background-color:#eeeeee" readonly/></td>
			</tr>
			<tr>
			<td style="width:20%" bgColor="#eeeeee">마켓</td>
			<td colspan="3"><input type="text" value="<?php echo $marketName ?>" style="background-color:#eeeeee" readonly/></td>
			</tr>
			<tr>
			<td style="width:20%" bgColor="#eeeeee">버전</td>
			<td colspan="3"><input type="text" value="<?php echo $ver ?>" style="background-color:#eeeeee" readonly/></td>
			</tr>
			<tr>
			<td style="width:20%" bgColor="#eeeeee">투입시간</td>
			<td><input type="text" value="<?php echo $spendTime ?>"  style="background-color:#eeeeee" readonly/></td>
			<td style="width:20%" bgColor="#eeeeee">주간 투입률</td>
			<td><input type="text" value="<?php echo $spendPercent ?>"  style="background-color:#eeeeee" readonly/></td>
			</tr>
			<tr>
			<td bgColor="#eeeeee">주간업무 요약</td>
			<td colspan="3"><textarea style="width:500px;height:150px;background-color:#eeeeee" readonly><?php echo $report ?></textarea></td>
			</tr>
			<tr>
			<td bgColor="#eeeeee">다음주 업무일정</td>
			<td colspan="3"><textarea style="width:500px;height:150px;background-color:#eeeeee" readonly><?php echo $nextReport ?></textarea></td>
			</tr>
		</table>
		</div>
		<div>
    </div>
    </form>
</body>
</html>
<?php @mysqli_close($link) ?>
