<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    $selectedMonth = $_GET['month'];
    $selectedYear = $_GET['year'];
    $fileName = "thong_ke_sach_muon_{$selectedMonth}_{$selectedYear}.xlsx";

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Mã phiếu mượn')
        ->setCellValue('B1', 'Mã độc giả')
        ->setCellValue('C1', 'Mã sách')
        ->setCellValue('D1', 'Mã tác giả')
        ->setCellValue('E1', 'Ngày mượn')
        ->setCellValue('F1', 'Ngày trả dự kiến')
        ->setCellValue('G1', 'Ngày trả thực tế')
        ->setCellValue('H1', 'Trạng thái')
        ->setCellValue('I1', 'Tiền phạt');
    // Bỏ cột "Số lần mượn" (J1)
    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
    ];
    $sheet->getStyle('A1:I1')->applyFromArray($headerStyle); // Chỉnh phạm vi từ J1 thành I1
    $row = 2;
    foreach ($reports as $book) {
        $sheet->setCellValue("A$row", $book['ma_phieu_muon'])
            ->setCellValue("B$row", $book['ma_doc_gia'])
            ->setCellValue("C$row", $book['ma_sach'])
            ->setCellValue("D$row", $book['ma_tac_gia'])
            ->setCellValue("E$row", $book['ngay_muon'])
            ->setCellValue("F$row", $book['ngay_tra_du_kien'])
            ->setCellValue("G$row", $book['ngay_tra_thuc_te'])
            ->setCellValue("H$row", $book['trang_thai'])
            ->setCellValue("I$row", $book['tien_phat']);
        // Bỏ cột "Số lần mượn"
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    $writer->save('php://output');
    exit;
}

$title = "Thống kê sách mượn theo tháng/năm";
ob_start();
?>

<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-center position-relative my-4">
        <a href="/reports" class="btn btn-outline-secondary position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="text-center text-primary">
            <i class="bi bi-bar-chart-line"></i> Thống kê Sách Mượn - Tháng <?php echo $selectedMonth; ?> / <?php echo $selectedYear; ?>
        </h2>
        <a href="?export=excel&month=<?php echo $selectedMonth; ?>&year=<?php echo $selectedYear; ?>" class="btn btn-success position-absolute end-0">
            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
        </a>
    </div>

    <!-- Bộ lọc tháng & năm -->
    <form action="/reports/borrow-return-report" method="GET" class="row g-2 align-items-center justify-content-center">
        <div class="col-auto">
            <label for="month" class="form-label fw-bold">Tháng</label>
            <select id="month" name="month" class="form-select">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?php echo $m; ?>" <?php if ($m == $selectedMonth) echo "selected"; ?>>Tháng <?php echo $m; ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="col-auto">
            <label for="year" class="form-label fw-bold">Năm</label>
            <input type="number" id="year" name="year" class="form-control" min="2000" max="<?php echo date('Y'); ?>" value="<?php echo $selectedYear; ?>" required>
        </div>

        <div class="col-auto">
            <button type="submit" class="btn btn-primary mt-4"><i class="bi bi-funnel"></i> Lọc</button>
        </div>
    </form>

    <!-- Danh sách sách được mượn -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title text-secondary"><i class="bi bi-book-half"></i> Chi tiết Sách Mượn</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Mã phiếu mượn</th>
                            <th>Mã độc giả</th>
                            <th>Mã sách</th>
                            <th>Mã tác giả</th>
                            <th>Ngày mượn</th>
                            <th>Ngày trả dự kiến</th>
                            <th>Ngày trả thực tế</th>
                            <th>Trạng thái</th>
                            <th>Tiền phạt</th>
                            <!-- Bỏ cột "Số lần mượn" -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $book): ?>
                            <tr>
                                <td class="text-center"><?php echo $book['ma_phieu_muon']; ?></td>
                                <td class="text-center"><?php echo $book['ma_doc_gia']; ?></td>
                                <td class="text-center"><?php echo $book['ma_sach']; ?></td>
                                <td class="text-center"><?php echo $book['ma_tac_gia']; ?></td>
                                <td class="text-center"><?php echo $book['ngay_muon']; ?></td>
                                <td class="text-center"><?php echo $book['ngay_tra_du_kien']; ?></td>
                                <td class="text-center"><?php echo $book['ngay_tra_thuc_te']; ?></td>
                                <td class="text-center"><?php echo $book['trang_thai']; ?></td>
                                <td class="text-center text-danger fw-bold"><?php echo number_format($book['tien_phat'], 0, ',', '.'); ?> đ</td>
                                <!-- Bỏ cột "Số lần mượn" -->
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title text-secondary"><i class="bi bi-bar-chart"></i> Biểu đồ thống kê</h5>
            <div class="row">
                <div class="col-md-6">
                    <canvas id="barChart"></canvas>
                </div>
                <div class="col-md-6">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Thu nhỏ kích thước canvas của biểu đồ */
    #barChart,
    #pieChart {
        max-width: 500px;
        /* Kích thước tối đa */
        max-height: 300px;
        /* Chiều cao tối đa */
        width: 100%;
        /* Đảm bảo responsive */
        height: auto;
        margin: 0 auto;
        /* Căn giữa */
    }

   
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Lấy dữ liệu từ PHP
    // Lấy dữ liệu từ PHP
const reports = <?php echo json_encode($reports); ?>;

// ---- Biểu đồ cột: Số lần mượn của từng mã sách ----
const bookCounts = {}; // Đếm số lần mượn theo mã sách
reports.forEach(report => {
    bookCounts[report.ma_sach] = (bookCounts[report.ma_sach] || 0) + 1;
});
const barLabels = Object.keys(bookCounts);
const barData = Object.values(bookCounts);

const barCtx = document.getElementById('barChart').getContext('2d');
new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: barLabels,
        datasets: [{
            label: 'Số lượt mượn',
            data: barData,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true, title: { display: true, text: 'Số lượt mượn' } },
            x: { title: { display: true, text: 'Mã sách' } }
        }
    }
});

// ---- Biểu đồ tròn: Tỷ lệ trạng thái phiếu mượn ----
const statusCounts = { "Đúng hạn": 0, "Trả trễ": 0, "Chưa trả": 0 };
reports.forEach(report => {
    if (!report.ngay_tra_thuc_te) {
        statusCounts["Chưa trả"]++;
    } else if (report.ngay_tra_thuc_te > report.ngay_tra_du_kien) {
        statusCounts["Trả trễ"]++;
    } else {
        statusCounts["Đúng hạn"]++;
    }
});
const pieLabels = Object.keys(statusCounts);
const pieData = Object.values(statusCounts);

const pieCtx = document.getElementById('pieChart').getContext('2d');
new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: pieLabels,
        datasets: [{
            label: 'Trạng thái',
            data: pieData,
            backgroundColor: [
                'rgba(75, 192, 192, 0.6)', // Đúng hạn (Màu xanh)
                'rgba(255, 159, 64, 0.6)', // Trả trễ (Màu cam)
                'rgba(255, 99, 132, 0.6)'  // Chưa trả (Màu đỏ)
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(255, 99, 132, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Tỷ lệ trạng thái trả sách' }
        }
    }
});

</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>