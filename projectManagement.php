<?php
  include "lib/db.php";
  include "lib/default.php";
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
  <script type="text/javascript" src="//image-glb.qpyou.cn/hiveone/shared_files/guide/js/ui.js"></script>
  <script src="js/report.js"></script>
</head>
<body>

<div class="C2Scontent">
	<h1 class="location">관리메뉴 > 프로젝트관리</h1>
	<div class="boardtype2">
		<div class="tabwrap">
			<div class="tabmenu tabmenu2">
				<a href="#tabcontents1" class="tab" data-tab="game">게임</a>
        <a href="#tabcontents2" class="tab" data-tab="etc">기타업무</a>        
			</div>
			<div class="tabcontents game" id="tabcontents1">
        <div class="create-area">
          <table>
            <thead>
              <tr>
                <th width="100px" scope="col">프로젝트 추가: </th>
                <td><input type="text" id="game_name"/></td>            
                <td width="50px" ><span><input type="button" class="button red" value="추가하기" onclick="PM.addGame()"></span></td>
              </tr>
            </thead>
          </table>
        </div>
        <div class="mt-20">
          <table>
            <thead>
              <tr>
                <th width="10%">번호</th>
                <th>프로젝트</th>
                <th width="25%">상태</th>
                <th width="20%">비고</th>              
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
			</div>
			<div class="tabcontents etc" id="tabcontents2" style="display:none;">
        <div class="create-area">
          <table>
            <thead>
              <tr>
                <th width="100px" scope="col">프로젝트 추가: </th>
                <td><input type="text" id="etc_name"/></td>            
                <td width="50px" ><span><input type="button" class="button red" value="추가하기" onclick="PM.addEtc()"></span></td>
              </tr>
            </thead>
          </table>
        </div>
        <div class="mt-20">
          <table>
            <thead>
              <tr>
                <th width="10%">번호</th>
                <th>프로젝트</th>
                <th width="25%">상태</th>
                <th width="20%">비고</th>              
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
    <div class="ui-icon ui-icon-closethick modal-close"></div>
    <div class="modal-header">      
    </div>
    <div class="modal-content">
    </div>
  </div>
</div>
<script type="text/javascript">
 var PM = {
  renderRow: function(rows, tab_name){
    var list = $('.tabcontents.'+tab_name+' tbody');
    var html = [];
    for(var num in rows){
      var row = rows[num];
      html.push('<tr data-project-id='+row.ProjectIdx+'>');
      html.push('<td>');
      html.push(row.ProjectIdx);
      html.push('</td>');
      html.push('<td>');
      html.push(row.ProjectName);
      html.push('</td>');
      html.push('<td>');
      html.push('<fieldset>\
                   <legend>Select a Location: </legend>\
                   <label for="radio-'+row.ProjectIdx+'-1" style="width:36px;">활성</label>\
                   <input type="radio" name="radio-'+row.ProjectIdx+'-1" id="radio-'+row.ProjectIdx+'-1" \
                   value="1" data-proejctIdx="'+row.ProjectIdx+'" \
                   onclick="PM.projectStatusChange('+row.ProjectIdx+', 1)" '+(row.is_enabled==1?"checked":'')+'>\
                   <label for="radio-'+row.ProjectIdx+'-2" style="width:36px;">비활성</label>\
                   <input type="radio" name="radio-'+row.ProjectIdx+'-1" id="radio-'+row.ProjectIdx+'-2" \
                   value="0" data-proejctIdx="'+row.ProjectIdx+'" \
                   onclick="PM.projectStatusChange('+row.ProjectIdx+', 0)" '+(row.is_enabled==0?"checked":"")+'>\
                 </fieldset>');
      html.push('</td>');
      html.push('<td>');
      html.push('<a href="#" class="button blue" onclick="PM.modifyForm('+row.ProjectIdx+')">수정</a> ');
      html.push('<a href="#" class="button red" onclick="PM.deleteProject('+row.ProjectIdx+')">삭제</a>');
      html.push('</td>');
      html.push('</tr>');            
    }
    list.html('');
    list.append(html.join(''));    
    parent.fncResizeHeight(document);

    //jquery ui radio active
    $( 'input[type="radio"]' ).checkboxradio({icon: false});
  },
  projectStatusChange: function(id, status){    
    data = {
      projectIdx: id,
      is_enabled: status
    }
    Report.get("setProjectStatusChange", data).then(function(res){
      if(res && res.result){
        alert('변경되었습니다.');
      }else{
        alert('변경실패 관리자에게 문의해주세요.');
      }
    });
  },
  addGame: function(type) {    
    var projectName = $('#game_name').val();
    
    var data = {
      isGame: 1,
      projectName: projectName
    }
    Report.get("createProject", data).then(function(res){     
      if(res && res.result){
        alert('생성되었습니다.');
      }else{
        alert('생성실패 관리자에게 문의해주세요.');
      }
    });
    $('[data-tab="game"]').click();
  },
  addEtc: function(type) {
    var projectName = $('#etc_name').val();
    
    var data = {
      isGame: 0,
      projectName: projectName
    }

    Report.get("createProject", data).then(function(res){
      if(res && res.result){
        alert('생성되었습니다.');
      }else{
        alert('생성실패 관리자에게 문의해주세요.');
      }
    });
    $('[data-tab="etc"]').click();
  },
  modifyForm: function(id) {
    var projectName =  $('tr[data-project-id='+id+'] td:eq(1)').text();
    $('tr[data-project-id='+id+'] td:eq(1)').html('<input type="text" class="input-style-01" value="'+projectName+'"/>');
    $('tr[data-project-id='+id+'] td:last').html('\
      <a href="#" class="button red" onclick="PM.modifyConfirm('+id+')">확인</a>\
      <a href="#" class="button blue" onclick="PM.resetForm('+id+')">취소</a>');
  },
  modifyConfirm: function(id) {
    var row = $('tr[data-project-id='+id+']');      
    var data = {
      projectIdx: id,
      projectName: $(row).find('td:eq(1) input').val()
    }
    Report.get("updateProject", data).then(function(res){
      if(res.result){
        PM.resetForm(id);
      }
    });
  },
  deleteProject: function(id) {
    Report.get("deleteProject", {projectIdx: id}).then(function(res){
      if(res.result===true){
        Report.alert('삭제되었습니다');
        $('tr[data-project-id='+id+']').remove();
      }else if(res.result==='has report') {
        Report.alert('해당 프로젝트로 작성된 보고서가 있습니다.\n\n관리자에게 문의 해 주세요');
      }else {
        Report.alert('삭제 실패. 관리자에게 문의 해 주세요');
      }
    });
  },
  resetForm: function(id) {
    var row = $('tr[data-project-id='+id+']');
    var data = {
      projectIdx: id
    }
    Report.get("getProjectFromId", data).then(function(res){
      $(row).find('td:eq(0)').text(Number(res.ProjectIdx));
      $(row).find('td:eq(1)').text(res.ProjectName);
      $(row).find('td:eq(2)').html('\
                  <fieldset>\
                    <legend>Select a Location: </legend>\
                    <label for="radio-'+id+'-1" style="width:36px;">활성</label>\
                    <input type="radio" name="radio-'+id+'-1" id="radio-'+id+'-1" \
                    value="1" data-proejctIdx="'+id+'" \
                    onclick="PM.projectStatusChange('+id+', 1)" '+(res.is_enabled==1?"checked":'')+'>\
                    <label for="radio-'+id+'-2" style="width:36px;">비활성</label>\
                    <input type="radio" name="radio-'+id+'-1" id="radio-'+id+'-2" \
                    value="0" data-proejctIdx="'+id+'" \
                    onclick="PM.projectStatusChange('+id+', 0)" '+(res.is_enabled==0?"checked":"")+'>\
                  </fieldset>');
      $(row).find('td:eq(3)').html('\
                  <a href="#" class="button blue" onclick="PM.modifyForm('+id+')">수정</a>\
                  <a href="#" class="button red" onclick="PM.deleteProject('+id+')">삭제</a>')
        //jquery ui radio active
      $( 'input[type="radio"]' ).checkboxradio({icon: false});
    });
  }
 }

 //init
 $(function(){

  $('.tabmenu2').tabMenu(0);  
  
	$('.tab').click(function(){
		var tab_name = $(this).attr("data-tab");

		switch(tab_name) {
			case 'game':
        Report.get("getGameProjects").then(function(res){          
          if(res==null){
            alert('프로젝트 목록이 없습니다.');
            return;
          }
          PM.renderRow(res, "game");
        });
				break;
			case 'etc':
        Report.get("getEtcProjects").then(function(res){
          if(res==null){
            alert('프로젝트 목록이 없습니다.');
            return;
          }
          PM.renderRow(res, "etc");
        });
				break;
			case 'platform':
				console.log('this is etc');
				break;
			case 'market':
				console.log('this is etc');
				break;
			default:
				console.log('no tab');
				break;
    }    
  });
  //init
  $('[data-tab="game"]').click();
 }); 
</script>
</body>
</html>