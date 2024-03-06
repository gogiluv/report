<?php
  include "lib/default.php";
  include "lib/db.php";
  
  // if(!empty($_POST)){
  //   $data = $_POST;
  //   $result = getStatisticsProject(null, $data["memberIdx"], $data["year"], $data["month"]);
  //   echo $result;
  //   return;
  // }
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
  <link rel="stylesheet" href="/css/ui.css" type="text/css" />
  <link rel="stylesheet" href="/css/button.css" type="text/css" />
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="css/common.css">    
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="js/report.js"></script>
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
              <th>통계 유형</th>
              <td>
                <select id="type" onchange="SP.selectType()">
                  <option value='month' selected>월간</option>
                  <option value='year'>연간</option>
                  <option value='period'>기간</option>
                </select>
              </td>
              <th>년</th>
              <td>
                <select id="year" onchange="SP.searchResult()">
                  <?php
                    for($i=2023; $i>=2013; $i--){
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
              <th width="10%">월</th>
              <th>프로젝트명</th>
              <th width="15%">업무 시간</th>
              <th width="15%">업무 시간 비율</th>
              <th width="16%">프로젝트 투입시간</th>
              <th width="15%">프로젝트 투입률</th>
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
      month: null, //new Date().getMonth() + 1
      type: 'month'
    },
    searchResult: function(){
      let data = SP.default_mym;
      data.memberIdx = $('select[id=name]').val() || null;
      data.year = $('select[id=year]').val() || new Date().getFullYear();
      data.month = $('select[id=month]').val() || null;
      data.type = $('select[id=type]').val() || 'month';

      Report.post('getStatisticsProject', data).then(function(res){        
        SP.renderRows(res);
      });
    },
    selectType: function() {
      console.log('selectType');
      data.type = $('select[id=type]').val() || 'month';
      console.log(data);

    },
    renderPeriodBox: function() {
      var html = `
      <td>
                  <span>
                    <label for="from">업무일</label>
                    <input type="text" id="from" name="from">
                  </span>
                  <span>
                    <label for="to">~</label>
                    <input type="text" id="to" name="to">
                  </span>`;
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

        // 0/0 처리
        if(work_h == 0) {$(row).find('td:eq(2)').text('0.00%'); continue;}

        $(row).find('td:eq(2)').text(((parseFloat(work_h)/work_h_sum[month])*100).toFixed(2)+'%');
      }

      //월별 프로젝트 투입시간 비율계산
      for(var i=0; i<$('tr[data-date]').length; i++){
        row = $('tr[data-date]')[i];
        var month = $(row).attr('data-date');
        var work_h = $(row).find('td[data-work-h]').attr('data-work-h');

        // 0/0 처리
        if(work_h == 0) {$(row).find('td:last').text('0.00%'); continue;}

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
