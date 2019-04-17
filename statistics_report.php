<?php
  include "lib/default.php";
  include "lib/db.php";
  if(SessionCheck() == false) {
    echo "<script>
            alert('세션이 만료되었습니다. 로그인 페이지로 이동합니다.');
            parent.location.href='index.php';
          </script>";
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
  <script src="https://code.highcharts.com/highcharts.src.js"></script>
  <script src="js/report.js"></script>
</head>
<body>
<div class="C2Scontent">
	<h1 class="location">관리메뉴 > 프로젝트별 통계</h1>   
  <div class="boardtype2" style="width:auto; max-width:1500px;">
    <div class="work-time-area">      
      <table style="width:800px;">
        <colgroup width="16%"></colgroup>
        <colgroup width="16%"></colgroup>
        <colgroup width="16%"></colgroup>
        <colgroup width="16%"></colgroup>
        <colgroup width="16%"></colgroup>
        <colgroup width="16%"></colgroup>        
        <thead>
          <tr>
            <th>지난달 근무시간</th>
            <td id="last-month-work-h"></td>
            <th>지난달 보고일 수</th>
            <td id="last-month-report-d"></td>
            <th>지난달 평균</th>
            <td id="last-month-avg"></td>
          </tr>
          <tr>
            <th>이번달 근무시간</th>
            <td id="current-month-work-h"></td>
            <th>이번달 보고일 수</th>
            <td id="current-month-report-d"></td>
            <th>이번달 평균</th>
            <td id="current-month-avg"></td>
          </tr>
        </thead>
      </table>
      <div style="padding:5px;">평균 = 근무시간 / (보고일 - 주말)</div>
    </div>
    <div class="chart-area mt-20" style="width:800px;">
      <div id="pie1"></div>
      <div id="chart1"></div>
      <div id="chart2"></div>
    </div>
  </div>
</div>
<script type="text/javascript">
  var SR = {
    chart1: Highcharts.chart('chart1', {
      title: {
          text: '월별 근무시간'
      },

      subtitle: {
          text: ''
      },

      xAxis: {
          
          type: 'datetime',
          dateTimeLabelFormats: { // don't display the dummy year
              week: '%m',
              day: '%m',
              month: '%m월',
              year: '%b'
          },
          title: {
              text: '날짜'
          },
          tickInterval: 1000*60*60*24*30
      },

      yAxis: {
          title: {
              text: '근무시간'
          }
      },
      legend: {
          layout: 'vertical',
          align: 'right',
          verticalAlign: 'middle'
      },

      plotOptions: {
          series: {
              label: {
                  connectorAllowed: false
              },
              //pointStart: 2010
          }
      },

      tooltip: {
          headerFormat: '<b>{series.name}</b><br>',
          pointFormat: '{point.y:.2f}'
      },

      series: [{
          showInLegend: false,
          name: '월별 근무 시간',
          data: []
      }],

      responsive: {
          rules: [{
              condition: {
                  maxWidth: 500
              },
              chartOptions: {
                  legend: {
                      layout: 'horizontal',
                      align: 'center',
                      verticalAlign: 'bottom'
                  }
              }
          }]
      }
    }),
    chart2: Highcharts.chart('chart2', {
      chart: {
          type: 'spline'
      },
      title: {
          text: '최근 일별 근무시간(보고서 작성일 기준)'
      },
      subtitle: {
          text: ''
      },
      xAxis: {
          type: 'datetime',
          dateTimeLabelFormats: { // don't display the dummy year
              week: '%m/%e',
              day: '%m/%e',
              month: '%m월%e일',
              year: '%b'
          },
          title: {
              text: '날짜'
          }
      },
      yAxis: {
          title: {
              text: '시간'
          },
          min: 0
      },
      tooltip: {
          headerFormat: '<b>{series.name}</b><br>',
          pointFormat: '{point.x:%b %e}: {point.y:.2f}'
      },

      plotOptions: {
          spline: {
              marker: {
                  enabled: true
              }
          }
      },

      colors: ['#6CF', '#39F', '#06C', '#036', '#000'],

      // Define the data points. All series have a dummy year
      // of 1970/71 in order to be compared on the same x axis. Note
      // that in JavaScript, months start at 0 for January, 1 for February etc.
      series: [{
          showInLegend: false,
          name: "최근 업무 시간",
          data: []
      }]
    }),
    pie1: Highcharts.chart('pie1', {
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie'
      },
      title: {
          text: '업무 별 비율(최근 한달)'
      },
      tooltip: {
          pointFormat: '{series.name}: <b>{point.y:.2f} 시간</b>'
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              dataLabels: {
                  enabled: true,
                  format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                  style: {
                      color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                  }
              }
          }
      },
      series: [{
          name: '업무 별 비율',
          colorByPoint: true,
          data: []
      }]
    })
  }

  $(function(){
    parent.fncResizeHeight(document);
    var today = new Date();
  
    Report.get('getSumWorkHourFromMonth', {year: today.getFullYear(), month: today.getMonth()}).then(function(r1){
      Report.get('getWorkDayCount', {year: today.getFullYear(), month: today.getMonth()}).then(function(r2){
        Report.get('getWorkDayCountFromWeekend', {year: today.getFullYear(), month: today.getMonth()}).then(function(r3){
          $('#last-month-work-h').text(r1.sum_work_h);
          $('#last-month-report-d').text(r2. count_work_d);
          $('#last-month-avg').text(Number(r1.sum_work_h/(r2.count_work_d-r3.count_work_d)).toFixed(2));
        });
      });
    });
    Report.get('getSumWorkHourFromMonth', {year: today.getFullYear(), month: today.getMonth()+1}).then(function(r1){
      Report.get('getWorkDayCount', {year: today.getFullYear(), month: today.getMonth()+1}).then(function(r2){
        Report.get('getWorkDayCountFromWeekend', {year: today.getFullYear(), month: today.getMonth()+1}).then(function(r3){
          $('#current-month-work-h').text(r1.sum_work_h);
          $('#current-month-report-d').text(r2. count_work_d);
          $('#current-month-avg').text(Number(r1.sum_work_h/(r2.count_work_d-r3.count_work_d)).toFixed(2));          
        });
      });
    });

    Report.get('getSumWorkHourFromRecentMonth', {limit: 12}).then(function(r1){
      var data_arr = [];

      for(var i in r1){        
        data_arr.push([Date.parse(r1[i].year+'-'+r1[i].month), Number(r1[i].sum_work_h)]);
      }
      SR.chart1.series[0].update({data: data_arr});
    });

    Report.get('getWorkHourPerDay', {limit: 14}).then(function(r1){      
      var data_arr = [];

      for(var i in r1){        
        data_arr.push([Date.parse(r1[i].work_d), Number(r1[i].work_h)]);
      }

      SR.chart2.series[0].update({data: data_arr});
    });

    Report.get('getWorkHourPerProject').then(function(r1){      
      var data_arr = [];
      for(var i in r1){        
        data_arr.push({name:r1[i].ProjectName, y: Number(r1[i].sum_work_h)});
      }
      SR.pie1.series[0].update({data: data_arr});
    });
    $('.highcharts-credits').remove();
  });
</script>
</body>
</html>