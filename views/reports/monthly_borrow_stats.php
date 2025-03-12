<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$title = "Thống kê Sách Mượn";
ob_start();
$filter = $_GET['filter'] ?? 'this_month'; // Mặc định là tháng này
$filterText = [
    'today' => 'Hôm nay',
    'this_week' => 'Tuần này',
    'this_month' => 'Tháng này',
    'this_year' => 'Năm nay'
][$filter];

if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Thống kê Sách Mượn");

    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
    ];
    $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
    // Tiêu đề
    $sheet->setCellValue('A1', 'Mã sách');
    $sheet->setCellValue('B1', 'Tên sách');
    $sheet->setCellValue('C1', 'Tác giả');
    $sheet->setCellValue('D1', 'Thể loại');
    $sheet->setCellValue('E1', 'Số lần mượn');

    $row = 2;
    foreach ($details as $book) {
        $sheet->setCellValue("A$row", $book['ma_sach']);
        $sheet->setCellValue("B$row", $book['ten_sach']);
        $sheet->setCellValue("C$row", $book['ten_tac_gia']);
        $sheet->setCellValue("D$row", $book['ten_the_loai']);
        $sheet->setCellValue("E$row", $book['so_lan_muon']);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    $filename = "ThongKeSachMuon" . $filter . ".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    exit;
}
$theLoaiData = [];
foreach ($details as $book) {
    $theLoai = $book['ten_the_loai'];
    $soLanMuon = $book['so_lan_muon'];

    if (isset($theLoaiData[$theLoai])) {
        $theLoaiData[$theLoai] += $soLanMuon;
    } else {
        $theLoaiData[$theLoai] = $soLanMuon;
    }
}

// Chuyển dữ liệu sang JSON để dùng trong JavaScript
$labelsPie = json_encode(array_keys($theLoaiData));
$dataPie = json_encode(array_values($theLoaiData));
?>

<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-center position-relative my-4 no-print">
        <a href="/reports" class="btn btn-outline-secondary position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="text-center text-primary"><i class="bi bi-bar-chart-line"></i> Thống kê Sách Mượn - <?= $filterText ?></h2>
        <a href="?filter=<?= $filter ?>&export=excel" class="btn btn-success position-absolute end-0">
            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
        </a>
    </div>
    <h2 class="print-title d-none">BÁO CÁO THỐNG KÊ SÁCH MƯỢN <?= strtoupper($filterText); ?></h2>

    <div class="d-flex justify-content-center gap-2 mb-4 no-print">
        <a href="?filter=today" class="btn <?= $filter == 'today' ? 'btn-primary' : 'btn-outline-primary' ?>">Hôm nay</a>
        <a href="?filter=this_week" class="btn <?= $filter == 'this_week' ? 'btn-primary' : 'btn-outline-primary' ?>">Tuần này</a>
        <a href="?filter=this_month" class="btn <?= $filter == 'this_month' ? 'btn-primary' : 'btn-outline-primary' ?>">Tháng này</a>
        <a href="?filter=this_year" class="btn <?= $filter == 'this_year' ? 'btn-primary' : 'btn-outline-primary' ?>">Năm nay</a>
    </div>

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body text-center">
            <h5 class="card-title text-secondary"><i class="bi bi-journal-bookmark-fill"></i> Tổng số sách đã mượn</h5>
            <p class="display-5 fw-bold text-success"><?= $total_borrow ?> quyển</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title text-secondary"><i class="bi bi-book-half"></i> Chi tiết Sách Mượn</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Mã sách</th>
                            <th class="text-start">Tên sách</th>
                            <th class="text-start">Tác giả</th>
                            <th class="text-start">Thể loại</th>
                            <th>Số lần mượn</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $book): ?>
                            <tr>
                                <td class="text-center"><?= $book['ma_sach'] ?></td>
                                <td class="text-start fw-medium"><?= $book['ten_sach'] ?></td>
                                <td class="text-start fst-italic"><?= $book['ten_tac_gia'] ?></td>
                                <td class="text-start"><?= $book['ten_the_loai'] ?></td>
                                <td class="text-center text-primary"><strong><?= $book['so_lan_muon'] ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row mt-4">

        <!-- Biểu đồ tròn (Phân bổ số lần mượn theo thể loại) -->
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title text-secondary"><i class="bi bi-pie-chart-fill"></i> Phân bổ Số lần mượn</h5>
                    <canvas id="theLoaiChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>


    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Biểu đồ tròn (Tỷ lệ mượn theo thể loại)
    var ctxPie = document.getElementById('theLoaiChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: <?= $labelsPie ?>,  // Nhãn (Thể loại sách)
            datasets: [{
                label: 'Số lần mượn',
                data: <?= $dataPie ?>,  // Dữ liệu số lần mượn theo thể loại
                backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9f40'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>



<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>