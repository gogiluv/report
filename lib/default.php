<?php
    //오늘 날짜
    function getToday() {
        return Date("Y-m-d");
    }
    
    //콘솔 출력용
    function clog($obj) {
        echo "<script>console.log('$obj');</script>";
    }
?>