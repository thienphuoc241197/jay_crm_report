<?php 
session_start();
         //echo session_id();
        // print_r($_SESSION);

  

echo '<h4>Để cập nhật bảng, Nhờ webdev xuất ra file CSV và copy vào đây: </h4>';
echo '<a href="https://youtu.be/BljT98f4cyc">Xem video hướng dẫn</a>';
echo '<form name="savefile" method="post" action="export-csv-update.php">
        <textarea rows="16" cols="100" name="textdata" required></textarea><br/>
        <input type="text" name="mst" required></input><br/>

        <input type="submit" name="submitsave" value="Update CSV file" class="btn">
    </form>';

    if (isset($_POST['submitsave'])){

            if (($_POST['submitsave'] == "Update CSV file") && ($_POST['mst'] == "0314143143")){
                $file = fopen("csv/domain.csv" ,"a+");
                $text = $_POST["textdata"];
                file_put_contents("csv/domains.csv", $text);
                fclose($file);
                echo "<h2>Updated Successfully!</h2>";

            } else{
                $message = "ERR!";
                echo "<script type='text/javascript'>alert('$message');</script>";
            }

    }

    echo "<hr>";

    echo '<h4>Cập nhật bank: </h4>';
    echo '<form name="savefile" method="post" action="export-csv-update.php">
        <textarea rows="16" cols="100" name="textdata" required></textarea><br/>
        <input type="text" name="mst" required></input><br/>

        <input type="submit" name="submitbank" value="Update CSV bank" class="btn">
    </form>';

    if (isset($_POST['submitbank'])){

            if (($_POST['submitbank'] == "Update CSV bank") && ($_POST['mst'] == "03141431430314143143")){
                $file = fopen("csv/bank.csv" ,"a+");
                $text = $_POST["textdata"];
                file_put_contents("csv/bank.csv", $text);
                fclose($file);
                echo "<h2>Updated Successfully!</h2>";

            } else{
                $message = "ERR!";
                echo "<script type='text/javascript'>alert('$message');</script>";
            }

    }

?>