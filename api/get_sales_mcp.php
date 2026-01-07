<?php
header('Content-Type: application/json');
$mysqli = new mysqli("localhost", "root", "", "db_powerone_mcp");

if ($mysqli->connect_errno) {
    echo json_encode(["error" => $mysqli->connect_error]);
    exit();
}

$sql = "SELECT 
            PROCESS_DATE, TRANSACT_DATE, SUPPLIER_NO, SUPPLIER_NAME,
            STORE_NO, STORE_NAME, CLASS, DEPT, `GROUP`,
            BRAND, WORLD, COLOUR, SIZE, ITEM, BARCODE,
            QTY, ITEM_PRICE, GROSS_AMT, DISC_AUTO, DISC_PROMO,
            DISC_EMPLOYEE, DISC_FREE_ITEM, DISC_STRUK, DISC_PCT,
            NET_AMT, POS_NO, TRANSACT_NO, TRANSACT_LINE_NO
        FROM sales_transaction_excel
        ORDER BY PROCESS_DATE DESC
        LIMIT 500";

$result = $mysqli->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "data" => $data
]);
?>
