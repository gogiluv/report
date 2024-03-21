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
        <table style="width:300px;">
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
              <!-- <th>통계 유형</th>
              <td>
                <select id="type" onchange="SP.selectType()">
                  <option value='month' selected>월간</option>
                  <option value='year'>연간</option>
                  <option value='period'>기간</option>
                </select>
              </td> -->
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
      
      <div class="sp-list mt-20" style="width:900px;">
        <table>
          <thead>
            <tr>
              <th width="10%" rowspan="3">월</th>
              <th rowspan="3">프로젝트명</th>
              <th width="12%" rowspan="3">업무 시간</th>
              <th width="12%" rowspan="3">업무 시간 비율</th>
              <th width="40%" colspan="4">프로젝트 투입시간</th>
            </tr>
            <tr>            
              <th width="20%" colspan="2">공통</th>
              <th width="20%" colspan="2">게임</th>
            </tr>
            <tr>            
              <th width="10%">시간</th>
              <th width="10%">비율</th>
              <th width="10%">시간</th>
              <th width="10%">비율</th>
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
      //data.type = $('select[id=type]').val() || 'month';

      Report.post('getStatisticsProject', data).then(function(res){        
        SP.renderRows(res);
      });
    },
    // selectType: function() {
    //   console.log('selectType');
    //   data.type = $('select[id=type]').val() || 'month';
    //   console.log(data);

    // },
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
      var project_h_sum ={}; //월별 프로젝트 투입시간 합계
      var game_mm = {};  //game man month
      var common_mm = {}; //common man month

      list.html(''); //초기화

      // 시간 합산용
      for(var num in rows){
        var row = rows[num];
        if(work_h_sum[row.month]) { work_h_sum[row.month] += Number(row.hour); }
        else { work_h_sum[row.month] = Number(row.hour); }
                
        // 연차 제외한 순수 근무시간
        // if(row.projectidx!=12){
        //   if(project_h_sum[row.month]) { project_h_sum[row.month] += Number(row.hour); }
        //   else { project_h_sum[row.month] = Number(row.hour); }
        // }
        if(project_h_sum[row.month]) { project_h_sum[row.month] += Number(row.hour); }
        else { project_h_sum[row.month] = Number(row.hour); }
      }

      for(var num in rows){
        var html = [];        
        var row = rows[num];
        var rendered = $('[data-date='+row.month+']');

        html.push('<tr data-date='+row.month+' data-isgame='+row.isgame+'>');
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
        html.push(((parseFloat(row.hour)/work_h_sum[row.month])*100).toFixed(2)+'%');
        html.push('</td>');

        /*
          게임과 그외(공통)의 프로젝트를 구분
          연차는 man month 계산에서 제외한다(projectidx 12 가 연차임) - 삭제
          20240321 연차도 포함하도록 수정
        */

        if(row.isgame==0) {
          //var mm = Number(((parseFloat(row.hour)/project_h_sum[row.month])).toFixed(2));
          var mm = Math.floor(((parseFloat(row.hour)/project_h_sum[row.month]))*100)/100;
          //man month 합산
          if(common_mm[row.month]) { common_mm[row.month] += mm; }
          else { common_mm[row.month] = mm; }

          html.push('<td data-common-h='+row.hour+'>');
          html.push(Number(row.hour));
          html.push('</td>');
          // html.push('<td>');            
          // html.push(mm);
          // html.push('</td>');
        } else {
          html.push('<td></td>');
          // html.push('<td></td>');
        }

        if(rendered.length == 0) {
          html.push('<td></td>');
        } else {
          rendered.first().find('td:eq(4)').attr('rowspan', rendered.length + 1);
        }


        if(row.isgame==1) {
          var mm = Math.ceil(((parseFloat(row.hour)/project_h_sum[row.month]))*100)/100;
          //var mm = Number(((parseFloat(row.hour)/project_h_sum[row.month])).toFixed(2));
          
          //man month 합산
          if(game_mm[row.month]) { game_mm[row.month] += mm; }
          else { game_mm[row.month] = mm; }

          html.push('<td data-game-h='+row.hour+'>');
          html.push(Number(row.hour));
          html.push('</td>');
          html.push('<td>');            
          html.push(mm);
          html.push('</td>');
        } else {
          html.push('<td>');
          html.push('</td>');
          html.push('<td>');
          html.push('</td>');  
        }
        // html.push('<td>');
        // html.push('</td>');        
        html.push('</tr>');
        list.append(html.join(''));

        parent.fncResizeHeight(document);
      }      
      
      
      // //월별 업무시간 비율계산
      // for(var i=0; i<$('tr[data-date]').length; i++){
      //   row = $('tr[data-date]')[i];
      //   var month = $(row).attr('data-date');
      //   var work_h = $(row).find('td[data-work-h]').attr('data-work-h');

      //   // 0/0 처리
      //   if(work_h == 0) {$(row).find('td:eq(2)').text('0.00%'); continue;}

      //   //$(row).find('td:eq(2)').text(((parseFloat(work_h)/work_h_sum[month])*100).toFixed(2)+'%');
      // }
      SP.sumWorkHour(game_mm, common_mm);
    },

    sumWorkHour: function(game_mm, common_mm){
      let month_concat = Object.keys(game_mm).concat(Object.keys(common_mm))
      let month = month_concat.filter((m, i)=> month_concat.indexOf(m)===i);      

      for(let i of month) {
        let html = [];
        let month_length = $('tr[data-date='+i+']').length;
      
        html.push('<tr class="sum">');        
        html.push('<td>합계</td>');
        html.push('<td colspan=3></td>');
        html.push('<td>');
        //html.push(common_mm[i]? common_mm[i].toFixed(2): '');
        // 게임 프로잭트 투입 시간이 있으면 공통은 "1 - 게임MM" 으로 계산한다
        // 게임 프로잭트 투입시간이 없으면 공통MM 기입
        html.push(game_mm[i]? (1 - game_mm[i]).toFixed(2): 1);
        html.push('</td>');        
        html.push('<td></td>');
        html.push('<td>');
        html.push(game_mm[i]? game_mm[i].toFixed(2): '')
        html.push('</td>');        
        html.push('</tr>');

        $('tr[data-date='+i+']').last().after(html.join(''));
        $('tr[data-date='+i+']').first().find('th:first').attr('rowspan', month_length + 1)
      }

      $('.sum td').css('background-color', 'lightyellow');
    },
  }

  $(function(){
    SP.searchResult();
  });
</script>
</body>
</html>
