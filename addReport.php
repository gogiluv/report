<?php
  include "lib/db.php";
  include "lib/default.php";  
  // if(!empty($_POST)){
  //   $data = $_POST;
  //   echo insertReport(null, $data);
  //   return;
  // }
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
  <script src="js/report.js"></script>
</head>
<body>

<div class="C2Scontent">
	<h1 class="location">관리메뉴 > 주간 보고(일별)</h1>
  <div>
    <div class="boardtype2" style="width:auto; max-width:1500px;">
      <div class="create-area">
        <table>
          <thead>
            <tr>
              <th scope="col">Today</th>
              <td><?=getToday()?></td>
              <th scope="col">업무 내역 추가</th>
              <td>
                <span><label for="datepicker">근무일</label><input type="text" id="datepicker" readonly/></span>
                <span><label for="work-hour">근무시간</label><input type="number" id="work-hour" value="0.00" step="0.01" min="0" max="24"/></span>
                <!-- <span>
                  <label for="work-hour">근무시간</label>
                  <input type="number" id="hour" value="0" step="1" min="0" max="23"/>시간
                  <input type="number" id="min" value="0" step="1" min="0" max="59"/>분
                  <input type="number" id="work-hour" value="0.00" step="0.01" min="0" max="24" style="-moz-appearance:textfield;"/>
                </span> -->
                <span class="project-select"></span>
                <span><input type="button" class="button red" value="추가하기" onclick="AddReport.addWork()"></span>
              </td>
            </tr>
          </thead>
        </table>
      </div>
      <div class="work-list mt-20">
        <table>
          <thead>
            <tr>
              <th width="120px">업무 일자</th>
              <th width="10%">업무 시간</th>
              <th width="20%">유형</th>
              <th width="*">내용</th>
              <th width="10%">비고</th>            
            </tr>
          </thead>
          <tbody>          
          </tbody>
        </table>
      </div>
      <div class="summary-area">
        <table>
          <tbody>
            <tr>
              <th width="15%">업무 시간 합계</th>
              <td width="15%"></td>
              <th width="15%">작성된 프로젝트</th>
              <td width="*"></td>
              <th width="15%"><input type="button" class="button red" value="제출하기" onclick="AddReport.reportSubmit()"></th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="overlay boardtype2">  
  <div class="modal-div">
    <div class="ui-icon ui-icon-closethick modal-close" onclick="AddReport.modalClose()"></div>
    <div class="modal-header">      
    </div>
    <div class="modal-content">
    </div>
  </div>
</div>
<script type="text/javascript">

  $(function(){
    $("#datepicker").datepicker({
      showOtherMonths: true,
      selectOtherMonths: true,
      changeMonth: true,
      changeYear: true,
      dateFormat:"yy-mm-dd",      
    });
    $('#datepicker').datepicker( "setDate" , AddReport.getToday() );

    AddReport.setProjects();

    //iframe resize;
    parent.fncResizeHeight(document);
    //parent.document.querySelector('#C2ScontentsFrame').height='1000px';

    if(localStorage.reportBackup){
      Report.confirm('작성중이던 보고서가 있습니다. 불러오시겠습니까?', function(b){
        if(!b) {localStorage.reportBackup=''; return;}

        //백업된것 붙인다.
        $('.work-list tbody').html(localStorage.reportBackup);
        AddReport.summary();
      })
    }

    AddReport.autoBackup = setInterval(() => {
      // 임시 백업
      // 가라로 한거니 바꾸는게 좋을듯. 파싱해서 서버에 저장하는게 낫지않을까
      if($('.work-list tbody').html().trim().length > 0) localStorage.reportBackup = $('.work-list tbody').html();
      console.log('localStorage.reportBackup='+localStorage.reportBackup);
    }, 1*60*1000);
  });

  var AddReport = {
    projects : {},

    autoBackup: null,

    getToday: function(){
      return Date.now();
    },

    getDay: function(date_str){
      var day_num = new Date(date_str).getDay();
      var day_str_arr = ['일','월','화','수','목', '금', '토'];

      return day_str_arr[day_num];
    },   

    addWork: function(){
      var selected_date = $('#datepicker').val();
      var work_hour = $('#work-hour').val();
      var project_id = $('#project-name').val();
      var project_name = $('#project-name option:selected').text();
      
      if(project_id === null) { Report.alert('프로젝트를 선택 해 주세요.'); return; }
      if(work_hour > 24) { Report.alert('근무시간은 24를 초과 할 수 없습니다.'); return; }

      //var row = $('#row-'+selected_date).length>0 ? $('#row-'+selected_date) : AddReport.createRow(selected_date, work_hour);
      var row = AddReport.createRow(selected_date, work_hour, project_name, project_id);
    },
    createRow: function(selected_date, work_hour, project_name, project_id){
      if(!selected_date){
        return;
      }
      var row_count = $('tr[data-date='+selected_date+']').length      
      //var row = document.createElement("tr");
      var row = $("<tr></tr>");
      //row.attr('data-row', row_count + 1);
      row.attr('data-date', selected_date);   
      
      var html = [];
      if(row_count>0){
        $('tr[data-date='+selected_date+']:first > th').attr('rowspan', row_count + 1);
      }else{
        html.push('<th>');
        html.push(selected_date + ' ' + AddReport.getDay(selected_date) + '요일');
        html.push('</th>');
      }
      html.push('<td>');
      html.push('<input type="number" id="work-hour" value="'+work_hour+'" step="0.01" min="0" max="24" onchange="AddReport.summary()"/>');
      //html.push(work_hour);
      html.push('</td>');
      html.push('<td>');
      html.push(AddReport.getProjectSelectHtml(project_name));
      //html.push('<td data-projectId='+project_id+'>');
      //html.push(project_name);
      html.push('</td>');
      html.push('<td>');
      html.push('<textarea placeholder="write here..."></textarea>');
      html.push('</td>');
      html.push('<td>');
      html.push('<input type="button" class="button blue" value="최근보고" onclick="AddReport.lastReport(this)">');
      html.push('<input type="button" class="button red mt-10" value="삭제" onclick="AddReport.deleteRow(this)">');
      html.push('</td>');

      row.html(html.join(''));
      if(row_count>0){
        // 추가되어있는 날짜인경우
        $('tr[data-date='+selected_date+']:last').after(row);
      }else{
        // 추가되지 않은 날짜인경우 추가할때 추가되어있는 날짜와 날짜를 비교한다.
        // 오름차순 정렬을 위함임
        var rows = $('tr[data-date]');
        for(var i=0; i < rows.length; i++){
          console.log('selected_date < rows[i]=', selected_date < $(rows[i]));
          if(selected_date < $(rows[i]).attr('data-date')){                        
            $(rows[i]).before(row);
            break;
          }
        }
        // 해당 날짜가 추가되지 않았거나 제일 큰 날짜인 경우 뒤에 붙인다
        if($('tr[data-date='+selected_date+']').length==0){ $('.work-list > table > tbody').append(row);}
      }
                  
      AddReport.drawDateLine(selected_date);
      AddReport.summary();
      parent.fncResizeHeight(document);
      row.find('textarea').focus();
      return row;
    },
    deleteRow: function(e){
      /*
      선택한 라인을 삭제한다.
      한줄 줄어들었으니 th의 rowspan을 1 감소
      해당 날짜의 제일 앞의 라인을 지우면 th가 삭제된다.
      th가 있는지 확인하고 없으면 추가하고 rowspan, selected_date 설정
      */
      var row = $(e).parent().parent();
      var selected_date = row.attr('data-date');
      var row_count = $('tr[data-date='+selected_date+']').length;
      
      row.remove();
      //삭제 후 첫라인에 th가 있는지 확인
      var row_th = $('tr[data-date='+selected_date+']:first > th');      
      if(row_th.length>0){
        $('tr[data-date='+selected_date+']:first > th').attr('rowspan', row_count - 1);
      }else{
        $('tr[data-date='+selected_date+']:first > td:eq(0)').before('<th rowspan='+(row_count-1)+'>'+selected_date+'</th>');
      }
      AddReport.drawDateLine(selected_date);      
      AddReport.summary();
      parent.fncResizeHeight(document);
    },
    drawDateLine: function(selected_date){
      //날짜 기준으로 경계를 선명하게 한다.
      var row = $('[data-date='+selected_date+']:last');
      $('tr[data-date='+selected_date+']').css('border-bottom', '');
      $('tr[data-date='+selected_date+'] > th').css('border-bottom', '2px solid #ddd');
      row.css('border-bottom', '2px solid #ddd');
    },
    summary: function(){
      var summary_area = $('.summary-area table tbody tr');
      var rows = $('.work-list table tbody tr');
      var work_time_sum = 0;
      var project_name_sum = '';
      var project_obj = {};
      
      for(var i=0; i < rows.length; i++){        
        var row = rows[i];

        var work_time = Number($(row).find('td:first input[type="number"]').val());
        work_time_sum+=work_time;

        //var project_name = $(row).find('td:eq(1)').text();
        var project_name = $(row).find('td:eq(1) > select option:selected').text();
        if(!project_obj[project_name]){
          project_obj[project_name] = 1;
        }else{
          project_obj[project_name] += 1;
        }
      }
      //키만 뽑아서 string 생성
      project_name_sum = Object.keys(project_obj).join(', ');

      summary_area.find('td:eq(0)').text(work_time_sum);
      summary_area.find('td:eq(1)').text(project_name_sum);
    },
    reportSubmit: function(){
      var data = {};
      var rows = $('.work-list table tbody tr');

      if(rows.length < 1){Report.alert('작성 내역이 없습니다.'); return;}
      
      //if(!confirm('제출하시겠습니까?')){ return; }
      Report.confirm('제출하시겠습니까?', function(b){
        if(!b) return;

        for(var i=0; i < rows.length; i++){        
          var row = rows[i];
          var work_date = $(row).attr('data-date');
          var work_hour = Number($(row).find('td:first input[type="number"]').val());
          //var project_name = $(row).find('td:eq(1)').text();
          //var project_id = $(row).find('td:eq(1)').attr('data-projectId');
          var project_name = $(row).find('td:eq(1) > select option:selected').text();
          var project_id = $(row).find('td:eq(1) > select option:selected').val();
          var content = $(row).find('textarea').val();
          data[i] = {
            work_date: work_date,
            work_hour: work_hour,
            project_name: project_name,
            project_id: project_id,
            content: content,
          }
        }
        // $.post('post_test.php', data, function(res){
        //   console.log(res);
        // });
        Report.post('insertReport', data).then(function(res){
          if(res.result){
            //백업된것 삭제
            localStorage.reportBackup='';
            //interval 중지
            clearInterval(AddReport.autoBackup);

            Report.confirm('보고서가 제출되었습니다.\n\n보고서 조회 메뉴로 이동하시겠습니까?', function(chk){
              if(chk){
                $(parent.document).find('li').removeClass('on');          
                $(parent.document).find('li:eq(3)').addClass('on');
              parent.OpenURL("pastReports.php");
              }else{
                location.reload();
              }
            });
            // var chk = confirm('보고서가 제출되었습니다.\n\n보고서 조회 메뉴로 이동하시겠습니까?');          
            // if(chk){
            //   $(parent.document).find('li').removeClass('on');          
            //   $(parent.document).find('li:eq(3)').addClass('on');
            //   parent.OpenURL("pastReports.php");
            // }else{
            //   location.reload();
            // }
          } else {
            alert('제출 실패')
          }
        });
      });
    },
    lastReport: function(e){
      var id = $(e).parent().parent().find('td:eq(1) select').val();
      console.log(id);

      var data = {
        projectIdx: id        
      }
      Report.get("getLastReportFromProject", data).then(function(res){
				if(res==null){
          Report.alert('해당 프로젝트의 이전 업무보고가 없습니다.');
					console.log(res);
					return;
        }
        AddReport.modalOpen(res);
      });
    },    
    modalOpen: function(report){
      $('.modal-header').text('보고일: '+ report.work_d);
      $('.modal-content').text(report.Report);
      $('.overlay').show();
    },
    modalClose: function(){
      $('.overlay').hide();
    },
    setProjects: function(){
      Report.get("getProjects").then(function(res){        
        AddReport.projects = res;
        //프로젝트 선택 select 생성
        AddReport.setProjectSelect();
      });
    },
    getProjectSelectHtml: function(project_name) {
      var rows = AddReport.projects;
      var html = [];
      var inserted_line = false;
      html.push('<select onchange="AddReport.summary()">');
      for(var num in rows){
        //구분선 넣기
        if(rows[num].IsGame==1 && !inserted_line){
          html.push('<option disabled></option>');
          html.push('<option disabled>-------------game--------------</option>');
          html.push('<option disabled></option>');
          inserted_line = true;
        }
        if(rows[num].ProjectName===project_name){
          html.push('<option value="'+rows[num].ProjectIdx+'" selected>');
        }else{
          html.push('<option value="'+rows[num].ProjectIdx+'">');        }
        
        html.push(rows[num].ProjectName);
        html.push('</option>');

      }
      html.push('<select>');
      return html.join('');
    },
    setProjectSelect: function(){
      var rows = AddReport.projects;
      var html = [];
      var inserted_line = false;

      html.push('<label for="project-name">프로젝트</label>');
      html.push('<select id="project-name">');      
      html.push('<option value="" disabled selected>....................</option>');
      for(var num in rows){
        //구분선 넣기
        if(rows[num].IsGame==1 && !inserted_line){
          html.push('<option disabled></option>');
          html.push('<option disabled>-------------game--------------</option>');
          html.push('<option disabled></option>');
          inserted_line = true;
        }
        html.push('<option value="'+rows[num].ProjectIdx+'">');
        html.push(rows[num].ProjectName);
        html.push('</option>');
      }
      html.push('<select>');
      $('.project-select').html(html.join(''));
    }
  }
</script>
</body>
</html>