<?php 
function outputHeaderHTML($pageTitle, $notice){ 
  global $dfQueryStr_config;
echo "<h2 style='padding: 15px; text-align:center'>{$pageTitle}</h2>"; 
echo "<p class='text-center'>{$notice}</p>" ;
?>
<div id="viewPercent"></div>

<div class="offcanvas offcanvas-start" id="demo">
  <!--div class="offcanvas-header">
    <h1 class="offcanvas-title">Menu</h1>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div-->
  <div class="offcanvas-body">

    <div class="container-fluid mt-2 mb-2 row justify-content-center align-items-center align-baseline main-nav">
      <a href="?report=welcome" class="btn btn-sm btn-outline-primary <?= $_SESSION['myRoleSales'];?>">🏡</a>
      <a href="?report=re01" class="btn btn-sm btn-outline-primary <?= $_SESSION['myRoleSales'];?>">🔽Re01.</a>
      <a href="?report=re01b&day=7" class="btn btn-sm btn-outline-primary <?= $_SESSION['myRoleSales'];?>">🔽Re01b7d.</a>
      <a href="?report=re01b&day=14" class="btn btn-sm btn-outline-primary <?= $_SESSION['myRoleSales'];?>">🔽Re01b14d.</a>
      <a href="?report=re01b&day=21" class="btn btn-sm btn-outline-primary <?= $_SESSION['myRoleSales'];?>">🔽Re01b21d.</a>
      <a href="?report=re01c" class="btn btn-sm btn-outline-primary <?= $_SESSION['myRoleSales'];?>">🔽Re01c.</a>
      <a href="?report=re01d" class="btn btn-sm btn-outline-primary <?= $_SESSION['myRoleSales'];?>">🔽Re01d.</a>
      <a href="?report=re02" class="btn btn-sm btn-outline-primary <?= $_SESSION['myRoleSales'];?>">🔺Re02.</a>
      <a href="?report=re02b" class="btn btn-sm btn-outline-primary <?= $_SESSION['myRoleSales'];?>">🔺Re02b.</a>
      <a href="?report=re02c" class="btn btn-sm btn-outline-primary <?= $_SESSION['myRoleSales'];?>">🔺Re02c.</a>
      <a href="?report=re02d" class="btn btn-sm btn-outline-primary <?= $_SESSION['myRoleSales'];?>">🔺Re02d.</a>
      <a href="?report=re03" class="btn btn-sm btn-outline-primary <?= $_SESSION['myRoleSales'];?>">⚒Re03.</a>

      <a href="?report=re04" class="btn btn-sm btn-outline-primary <?= $_SESSION['myRoleSales'];?>">🌎Re04.</a>
      <a href="?report=re05" class="btn btn-sm btn-outline-primary <?= $_SESSION['myRoleSales'];?>">🏧Re05.</a>
    </div>
  </div>
</div>



<div class="container-fluid mt-2 mb-2" id="searchBox">

<button id="searchBoxbtn">+</button>

<div class="form-group row justify-content-center align-items-center align-baseline searchRow" id="searchBoxContent">
    <div class="col-md-4 col-sm-auto" style="min-height: 100px">
    <div class="input-group">
    <div class="input-group-prepend">
      <div class="input-group-text" id="btnGroupAddon">.<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
  <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
</svg></div>
    </div>
    <input type="text" id="myInput" class="form-control" placeholder="Vd: important! | 2022-12 | 902 | tuetamads ..." aria-label="tim kiem vi du 905; 2022-11.." aria-describedby="btnGroupAddon"   title="Vd: important! | 2022-12 | 902 | tuetamads ..."   value="<?php echo $dfQueryStr_config['search']; ?>" onkeyup="searchFunctionbt()">
    
  </div>
  <div class="row">
    <button onClick="textSearch('date');" class="btn btn-link btn-sm">This Month</button>
    <button onClick="textSearch('2022');" class="btn btn-link btn-sm">This Year</button>
    <button onClick="textSearch('important!');" class="btn btn-link btn-sm">important!</button>

    <button id="Countlastrow" class="btn btn-link btn-sm" onClick="countLastRow()">
    
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calculator" viewBox="0 0 16 16">
    <path d="M12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h8zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4z"/>
    <path d="M4 2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-2zm0 4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-4z"/>
    </svg>
    Sum
    </button>
  </div>

    </div>

    <div class="col-sm-auto py-2" style="min-height: 100px">
    <p class="text-center">Version <strong class="text-danger">1.01</strong></p>
    </div>

    <div class=" col-sm-auto" style="min-height: 100px">
    


        <div class=" my-2 d-flex justify-content-around btn-row">
        <div class="row">

    <button  onClick="searchHiglightFunction();"  class="btn btn-primary  btn-sm mx-2" type="button"  data-toggle="modal" data-target="#bulkModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
            </svg>
            Bulk Edit
    </button>

    <button id="viewAllNote" class="btn btn-info  btn-sm mx-2">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-expand" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8ZM7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2ZM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10Z"/>
    </svg>    
    Expand
    </button>

    <button onClick="searchHiglightFunction();" class="btn btn-warning  btn-sm mx-2">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lightbulb" viewBox="0 0 16 16">
    <path d="M2 6a6 6 0 1 1 10.174 4.31c-.203.196-.359.4-.453.619l-.762 1.769A.5.5 0 0 1 10.5 13a.5.5 0 0 1 0 1 .5.5 0 0 1 0 1l-.224.447a1 1 0 0 1-.894.553H6.618a1 1 0 0 1-.894-.553L5.5 15a.5.5 0 0 1 0-1 .5.5 0 0 1 0-1 .5.5 0 0 1-.46-.302l-.761-1.77a1.964 1.964 0 0 0-.453-.618A5.984 5.984 0 0 1 2 6zm6-5a5 5 0 0 0-3.479 8.592c.263.254.514.564.676.941L5.83 12h4.342l.632-1.467c.162-.377.413-.687.676-.941A5 5 0 0 0 8 1z"/>
    </svg>
        Highlight
    </button>

    <!-- Button trigger email modal -->
    <button onClick="emailProcess()" type="button" class="btn btn-primary" data-toggle="modal" data-target="#emailModal">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-at" viewBox="0 0 16 16">
    <path d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2H2Zm3.708 6.208L1 11.105V5.383l4.708 2.825ZM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2-7-4.2Z"/>
    <path d="M14.247 14.269c1.01 0 1.587-.857 1.587-2.025v-.21C15.834 10.43 14.64 9 12.52 9h-.035C10.42 9 9 10.36 9 12.432v.214C9 14.82 10.438 16 12.358 16h.044c.594 0 1.018-.074 1.237-.175v-.73c-.245.11-.673.18-1.18.18h-.044c-1.334 0-2.571-.788-2.571-2.655v-.157c0-1.657 1.058-2.724 2.64-2.724h.04c1.535 0 2.484 1.05 2.484 2.326v.118c0 .975-.324 1.39-.639 1.39-.232 0-.41-.148-.41-.42v-2.19h-.906v.569h-.03c-.084-.298-.368-.63-.954-.63-.778 0-1.259.555-1.259 1.4v.528c0 .892.49 1.434 1.26 1.434.471 0 .896-.227 1.014-.643h.043c.118.42.617.648 1.12.648Zm-2.453-1.588v-.227c0-.546.227-.791.573-.791.297 0 .572.192.572.708v.367c0 .573-.253.744-.564.744-.354 0-.581-.215-.581-.8Z"/>
    </svg>
    Email
    </button>

    <button id="Refresh" class="btn btn-outline-primary  btn-sm mx-2" onClick="window.location.reload('Refresh')">  
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
    </svg>
    Refresh
    </button>
        </div>
    </div>
    </div>
</div>
</div>

<script>
document.querySelector("#searchBoxbtn").onclick = function () {
  var x = document.getElementById("searchBoxContent");

  if (x.style.display == "" || x.style.display == "none") {
    x.style.display = "block";
    this.innerText = "+";
  } else {
    x.style.display = "none";
    this.innerText = "-";
  }
};

function redirectSelect() {
  var x = document.getElementById("selectReport").value;
  window.location.replace(
    "https://crm.jaybranding.com/jayreport/?report=" + x
  );
}
</script>


<?php 
} //end function output header html
?>

<?php 
//output html table
function exportHTMLtable($result) {
        global $reportCode;
        $columns = [];
        $resultset = [];

        # Set columns and results array
        while ($row = mysqli_fetch_assoc($result)) {
            if (empty($columns)) {
                $columns = array_keys($row);
            }
            $resultset[] = $row;
        }

        # If records not found
        if( !(count($resultset) > 0 )) {
            echo '<h4> Table with no data </h4>';
        } else {
    ?>

    <div class="table-responsive">
    <table class="table table-striped table-sm table-hover <?= $_SESSION['myRoleSales'] ?> <?=$_SESSION['myRole']?> <?= $reportCode?>" id="myTable" name="<?php echo $reportCode?>">
        <thead class="thead-light">
            <tr class="nofilter table-primary">
                <?php 
                $findNextAmountCol = 0;
                $countNextAmountCol = 0;
                $sumNextAmountCol = 0;
                foreach ($columns as $k => $column_name ) : ?>
                    <th> 
                    <?php 
                    echo $column_name;
                    if ($column_name == "Next Payment Amount"){
                        $findNextAmountCol = $k;
                    }
                    ?> 
                </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php
                // output data of each row
                //$last_key = end(array_keys($resultset));

                foreach($resultset as $index => $row) {
                $column_counter =0;
                $sumValue = 0;
                
            ?>
                <tr class='success show'>
                    <?php for ($i=0; $i < count($columns); $i++):?>
                        <td> 
                        <?php 
                 
                           
                            echo $row[$columns[$column_counter]];
                            $column_counter++;
                           

                        
                         ?>   </td>
                    <?php endfor;?>
                </tr>
            <?php } ?>
            <tfoot>
            <tr class="lastRow nofilter table-primary">
                <?php foreach ($columns as $k => $column_name ) : ?>
                    <td>  </td>
                <?php endforeach; ?>
            </tr>
            </tfoot>
        </tbody>
    </table>
</div>

<?php allJS();?>


<?php
        }
}// end output html table

?>

<?php //function bulk edit


function bulkEdit(){
  $xhtml = '';
  $xhtml .= '<h4>Edit Trạng Thái Expense</h4>';
  $xhtml .= '<div class="container">';

  $xhtml .= '<form action="export-bulk-update.php" class="form-group" method="POST" id="form1">
  <textarea required class="form-control expno" placeholder="Exp No. với dấu phẩy. ví dụ: 123,456,890" name="expno"></textarea>
  <select required data-fieldto="expenses" data-fieldid="21" name="statusTo" class="selectpicker form-control" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98"><option value="" disabled selected>Thay đổi thành trạng thái:</option><option value="Draft">Draft</option><option value="Done-C">Done-C</option><option value="Trình Duyệt Kế Toán">Trình Duyệt Kế Toán</option><option value="Trình Duyệt Giám Đốc">Trình Duyệt Giám Đốc</option><option value="Giám Đốc OK Chuyển Tiền">Giám Đốc OK Chuyển Tiền</option><option value="Đã Chuyển Khoản">Đã Chuyển Khoản</option></select>
  <!--input type="text" placeholder="key" class="form-control" name="MSTkey"/-->
  <input type="hidden" name="fieldid" value="21" />
  <input type="submit" class="btn btn-primary" value="form1" name="formName" form="form1"/>
  
  </form>';

  $xhtml .= '</div>';
  $xhtml .= '<hr>';
  $xhtml .= '<h4>Edit Giám Đốc Comment</h4>';
  $xhtml .= '<div class="container">';

  $xhtml .= '<form action="export-bulk-update.php" class="form-group" method="POST" id="form2">
  <textarea required class="form-control expno" placeholder="Exp No. với dấu phẩy. ví dụ: 123,456,890" name="expno"></textarea>
  <input type="hidden" name="fieldid" value="19" />
  <input type="submit" class="btn btn-primary" value="form2" name="formName" form="form2"/>
  
  </form>';

  $xhtml .= '</div>';
  $xhtml .= '<hr>';
  $xhtml .= '<h4>Edit Budget -> Amount Expense</h4>';
  $xhtml .= '<div class="container">';

  $xhtml .= '<form action="export-bulk-update.php" class="form-group" method="POST" id="form3">
  <textarea required class="form-control expno" placeholder="Exp No. với dấu phẩy. ví dụ: 123,456,890" name="expno"></textarea>
  <input type="hidden" name="fieldid" value="12" />
  <input type="submit" class="btn btn-primary" value="form3" name="formName" form="form3"/>';

  $xhtml .= '</div>';

  echo $xhtml;
}

//end function bulk edit
?>

<?php 
//output object
function convertObject($result,$chartid){


  $columns = array();
  $resultset = array();

  # Set columns and results array
  while ($row = mysqli_fetch_assoc($result)) {
      if (empty($columns)) {
          $columns = array_keys($row);
      }
      $resultset[] = $row;
  }


  # If records not found
  if( !(count($resultset > 0 ))) {
        exit;// if json not found
  } else {


    echo "<script>  localStorage.setItem('arrayData', '".json_encode($resultset)."');</script>";

                // output data of each row
                //$last_key = end(array_keys($resultset));

                foreach ($columns as $k => $column_name ) :

                  if ($k != 0){
                  $otherCols .=  "{ label: '".$column_name ."',";
                  $otherCols .= "data:[";
                  }
                

                  foreach ($resultset as $l => $row_value) :

                    if ($k == 0){

                      $firstCol .= "'". $row_value[$column_name]."',";

                    } else{

                      $otherCols .= $row_value[$column_name].",";
                    }


                  endforeach;
                  
                  if ($k != 0){
                  $otherCols .= "],";
                  $otherCols .= "borderWidth: 1,},";
                  }

                endforeach;

                $firstCol = "labels: [".$firstCol."]";
               // $otherCols = "datasets:[" .$otherCols. "]";

                $jsonChartData = <<<string
                {
                  type: 'bar',
                  data: {
                    $firstCol,
                    datasets: [
                      $otherCols
                    ],
                  },
                  options: {
                    indexAxis: 'y',
                    maintainAspectRatio: false,
                    title: {
                      display: true,
                      text: 'Chart.js Horizontal Bar Chart'
                    },
                    scales: {
                      y: {
                        beginAtZero: true,
                        ticks: {
                          display: false,
                        },
                      },
                      
                    },
                  },
                }
                string;
              
            

               $drawChart = <<<MYSTRING

                      <div class="chart-container" style="position: relative; height:100vh; width:100%">
                          <canvas id="myChart{$chartid}"></canvas>
                      </div>
                      <script>
                      var ctx = document.getElementById("myChart{$chartid}");
                      new Chart(ctx, {$jsonChartData});
                      </script>
              MYSTRING;
              $chartCount++;
              echo $drawChart;
                
             } 


  }

// end output object table

?>

<?php
//function notice modal

function displayNotice($title, $message){

  $string = <<<STRING
  <div class="modal modal-notice" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">$title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>$message</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>$('.modal-notice').modal('show')</script>
STRING;

echo $string;


}


?>

<?php
function allJS(){
  //function all JS
  
?>

<script>
  function searchFunctionbt() {
    var value = $('#myInput').val().toLowerCase();
      $("#myTable tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });

      //countLastRow();
  }


  //search then sum
  var counter = 0;



function searchFunction() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  if(input.value.length > 3){

    for (var i = 1; i < tr.length - 1; i++) {
      var tds = tr[i].getElementsByTagName("td");

      var flag = false;

      for (var j = 0; j < tds.length; j++) {
        var td = tds[j];

        if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
          flag = true;
        }
      }
      if (flag) {
        tr[i].className = "show";
      } else {
        tr[i].className = "hide";
      }
    }
  };
  //countLastRow();
}

searchFunction();

function countLastRow() {
  $("date").text()
  trtdLast = document.querySelectorAll("#myTable tr.lastRow td");
  tr = $("#myTable tbody tr:visible");

  sumCount = [];

  for (var x = 0; x < tr.length; x++) {
    var tds = tr[x].getElementsByTagName("td");

    for (var j = 0; j < tds.length; j++) {
      var td = tds[j];

      if (x == 0) {
        sumCount[j] = parseInt(td.innerText.replace(/,/g, ""));
      } else {
        sumCount[j] = sumCount[j] + parseInt(td.innerText.replace(/,/g, ""));
      }

      if (!isNaN(sumCount[j]) && sumCount[j] > 500000) {
        trtdLast[j].innerText = sumCount[j].toLocaleString();
      }
    }
  }

  trtdLast[0].innerText = tr.length + " rows";
  console.log(tr);

}

function searchHiglightFunction() {
  tr = document.querySelectorAll("#myTable tr");
  var toggleHighlight = false;

  if (document.querySelectorAll("#myTable tr.table-warning").length == 0) {
    alert("No Highlight Row, please highlight some rows!");
    return;
  }

  var commaNo = "";

  for (var i = 1; i < tr.length - 1; i++) {
    if (tr[i].classList.contains("table-warning")) {
      tr[i].className = "show";
      tr[i].classList.add("table-warning");
      commaNo = commaNo + tr[i].querySelector("td").innerText +",";

    } else {
      tr[i].className = "hide";
    }

  }
  commaNo = commaNo.slice(0, -1);
  trtdLast[1].innerText = commaNo;

  $("textarea.expno").text(commaNo);

  countLastRow();
}

function thisMonth() {
  var today = new Date();
  var todayYearMonth = today.getFullYear() + "-" + (today.getMonth() + 1);
  var myInput = document.querySelector("#myInput");

  if (myInput.value == todayYearMonth) {
    myInput.value = "";
  } else {
    myInput.value = todayYearMonth;
  }

  searchFunction();
}

function textSearch(text){
  var myInput = document.querySelector("#myInput");

  if (text=="date"){
    var today = new Date();
    if (today.getMonth() < 10){
      var todayYearMonth = today.getFullYear() + "-0" + (today.getMonth() + 1);
    }else{
      var todayYearMonth = today.getFullYear() + "-" + (today.getMonth() + 1);
    }
    text = todayYearMonth;
  }
  if (myInput.value == text) {
    myInput.value = "";
  } else {
    myInput.value = text;
  }
  searchFunction();

}




const urlParams = new URLSearchParams(window.location.search);
const reportParam = urlParams.get("report");

var optionSelect = document.querySelectorAll("#selectReport option");

for (var i = 0; i < optionSelect.length; i++) {
  if (optionSelect[i].value == reportParam) {
    optionSelect[i].setAttribute("selected", true);
  }
}

var btnPlus = document.querySelectorAll(".expand");

for (var i = 0; i < btnPlus.length; i++) {
  if (btnPlus[i].innerHTML == '<span class="note"></span>') {
    btnPlus[i].style.display = "none";
  }
}

function exportTableToExcel(
  tableID,
  filename = '<?php
  global $reportCode;
  echo "JAY-". $reportCode ."-". date('Ymdhis'); ?>'
) {
  var downloadLink;
  var dataType = "application/vnd.ms-excel";
  var tableSelect = document.getElementById(tableID);
  var tableHTML = tableSelect.outerHTML.replace(/ /g, "%20");

  // Specify file name
  filename = filename ? filename + ".xls" : "excel_data.xls";

  // Create download link element
  downloadLink = document.createElement("a");

  document.body.appendChild(downloadLink);

  if (navigator.msSaveOrOpenBlob) {
    var blob = new Blob(["\ufeff", tableHTML], {
      type: dataType,
    });
    navigator.msSaveOrOpenBlob(blob, filename);
  } else {
    // Create a link to the file
    downloadLink.href = "data:" + dataType + ", " + tableHTML;

    // Setting the file name
    downloadLink.download = filename;

    //triggering the function
    downloadLink.click();
  }
}



document.querySelector("#viewAllNote").onclick = function () {
  var x = document.querySelectorAll(".note");

  for (var i = 0; i < x.length; i++) {
    x[i].classList.toggle("expandall");
  }
};

var trvar = document.querySelectorAll("#myTable tr");
for (var i = 0; i < trvar.length; i++) {
  trvar[i].onclick = function () {
    this.classList.toggle("table-warning");
  };
}
</script>

<?php
} //end function all JS
?>