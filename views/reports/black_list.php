<?php
$title = "Danh sách đen";
ob_start();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
    <div class="card shadow-sm mt-4 border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center my-4">
                <h2 class="card-title text-danger fs-4 fs-md-3">
                    <i class="bi bi-exclamation-triangle-fill"></i> Danh Sách Đen
                </h2>
                <a href="?export=blacklist" class="btn btn-danger">
                    <i class="bi bi-file-earmark-excel-fill"></i> Xuất Excel
                </a>
            </div>
            <div class="table-responsive">
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
        </div>
    </div>
</div>


<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php'; ?>