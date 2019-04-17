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
			<div class="tabcontents etc" id="tabcontents2">
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
      html.push('<tr>');
      html.push('<td>');
      html.push(row.ProjectIdx);
      html.push('</td>');
      html.push('<td>');
      html.push(row.ProjectName);
      html.push('</td>');
      html.push('<td>');
      // if(row.is_enabled){
      //   html.push('<label for="radio-'+num+'-1">활성</label>')
      //   html.push('<input type="radio" name="radio-'+num+'" id="radio-'+num+'-1" value="1" checked>');
      //   html.push('<label for="radio-'+num+'-2">비활성</label>')
      //   html.push('<input type="radio" name="radio-'+num+'" id="radio-'+num+'-2"  value="0" />');    
      // }
      html.push('<fieldset>\
                   <legend>Select a Location: </legend>\
                   <label for="radio-'+row.ProjectIdx+'-1">활성</label>\
                   <input type="radio" name="radio-'+row.ProjectIdx+'-1" id="radio-'+row.ProjectIdx+'-1" \
                   value="1" data-proejctIdx="'+row.ProjectIdx+'" \
                   onclick="PM.projectStatusChange('+row.ProjectIdx+', 1)" '+(row.is_enabled==1?"checked":'')+'>\
                   <label for="radio-'+row.ProjectIdx+'-2">비활성</label>\
                   <input type="radio" name="radio-'+row.ProjectIdx+'-1" id="radio-'+row.ProjectIdx+'-2" \
                   value="0" data-proejctIdx="'+row.ProjectIdx+'" \
                   onclick="PM.projectStatusChange('+row.ProjectIdx+', 0)" '+(row.is_enabled==0?"checked":"")+'>\
                 </fieldset>');
      html.push('</td>');
      html.push('<td>');
      //html.push('<a href="#" class="button red">삭제</a>');
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