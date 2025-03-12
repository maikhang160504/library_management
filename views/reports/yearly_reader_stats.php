<?php
$title = "Thống kê độc giả mượn sách trong năm";
ob_start();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$year = date('Y'); // Lấy năm hiện tại

// Xuất Excel nếu yêu cầu
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Tiêu đề cột
    $sheet->setCellValue('A1', 'Mã Độc Giả');
    $sheet->setCellValue('B1', 'Tên Độc Giả');
    $sheet->setCellValue('C1', 'Số Lần Mượn');
    $sheet->setCellValue('D1', 'Thể Loại Mượn Nhiều Nhất');

    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
    ];
    $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
    // Đổ dữ liệu vào Excel
    $row = 2;
    foreach ($details as $reader) {
        $sheet->setCellValue('A' . $row, $reader['ma_doc_gia']);
        $sheet->setCellValue('B' . $row, $reader['ten_doc_gia']);
        $sheet->setCellValue('C' . $row, $reader['so_lan_muon']);
        $sheet->setCellValue('D' . $row, $reader['the_loai_muon_nhieu_nhat']);
        $row++;
    }

    // Xuất file Excel
    $filename = "ThongKeDocGiaMuonSach_$year.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>

<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-center position-relative my-4">
        <a href="/reports" class="btn btn-outline-secondary px-4 py-2 position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="text-center mb-4 no-print">📊 Thống kê độc giả Mượn Sách trong Năm <?php echo $year; ?></h2>
        <a href="?export=excel" class="btn btn-success position-absolute end-0">
            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body text-center">
            <h5 class="card-title text-primary">📌 Tổng số độc giả đã mượn sách</h5>
            <p class="fs-3 fw-bold text-success"><?php echo $stats['SoDocGiaMuon']; ?> độc giả</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-primary">📋 Chi tiết độc giả Mượn Sách</h5>
            <table class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Mã độc giả</th>
                        <th class="text-start">Tên độc giả</th>
                        <th>Số lần mượn</th>
                        <th class="text-start">Thể loại sách mượn nhiều nhất</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($details as $reader): ?>
                        <tr>
                            <td><?php echo $reader['ma_doc_gia']; ?></td>
                            <td class="text-start"><?php echo $reader['ten_doc_gia']; ?></td>
                            <td class="fs-5 text-success"><strong><?php echo $reader['so_lan_muon']; ?></strong></td>
                            <td class="text-start"><?php echo $reader['the_loai_muon_nhieu_nhat']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h5 class="card-title text-primary"><i class="bi bi-bar-chart-fill"></i> Số lần Mượn theo Độc Giả</h5>
            <canvas id="docGiaChart" style="max-height: 300px;"></canvas>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    var ctx = document.getElementById('docGiaChart').getContext('2d');

    // Lấy dữ liệu từ PHP
    var labels = <?= json_encode(array_column($details, 'ten_doc_gia')) ?>; // Tên độc giả
    var data = <?= json_encode(array_column($details, 'so_lan_muon')) ?>; // Số lần mượn

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Số lần mượn',
                data: data,
                backgroundColor: '#4caf50',
                borderColor: '#388e3c',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Số lần mượn'
                    }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>