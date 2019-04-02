<?php
  include "lib/default.php";
  include "lib/db.php";
	
	//get
  if(!empty($_GET)){
    $data = $_GET;
		$result = getReportsFromDate(null, $data);
		
		echo $result;
    return;
	}
	//post
	if(!empty($_POST)){
    $data = $_POST;
		$result = deleteReport(null, $data);
		
		echo $result;
    return;
  }
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
	<h1 class="location">관리메뉴 > 지난보고서</h1>
  <div>    
    <div class="boardtype2" style="width:auto; max-width:1300px;">
      <div class="select-area">
        <table style="width:400px;">
          <thead>
            <tr>
              <td>
								<span>
									<label for="from">업무일</label>
									<input type="text" id="from" name="from">
								</span>
								<span>
									<label for="to">~</label>
									<input type="text" id="to" name="to">
								</span>
								<span><input type="button" class="button red" value="검색하기" onclick="PR.search()"></span>
							</td>
            </tr>
          </thead>
        </table>
      </div>
      <div class="pr-list mt-20">
        <table>
          <thead>
            <tr>
							<th width="10%">업무일자</th>
							<th width="10%">근무 시간</th>
              <th width="18%">유형</th>
							<th width="*">내용</th>              
							<th width="12%">비고</th>
            </tr>            
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>      
    </div>
  </div>
</div>
<div class="overlay boardtype2">  
  <div class="modal-div">
    <div class="ui-icon ui-icon-closethick modal-close" onclick="PR.expandModalClose()"></div>
    <div class="modal-header">      
    </div>
    <div class="modal-content">
    </div>
  </div>
</div>
<script type="text/javascript">
	var PR = {
    projects : {},
      
    getToday: function(){
      return Date.now();
		},
		search: function() {
			var data = {
				from: $('#from').val(),
				to: $('#to').val()
			}
			$.get('pastReports.php', data, function(res){
				var json_res = null;
        try {
          json_res = JSON.parse(res);
        } catch (e) {
          console.log(e);
          console.log(res);
          return;
				}
				if(json_res.error){
					console.log(json_res.error);
					return;
				}
        PR.renderRows(json_res);
			})
		},
		renderRows: function(rows) {
      var list = $('.pr-list tbody');

      list.html(''); //초기화
      for(var num in rows){
        var html = [];        
        row = rows[num];
        var rendered = $('[data-date='+row.work_d+']');

        html.push('<tr data-date='+row.work_d+' data-report-id='+row.reportidx+'>');
        if(rendered.length == 0) {
          html.push('<th>');
          html.push(row.work_d);
          html.push('</th>');
        } else {
          rendered.first().find('th:first').attr('rowspan', rendered.length + 1);
				}
				html.push('<td data-work-h='+row.work_h+'>');
        html.push(Number(row.work_h));
        html.push('</td>');
        html.push('<td>');
        html.push(row.projectname);
        html.push('</td>');
        html.push('<td class="preview-scroll-hidden mh-200">');
        html.push('<div class="ui-icon ui-icon-arrow-4-diag btn expand-btn" onclick="PR.expandReport('+row.reportidx+')"></div>');
				html.push(row.report);
				html.push('</td>');
        html.push('<td>');
        html.push('<input type="button" class="button blue mr-10" value="수정" onclick="PR.modifyForm('+row.reportidx+')">');
        html.push('<input type="button" class="button red" value="삭제" onclick="PR.deleteRow('+row.reportidx+')">');
				html.push('</td>');				
        html.push('</tr>');
        list.append(html.join(''));

        parent.fncResizeHeight(document);	//resize
			}			
		},
		deleteRow: function(id){
			var chk = confirm('삭제하시겠습니까?');
			if(!chk){return;}

			var data = {
				reportIdx: id
			}

			$.post('pastReports.php', data, function(res){
				var json_res = null;
        try {
          json_res = JSON.parse(res);
        } catch (e) {
          console.log(e);
          console.log(res);
          return;
				}
				if(json_res==null || json_res.error){
					console.log(json_res);
					return;
				}
        PR.search();
			})
    },
    expandReport: function(id){
      var data = {
        reportIdx: id        
      }
      Report.get("getReportFromId", data).then(function(res){
        var json_res = null;
        try {
          json_res = JSON.parse(res);
        } catch (e) {
          console.log(e);
          console.log(res);
          return;
				}
				if(json_res==null || json_res.error){
					console.log(json_res);
					return;
				}
        PR.expandModalOpen(json_res);
      });
      // $.get('lib/api.php', data, function(res){
      //   console.log(res);
      // });
    },
    expandModalOpen: function(report){
      $('.modal-header').text('보고일: '+ report.work_d);
      $('.modal-content').text(report.Report);
      $('.overlay').show();
    },
    expandModalClose: function(){
      $('.overlay').hide();
    },
    modifyForm: function(id){
      var row = $('tr[data-report-id='+id+']');
      var data = {
        reportIdx: id        
      }
      Report.get("getReportFromId", data).then(function(res){
        var json_res = null;
        try {
          json_res = JSON.parse(res);
        } catch (e) {
          console.log(e);
          console.log(res);
          return;
				}
				if(json_res==null || json_res.error){
					console.log(json_res);
					return;
        }
        $(row).find('td:eq(0)').html('<input type="number" id="work-hour" value="'+json_res.work_h+'" step="0.01" min="0" max="24" onchange=""/>');
        $(row).find('td:eq(1)').html(PR.getProjectSelectHtml(json_res.ProjectIdx));
        $(row).find('td:eq(2)').css('height', '100px');
        $(row).find('td:eq(2)').html('<textarea placeholder="write here...">'+json_res.Report+'</textarea>');
        $(row).find('td:eq(3)').html('<input type="button" class="button blue mr-10" value="확인" onclick="PR.modifyConfirm('+id+')">'
          +'<input type="button" class="button red" value="취소" onclick="PR.resetForm('+id+')">');        
      });
    },
    modifyConfirm: function(id) {
      var row = $('tr[data-report-id='+id+']');      
      var data = {
        report_id: id,
        work_hour: Number($(row).find('td:first input[type="number"]').val()),
        project_id: $(row).find('td:eq(1) > select option:selected').val(),
        content: $(row).find('textarea').val()
      }
      Report.get("updateReport", data).then(function(res){
        var json_res = null;
        try {
          json_res = JSON.parse(res);
        } catch (e) {
          console.log(e);
          console.log(res);
          return;
				}
				if(json_res==null || json_res.error){
					console.log(json_res);
					return;
        }

        // 업데이트 후 해당 항목 불러옴
        // 성공체크
        if(json_res.result){
          PR.resetForm(id);
        }
      });
    },
    resetForm: function(id) {
      var row = $('tr[data-report-id='+id+']');
      var data = {
        reportIdx: id        
      }
      Report.get("getReportFromId", data).then(function(res){
        var json_res = null;
        try {
          json_res = JSON.parse(res);
        } catch (e) {
          console.log(e);
          console.log(res);
          return;
				}
				if(json_res==null || json_res.error){
					console.log(json_res);
					return;
        }
        
        $(row).find('td:eq(0)').text(Number(json_res.work_h));
        $(row).find('td:eq(1)').text(json_res.ProjectName);
        $(row).find('td:eq(2)').css('height', '');
        $(row).find('td:eq(2)').html(
          '<div class="ui-icon ui-icon-arrow-4-diag btn expand-btn" onclick="PR.expandReport('+json_res.ReportIdx+')"></div>'
          + json_res.Report
        );        
        $(row).find('td:eq(3)').html('<input type="button" class="button blue mr-10" value="수정" onclick="PR.modifyForm('+json_res.ReportIdx+')">'
          + '<input type="button" class="button red" value="삭제" onclick="PR.deleteRow('+json_res.ReportIdx+')">');        
      });
    },

    getProjectSelectHtml: function(projectIdx) {
      var rows = PR.projects;
      var html = [];
      
      html.push('<select>');

      for(var num in rows){
        if(rows[num].ProjectIdx===projectIdx){
          html.push('<option value="'+rows[num].ProjectIdx+'" selected>');
        }else{
          html.push('<option value="'+rows[num].ProjectIdx+'">');        }
        
        html.push(rows[num].ProjectName);
        html.push('</option>');
      }

      html.push('<select>');

      return html.join('');
    },

    setProjects: function(){
      Report.get("getProjects").then(function(res){
        var json_res = null;
        try {
          json_res = JSON.parse(res);
        } catch (e) {
          console.log(e);
          console.log(res);
          return;
        }
				if(json_res==null || json_res.error){
					console.log(json_res);
					return;
        }        
        PR.projects = json_res;
      });
    }
	}
  
	$(function(){
    // $("#datepicker").datepicker({
    //   showOtherMonths: true,
    //   selectOtherMonths: true,
    //   changeMonth: true,
    //   changeYear: true,
    //   dateFormat:"yy-mm-dd",      
    // });
		// $('#datepicker').datepicker( "setDate" , PR.getToday() );
		
    var dateFormat = "yy-mm-dd",
      from = $( "#from" )
        .datepicker({
					showOtherMonths: true,
          defaultDate: 0,
					changeMonth: true,
					changeYear: true,
    			dateFormat:"yy-mm-dd",      
          //numberOfMonths: 3
        })
        .on( "change", function() {
          to.datepicker( "option", "minDate", getDate( this ) );
        });

      from.datepicker( "setDate" , -6);

      to = $( "#to" ).datepicker({
				showOtherMonths: true,
        defaultDate: 0,
				changeMonth: true,
				changeYear: true,
    		dateFormat:"yy-mm-dd",      
        //numberOfMonths: 3
      })
      .on( "change", function() {
        from.datepicker( "option", "maxDate", getDate( this ) );
			});

			to.datepicker( "setDate" , PR.getToday() );
 
    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      } 
      return date;
    }
    
    PR.search();
    PR.setProjects();
  });
</script>
</body>
</html>