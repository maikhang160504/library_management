<?php
$title = "Danh sách đen";
ob_start();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


// Xuất file Excel nếu có yêu cầu
if (isset($_GET['export']) && $_GET['export'] == 'blacklist') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Tiêu đề cột
    $sheet->setCellValue('A1', 'Mã Độc Giả');
    $sheet->setCellValue('B1', 'Họ Tên');
    $sheet->setCellValue('C1', 'Số Lần Bị Phạt');
    $sheet->setCellValue('D1', 'Tổng Tiền Phạt');

    // Đổ dữ liệu vào Excel
    $row = 2;
    foreach ($blacklist as $reader) {
        $sheet->setCellValue('A' . $row, $reader['ma_doc_gia']);
        $sheet->setCellValue('B' . $row, $reader['ten_doc_gia']);
        $sheet->setCellValue('C' . $row, $reader['so_lan_bi_phat']);
        $sheet->setCellValue('D' . $row, number_format($reader['tong_tien_phat'], 0, ',', '.') . ' VND');
        $row++;
    }

    // Xuất file Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="danh_sach_den.xlsx"');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>

<div class="container my-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-center position-relative my-4">
                <a href="/reports" class="btn btn-outline-secondary position-absolute start-0">
                    <i class="bi bi-arrow-left-circle"></i> Quay lại
                </a>
                <h2 class="text-center text-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i> Danh Sách Đen
                </h2>
                <a href="?export=blacklist" class="btn btn-success position-absolute end-0">
                    <i class="bi bi-file-earmark-excel-fill"></i> Xuất Excel
                </a>
            </div>

            <!-- Bảng danh sách trước -->
            <div class="table-responsive mb-4">
                <table class="table table-striped table-hover text-center align-middle">
                    <thead class="table-danger">
                        <tr>
                            <th>Mã Độc Giả</th>
                            <th>Tên Độc Giả</th>
                            <th>Số Lần Bị Phạt</th>
                            <th>Tổng Tiền Phạt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($blacklist as $reader): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= $reader['ma_doc_gia'] ?></span></td>
                                <td class="fw-bold"><?= $reader['ten_doc_gia'] ?></td>
                                <td class="text-danger fw-semibold"><?= $reader['so_lan_bi_phat'] ?> lần</td>
                                <td class="text-danger fw-semibold"><?= number_format($reader['tong_tien_phat'], 0, ',', '.') ?> VND</td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($blacklist)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Không có độc giả nào trong danh sách đen.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Chọn số lượng hiển thị -->
            <div class="mb-3">
                <label for="limitSelect" class="fw-bold">Số lượng độc giả hiển thị:</label>
                <select id="limitSelect" class="form-select w-auto d-inline-block">
                    <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>Top 10</option>
                    <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>Top 20</option>
                    <option value="all" <?= $limit == null ? 'selected' : '' ?>>Tất cả</option>
                </select>
            </div>

            <!-- Biểu đồ sau -->
            <canvas id="barChart" class="my-4"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.getElementById('limitSelect').addEventListener('change', function () {
    window.location.href = "?limit=" + this.value;
});

const labels = <?= json_encode(array_column($blacklist, 'ten_doc_gia')) ?>;
const soLanBiPhat = <?= json_encode(array_column($blacklist, 'so_lan_bi_phat')) ?>;
const tongTienPhat = <?= json_encode(array_column($blacklist, 'tong_tien_phat')) ?>;

const ctx = document.getElementById('barChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Số lần bị phạt',
            data: soLanBiPhat,
            backgroundColor: 'rgba(255, 99, 132, 0.8)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        indexAxis: 'y',
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
