<?php 
  include 'lib/db.php';
  $data = $_POST;
  $link = DBConnect();
  $memberIdx = $_SESSION['report_login_userIdx'];    
  
  $sql = "INSERT INTO eco_report_new (memberidx, work_d, work_h, projectidx, content) values ";

  /*
  싱클쿼터('), 더블쿼터(") 사용을 위해 addslashes를 적용할 필요가 있음
  row가 여러개니 벌크로 insert 한다
  위의 이유로 sprint로 sql을 만들어서 쿼리한다
  */
  $str_format = "(%d, '%s', %f, %d, '%s'), ";
  foreach($data as $row){    
    $sql .= sprintf($str_format,$memberIdx, $row[work_date], $row[work_hour], $row[project_id], @addslashes($row[content]));
  }
  $sql = substr($sql, 0, strlen($sql) - 2);  
  $result = mysqli_query($link, $sql);
  
  echo $result;
  mysqli_close($link);
?>