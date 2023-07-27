<?php
    require_once '../config.php';
    require_once '../export-all-queries-var.php';

    if(!isset($_POST['confirm']) || $_POST['confirm'] != 'yes') {
        echo '<pre>';
        echo 'This function isn\'t accessed properly!';
        echo '</pre>';
        exit();
    }

    if(isset($_POST['apd-update'])) {
        $sql = $_POST['sqlStr'];
        $expono = $_POST['expno'];

        $res = $link_sqli->query($sql);
        if (!$res) {
            echo "Error: " . $link_sqli->error;
            exit();
        }

        // header('location: ../?report=re02&eid=' . $expono);
        echo "<em>Cập nhật thành công! Chuyển hướng trong 5 giây!</em>";
        header('refresh:5; url=../?report=re02&eid=' . $expono);
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
</body>
</html>
