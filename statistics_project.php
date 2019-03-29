<?php
  include "lib/default.php";
  include "lib/db.php";
  
  if(!empty($_POST)){
    $data = $_POST;
    $result = getStatisticsProject(null, $data["memberIdx"], $data["year"], $data["month"]);
    echo $result;
    return;
  }
  //팀원 list
  $members = getMemberAll(null);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Page Title</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://manager.com2us.com/guide/css/ui.css" type="text/css" />
  <link rel="stylesheet" href="http://manager.com2us.com/guide/css/button.css" type="text/css" />
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="css/common.css">    
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body>

<div class="C2Scontent">
	<h1 class="location">관리메뉴 > 프로젝트별 통계</h1>
  <div>
    <div>
    <div class="boardtype2" style="width:auto; max-width:1500px;">
      <div class="select-area">
        <table style="width:500px;">
          <thead>
            <tr>
              <?php
                if ($_SESSION["report_login_level"] >= 3)
                {
              ?>
              <th>팀원</th>
              <td>
                <select id="name" onchange="SP.searchResult()">
                  <option value="">All</option>
                  <?php                  
                    while($row = mysqli_fetch_array($members))
                    {                        
                      echo "<option value=$row[MemberIdx]>$row[MemberName]</option>";
                    }
                    @mysqli_free_result($members);
                  ?>
                </select>
              </td>
              <?php 
                }
              ?>
              <th>년</th>
              <td>
                <select id="year" onchange="SP.searchResult()">
                  <?php
                    for($i=2019; $i>=2013; $i--){
                     echo "<option value=$i>$i</option>";
                    }
                  ?>
                </select>
              </td>
              <th>월</th>
              <td>
                <select id="month" onchange="SP.searchResult()">
                  <option value="">All</option>
                  <?php
                    for($i=1; $i<=12; $i++){
                     echo "<option value=$i>$i</option>";
                    }
                  ?>
                </select>
              </td>              
            </tr>
          </thead>
        </table>
      </div>
      <div class="sp-list mt-20" style="width:800px;">
        <table>
          <thead>
            <tr>
              <th width="15%">월</th>
              <th>프로젝트명</th>
              <th width="20%">프로젝트 투입시간</th>
              <th width="20%">프로젝트 투입률</th>              
            </tr>            
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>      
    </div>
  </div>
</div>
<script type="text/javascript">
  var SP = {
    default_mym: {
      memberIdx: null,
      year: new Date().getFullYear(),
      month: null //new Date().getMonth() + 1
    },
    searchResult: function(){
      data = SP.default_mym;
      data.memberIdx = $('select[id=name]').val() || null;
      data.year = $('select[id=year]').val() || new Date().getFullYear();
      data.month = $('select[id=month]').val() || null;

      $.post('statistics_project.php', data, function(res){        
        //json_res = JSON.parse(res);
        var json_res = null;
        try {
          json_res = JSON.parse(res);
        } catch (e) {
          console.log(e);
          console.log(res);
          return;
        }  
        if(json_res==null || json_res.error){
          console.error(json_res);
          return;
        }
        SP.renderRows(json_res);
      });
    },
    renderRows: function(rows){
      //console.log(rows);
      var list = $('.sp-list tbody');
      var work_h_sum ={}; //월별 업무시간 합계

      list.html(''); //초기화
      for(var num in rows){
        var html = [];        
        row = rows[num];
        var rendered = $('[data-date='+row.month+']');

        html.push('<tr data-date='+row.month+'>');
        if(rendered.length == 0) {
          html.push('<th>');
          //html.push((new Date(row.work_d).getMonth()+1)+'월');
          html.push(row.month+'월');
          html.push('</th>');
        } else {
          rendered.first().find('th:first').attr('rowspan', rendered.length + 1);
        }
        html.push('<td>');
        html.push(row.projectname);
        html.push('</td>');
        html.push('<td data-work-h='+row.hour+'>');
        html.push(Number(row.hour));
        html.push('</td>');
        html.push('<td>');
        html.push('</td>');        
        html.push('</tr>');
        list.append(html.join(''));

        if(work_h_sum[row.month]) { work_h_sum[row.month] += Number(row.hour); }
        else { work_h_sum[row.month] = Number(row.hour); }

        parent.fncResizeHeight(document);
      }

      //월별 업무시간 비율계산
      for(var i=0; i<$('tr[data-date]').length; i++){
        row = $('tr[data-date]')[i];
        var month = $(row).attr('data-date');
        var work_h = $(row).find('td[data-work-h]').attr('data-work-h');        
        $(row).find('td:last').text(((parseFloat(work_h)/work_h_sum[month])*100).toFixed(2)+'%');
      }
    }
  }

  $(function(){
    SP.searchResult();
  });
</script>
</body>
</html>