<?php
  include "lib/default.php";
  include "lib/db.php";
	
	//get
  if(!empty($_GET)){
    $data = $_GET;
		$result = getReportsFromMemberWithDate(null, $data);
		
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
  <script src="js/report.js"></script>
</head>
<body>

<div class="C2Scontent">
	<h1 class="location">관리메뉴 > 업무 보고 취합</h1>
  <div>
    <div>
      <div class="boardtype2" style="width:auto; max-width:1500px;">
        <div class="select-area">
          <table style="width:500px;">
            <thead>
              <tr>
                <td>
                  <select id="name">
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

                <td>
                  <span>
                    <label for="from">업무일</label>
                    <input type="text" id="from" name="from">
                  </span>
                  <span>
                    <label for="to">~</label>
                    <input type="text" id="to" name="to">
                  </span>
                  <span><input type="button" class="button red" value="검색하기" onclick="RS.search()"></span>
                </td>
              </tr>
            </thead>
          </table>
        </div>
        <div class="rs-list mt-20">
          <table>
            <thead>
              <tr>
                <th width="10%">이름</th>
                <th width="10%">업무일자</th>
                <th width="10%">근무 시간</th>
                <th width="15%">유형</th>
                <th width="*">내용</th>
                <th width="10%">비고</th>
              </tr>            
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>      
      </div>
    </div>
  </div>
</div>
<div class="overlay boardtype2">  
  <div class="modal-div">
    <div class="ui-icon ui-icon-closethick modal-close" onclick="RS.expandModalClose()"></div>
    <div class="modal-header">      
    </div>
    <div class="modal-content">
    </div>
  </div>
</div>
<script type="text/javascript">
	var RS = {
    getToday: function(){
      return Date.now();
		},
		search: function() {
			var data = {
        memberIdx: $('#name').val(),
				from: $('#from').val(),
				to: $('#to').val()
      }

			$.get('reportSummary.php', data, function(res){
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
        RS.renderRows(json_res);
			})
		},
		renderRows: function(rows) {
      var list = $('.rs-list tbody');

      list.html(''); //초기화
      for(var num in rows){
        var html = [];        
        row = rows[num];
        var rendered_member = $('tr[data-member-name='+row.membername+']');
        var rendered_date = $('tr[data-member-name='+row.membername+'][data-date='+row.work_d+']');

        html.push('<tr data-member-name='+row.membername+' data-date='+row.work_d+' data-report-id='+row.reportidx+'>');
        if(rendered_member.length == 0) {
          html.push('<th>');
          html.push(row.membername);
          html.push('</th>');
        } else {
          rendered_member.first().find('th:first').attr('rowspan', rendered_member.length + 1);
        }
        if(rendered_date.length == 0) {
          html.push('<td>');
          html.push(row.work_d);
          html.push('</td>');
        } else {
          rendered_date.first().find('td:first').attr('rowspan', rendered_date.length + 1);
				}				        
        html.push('<td data-work-h='+row.work_h+'>');
        html.push(Number(row.work_h));
        html.push('</td>');
        html.push('<td>');
				html.push(row.projectname);
        html.push('</td>');
        html.push('<td>');        
        html.push('<div class="preview-scroll-hidden mh-200">');
        html.push('<div class="ui-icon ui-icon-arrow-4-diag btn expand-btn" onclick="RS.expandReport('+row.reportidx+')"></div>');
        html.push(row.report);
        html.push('</td>');
        html.push('<td>');
				//html.push('<input type="button" class="button red" value="삭제" onclick="RS.deleteRow('+row.reportidx+')">');
        html.push('</td>');
        html.push('</tr>');        
        list.append(html.join(''));

        parent.fncResizeHeight(document);	//resize
			}			
    },
    
		deleteRow: function(id){
      // TODO: 필요한지 확인 해야함      
			var chk = confirm('삭제하시겠습니까?');
      
      if(!chk){return;}

			var data = {
				reportIdx: id
			}

			$.post('reportSummary.php', data, function(res){
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
        RS.search();
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
        RS.expandModalOpen(json_res);
      });
      // $.get('lib/api.php', data, function(res){
      //   console.log(res);
      // });
    },
    expandModalOpen: function(report){
      console.log(report);
      $('.modal-header').text('보고일: '+ report.work_d);
      $('.modal-content').text(report.Report);
      $('.overlay').show();
    },
    expandModalClose: function(){
      $('.overlay').hide();
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
		// $('#datepicker').datepicker( "setDate" , RS.getToday() );
		
    var dateFormat = "yy-mm-dd",
      from = $( "#from" )
        .datepicker({
					showOtherMonths: true,
          defaultDate: "0",
					changeMonth: true,
					changeYear: true,
    			dateFormat:"yy-mm-dd",      
          //numberOfMonths: 3
        })
        .on( "change", function() {
          to.datepicker( "option", "minDate", getDate( this ) );
				})
			from.datepicker( "setDate" , -6);

      to = $( "#to" ).datepicker({
				showOtherMonths: true,
        defaultDate: "0",
				changeMonth: true,
				changeYear: true,
    		dateFormat:"yy-mm-dd",      
        //numberOfMonths: 3
      })
      .on( "change", function() {
        from.datepicker( "option", "maxDate", getDate( this ) );
			});

			to.datepicker( "setDate" , RS.getToday() );
 
    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      } 
      return date;
    }
    //init
    RS.search();
  });
</script>
</body>
</html>