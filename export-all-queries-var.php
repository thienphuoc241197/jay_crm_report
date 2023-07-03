<?php
    session_start();
    
    $_SESSION['myRoleSales'] = $_COOKIE['myRoleSales'] ?? '';

    $_SESSION['myRole'] = $_COOKIE['myRole'] ?? '';
    if($_SESSION['myRole'])  $_SESSION['myRoleSales'] = "";

    unset($_COOKIE['myRole']);
    unset($_COOKIE['myRoleSales']);
   
    // BEGIN: Establish a connection to the database
    // INSTRUCTION: Fill in your connection specific four elements of the $config array in the config.php file.

    // Server hostname or IP address
    $server_hostname = $db_config['db_host'];

    // The name of your MySQL database instance
    $database_name = $db_config['db_name'];

    // The username of your database login credential 
    $username = $db_config['db_user'];

    // The password of your database login credential
    $password = $db_config['db_pass'];

    $link_sqli = null;
    if (
        ($dfQueryStr_config['key'] == 'excel-import') || ( $_SESSION['myRole'] == 'admin-level-report') ||
        (
            in_array($dfQueryStr_config['report'], ['re02c', 're04', 'detailProjectID', 're03', 're03b']) && ($_SESSION['myRoleSales'] == 'sales-level-report')
        )
    ){
        $link_sqli = mysqli_connect($server_hostname, $username, $password, $database_name);
    }

    // If an error occurred while connecting to the database, display the error code and exit.
    if (!$link_sqli) {
        echo "<pre>";
        echo "Error: ACCESS DENIED - PLEASE LOGIN." . PHP_EOL;
        echo "Debugging error #: " . mysqli_connect_errno() . PHP_EOL;
        echo "Error description: " . mysqli_connect_error() . PHP_EOL;
        echo "</pre>";
        exit;
    }
    // END: Establish a connection to the database

    $link_sqli->set_charset("utf8");

    $yearStr = $dfQueryStr_config['year'];
    $dayInt = $dfQueryStr_config['dayInterval'];
    $projectID = $dfQueryStr_config['projectID'];
    $userID = $dfQueryStr_config['userID'];
    $expenseID = $dfQueryStr_config['expenseID'];

    //all queries

    $re03bSQLstr = <<<SQLSTRING
    SELECT tp.id AS `Project ID`,
    tp.name AS `Project Name`,
    tex.id AS `Expense ID`,
    tex.expense_name AS `Expense Name`,
    FORMAT(tex.amount, 0) AS `Amount`,
    FORMAT(tcfb.value, 0) AS `Budget VND`,
    FORMAT(tcfb.value + tcfb.value * COALESCE(tax8p.taxrate, 0) * 0.01 + tcfb.value * COALESCE(tax10p.taxrate, 0) * 0.01 + tcfb.value * COALESCE(taxpit.taxrate, 0) * 0.01,0) as `Budget (w/ Tax)`,
    '' AS `Estimate No.`,
    '' AS `Project Total`,
    '' AS `Project Total (w/ Tax)`,
    '' AS `Actual Revenue`,
    '' AS `Percent Budget`,
    '' AS `Percent Budget (w/ Tax)`,
    '' AS `Percent Paid`
    FROM tblprojects AS tp
        LEFT JOIN tblexpenses AS tex ON tp.id = tex.project_id
        LEFT JOIN tblcustomfieldsvalues AS tcfb ON tex.id = tcfb.relid AND tcfb.fieldid = '12'
        LEFT JOIN tbltaxes AS tax10p ON tex.tax = tax10p.id AND tax10p.id = 1
        LEFT JOIN tbltaxes AS tax8p ON tex.tax = tax8p.id AND tax8p.id = 2
        LEFT JOIN tbltaxes AS taxpit ON tex.tax = taxpit.id AND taxpit.id = 3
    WHERE tp.id = {$projectID}

    UNION

    SELECT tp.id AS `Project ID`,
    tp.name AS `Project Name`,
    '' AS `Expense ID`,
    '' AS `Expense Name`,
    '' AS `Amount`,
    '' AS `Budget VND`,
    '' as `Budget w/ Tax`,
    tes.number AS `Estimate No.`,
    FORMAT(tes.subtotal, 0) AS `Project Total`,
    FORMAT(tes.total, 0) AS `Project Total (w/ Tax)`,
    FORMAT(ti.subtotal, 0) AS `Actual Revenue`,
    '' AS `Percent Budget`,
    '' AS `Percent Budget (w/ Tax)`,
    '' AS `Percent Paid`
    FROM tblprojects AS tp
        LEFT JOIN tblestimates AS tes ON tp.id = tes.project_id
        LEFT JOIN tblinvoices AS ti ON tes.invoiceid = ti.id
    WHERE tp.id = {$projectID}

    UNION

    SELECT tp.id AS `Project ID`,
    tp.name AS `Project Name`,
    '' AS `Expense ID`,
    '' AS `Expense Name`,
    FORMAT(SUM(tex.amount),0) as `Amount`,
    FORMAT(SUM(tcfb.value), 0) AS `Budget VND`,
    format(SUM(tcfb.value) + SUM(tcfb.value) * COALESCE(tax8p.taxrate, 0) * 0.01 + SUM(tcfb.value) * COALESCE(tax10p.taxrate, 0) * 0.01 + SUM(tcfb.value) * COALESCE(taxpit.taxrate, 0) * 0.01,0) as `Budget (w/ Tax)`,
    '' AS `Estimate No.`,
    format(newtab.project_subtotal,0) AS `Project Total`,
    format(newtab.project_total,0) AS `Project Total (w/ Tax)`,
    format(newtab.actual_revenue,0) AS `Actual Revenue`,
    CONCAT(FORMAT(((newtab.project_subtotal - SUM(tcfb.value))/newtab.project_subtotal)*100,2),'%') AS `Percent Budget`,
    CONCAT(FORMAT(((newtab.project_total - (SUM(tcfb.value) + SUM(tcfb.value) * COALESCE(tax8p.taxrate, 0) * 0.01 + SUM(tcfb.value) * COALESCE(tax10p.taxrate, 0) * 0.01 + SUM(tcfb.value) * COALESCE(taxpit.taxrate, 0) * 0.01))/newtab.project_total)*100,2),'%') AS `Percent Budget (w/ Tax)`,
    CONCAT(FORMAT((SUM(tex.amount)/newtab.project_total)*100,2),'%') AS `Percent Paid`
    FROM tblprojects AS tp
        LEFT JOIN tblexpenses AS tex ON tp.id = tex.project_id
        LEFT JOIN tblcustomfieldsvalues as tcfb on tex.id = tcfb.relid AND tcfb.fieldid = '12'
        left join (
        SELECT tp.id, SUM(tes.subtotal) AS project_subtotal, SUM(tes.total) AS project_total, SUM(ti.subtotal) AS actual_revenue
            FROM tblprojects AS tp 
                INNER JOIN tblestimates AS tes ON tp.id = tes.project_id
                INNER JOIN tblinvoices AS ti ON tes.invoiceid = ti.id
            GROUP BY tp.id
        ) AS newtab ON newtab.id = tex.project_id
        left join tbltaxes AS tax10p ON tex.tax = tax10p.id AND tax10p.id = 1
        left join tbltaxes AS tax8p ON tex.tax = tax8p.id AND tax8p.id = 2
        left join tbltaxes AS taxpit ON tex.tax = taxpit.id AND taxpit.id = 3
    WHERE tp.id = {$projectID}
    GROUP BY tp.id

    SQLSTRING;


    $re03cSQLstr = <<<SQLSTRING
    SELECT CONCAT('<a href=https://crm.jaybranding.com/jayreport/?report=re03b&pid=',checkSync.project_id,'>',checkSync.project_id,'</a>') AS `Project ID`,
    checkSync.project_name AS `Project Name`,
    checkSync.estimate_number AS `Estimate No.`,
    ts.firstname AS `Sale Agent`

    FROM tblstaff AS ts
        INNER JOIN (
            SELECT tp.id AS project_id,
            tp.name AS project_name,
            GROUP_CONCAT(tes.number) AS estimate_number,
            GROUP_CONCAT(DISTINCT ts.staffid) AS staff_id
            FROM tblprojects AS tp
                INNER JOIN tblestimates AS tes ON tp.id = tes.project_id
                LEFT JOIN tblstaff AS ts ON ts.staffid = tes.sale_agent
            GROUP BY tp.id
            HAVING (COUNT(DISTINCT ts.firstname) = 1)
        ) AS checkSync ON checkSync.staff_id = ts.staffid AND ts.staffid = {$userID}
    ORDER BY checkSync.project_id DESC

    SQLSTRING;



    $re03SQLstr = <<<SQLSTRING
    /*Percentage plan - commission */
    select 

    CONCAT('<a href=https://crm.jaybranding.com/jayreport/?report=re03b&pid=',tblexpenses.project_id,'>',tblexpenses.project_id,'</a>') As `Project ID`,
    tp.name,
    format(newtab.project_subtotal,0) as `Total`,
    format(newtab.project_total,0) as `Total w/ Tax`,
    format(newtab.actual_revenue,0) as `Actual Revenue`,
    format(SUM(tcfb.value),0) as `Budget VND`,
    format(SUM(tblexpenses.amount),0) as `Amount`,
    format(SUM(tcfb.value) + SUM(tcfb.value) * COALESCE(tax8p.taxrate, 0) * 0.01 + SUM(tcfb.value) * COALESCE(tax10p.taxrate, 0) * 0.01 + SUM(tcfb.value) * COALESCE(taxpit.taxrate, 0) * 0.01,0) as `Budget w/ Tax`,
    CONCAT(FORMAT(((newtab.project_subtotal - SUM(tcfb.value))/newtab.project_subtotal)*100,2),'%') as `Percent Budget`,
    CONCAT(FORMAT(((newtab.project_total - (SUM(tcfb.value) + SUM(tcfb.value) * COALESCE(tax8p.taxrate, 0) * 0.01 + SUM(tcfb.value) * COALESCE(tax10p.taxrate, 0) * 0.01 + SUM(tcfb.value) * COALESCE(taxpit.taxrate, 0) * 0.01))/newtab.project_total)*100,2),'%') as `Percent Budget Tax`,
    -- CONCAT(FORMAT(((newtab.project_total - SUM(tblexpenses.amount))/newtab.project_total)*100,2),'%') as `Percent Paid`
    CONCAT(FORMAT((SUM(tblexpenses.amount)/newtab.project_total)*100,2),'%') as `Percent Paid`

    from tblexpenses 
    left join tblprojects as tp on tblexpenses.project_id = tp.id
    left join tblcustomfieldsvalues as tcfb on tblexpenses.id = tcfb.relid AND tcfb.fieldid = '12'
    left join (
        SELECT tp.id, SUM(tes.subtotal) AS project_subtotal, SUM(tes.total) AS project_total, SUM(ti.subtotal) AS actual_revenue
            FROM tblprojects AS tp 
                INNER JOIN tblestimates AS tes ON tp.id = tes.project_id
                INNER JOIN tblinvoices AS ti ON tes.invoiceid = ti.id
            GROUP BY tp.id
    ) AS newtab ON newtab.id = tblexpenses.project_id
    left join tbltaxes AS tax10p ON tblexpenses.tax = tax10p.id AND tax10p.id = 1
    left join tbltaxes AS tax8p ON tblexpenses.tax = tax8p.id AND tax8p.id = 2
    left join tbltaxes AS taxpit ON tblexpenses.tax = taxpit.id AND taxpit.id = 3

    where tblexpenses.project_id != 0 AND tblexpenses.date > '{$yearStr}-01-01'
    group by tblexpenses.project_id
    order by tblexpenses.project_id DESC

    SQLSTRING;

    // Condition Re02
    $re02Condition = '';
    if ($expenseID) {
        $re02Condition = " AND tblexpenses.id IN({$expenseID})";
    }

    $re02SQLstr = <<<SQLSTRING
    /*Expense Detail Report */
    select
    CONCAT('<a href=https://crm.jaybranding.com/admin/expenses/expense/',tblexpenses.id,'>',tblexpenses.id,'</a>') As `Exp No`,
    tblexpenses.date AS `test_date`,
    tblexpenses.expense_name as `Expense Name`, 
    tec.name as `Expense Category`,
    concat('<status class="exp ',replace(tcf21.value,' ',''),'">',tcf21.value,'</status>') as `Expense Status`,
    tcfe.value AS 'Supplier Code',
    tcf15.value AS 'Bank No',
    tcf17.value AS 'Name',
    tcf16.value AS 'At Bank',
    tcf5.value AS 'MST',
    -- Concat('<div oootabindex="0" class="expand"><span class="note">Supplier Code: <b>',tcfe.value ,'</b><br> Bank No: ',tcf15.value,' <br> Name: ',tcf17.value,' <br> At Bank: ',tcf16.value,' <br> MST: ',tcf5.value,'</span></div>') as `Bank Info`,
    tes.number as `Estimate No.`,
    format(SUM(tes.total),0) as `Project Total (VAT)`,
    tblexpenses.project_id,
    tcfc.relid as `Customer ID`,
    -- tp.name as `Project Name`,
    CONCAT('<a href=https://crm.jaybranding.com/admin/projects/view/',tblexpenses.project_id,'>',tp.name,'</a>') As `Project Name`,
    CONCAT(tcf25.value, ' %') AS `Expense Paid Percent`,
    format(tcfb.value,0) as `Budget VND`,
    format(tblexpenses.amount,0) as `Amount`,
    tcf18.value as `Budget Payout Date`,
    format(tcfb.value * COALESCE(tax8p.taxrate, 0) * 0.01,0) as 'VAT (8%)',
    format(tcfb.value * COALESCE(tax10p.taxrate, 0) * 0.01,0) as 'VAT (10%)',
    format(tcfb.value * COALESCE(taxpit.taxrate, 0) * 0.01,0) as 'PIT',
    format(tcfb.value + tcfb.value * COALESCE(tax8p.taxrate, 0) * 0.01 + tcfb.value * COALESCE(tax10p.taxrate, 0) * 0.01,0) as `Total Budget w/ Tax`, /* Budget + VAT */
    format(tcfb.value + tcfb.value * COALESCE(taxpit.taxrate, 0) * 0.01,0) as `Total Budget w/ PIT`, /* Budget - PIT */
    if(tblexpenses.amount = 0, 0, format(tblexpenses.amount + tcfb.value * COALESCE(tax8p.taxrate, 0) * 0.01 + tcfb.value * COALESCE(tax10p.taxrate, 0) * 0.01 + tcfb.value * COALESCE(taxpit.taxrate, 0) * 0.01,0)) as `Paid VND`,
    tcf24.value AS `Actual Paid Date`,
    format(IF(tblexpenses.amount = 0, tcfb.value, tblexpenses.amount), 0) AS `Adjusted Planned Amount`,
    format(IF(tblexpenses.amount = 0, tcfb.value, tblexpenses.amount) - tblexpenses.amount,0) as `Remain VND`,
    if(isnull(tcf18.value), 'Error', if(isnull(tcf24.value), if(datediff(curdate(), STR_TO_DATE(tcf18.value, '%Y-%m-%d')) <= 0, 0, datediff(curdate(), STR_TO_DATE(tcf18.value, '%Y-%m-%d'))), 0)) AS `Aging`,
    tblstaff.firstname as `NV Sales`
    -- CONCAT(if(isnull(tblexpenses.expense_name), ' Lack Expense Name | ', ''), if(isnull(tblexpenses.project_id), ' Lack Project ID | ', ''), if(isnull(tcfe.value), ' Lack Supplier Code | ', ''), if(isnull(tcf15.value), ' Lack Bank No | ', ''), if(isnull(tcf17.value), ' Lack Name | ', ''), if(isnull(tcf16.value), ' Lack At Bank | ', ''), if(isnull(tcf5.value), ' Lack MST | ', '')) as `Input Check`
    
    from tblexpenses
    left join tblexpenses_categories as tec on tec.id = tblexpenses.category
    left join tblprojects as tp on tblexpenses.project_id = tp.id
    left join tblcustomfieldsvalues as tcfe on tblexpenses.id = tcfe.relid AND tcfe.fieldid = '13'
    left join tblcustomfieldsvalues as tcfb on tblexpenses.id = tcfb.relid AND tcfb.fieldid = '12'
    left join tblcustomfieldsvalues as tcfc on tcfe.value = tcfc.value AND tcfc.fieldid = '14'
    left join tblcustomfieldsvalues as tcf18 on tcf18.relid = tblexpenses.id AND tcf18.fieldid = '18' /*Budget Payout Date */
    left join tblcustomfieldsvalues as tcf17 on tcf17.relid = tcfc.relid AND tcf17.fieldid = '17' /*ten chu tai khoan */
    left join tblcustomfieldsvalues as tcf16 on tcf16.relid = tcfc.relid AND tcf16.fieldid = '16' /*ten ngan hang */
    left join tblcustomfieldsvalues as tcf15 on tcf15.relid = tcfc.relid AND tcf15.fieldid = '15' /*so tai khoan */
    left join tblcustomfieldsvalues as tcf5 on tcf5.relid = tcfc.relid AND tcf5.fieldid = '5' /*MST */
    left join tblcustomfieldsvalues as tcf21 on tblexpenses.id = tcf21.relid AND tcf21.fieldid = '21' /*Status */
    left join tblcustomfieldsvalues as tcf24 on tblexpenses.id = tcf24.relid AND tcf24.fieldid = '24' /*Actual Paid Date */
    left join tblcustomfieldsvalues as tcf25 on tblexpenses.id = tcf25.relid AND tcf25.fieldid = '25' /* Expense Paid Percent */
    
    left join tblestimates as tes on tblexpenses.project_id = tes.project_id AND tblexpenses.project_id != '0'
    left join tblstaff ON tes.sale_agent = tblstaff.staffid
    left join tbltaxes AS tax10p ON tblexpenses.tax = tax10p.id AND tax10p.id = 1
    left join tbltaxes AS tax8p ON tblexpenses.tax = tax8p.id AND tax8p.id = 2
    left join tbltaxes AS taxpit ON tblexpenses.tax = taxpit.id AND taxpit.id = 3
    
    where  tblexpenses.date > '{$yearStr}-01-01' {$re02Condition}
    group by tblexpenses.id
    order by tblexpenses.date desc
    SQLSTRING;
    
    $re02bSQLstr = <<<SQLSTRING
    
    select
    CONCAT('<a href=https://crm.jaybranding.com/admin/expenses/expense/',tblexpenses.id,'>',tblexpenses.id,'</a>') As `Exp No`,
    tblexpenses.date,
    ifnull(tcfe.value,'') AS 'Supplier Code',
    ifnull(tcf15.value,'') AS 'Bank No',
    ifnull(tcf17.value,'') AS 'Name',
    ifnull(tcf16.value,'') AS 'At Bank',
    ifnull(tcf5.value,'') AS 'MST',
    -- Concat('<div tabindex="0" class="expand"><span class="note">Supplier Code: <b>',ifnull(tcfe.value,'') ,'</b><br> Bank No: ',ifnull(tcf15.value,''),' <br> Name: ',ifnull(tcf17.value,''),' <br> At Bank: ',ifnull(tcf16.value,''),' <br> MST: ',ifnull(tcf5.value,''),'</span></div>') as `Bank Info`,
    Concat('<div tabindex="0" class="expand"><span class="note">',replace(tblexpenses.note,'important!','<b class="text-danger">important!</b>') ,'</span></div>') as `Admin Comment`,
    Concat('<div tabindex="0" class="expand"><span class="note">',tcf20.value ,'</span></div>') as `KT Comment`,
    Concat('<div tabindex="0" class="expand"><span class="note">',tcf19.value ,'</span></div>') as `GD Comment`,
    tblexpenses.expense_name as `Expense Name`, 
    tec.name as `Expense Category`,
    concat('<status class="exp ',replace(tcf21.value,' ',''),'">',tcf21.value,'</status>') as `Expense Status`,
    concat('<button class="btn btn-outline-secondary btn-sm" onClick="clickEstViewPercent(this,',tp.id,')">',tp.id,'</button>') as `Project ID`,
    
    format(tes.total,0) as `Estimate Total (VAT)`,
    -- tp.name as `Project Name`,
    CONCAT('<a href=https://crm.jaybranding.com/admin/projects/view/',tblexpenses.project_id,'>',tp.name,'</a>') As `Project Name`,
    CONCAT(tcf25.value, ' %') AS `Expense Paid Percent`,
    format(tcfb.value,0) as `Budget VND`,
    tcf18.value as `Budget Payout Date`,
    format(tcfb.value * COALESCE(tax8p.taxrate, 0) * 0.01,0) as 'VAT (8%)',
    format(tcfb.value * COALESCE(tax10p.taxrate, 0) * 0.01,0) as 'VAT (10%)',
    format(tcfb.value * COALESCE(taxpit.taxrate, 0) * 0.01,0) as 'PIT',
    format(tcfb.value + tcfb.value * COALESCE(tax8p.taxrate, 0) * 0.01 + tcfb.value * COALESCE(tax10p.taxrate, 0) * 0.01,0) as `Total Budget w/ Tax`, /* Budget + VAT */
    format(tcfb.value + tcfb.value * COALESCE(taxpit.taxrate, 0) * 0.01,0) as `Total Budget w/ PIT`, /* Budget - PIT */
    -- format(tblexpenses.amount*IF(tblexpenses.tax=2, 1.08, 1),0) as `Paid VND`,
    if(tblexpenses.amount = 0, 0, format(tblexpenses.amount + tcfb.value * COALESCE(tax8p.taxrate, 0) * 0.01 + tcfb.value * COALESCE(tax10p.taxrate, 0) * 0.01 + tcfb.value * COALESCE(taxpit.taxrate, 0) * 0.01,0)) as `Paid VND`,
    tcf24.value AS `Actual Paid Date`,
    format(IF(tblexpenses.amount = 0, tcfb.value, tblexpenses.amount), 0) AS `Adjusted Planned Amount`,
    format(IF(tblexpenses.amount = 0, tcfb.value, tblexpenses.amount) - tblexpenses.amount,0) as `Remain VND`,
    -- format(tcfb.value - tblexpenses.amount*IF(tblexpenses.tax=2, 1.08, 1),0) as `Remain VND`,
    if(isnull(tcf18.value), 'Error', if(isnull(tcf24.value), if(datediff(curdate(), STR_TO_DATE(tcf18.value, '%Y-%m-%d')) <= 0, 0, datediff(curdate(), STR_TO_DATE(tcf18.value, '%Y-%m-%d'))), 0)) AS `Aging`,
    tblstaff.firstname as `NV Sales`
    
    from tblexpenses 
    left join tblexpenses_categories as tec on tec.id = tblexpenses.category
    left join tblprojects as tp on tblexpenses.project_id = tp.id
    left join tblcustomfieldsvalues as tcfe on tblexpenses.id = tcfe.relid AND tcfe.fieldid = '13'
    left join tblcustomfieldsvalues as tcfb on tblexpenses.id = tcfb.relid AND tcfb.fieldid = '12'
    left join tblcustomfieldsvalues as tcfc on tcfe.value = tcfc.value AND tcfc.fieldid = '14'
    left join tblcustomfieldsvalues as tcf18 on tcf18.relid = tblexpenses.id AND tcf18.fieldid = '18' /*Budget Payout Date */
    left join tblcustomfieldsvalues as tcf17 on tcf17.relid = tcfc.relid AND tcf17.fieldid = '17' /*ten chu tai khoan */
    left join tblcustomfieldsvalues as tcf16 on tcf16.relid = tcfc.relid AND tcf16.fieldid = '16' /*ten ngan hang */
    left join tblcustomfieldsvalues as tcf15 on tcf15.relid = tcfc.relid AND tcf15.fieldid = '15' /*so tai khoan */
    left join tblcustomfieldsvalues as tcf5 on tcf5.relid = tcfc.relid AND tcf5.fieldid = '5' /*MST */
    left join tblcustomfieldsvalues as tcf21 on tcf21.relid = tblexpenses.id AND tcf21.fieldid = '21' /*Tình Trạng */
    left join tblcustomfieldsvalues as tcf20 on tcf20.relid = tblexpenses.id AND tcf20.fieldid = '20' /*KT comment */
    left join tblcustomfieldsvalues as tcf19 on tcf19.relid = tblexpenses.id AND tcf19.fieldid = '19' /*GD comment */
    left join tblcustomfieldsvalues as tcf24 on tblexpenses.id = tcf24.relid AND tcf24.fieldid = '24' /*Actual Paid Date */
    left join tblcustomfieldsvalues as tcf25 on tblexpenses.id = tcf25.relid AND tcf25.fieldid = '25' /* Expense Paid Percent */
    
    left join tblestimates as tes on tblexpenses.project_id = tes.project_id AND tblexpenses.project_id != '0'
    left join tblstaff ON tes.sale_agent = tblstaff.staffid
    left join tbltaxes AS tax10p ON tblexpenses.tax = tax10p.id AND tax10p.id = 1
    left join tbltaxes AS tax8p ON tblexpenses.tax = tax8p.id AND tax8p.id = 2
    left join tbltaxes AS taxpit ON tblexpenses.tax = taxpit.id AND taxpit.id = 3
    
    where (tcf21.value = "Giám Đốc OK Chuyển Tiền" || tcf21.value = "Trình Duyệt Giám Đốc" )
    AND tblexpenses.date > '{$yearStr}-01-01'
    group by tblexpenses.id
    order by tblexpenses.id desc
    
    SQLSTRING;
    
    $re02cSQLstr = <<<SQLSTRING
    
    select
    CONCAT('<a href=https://crm.jaybranding.com/admin/expenses/expense/',tblexpenses.id,'>',tblexpenses.id,'</a>') As `Exp No`,
    tblexpenses.date,
    ifnull(tcfe.value,'') AS 'Supplier Code',
    ifnull(tcf15.value,'') AS 'Bank No',
    ifnull(tcf17.value,'') AS 'Name',
    ifnull(tcf16.value,'') AS 'At Bank',
    ifnull(tcf5.value,'') AS 'MST',
    -- Concat('<div tabindex="0" class="expand"><span class="note">Supplier Code: <b>',ifnull(tcfe.value,'') ,'</b><br> Bank No: ',ifnull(tcf15.value,''),' <br> Name: ',ifnull(tcf17.value,''),' <br> At Bank: ',ifnull(tcf16.value,''),' <br> MST: ',ifnull(tcf5.value,''),'</span></div>') as `Bank Info`,
    Concat('<div tabindex="0" class="expand"><span class="note">',tblexpenses.note ,'</span></div>') as `Comment`,
    Concat('<div tabindex="0" class="expand"><span class="note">',tcf20.value ,'</span></div>') as `KT Comment`,
    Concat('<div tabindex="0" class="expand"><span class="note">',tcf19.value ,'</span></div>') as `GD Comment`,
    tblexpenses.expense_name as `Expense Name`, 
    tec.name as `Expense Category`,
    concat('<status class="exp ',replace(tcf21.value,' ',''),'">',tcf21.value,'</status>') as `Expense Status`,
    concat('<button class="btn btn-outline-secondary btn-sm" onClick="clickEstViewPercent(this,',tp.id,')">',tp.id,'</button>') as `Project ID`,
    
    format(tes.total,0) as `Estimate Total (VAT)`,
    -- tp.name as `Project Name`,
    CONCAT('<a href=https://crm.jaybranding.com/admin/projects/view/',tblexpenses.project_id,'>',tp.name,'</a>') As `Project Name`,
    CONCAT(tcf25.value, ' %') AS `Expense Paid Percent`,
    format(tcfb.value,0) as `Budget VND`,
    tcf18.value as `Budget Payout Date`,
    format(tcfb.value * COALESCE(tax8p.taxrate, 0) * 0.01,0) as 'VAT (8%)',
    format(tcfb.value * COALESCE(tax10p.taxrate, 0) * 0.01,0) as 'VAT (10%)',
    format(tcfb.value * COALESCE(taxpit.taxrate, 0) * 0.01,0) as 'PIT',
    format(tcfb.value + tcfb.value * COALESCE(tax8p.taxrate, 0) * 0.01 + tcfb.value * COALESCE(tax10p.taxrate, 0) * 0.01,0) as `Total Budget w/ Tax`, /* Budget + VAT */
    format(tcfb.value + tcfb.value * COALESCE(taxpit.taxrate, 0) * 0.01,0) as `Total Budget w/ PIT`, /* Budget - PIT */
    -- format(tblexpenses.amount*IF(tblexpenses.tax=2, 1.08, 1),0) as `Paid VND`,
    if(tblexpenses.amount = 0, 0, format(tblexpenses.amount + tcfb.value * COALESCE(tax8p.taxrate, 0) * 0.01 + tcfb.value * COALESCE(tax10p.taxrate, 0) * 0.01 + tcfb.value * COALESCE(taxpit.taxrate, 0) * 0.01,0)) as `Paid VND`,
    tcf24.value AS `Actual Paid Date`,
    format(IF(tblexpenses.amount = 0, tcfb.value, tblexpenses.amount), 0) AS `Adjusted Planned Amount`,
    format(IF(tblexpenses.amount = 0, tcfb.value, tblexpenses.amount) - tblexpenses.amount,0) as `Remain VND`,
    -- format(tcfb.value - tblexpenses.amount*IF(tblexpenses.tax=2, 1.08, 1),0) as `Remain VND`,
    if(isnull(tcf18.value), 'Error', if(isnull(tcf24.value), if(datediff(curdate(), STR_TO_DATE(tcf18.value, '%Y-%m-%d')) <= 0, 0, datediff(curdate(), STR_TO_DATE(tcf18.value, '%Y-%m-%d'))), 0)) AS `Aging`,
    tblstaff.firstname as `NV Sales`
    
    from tblexpenses 
    left join tblexpenses_categories as tec on tec.id = tblexpenses.category
    left join tblprojects as tp on tblexpenses.project_id = tp.id
    left join tblcustomfieldsvalues as tcfe on tblexpenses.id = tcfe.relid AND tcfe.fieldid = '13'
    left join tblcustomfieldsvalues as tcfb on tblexpenses.id = tcfb.relid AND tcfb.fieldid = '12'
    left join tblcustomfieldsvalues as tcfc on tcfe.value = tcfc.value AND tcfc.fieldid = '14'
    left join tblcustomfieldsvalues as tcf18 on tcf18.relid = tblexpenses.id AND tcf18.fieldid = '18' /*Budget Payout Date */
    left join tblcustomfieldsvalues as tcf17 on tcf17.relid = tcfc.relid AND tcf17.fieldid = '17' /*ten chu tai khoan */
    left join tblcustomfieldsvalues as tcf16 on tcf16.relid = tcfc.relid AND tcf16.fieldid = '16' /*ten ngan hang */
    left join tblcustomfieldsvalues as tcf15 on tcf15.relid = tcfc.relid AND tcf15.fieldid = '15' /*so tai khoan */
    left join tblcustomfieldsvalues as tcf5 on tcf5.relid = tcfc.relid AND tcf5.fieldid = '5' /*MST */
    left join tblcustomfieldsvalues as tcf21 on tcf21.relid = tblexpenses.id AND tcf21.fieldid = '21' /*Tình Trạng */
    left join tblcustomfieldsvalues as tcf20 on tcf20.relid = tblexpenses.id AND tcf20.fieldid = '20' /*KT comment */
    left join tblcustomfieldsvalues as tcf19 on tcf19.relid = tblexpenses.id AND tcf19.fieldid = '19' /*GD comment */
    left join tblcustomfieldsvalues as tcf24 on tblexpenses.id = tcf24.relid AND tcf24.fieldid = '24' /*Actual Paid Date */
    left join tblcustomfieldsvalues as tcf25 on tblexpenses.id = tcf25.relid AND tcf25.fieldid = '25' /* Expense Paid Percent */
    
    left join tblestimates as tes on tblexpenses.project_id = tes.project_id AND tblexpenses.project_id != '0'
    left join tblstaff ON tes.sale_agent = tblstaff.staffid
    left join tbltaxes AS tax10p ON tblexpenses.tax = tax10p.id AND tax10p.id = 1
    left join tbltaxes AS tax8p ON tblexpenses.tax = tax8p.id AND tax8p.id = 2
    left join tbltaxes AS taxpit ON tblexpenses.tax = taxpit.id AND taxpit.id = 3
    
    where tp.name != "" AND tcf21.value != "" AND tblexpenses.date > '{$yearStr}-01-01'
    group by tblexpenses.id
    order by tblexpenses.id desc
    SQLSTRING;
    
    $re02dSQLstr = <<<SQLSTRING
    
    select
    tcfe.value as 'Supplier code',
    /*CONCAT('<a href=https://crm.jaybranding.com/admin/expenses/expense/',tblexpenses.id,'>',tblexpenses.id,'</a>') As `Exp No`,*/
    tblexpenses.date,
    ifnull(tcfe.value,'') AS 'Supplier Code',
    ifnull(tcf15.value,'') AS 'Bank No',
    ifnull(tcf17.value,'') AS 'Name',
    ifnull(tcf16.value,'') AS 'At Bank',
    ifnull(tcf5.value,'') AS 'MST',
    -- Concat('<div tabindex="0" class="expand"><span class="note">Supplier Code: <b>',ifnull(tcfe.value,'') ,'</b><br> Bank No: ',ifnull(tcf15.value,''),' <br> Name: ',ifnull(tcf17.value,''),' <br> At Bank: ',ifnull(tcf16.value,''),' <br> MST: ',ifnull(tcf5.value,''),'</span></div>') as `Bank Info`,
    Concat('<div tabindex="0" class="expand"><span class="note">',replace(tblexpenses.note,'important!','<b class="text-danger">important!</b>') ,'</span></div>') as `Admin Comment`,
    Concat('<div tabindex="0" class="expand"><span class="note">',tcf20.value ,'</span></div>') as `KT Comment`,
    Concat('<div tabindex="0" class="expand"><span class="note">',tcf19.value ,'</span></div>') as `GD Comment`,
    tblexpenses.expense_name as `Expense Name`, 
    tec.name as `Expense Category`,
    concat('<status class="exp ',replace(tcf21.value,' ',''),'">',tcf21.value,'</status>') as `Expense Status`,
    concat('<button class="btn btn-outline-secondary btn-sm" onClick="clickEstViewPercent(this,',tes.number,')">',tes.number,'</button>') as `Estimate No.`,
    
    format(tes.total,0) as `Estimate Total (VAT)`,
    -- tp.name as `Project Name`,
    CONCAT('<a href=https://crm.jaybranding.com/admin/projects/view/',tblexpenses.project_id,'>',tp.name,'</a>') As `Project Name`,
    CONCAT(tcf25.value, ' %') AS `Expense Paid Percent`,
    format(sum(tcfb.value),0) as `Budget VND`,
    tcf18.value as `Budget Payout Date`,
    format(tcfb.value * COALESCE(tax8p.taxrate, 0) * 0.01,0) as 'VAT (8%)',
    format(tcfb.value * COALESCE(tax10p.taxrate, 0) * 0.01,0) as 'VAT (10%)',
    format(tcfb.value * COALESCE(taxpit.taxrate, 0) * 0.01,0) as 'PIT',
    format(tcfb.value + tcfb.value * COALESCE(tax8p.taxrate, 0) * 0.01 + tcfb.value * COALESCE(tax10p.taxrate, 0) * 0.01,0) as `Total Budget w/ Tax`, /* Budget + VAT */
    format(tcfb.value + tcfb.value * COALESCE(taxpit.taxrate, 0) * 0.01,0) as `Total Budget w/ PIT`, /* Budget - PIT */
    -- format(tblexpenses.amount*IF(tblexpenses.tax=2, 1.08, 1),0) as `Paid VND`,
    if(tblexpenses.amount = 0, 0, format(tblexpenses.amount + tcfb.value * COALESCE(tax8p.taxrate, 0) * 0.01 + tcfb.value * COALESCE(tax10p.taxrate, 0) * 0.01 + tcfb.value * COALESCE(taxpit.taxrate, 0) * 0.01,0)) as `Paid VND`,
    tcf24.value AS `Actual Paid Date`,
    format(IF(tblexpenses.amount = 0, tcfb.value, tblexpenses.amount), 0) AS `Adjusted Planned Amount`,
    format(IF(tblexpenses.amount = 0, tcfb.value, tblexpenses.amount) - tblexpenses.amount,0) as `Remain VND`,
    -- format(tcfb.value - tblexpenses.amount*IF(tblexpenses.tax=2, 1.08, 1),0) as `Remain VND`,
    if(isnull(tcf18.value), 'Error', if(isnull(tcf24.value), if(datediff(curdate(), STR_TO_DATE(tcf18.value, '%Y-%m-%d')) <= 0, 0, datediff(curdate(), STR_TO_DATE(tcf18.value, '%Y-%m-%d'))), 0)) AS `Aging`,
    tblstaff.firstname as `NV Sales`
    
    from tblexpenses 
    left join tblexpenses_categories as tec on tec.id = tblexpenses.category
    left join tblprojects as tp on tblexpenses.project_id = tp.id
    left join tblcustomfieldsvalues as tcfe on tblexpenses.id = tcfe.relid AND tcfe.fieldid = '13'
    left join tblcustomfieldsvalues as tcfb on tblexpenses.id = tcfb.relid AND tcfb.fieldid = '12'
    left join tblcustomfieldsvalues as tcfc on tcfe.value = tcfc.value AND tcfc.fieldid = '14'
    left join tblcustomfieldsvalues as tcf18 on tcf18.relid = tblexpenses.id AND tcf18.fieldid = '18' /*Budget Payout Date */
    left join tblcustomfieldsvalues as tcf17 on tcf17.relid = tcfc.relid AND tcf17.fieldid = '17' /*ten chu tai khoan */
    left join tblcustomfieldsvalues as tcf16 on tcf16.relid = tcfc.relid AND tcf16.fieldid = '16' /*ten ngan hang */
    left join tblcustomfieldsvalues as tcf15 on tcf15.relid = tcfc.relid AND tcf15.fieldid = '15' /*so tai khoan */
    left join tblcustomfieldsvalues as tcf5 on tcf5.relid = tcfc.relid AND tcf5.fieldid = '5' /*MST */
    left join tblcustomfieldsvalues as tcf21 on tcf21.relid = tblexpenses.id AND tcf21.fieldid = '21' /*Tình Trạng */
    left join tblcustomfieldsvalues as tcf20 on tcf20.relid = tblexpenses.id AND tcf20.fieldid = '20' /*KT comment */
    left join tblcustomfieldsvalues as tcf19 on tcf19.relid = tblexpenses.id AND tcf19.fieldid = '19' /*GD comment */
    left join tblcustomfieldsvalues as tcf24 on tblexpenses.id = tcf24.relid AND tcf24.fieldid = '24' /*Actual Paid Date */
    left join tblcustomfieldsvalues as tcf25 on tblexpenses.id = tcf25.relid AND tcf25.fieldid = '25' /* Expense Paid Percent */
    
    left join tblestimates as tes on tblexpenses.project_id = tes.project_id AND tblexpenses.project_id != '0'
    left join tblstaff ON tes.sale_agent = tblstaff.staffid
    left join tbltaxes AS tax10p ON tblexpenses.tax = tax10p.id AND tax10p.id = 1
    left join tbltaxes AS tax8p ON tblexpenses.tax = tax8p.id AND tax8p.id = 2
    left join tbltaxes AS taxpit ON tblexpenses.tax = taxpit.id AND taxpit.id = 3
    
    where (tcf21.value = "Giám Đốc OK Chuyển Tiền" || tcf21.value = "Trình Duyệt Giám Đốc"  || tcf21.value = "Done-C" )
    AND tblexpenses.date > '{$yearStr}-01-01'
    group by tcfe.value
    order by tblexpenses.id desc
    
    SQLSTRING;



    $re01SQLstr = <<<SQLSTRING
    /*Estimate & Payment Detail Report */

    Select
        tblestimates.number As `So Bao Gia`,
        tis.number as `So INVOICE`,
        /*tblestimates.invoiceid,*/
        record.id as`So Thanh Toan`,
        tblestimates.date,
        tblstaff.firstname as `NV sales`,    
        tblclients.company as `Ten Cty`,
        tblprojects.name as `Project Name`,
        concat('<status class="project ps',replace(tblprojects.status,' ',''),'">',tblprojects.status,'</status>') as `Project Status`,
        
        format(tblestimates.subtotal,0) as `Tong chua thue`,
        format(tblestimates.total_tax,0) as `Tong VAT thue`,
        format(tblestimates.total,0) as `Tong co thue`,
        CONCAT('<status class="estimate e',
        REPLACE(tis.status, ' ', ''),
        '">',
        tis.status,
        '</status>') AS `Estimate Status`,
        ifnull(format(record.amount,0),0) as `Da Thanh Toan`,
        format(SUM(ifnull(sump.amount,0)),0) as `TT SUM`,
        record.date as `Ngay Thanh Toan`,
        format(tblestimates.total - sum(ifnull(sump.amount,0)),0) as `Còn Lai (SUM)`,
        format(ifnull(np.value,0),0) as `Next Payment Amount`,
        pf.value as `Payment Forecast Date`,
        pt.value as `Payment Terms`,
        tis.adminnote as `Ghi Chú Estimate - Admin`,
        record.note as `Ghi Chú Payments 1|2|3`,
        tis.adminnote As `Ghi Chú Invoice`

    
    From
        tblestimates
            LEFT JOIN tblinvoices as tis ON tblestimates.invoiceid = tis.id 
            left JOIN tblinvoicepaymentrecords as record ON tblestimates.invoiceid = record.invoiceid
            left join tblinvoicepaymentrecords as sump ON tblestimates.invoiceid = sump.invoiceid
            left join tblstaff ON tblestimates.sale_agent = tblstaff.staffid
            left join tblclients ON tblestimates.clientid = tblclients.userid
            left join tblprojects ON tis.project_id = tblprojects.id
            left join tblcustomfieldsvalues as np ON tblestimates.id = np.relid AND np.fieldid = '7'
            left join tblcustomfieldsvalues as pf ON tblestimates.id = pf.relid AND pf.fieldid = '10'
            left join tblcustomfieldsvalues as pt ON tblestimates.id = pt.relid AND pt.fieldid = '9'


    Where
            tblestimates.date > '{$yearStr}-01-01'
            group by tis.id 

    ORDER BY tblestimates.number DESC;


    SQLSTRING;

    $re01bSQLstr = <<<SQLSTRING
    /*Estimate & Payment summary Report */
    Select
        CONCAT('<a title="edit" href="https://crm.jaybranding.com/admin/estimates/estimate/',tblestimates.id,'">',tblestimates.number,'</a>') As `Est No`,
        tblstaff.firstname as `NV sales`,  
        CONCAT('<div tabindex="0" class="expand"><span class="note">', replace(tblestimates.adminnote,'important!','<b class="text-danger">important!</b>'),'</span></div>') as 'Admin Comment',
        CONCAT('<div tabindex="1" class="expand"><span class="note">',tblprojects.name,'</span></div>') as 'Project Name',
        CONCAT('<div tabindex="1" class="expand"><span class="note">',p1.value,'</span></div>') as 'Project Note',    
        tblclients.company as `Company Name`,
        format(tblestimates.total,0) as `Total with Tax`,
        CONCAT('<status class="estimate e',
        REPLACE(tis.status, ' ', ''),
        '">',
        tis.status,
        '</status>') AS `Estimate Status`,
        concat('<status class="project ps',replace(tblprojects.status,' ',''),'">',tblprojects.status,'</status>') as `Project Status`,
        format(ifnull(record.amount,0),0) as `Paid Amount`,
        format(SUM(ifnull(sump.amount,0)),0) as `Paid Amount SUM`,
        format(ifnull(np.value,0),0) as `Next Payment Amount`,
        concat('<date>',pf.value,'</date') as `Payment Forecast Date`
        
    
    From
        tblestimates
            LEFT JOIN tblinvoices as tis ON tblestimates.invoiceid = tis.id 
            left JOIN tblinvoicepaymentrecords as record ON tblestimates.invoiceid = record.invoiceid
            left join tblinvoicepaymentrecords as sump ON tblestimates.invoiceid = sump.invoiceid
            left join tblstaff ON tblestimates.sale_agent = tblstaff.staffid
            left join tblclients ON tblestimates.clientid = tblclients.userid
            left join tblprojects ON tblestimates.project_id = tblprojects.id
            left join tblcustomfieldsvalues as np ON tblestimates.id = np.relid AND np.fieldid = '7'
            left join tblcustomfieldsvalues as pf ON tblestimates.id = pf.relid AND pf.fieldid = '10'
            left join tblcustomfieldsvalues as pt ON tblestimates.id = pt.relid AND pt.fieldid = '9'
            left join tblcustomfieldsvalues as p1 ON tblprojects.id = p1.relid AND p1.fieldid = '1'


    Where
    pf.value < (DATE_ADD(NOW(), INTERVAL {$dayInt} DAY)) AND pf.value != ""
    AND tis.status != 2 
    AND tblestimates.status != 3
    group by  tis.id 

    ORDER BY tblestimates.number DESC;


    SQLSTRING;


    $re01cSQLstr = <<<SQLSTRING

    Select
        CONCAT('<a title="edit" href="https://crm.jaybranding.com/admin/estimates/estimate/',tblestimates.id,'">',tblestimates.number,'</a>') As `Est No`,
        tblstaff.firstname as `NV sales`,  
        CONCAT('<div tabindex="0" class="expand"><span class="note">', replace(tblestimates.adminnote,'important!','<b class="text-danger">important!</b>'),'</span></div>') as 'Admin Comment',
        CONCAT('<div tabindex="1" class="expand"><span class="note">',tblprojects.name,'</span></div>') as 'Project Name',
        CONCAT('<div tabindex="1" class="expand"><span class="note">',p1.value,'</span></div>') as 'Project Note',    
        tblclients.company as `Company Name`,
        format(tblestimates.total,0) as `Total with Tax`,
        CONCAT('<status class="estimate e',
        REPLACE(tis.status, ' ', ''),
        '">',
        tis.status,
        '</status>') AS `Estimate Status`,
        concat('<status class="project ps',replace(tblprojects.status,' ',''),'">',tblprojects.status,'</status>') as `Project Status`,
        format(ifnull(record.amount,0),0) as `Paid Amount`,
        format(SUM(ifnull(sump.amount,0)),0) as `Paid Amount SUM`,
        format(ifnull(np.value,0),0) as `Next Payment Amount`,
        pf.value as `Payment Forecast Date`
        
    
    From
        tblestimates
            LEFT JOIN tblinvoices as tis ON tblestimates.invoiceid = tis.id 
            left JOIN tblinvoicepaymentrecords as record ON tblestimates.invoiceid = record.invoiceid
            left join tblinvoicepaymentrecords as sump ON tblestimates.invoiceid = sump.invoiceid
            left join tblstaff ON tblestimates.sale_agent = tblstaff.staffid
            left join tblclients ON tblestimates.clientid = tblclients.userid
            left join tblprojects ON tblestimates.project_id = tblprojects.id
            left join tblcustomfieldsvalues as np ON tblestimates.id = np.relid AND np.fieldid = '7'
            left join tblcustomfieldsvalues as pf ON tblestimates.id = pf.relid AND pf.fieldid = '10'
            left join tblcustomfieldsvalues as pt ON tblestimates.id = pt.relid AND pt.fieldid = '9'
            left join tblcustomfieldsvalues as p1 ON tblprojects.id = p1.relid AND p1.fieldid = '1'


    Where
    (pf.value > (DATE_ADD(NOW(), INTERVAL 30 DAY)) OR pf.value < DATE(NOW()) OR pf.value IS NULL)
    AND tis.status != 2
    AND tblestimates.status != 3
    group by tblestimates.number

    ORDER BY tblestimates.number DESC;


    SQLSTRING;


    $re01dSQLstr = <<<SQLSTRING

    Select
        CONCAT('<a title="edit" href="https://crm.jaybranding.com/admin/estimates/estimate/',tblestimates.id,'">',tblestimates.number,'</a>') As `Est No`,
        tblestimates.date,
        tblstaff.firstname as `NV sales`,  
        tblclients.company as `Ten Cty`,
        format(tblestimates.total,0) as `Tong co thue`,
        tblestimates.status

    
    From
        tblestimates

            left join tblstaff ON tblestimates.sale_agent = tblstaff.staffid
            left join tblclients ON tblestimates.clientid = tblclients.userid

            
    where tblestimates.invoiceid is null AND tblestimates.status != "3"

    SQLSTRING;

    $reportDetailProjectID = <<<SQLSTRING

    

    SELECT tp.id,

    tp.name,

    FORMAT(newtab.subtotal, 0) AS `Project Total`,

    FORMAT(SUM(tcf.value), 0) AS `Budget`,

    CONCAT(FORMAT(((newtab.subtotal - SUM(tcf.value))/newtab.subtotal)*100,2),'%') AS `Percent`,

    FORMAT(newtab.total, 0) AS `Project Total (w/ Tax)`,

    FORMAT(SUM(tcf.value) + SUM(tcf.value) * COALESCE(tax8p.taxrate, 0) * 0.01 + SUM(tcf.value) * COALESCE(tax10p.taxrate, 0) * 0.01 + SUM(tcf.value) * COALESCE(taxpit.taxrate, 0) * 0.01,0) AS `Budget (w/ Tax)`,

    CONCAT(FORMAT(((newtab.total - (SUM(tcf.value) + SUM(tcf.value) * COALESCE(tax8p.taxrate, 0) * 0.01 + SUM(tcf.value) * COALESCE(tax10p.taxrate, 0) * 0.01 + SUM(tcf.value) * COALESCE(taxpit.taxrate, 0) * 0.01))/newtab.total)*100,2),'%') AS `Percent (w/ Tax)`,

    CONCAT(FORMAT((SUM(tex.amount)/newtab.total)*100,2),'%') as `Percent Paid`

    

    FROM tblprojects AS tp

        LEFT JOIN tblexpenses AS tex ON tp.id = tex.project_id

        LEFT JOIN tblcustomfieldsvalues AS tcf ON tex.id = tcf.relid AND tcf.fieldid = 12

        LEFT JOIN (

            SELECT tp.id, SUM(tes.subtotal) AS subtotal, SUM(tes.total) AS total

            FROM tblprojects AS tp

            LEFT JOIN tblestimates AS tes ON tp.id = tes.project_id

            WHERE tp.id = {$projectID}

            GROUP BY tp.id

        ) newtab ON tp.id = newtab.id

        LEFT JOIN tbltaxes AS tax10p ON tex.tax = tax10p.id AND tax10p.id = 1

        LEFT JOIN tbltaxes AS tax8p ON tex.tax = tax8p.id AND tax8p.id = 2

        LEFT JOIN tbltaxes AS taxpit ON tex.tax = taxpit.id AND taxpit.id = 3

    WHERE tp.id = {$projectID}

    GROUP BY tp.id

    

    SQLSTRING;


    $reportDashboardFullYear = <<<SQLSTRING

    SELECT 
        tet.ymdate AS 'YearMonth', /*tpt.ymsum as 'Proposal',*/ tet.ymsum as 'Estimate', tit.ymsum as 'Invoice', tipt.ymsum as 'Payment', tex.ymsum as 'Expense'
    FROM
        (SELECT 
            DATE_FORMAT(date, '%Y-%m') AS ymdate,
            Ceiling(SUM(total)) AS ymsum
        FROM
            tblestimates
        GROUP BY ymdate) AS tet
            JOIN
        (SELECT 
            DATE_FORMAT(date, '%Y-%m') AS ymdate,
            Ceiling(SUM(total)) AS ymsum
        FROM
            tblproposals
        GROUP BY ymdate) AS tpt ON tet.ymdate = tpt.ymdate
            JOIN
        (SELECT 
            DATE_FORMAT(date, '%Y-%m') AS ymdate,
            Ceiling(SUM(total)) AS ymsum
        FROM
            tblinvoices
        GROUP BY ymdate) AS tit ON tit.ymdate = tet.ymdate
            JOIN
        (SELECT 
            DATE_FORMAT(date, '%Y-%m') AS ymdate,
            Ceiling(SUM(amount)) AS ymsum
        FROM
            tblinvoicepaymentrecords
        GROUP BY ymdate) AS tipt ON tipt.ymdate = tet.ymdate
        join
        (SELECT 
            DATE_FORMAT(date, '%Y-%m') AS ymdate,
            Ceiling(SUM(amount)) AS ymsum
        FROM
            tblexpenses
        GROUP BY ymdate) AS tex ON tex.ymdate = tet.ymdate
        
    ORDER BY tet.ymdate DESC


    SQLSTRING;

    $reportDashboardFullYearTable = <<<SQLSTRING

    SELECT 
        tet.ymdate AS 'YearMonth', /*tpt.ymsum as 'Proposal',*/ tet.ymsum as 'Estimate', tit.ymsum as 'Invoice', tipt.ymsum as 'Payment', tex.ymsum as 'Expense'
    FROM
        (SELECT 
            DATE_FORMAT(date, '%Y-%m') AS ymdate,
            FORMAT(SUM(total),0) AS ymsum
        FROM
            tblestimates
        GROUP BY ymdate) AS tet
            JOIN
        (SELECT 
            DATE_FORMAT(date, '%Y-%m') AS ymdate,
            FORMAT(SUM(total),0) AS ymsum
        FROM
            tblproposals
        GROUP BY ymdate) AS tpt ON tet.ymdate = tpt.ymdate
            JOIN
        (SELECT 
            DATE_FORMAT(date, '%Y-%m') AS ymdate,
            FORMAT(SUM(total),0) AS ymsum
        FROM
            tblinvoices
        GROUP BY ymdate) AS tit ON tit.ymdate = tet.ymdate
            JOIN
        (SELECT 
            DATE_FORMAT(date, '%Y-%m') AS ymdate,
            FORMAT(SUM(amount),0) AS ymsum
        FROM
            tblinvoicepaymentrecords
        GROUP BY ymdate) AS tipt ON tipt.ymdate = tet.ymdate
        join
        (SELECT 
            DATE_FORMAT(date, '%Y-%m') AS ymdate,
            FORMAT(SUM(amount),0) AS ymsum
        FROM
            tblexpenses
        GROUP BY ymdate) AS tex ON tex.ymdate = tet.ymdate
        /* where tet.ymdate like '%{$yearStr}%' */
        ORDER BY tet.ymdate DESC
    SQLSTRING;

    $reportDashboardPayment4weeks = <<<SQLSTRING

    SELECT  STR_TO_DATE(CONCAT(YEARWEEK(CURDATE()),' Sunday'), '%X%V %W') as 'From Date', tbtw.amtw  as 'Payment TW', tb1.am1  as 'Payment-1w', tb2.am2  as 'Payment-2w', tb3.am3  as 'Payment-3w'
    FROM tblinvoicepaymentrecords as tb1,
    (
    SELECT sum(ceiling(amount)) as amtw
    FROM tblinvoicepaymentrecords
    WHERE YEARWEEK(date) = (YEARWEEK(CURDATE()))
    ) as tbtw,
    (
    SELECT sum(ceiling(amount)) as am1
    FROM tblinvoicepaymentrecords
    WHERE YEARWEEK(date) = (YEARWEEK(CURDATE())-1)
    ) as tb1,
    (
    SELECT sum(ceiling(amount)) as am2
    FROM tblinvoicepaymentrecords
    WHERE YEARWEEK(date) = (YEARWEEK(CURDATE())-2)
    ) as tb2,
    (
    SELECT sum(ceiling(amount)) as am3
    FROM tblinvoicepaymentrecords
    WHERE YEARWEEK(date) = (YEARWEEK(CURDATE())-3)
    ) as tb3
    limit 1

    SQLSTRING;


    $reportDashboardThisMonth = <<<SQLSTRING

    SELECT 
        tet.ymdate AS 'YearMonth', /*tpt.ymsum as 'Proposal',*/ tet.ymsum as 'Estimate', tit.ymsum as 'Invoice', tipt.ymsum as 'Payment', tex.ymsum as 'Expense'
    FROM
        (SELECT 
            DATE_FORMAT(date, '%Y-%m') AS ymdate,
            Ceiling(SUM(total)) AS ymsum
        FROM
            tblestimates
        GROUP BY ymdate) AS tet
            JOIN
        (SELECT 
            DATE_FORMAT(date, '%Y-%m') AS ymdate,
            Ceiling(SUM(total)) AS ymsum
        FROM
            tblproposals
        GROUP BY ymdate) AS tpt ON tet.ymdate = tpt.ymdate
            JOIN
        (SELECT 
            DATE_FORMAT(date, '%Y-%m') AS ymdate,
            Ceiling(SUM(total)) AS ymsum
        FROM
            tblinvoices
        GROUP BY ymdate) AS tit ON tit.ymdate = tet.ymdate
            JOIN
        (SELECT 
            DATE_FORMAT(date, '%Y-%m') AS ymdate,
            Ceiling(SUM(amount)) AS ymsum
        FROM
            tblinvoicepaymentrecords
        GROUP BY ymdate) AS tipt ON tipt.ymdate = tet.ymdate
        join
        (SELECT 
            DATE_FORMAT(date, '%Y-%m') AS ymdate,
            Ceiling(SUM(amount)) AS ymsum
        FROM
            tblexpenses
        GROUP BY ymdate) AS tex ON tex.ymdate = tet.ymdate
        where tet.ymdate like  DATE_FORMAT(curdate(), '%Y-%m')
        ORDER BY tet.ymdate DESC
    SQLSTRING;
?>