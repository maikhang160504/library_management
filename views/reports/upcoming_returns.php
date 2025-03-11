<?php
$title = "Thống kê độc giả sắp đến hạn trả sách";
ob_start();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Xử lý bộ lọc nhập số ngày (mặc định là 3 ngày)
$days = isset($_GET['days']) ? (int) $_GET['days'] : 3;

// Xuất Excel nếu yêu cầu
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Tiêu đề cột
    $sheet->setCellValue('A1', 'Mã Phiếu Mượn');
    $sheet->setCellValue('B1', 'Mã Độc Giả');
    $sheet->setCellValue('C1', 'Họ Tên');
    $sheet->setCellValue('D1', 'Mã Sách');
    $sheet->setCellValue('E1', 'Tên Sách');
    $sheet->setCellValue('F1', 'Ngày Mượn');
    $sheet->setCellValue('G1', 'Ngày Trả Dự Kiến');

    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
    ];
    $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
    // Đổ dữ liệu vào Excel
    $row = 2;
    foreach ($upcomingReturns as $return) {
        $sheet->setCellValue('A' . $row, $return['ma_phieu_muon']);
        $sheet->setCellValue('B' . $row, $return['ma_doc_gia']);
        $sheet->setCellValue('C' . $row, $return['ten_doc_gia']);
        $sheet->setCellValue('D' . $row, $return['ma_sach']);
        $sheet->setCellValue('E' . $row, $return['ten_sach']);
        $sheet->setCellValue('F' . $row, $return['ngay_muon']);
        $sheet->setCellValue('G' . $row, $return['ngay_tra_du_kien']);
        $row++;
    }

    // Xuất file Excel
    $filename = "DocGiaSapDenHanTraSach.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>

<div class="container mt-4">
    <!-- Tiêu đề và nút điều hướng -->
    <div class="d-flex align-items-center justify-content-center position-relative my-4">
        <a href="/reports" class="btn btn-outline-secondary position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="text-center text-primary">
            <i class="bi bi-calendar-event"></i> Thống kê Độc Giả Sắp Đến Hạn Trả Sách
        </h2>
        <a href="?export=excel&days=<?php echo $days; ?>" class="btn btn-success position-absolute end-0">
            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
        </a>
    </div>

    <!-- Bộ lọc nhập số ngày -->
    <form action="/reports/upcoming-returns" method="GET">
        <div class="row justify-content-center mb-3">
            <div class="col-auto">
                <label for="days" class="form-label fw-bold">Số ngày sắp đến hạn:</label>
            </div>
            <div class="col-auto">
                <input type="number" class="form-control" name="days" id="days" value="<?php echo $days; ?>" min="1">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Lọc</button>
            </div>
        </div>
    </form>

    <!-- Bảng danh sách độc giả sắp đến hạn trả sách -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title text-secondary"><i class="bi bi-list-check"></i> Danh sách độc giả</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Mã Phiếu Mượn</th>
                            <th>Mã Độc Giả</th>
                            <th>Họ Tên</th>
                            <th>Mã Sách</th>
                            <th>Tên Sách</th>
                            <th>Ngày Mượn</th>
                            <th>Ngày Trả Dự Kiến</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($upcomingReturns)): ?>
                            <?php foreach ($upcomingReturns as $return): ?>
                                <tr>
                                    <td class="text-center"><?php echo $return['ma_phieu_muon']; ?></td>
                                    <td class="text-center"><?php echo $return['ma_doc_gia']; ?></td>
                                    <td><?php echo $return['ten_doc_gia']; ?></td>
                                    <td class="text-center"><?php echo $return['ma_sach']; ?></td>
                                    <td><?php echo $return['ten_sach']; ?></td>
                                    <td class="text-center"><?php echo $return['ngay_muon']; ?></td>
                                    <td class="text-center text-danger fw-bold"><?php echo $return['ngay_tra_du_kien']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Không có độc giả nào sắp đến hạn trả sách.</td>
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
include __DIR__ . '/../layouts/main.php';
?>