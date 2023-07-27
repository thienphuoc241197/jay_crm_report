<?php
require_once 'config.php';
require_once 'export-all-queries-var.php';

if (!isset($_POST['expno'])) {
    echo '<pre>';
    echo 'This function isn\'t accessed properly!';
    echo '</pre>';
    exit();
}

$expono = str_replace(" ", "", $_POST["expno"]);
$statusTo = $_POST["statusTo"] ?? '';
$fieldId = $_POST["fieldid"];

if ($_POST['formName'] == 'form1' || $_POST['formName'] == 'form2') {
    $expno_arr = explode(",", $expono); 
    //print_r($expno_arr);
    
    $SQLstring = '';
    foreach ($expno_arr as $value) {
    
        if ($fieldId == "19"){
            $statusTo = "Ok - Trinh Nguyen - " . date("Y/m/d");
            $SQLstring = $SQLstring . 'INSERT INTO tblcustomfieldsvalues SET fieldto = "expenses", value = "'.addslashes($statusTo).'", fieldid = '.$fieldId.', relid = '.$value.';';
        }else {
            $SQLstring = $SQLstring . 'UPDATE tblcustomfieldsvalues SET value = "'.addslashes($statusTo).'" WHERE (fieldid = '.$fieldId.' AND relid = '.$value.');';
    
        }
      }
      
      //printf($SQLstring);
      echo "<button>Copy text to clipboard</button><br>";
      echo "<textarea rows=10 cols=50>$SQLstring</textarea>";
    
    //   if(mysqli_query($mysqli, $SQLstring)){  
    //     echo "Record updated successfully";  
    // }else{  
    //     echo "Could not update record: $SQLstring2". mysqli_error($mysqli);  
    // }  
} elseif ($_POST['formName'] == 'form3') {
    $SQLstring = "SELECT tblexpenses.id, tblexpenses.expense_name, tcfb.value as 'budget', tblexpenses.amount as 'amount'
    FROM tblexpenses LEFT JOIN tblcustomfieldsvalues AS tcfb ON tblexpenses.id = tcfb.relid AND tcfb.fieldid = $fieldId
    WHERE tblexpenses.id IN ($expono)
    GROUP BY tblexpenses.id
    ORDER BY tblexpenses.id DESC";
    $res = $link_sqli -> query($SQLstring);
    $res = $res -> fetch_all(MYSQLI_ASSOC);

    $confirmTableForm = '<form action="sql_process/updateSQLProcess.php" method="POST">
    <p><em>Vui lòng kiểm tra so sánh giữa <del class="text-danger font-weight-bold">text đỏ</del> (giá trị cũ) và giá trị mới ở cột amount</em></p>
    <table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">Expense ID</th>
        <th scope="col">Tên Expense</th>
        <th scope="col">Budget</th>
        <th scope="col">Amount</th>
      </tr>
    </thead>
    <tbody>';
    $SQLUpdateString = '';
    foreach ($res as $value) {
        $confirmTableForm .= '<tr>
                                <th scope="row"><a href="https://crm.jaybranding.com/admin/expenses/expense/' . $value['id'] . '" target="_blank">' . $value['id'] . '</a></th>
                                <td>' . $value['expense_name'] . '</td>
                                <td>' . number_format($value['budget']) . '</td>
                                <td>
                                <p><del class="text-danger font-weight-bold">' . number_format($value['amount']) . '</del></p>
                                <p>' . number_format($value['budget']) . '</p>
                                </td>
                                </tr>';
        $SQLUpdateString .= "UPDATE tblexpenses SET tblexpenses.amount = " . $value['budget'] . " WHERE tblexpenses.id = " . $value['id'] . ";";
    }
      $confirmTableForm .= '</tbody>
      </table>
      <div class="row">
        <input type="hidden" name="confirm" value="yes">
        <input type="hidden" name="expno" value="' . $expono . '">
        <input type="hidden" name="sqlStr" value="' . $SQLUpdateString . '">
        <input class="btn btn-primary ml-auto" type="submit" name="budget-to-amount" value="Xác nhận">
      </div>
      </form>';
} elseif ($_POST['formName'] == 'form4') {
    $date = DateTime::createFromFormat('Y-m-d', $_POST['apd']);
    $format_date = $date->format('d/m/Y');

    $SQLstring = "SELECT tblexpenses.id, tblexpenses.expense_name
    FROM tblexpenses
    WHERE tblexpenses.id IN ($expono)
    GROUP BY tblexpenses.id
    ORDER BY tblexpenses.id DESC";
    $res = $link_sqli -> query($SQLstring);
    $res = $res -> fetch_all(MYSQLI_ASSOC);

    $confirmTableForm = '<form action="sql_process/insertSQLProcess.php" method="POST">
    <p><em>Vui lòng kiểm tra lại giá trị <strong class="text-danger font-weight-bold">Actual Paid Date</strong> sau khi cập nhật</em></p>
    <table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">Expense ID</th>
        <th scope="col">Tên Expense</th>
        <th scope="col">Actual Paid Date</th>
      </tr>
    </thead>
    <tbody>';
    $SQLInsertString = 'INSERT INTO tblcustomfieldsvalues (relid, fieldid, fieldto, value) VALUES ';
    foreach ($res as $value) {
        $confirmTableForm .= '<tr>
                                <th scope="row"><a href="https://crm.jaybranding.com/admin/expenses/expense/' . $value['id'] . '" target="_blank">' . $value['id'] . '</a></th>
                                <td>' . $value['expense_name'] . '</td>
                                <td>
                                <p class="text-danger font-weight-bold">' . $format_date . '</p>
                                </td>
                                </tr>';
    $SQLInsertString .= "(" . $value['id'] . ", " . $fieldId . ", 'expenses', '" . $_POST['apd'] . "'),";
    }
    $SQLInsertString = substr($SQLInsertString, 0, -1);
      $confirmTableForm .= '</tbody>
      </table>
      <div class="row">
        <input type="hidden" name="confirm" value="yes">
        <input type="hidden" name="expno" value="' . $expono . '">
        <input type="hidden" name="sqlStr" value="' . $SQLInsertString . '">
        <input class="btn btn-primary ml-auto" type="submit" name="apd-update" value="Xác nhận">
      </div>
      </form>';
}


mysqli_close($link_sqli);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Edit - JAY CRM Export</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container p-4">
        <?php echo isset($confirmTableForm) ? $confirmTableForm : ''; ?>
    </div>
<script>
    document.querySelector("button").onclick = function(){
    document.querySelector("textarea").select();
    document.execCommand('copy');
    alert("Copied")
}
</script>
</body>
</html>
