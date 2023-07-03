<?php 
  require_once 'config.php';
  require_once 'export-all-queries-var.php';
  //echo "<pre>";
  // echo print_r($_SESSION, TRUE);
  // echo print_r($_COOKIE, TRUE);
  // echo "</pre>";
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <title>Report <?php echo $dfQueryStr_config['report']; ?> HTML Table</title>
    <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<link rel="stylesheet" href="export.css">
<script type="text/javascript" id="tinymce-js" src="https://crm.jaybranding.com/assets/plugins/tinymce/tinymce.min.js?v=2.9.3"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.3.0/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
window.addEventListener('load', function () {
    document.querySelector("div.container-fluid.main").style.display = "block";
    document.querySelector("div.loader").style.display = "none";
});

</script>
</head>

<body>
<?php
        // $pageTitle = '';
        // $notice = '';
        $reportCode = $dfQueryStr_config['report'];
        $yearRow = "From Year: <a href='/jayreport/?report={$reportCode}&year=2023'>2023</a> | <a href='/jayreport/?report={$reportCode}&year=2022'>2022</a> | <a href='/jayreport/?report={$reportCode}&year=2021'>2021</a> | <a href='/jayreport/?report={$reportCode}&year=2020'>2020</a>";

        # Fetch records
        $reportData = [
          "re01" => [
            "sql" => $re01SQLstr,
            "salesReport" => 0,
            "notice" => "Re01 - <b class='text-danger'>[Estimates]</b> - Các report liên quan đến Estimates - Báo Giá " . $yearRow
          ],
          "re01b" => [
            "sql" => $re01bSQLstr,
            "salesReport" => 0,
            "notice" => "Re01b - Các estimate cần được xem xét trong thời gian 1-4 tuần để thu tiền, <b>kế toán (chính)</b>> cần nắm được tiến độ chứ không comment chung chung"
          ],
          "re01c" => [
            "sql" => $re01cSQLstr,
            "salesReport" => 0,
            "notice" => "Re01c - Các estimate cần có sự phối hợp giữa sales hoặc <b>production (chính)</b> để hoàn thành dự án"
          ],
          "re01d" => [
            "sql" => $re01dSQLstr,
            "salesReport" => 0,
            "notice" => "Re01d - Các estimate cần có sự phối hợp giữa <b>sales (chính)</b> để chuyển sang thu tiền"
          ],
          "re02" => [
            "sql" => $re02SQLstr,
            "salesReport" => 0,
            "notice" => "Re02 - <b class='text-danger'>[Expenses]</b>- Các báo cáo report liên quan đến chi phí expenses"
          ],
          "re02b" => [
            "sql" => $re02bSQLstr,
            "salesReport" => 0,
            "notice" => "Re02b - Chi phí expenses cần duyệt bởi Giám Đốc"
          ],
          "re02c" => [
            "sql" => $re02cSQLstr,
            "salesReport" => 1,
            "notice" => "Re02c - Chi phí expenses cần được xem qua có trạng thái Trình duyệt Kế Toán"
          ],
          "re02d" => [
            "sql" => $re02dSQLstr,
            "salesReport" => 1,
            "notice" => "Re02d - Chi phí expenses nhóm theo Supplier code"
          ],
          "re03" => [
            "sql" => $re03SQLstr,
            "salesReport" => 1,
            "notice" => "Re03 - <b class='text-danger'>[Projects]</b> - Báo cáo Projects Dự án có thống kê % doanh thu chi phí"
          ],
          "re03b" => [
            "sql" => $re03bSQLstr,
            "salesReport" => 1,
            "notice" => "Re03b - <b class='text-danger'>[Projects]</b> - Báo cáo chi tiết theo project ID"
          ],
          "re03c" => [
            "sql" => $re03cSQLstr,
            "salesReport" => 1,
            "notice" => "Re03c - <b class='text-danger'>[Projects]</b> - Báo cáo chi tiết theo Sales AM"
          ],
          "re04" => [
            "sql" => $re03SQLstr,
            "salesReport" => 1,
            "notice" => "Re04 - <b class='text-danger'>[Domain]</b> - Theo dõi ngày hết hạn Domains - <a href='https://crm.jaybranding.com/admin/knowledge_base/view/huong-dan-cap-nhat-report-04-re04'>Hướng dẫn cập nhật report</a> AM"
          ],
          "re05" => [
            "sql" => $re03SQLstr,
            "salesReport" => 0,
            "notice" => "Re05 - <b class='text-danger'>[Domain]</b> - Theo dõi ngày hết hạn Domains - <a href='https://crm.jaybranding.com/admin/knowledge_base/view/huong-dan-cap-nhat-report-04-re04'>Hướng dẫn cập nhật report</a>"
          ],
          "welcome" => [
            "sql" => "SELECT * FROM admin_crm.tbltickets_predefined_replies",
            "notice" => "Re05 - <b class='text-danger'>[Domain]</b> - Theo dõi ngày hết hạn Domains - <a href='https://crm.jaybranding.com/admin/knowledge_base/view/huong-dan-cap-nhat-report-04-re04'>Hướng dẫn cập nhật report</a>",
            "pageTitle" => "Vui lòng chọn report trong thanh bên dưới:"
          ],
        ];

        if($reportCode == "detailProjectID") {
          $pID = $dfQueryStr_config['projectID'];
          $sql = $reportDetailProjectID;

          $result = mysqli_query($link_sqli, $sql, MYSQLI_USE_RESULT);  

          $simpleTable = $result->fetch_all(MYSQLI_ASSOC);

          //print_r (array_keys($simpleTable));

          echo "<table>";
          echo "<tr><td>Total: </td><td>". $simpleTable[0]["Project Total"] . "</td></tr>";
          echo "<tr><td>Budget: </td><td> ".$simpleTable[0]["Budget"] . "</td></tr>";
          echo "<tr><td>Percent: </td><td><b> ".  $simpleTable[0]["Percent"] . "</b></td></tr>";
          echo "<tr><td>Project Total (w/ Tax): </td><td> ".$simpleTable[0]["Project Total (w/ Tax)"] . "</td></tr>";
          echo "<tr><td>Budget (w/ Tax): </td><td> ".$simpleTable[0]["Budget (w/ Tax)"] . "</td></tr>";
          echo "<tr><td>Percent Paid: </td><td>".$simpleTable[0]["Percent Paid"]. "</td></tr>";
          echo "<tr><td>Percent (w/ Tax): </td><td>".$simpleTable[0]["Percent (w/ Tax)"]. "</td></tr>";
          echo "</table>";
          echo "<a href='/jayreport/?report=re03b&pid={$pID}'>Detail..</a>";

          exit;     
        }

        if (isset($reportData[$reportCode])) {
          $report = $reportData[$reportCode];
          $sql = $report['sql'] ?? '';
          $salesReport = $report['salesReport'] ?? '';
          $notice = $report['notice'] ?? '';
          $pageTitle = $report['pageTitle'] ?? '';
        } else {
          echo "ACCESS DENNIED";
          exit;
        }

        // if ($reportCode == "re01b"){$sql = $re01bSQLstr ; $salesReport=0; $notice="Re01b - Các estimate cần được xem xét trong thời gian 1-4 tuần để thu tiền, <b>kế toán (chính)</b>> cần nắm được tiến độ chứ không comment chung chung"; } 
        // else if($reportCode == "re01"){$sql = $re01SQLstr ; $salesReport=0; $notice="Re01 - <b class='text-danger'>[Estimates]</b> - Các report liên quan đến Estimates - Báo Giá " . $yearRow; }
        // else if($reportCode == "re01c"){$sql = $re01cSQLstr ; $salesReport=0; $notice="Re01c - Các estimate cần có sự phối hợp giữa sales hoặc <b>production (chính)</b> để hoàn thành dự án";}
        // else if($reportCode == "re01d"){$sql = $re01dSQLstr ; $salesReport=0; $notice="Re01d - Các estimate cần có sự phối hợp giữa <b>sales (chính)</b> để chuyển sang thu tiền";}
        // else if($reportCode == "re02"){$sql = $re02SQLstr ; $salesReport=0; $notice="Re02 - <b class='text-danger'>[Expenses]</b>- Các báo cáo report liên quan đến chi phí expenses";}
        // else if($reportCode == "re02b"){$sql = $re02bSQLstr ; $salesReport=0; $notice="Re02b - Chi phí expenses cần duyệt bởi Giám Đốc";}
        // else if($reportCode == "re02c"){$sql = $re02cSQLstr ; $salesReport=1; $notice="Re02c - Chi phí expenses cần được xem qua có trạng thái Trình duyệt Kế Toán";}
        // else if($reportCode == "re02d"){$sql = $re02dSQLstr ; $salesReport=1; $notice="Re02d - Chi phí expenses nhóm theo Supplier code";}
        // else if($reportCode == "re03"){$sql = $re03SQLstr ; $salesReport=1; $notice="Re03 - <b class='text-danger'>[Projects]</b> - Báo cáo Projects Dự án có thống kê % doanh thu chi phí";}
        // else if($reportCode == "re03b"){$sql = $re03bSQLstr ; $salesReport=1; $notice="Re03b - <b class='text-danger'>[Projects]</b> - Báo cáo chi tiết theo project ID";}
        // else if($reportCode == "re03c"){$sql = $re03cSQLstr ; $salesReport=1; $notice="Re03c - <b class='text-danger'>[Projects]</b> - Báo cáo chi tiết theo Sales AM";}
        // else if($reportCode == "re04"){$sql = $re03SQLstr ;$salesReport=1;  $notice="Re04 - <b class='text-danger'>[Domain]</b> - Theo dõi ngày hết hạn Domains - <a href='https://crm.jaybranding.com/admin/knowledge_base/view/huong-dan-cap-nhat-report-04-re04'>Hướng dẫn cập nhật report</a>";}
        // else if($reportCode == "re05"){$sql = $re03SQLstr ; $salesReport=0; $notice="Re05 - <b class='text-danger'>[Domain]</b> - Theo dõi ngày hết hạn Domains - <a href='https://crm.jaybranding.com/admin/knowledge_base/view/huong-dan-cap-nhat-report-04-re04'>Hướng dẫn cập nhật report</a>";}

        // else if($reportCode == "welcome") {
        //   $sql = "SELECT * FROM admin_crm.tbltickets_predefined_replies" ; //replies database
        //   $notice="Để xem report trên excel <a target='_blank' href='https://1drv.ms/x/s!Akod4fV2Lc8Clj9gamM8uDa-_BrJ?e=HfIpQH'>Report Doanh Thu Công Nợ</a>, tốt nhất nên mở file excel có sẵn bằng phần mềm excel (ko phải trình duyệt) và Reresh là được, không cần download nhiều lần."; 
        //   $pageTitle ="Vui lòng chọn report trong thanh bên dưới:";
        
        // } else if($reportCode == "detailProjectID"){
        //     $pID = $dfQueryStr_config['projectID'];
        //     $sql = $reportDetailProjectID;

        //     $result = mysqli_query($link_sqli, $sql, MYSQLI_USE_RESULT);  

        //     $simpleTable = $result->fetch_all(MYSQLI_ASSOC);

        //     //print_r (array_keys($simpleTable));

        //     echo "<table>";
        //     echo "<tr><td>Total: </td><td>". $simpleTable[0]["Project Total"] . "</td></tr>";
        //     echo "<tr><td>Budget: </td><td> ".$simpleTable[0]["Budget"] . "</td></tr>";
        //     echo "<tr><td>Percent: </td><td><b> ".  $simpleTable[0]["Percent"] . "</b></td></tr>";
        //     echo "<tr><td>Project Total (w/ Tax): </td><td> ".$simpleTable[0]["Project Total (w/ Tax)"] . "</td></tr>";
        //     echo "<tr><td>Budget (w/ Tax): </td><td> ".$simpleTable[0]["Budget (w/ Tax)"] . "</td></tr>";
        //     echo "<tr><td>Percent Paid: </td><td>".$simpleTable[0]["Percent Paid"]. "</td></tr>";
        //     echo "<tr><td>Percent (w/ Tax): </td><td>".$simpleTable[0]["Percent (w/ Tax)"]. "</td></tr>";
        //     echo "</table>";
        //     echo "<a href='/jayreport/?report=re03b&pid={$pID}'>Detail..</a>";

        //     exit;
            
        // } else {
        //     echo "ACCESS DENNIED";
        //     exit;
        // }

        require_once 'export-header.php'; 

?>

<div class="loader"></div>
<div class="container-fluid main">

<?php outputHeaderHTML($pageTitle, $notice); ?>

<?php 
//dashboard info
    if($reportCode == "welcome"){
        if($_GET["mail"] == "sent"){
            displayNotice("<span class='text-success'>Email Sent</span>","📤 Email sent successfully!");
        } 

        //input $sql as replies json
        $result = mysqli_query($link_sqli, $sql, MYSQLI_USE_RESULT);
        $jsonfile = $result->fetch_all(MYSQLI_ASSOC);
        file_put_contents('replies.json', json_encode($jsonfile));


        //summary table
        $sql = $reportDashboardFullYearTable;
        $result = mysqli_query($link_sqli, $sql, MYSQLI_USE_RESULT);  
        exportHTMLtable($result); 

        //payment 4 weeks
        $sql = $reportDashboardPayment4weeks;
        $result = mysqli_query($link_sqli, $sql, MYSQLI_USE_RESULT);  
        //exportHTMLtable($result); 
        convertObject($result,"03");

        //output reportdashboard1 table ym
        $sql = $reportDashboardThisMonth;
        $result = mysqli_query($link_sqli, $sql, MYSQLI_USE_RESULT);  
        //exportHTMLtable($result); 
        convertObject($result,"01");

        $sql = $reportDashboardFullYear;
        $result = mysqli_query($link_sqli, $sql, MYSQLI_USE_RESULT);    
        convertObject($result,"02");
        exit;
    } else

    if($reportCode == "re04"){
        echo '<div class="table-responsive">';
        echo '<table class="table table-striped table-sm table-hover " id="myTable" name="'.$reportCode.'">';
    
        $f = fopen("csv/domains.csv", "r");
        $j = 0;
        while (($line = fgetcsv($f)) !== false) {
                $i=0;

                if ($j==0) echo '<thead class="thead-light">';
                echo "<tr>";
                foreach ($line as $cell) {
                    if ( (($i==3) || ($i==4)) && ($j!=0) ){

                        echo "<td>". date('Y-m-d', strtotime(htmlspecialchars($cell)))."</td>";

                    }else{

                        echo "<td>". htmlspecialchars($cell) .  "</td>";
                    }
                        $i++;
                }
                echo "</tr>\n";
                if ($j==0) echo '</thead>';

                $j++;
        }
        fclose($f);

        echo '<tr class="lastRow nofilter table-primary"><td><td></tr>';
    
        echo "\n</table></div>";
        
        allJS();

   
    
      } else

      if($reportCode == "re05"){
         
        echo '<div class="table-responsive">';
        echo '<table class="table table-striped table-sm table-hover " id="myTable" name="'.$reportCode.'">';
    
        $f = fopen("csv/bank.csv", "r");
        $j = 0;
        while (($line = fgetcsv($f)) !== false) {
                $i=0;

                if ($j==4) echo '<thead class="thead-light">';
                if ($j >= 4) echo "<tr>";


                if ($j >= 4){


                  foreach ($line as $cell) {

                  
                    if ($i === 8){

                      $cellContent =   "";

                    } else if (($i >=4) && ($i <= 7)){

                      if (is_numeric($cell)){

                        $cellContent =  number_format($cell);
                      } else{
                        $cellContent =   htmlspecialchars($cell);
                      }
                     
                    }
                    else{
                      $cellContent =   htmlspecialchars($cell);
                    }
                      echo "<td>". $cellContent .  "</td>";

                  
                      $i++;
              }


                }
               
                if ($j >= 4) echo "<tr>";
                if ($j==4) echo '</thead>';

                $j++;
        }
        fclose($f);

        echo '<tfoot><tr class="lastRow nofilter table-primary"><td><td></tr></tfoot>';
    
        echo "\n</table></div>";
        
        allJS();

    } else {
    // $result = $link_sqli->query($sql);
    $result = mysqli_query($link_sqli, $sql, MYSQLI_USE_RESULT);  
    exportHTMLtable($result); 
    }
?>

<?php 

if ($dfQueryStr_config['mail'] == "sent"){
  echo '<div class="alert alert-success" role="alert">Mail Sent</div>';
}
?>


<!-- Modal -->

<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
    <form id="email-from" method="post" action="/jayreport/export-email.php">

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Send This Table to An Email</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div class="form-group">
            <label for="exampleFormControlInput1">Name:</label>
            <input required name="myfullname" type="text" class="form-control" id="name" value="<?php echo $_COOKIE['myFullName']?>" disabled>
        </div>
        <div class="form-group">
            <label for="exampleFormControlInput1">My Email (CC):</label>
            <input required name="myemail" type="email" class="form-control" id="myemail" placeholder="<?php echo $_COOKIE['myFullName']?>" value="<?php echo $_COOKIE['myEmail']?>" disabled>
        </div>
        <div class="form-group">
            <label for="exampleFormControlInput1">Email address:</label>
            <input required name="email" type="email" class="form-control" id="email" >
        </div>
        <div class="form-group">
            <label for="exampleFormControlInput1">Subject:</label>
            <input required name="subject" type="text" class="form-control" id="subject" value="Approval for form: <?php echo strip_tags($notice); ?> ">
        </div>
        <div class="form-group">
            <label for="exampleFormControlTextarea1">Message:</label>
            <textarea required name="message" class="form-control" id="message" rows="3" name="mailMessage" placeholder="Vd. Gửi anh Trình xem Duyệt"></textarea>
            <input type="hidden" value="" name="tablefield" id="tablefield" />
            <input type="hidden" value="<?= $_GET["report"]?>" name="report" id="form-report"/>
        </div>

<script>

//Xử lý cho email

function emailProcess(){


    document.querySelector("#myTable").setAttribute("border", "1");

    document.querySelector("#myTable > thead").style.background = "grey";

    var trvar = document.querySelectorAll("#myTable tr");
    for (var i = 0; i < trvar.length ; i++) {
        if(trvar[i].classList.contains("hide")){
            trvar[i].style.display = "none";
        };
    };

    document.querySelector("#tablefield").value = document.querySelector("#myTable").outerHTML;


}







</script>
       

      </div>
      <div class="modal-footer">
      <div class="form-group">
            <button type="submit" name="email-submit" class="btn btn-primary" id="btn-email-submit">Send Email</button>
        </div>
      </div>
    </form>

    </div>
  </div>
</div>


<!-- modal của bulk edit -->


<div class="modal fade" id="bulkModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Bulk Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <span>Khi chọn highlight, các id sẽ tự đưa vào đây</span>
      <?php
        bulkedit();
    ?>

      </div>
      <div class="modal-footer">
      
      </div>

    </div>
  </div>
</div>


<script>
    function clickEstViewPercent(element,id){
    
        $.ajax({
        url: "https://crm.jaybranding.com/jayreport/?report=detailProjectID&pid="+id,
      }).done(function (data) {
        // data what is sent back by the php page
        
        $(element).after(data); // display data
        $(element).attr('disabled', 'disabled');
      });

    }    
      

    $(document).ready(function () {
        $("#myTable a").attr("target","_blank");

        // sales right cannot see commission
        $("#myTable.sales-level-report > tbody > tr.show:contains('Chi phí BDM CTY')").attr("class", "hide");
        $("#myTable.sales-level-report > tbody > tr.show:contains('Chi phí khấu hao')").attr("class", "hide");
        $("#myTable.sales-level-report > tbody > tr.show:contains('Chi phí quản lý')").attr("class", "hide");
    }); 
</script>
</div>
</body>
</html>