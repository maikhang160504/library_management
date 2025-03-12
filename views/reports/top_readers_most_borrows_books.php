<?php
$title = "Thống kê Mượn Sách";
ob_start();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Tiêu đề cột
    $sheet->setCellValue('A1', 'Mã độc giả');
    $sheet->setCellValue('B1', 'Tên độc giả');
    $sheet->setCellValue('C1', 'Số lượt mượn');
    $sheet->setCellValue('E1', 'Mã sách');
    $sheet->setCellValue('F1', 'Tên sách');
    $sheet->setCellValue('G1', 'Số lượt mượn');

    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
    ];
    $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
    // Đổ dữ liệu vào Excel
    $row = 2;
    foreach ($readers as $reader) {
        $sheet->setCellValue('A' . $row, $reader['ma_doc_gia']);
        $sheet->setCellValue('B' . $row, $reader['ten_doc_gia']);
        $sheet->setCellValue('C' . $row, $reader['so_luot_muon']);
        $row++;
    }
    $row = 2;
    foreach ($books as $book) {
        $sheet->setCellValue('E' . $row, $book['ma_sach']);
        $sheet->setCellValue('F' . $row, $book['ten_sach']);
        $sheet->setCellValue('G' . $row, $book['so_luot_muon']);
        $row++;
    }
    
    // Xuất file Excel
    $filename = "ThongKeSachMuonTu" . ($_GET['startDate'] ?? '___') . "Den" . ($_GET['endDate'] ?? '___') . ".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>

<div class="container mt-4">
    <!-- Tiêu đề & Nút quay lại -->
    <div class="d-flex align-items-center justify-content-center position-relative my-4">
        <a href="/reports" class="btn btn-outline-secondary  position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class=" text-center flex-grow-1">
            📊 Thống kê Mượn Sách từ <?php echo isset($startDate) ? $startDate : '___'; ?> đến <?php echo isset($endDate) ? $endDate : '___'; ?>
        </h2>
        <a href="?export=excel&startDate=<?php echo $startDate; ?>&endDate=<?php echo $endDate; ?>" class="btn btn-success position-absolute end-0">
            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
        </a>
    </div>

    <!-- Bộ lọc ngày bắt đầu & ngày kết thúc -->
    <div class="row justify-content-center mt-4">
        <div class="col-md-4">
            <label for="startDate" class="form-label">Ngày bắt đầu:</label>
            <input type="date" id="startDate" class="form-control" value="<?php echo $startDate ?? ''; ?>">
        </div>
        <div class="col-md-4">
            <label for="endDate" class="form-label">Ngày kết thúc:</label>
            <input type="date" id="endDate" class="form-control" value="<?php echo $endDate ?? ''; ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button id="filterBtn" class="btn btn-primary w-100">Lọc thống kê</button>
        </div>
    </div>

    <!-- Độc giả mượn nhiều sách nhất -->
    <div class="card shadow-sm my-4">
        <div class="card-body">
            <h5 class="card-title text-primary">📌 Độc giả mượn nhiều sách nhất</h5>
            <table class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Mã độc giả</th>
                        <th>Tên độc giả</th>
                        <th>Số lượt mượn</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($readers as $reader): ?>
                        <tr>
                            <td><?php echo $reader['ma_doc_gia']; ?></td>
                            <td class="text-start"><?php echo $reader['ten_doc_gia']; ?></td>
                            <td class="fs-5 text-success"><strong><?php echo $reader['so_luot_muon']; ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sách được mượn nhiều nhất -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-primary">📚 Sách được mượn nhiều nhất</h5>
            <table class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Mã sách</th>
                        <th class="text-start">Tên sách</th>
                        <th>Số lượt mượn</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?php echo $book['ma_sach']; ?></td>
                            <td class="text-start"><?php echo $book['ten_sach']; ?></td>
                            <td class="fs-5 text-success"><strong><?php echo $book['so_luot_muon']; ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
    <div class="card-body">
        <h5 class="card-title text-primary">📊 Biểu đồ Độc giả Mượn Nhiều Nhất</h5>
        <canvas id="readersChart" style="max-height: 300px;"></canvas>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-body">
        <h5 class="card-title text-primary">📊 Biểu đồ Sách Được Mượn Nhiều Nhất</h5>
        <canvas id="booksChart" style="max-height: 300px;"></canvas>
    </div>
</div>

</div>

<!-- JavaScript: Xử lý lọc thống kê -->
<script>
    document.getElementById("filterBtn").addEventListener("click", function() {
        var startDate = document.getElementById("startDate").value;
        var endDate = document.getElementById("endDate").value;

        if (!startDate || !endDate) {
            alert("Vui lòng chọn cả ngày bắt đầu và ngày kết thúc!");
            return;
        }

        if (startDate > endDate) {
            alert("Ngày bắt đầu không được lớn hơn ngày kết thúc!");
            return;
        }

        var url = "/reports/top-readers-most-borrowed-book?startDate=" + startDate + "&endDate=" + endDate;
        window.location.href = url;
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Biểu đồ Độc giả
    var readersCtx = document.getElementById('readersChart').getContext('2d');
    var readersLabels = <?= json_encode(array_column($readers, 'ten_doc_gia')) ?>;
    var readersData = <?= json_encode(array_column($readers, 'so_luot_muon')) ?>;

    new Chart(readersCtx, {
        type: 'bar',
        data: {
            labels: readersLabels,
            datasets: [{
                label: 'Số lượt mượn',
                data: readersData,
                backgroundColor: '#4CAF50',
                borderColor: '#388E3C',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Số lượt mượn' }
                }
            },
            plugins: { legend: { display: false } }
        }
    });

    // Biểu đồ Sách
    var booksCtx = document.getElementById('booksChart').getContext('2d');
    var booksLabels = <?= json_encode(array_column($books, 'ten_sach')) ?>;
    var booksData = <?= json_encode(array_column($books, 'so_luot_muon')) ?>;

    new Chart(booksCtx, {
        type: 'bar',
        data: {
            labels: booksLabels,
            datasets: [{
                label: 'Số lượt mượn',
                data: booksData,
                backgroundColor: '#2196F3',
                borderColor: '#1976D2',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Số lượt mượn' }
                }
            },
            plugins: { legend: { display: false } }
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>