<?php
include 'config/db.php';

// Get request parameters from DataTables
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$offset = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 1;
$search = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : "";
$orderColumnIndex = isset($_POST['order'][0]['column']) ? (int)$_POST['order'][0]['column'] : 0;
$orderDir = isset($_POST['order'][0]['dir']) && in_array($_POST['order'][0]['dir'], ['asc', 'desc']) ? strtoupper($_POST['order'][0]['dir']) : 'DESC';

// Define column mapping (update these to match your DataTable columns)
$columns = [
    0 => 'da.computer_center_number',
    1 => 'da.asset_number',
    2 => 'da.list_device',
    3 => 'd.depart_name',
    4 => 'da.brand',
    5 => 'da.model',
    6 => 'da.purchase_date',
    7 => 'da.price'
];

// Get column name for sorting
$orderBy = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'da.id';

// Build WHERE condition for searching across all columns
$searchQuery = "";
$searchParams = [];

if (!empty($search)) {
    $searchQuery = " AND (";
    foreach ($columns as $column) {
        $searchQuery .= "$column LIKE :search OR ";
    }
    $searchQuery = rtrim($searchQuery, " OR ") . ")"; // Remove last OR
    $searchParams[':search'] = "%{$search}%";
}

// Count total records (without filtering)
$totalRecordsQuery = "SELECT COUNT(*) FROM run_number.device_asset WHERE is_deleted != 1";
$totalRecordsStmt = $conn->prepare($totalRecordsQuery);
$totalRecordsStmt->execute();
$totalRecords = $totalRecordsStmt->fetchColumn();

// Count total records after filtering
$totalFilteredQuery = "SELECT COUNT(*) FROM run_number.device_asset da
    LEFT JOIN OrderIT.depart d ON da.depart_id = d.depart_id
    WHERE da.is_deleted != 1 $searchQuery";

$totalFilteredStmt = $conn->prepare($totalFilteredQuery);
foreach ($searchParams as $key => $value) {
    $totalFilteredStmt->bindValue($key, $value, PDO::PARAM_STR);
}
$totalFilteredStmt->execute();
$totalFiltered = $totalFilteredStmt->fetchColumn();

// Fetch paginated data with filtering and sorting
$sql = "SELECT da.*, d.depart_name 
FROM run_number.device_asset da
LEFT JOIN OrderIT.depart d ON da.depart_id = d.depart_id
WHERE da.is_deleted != 1 $searchQuery
        ORDER BY $orderBy $orderDir
        LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($sql);
foreach ($searchParams as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if asset_number exists in data_report
foreach ($data as &$row) {
    $checkSql = "SELECT COUNT(*) FROM OrderIT.data_report WHERE number_device = :asset_number";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bindParam(":asset_number", $row['asset_number']);
    $checkStmt->execute();
    $rowCount = $checkStmt->fetchColumn();

    // If count > 0, set historyExists = true
    $row['historyExists'] = $rowCount > 0;
}

// Send data in DataTables format
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => $data
]);
