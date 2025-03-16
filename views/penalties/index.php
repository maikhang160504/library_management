<?php

use App\Models\Penalty;

$title = "Danh sách phí phạt";
ob_start();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Tiêu đề cột
    $sheet->setCellValue('A1', 'Mã Độc Giả');
    $sheet->setCellValue('B1', 'Họ Tên');
    $sheet->setCellValue('C1', 'Ngày hết hạn');
    $sheet->setCellValue('D1', 'Ngày trả sách');
    $sheet->setCellValue('E1', 'Số tiền phạt');

    // Định dạng tiêu đề
    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
    ];
    $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

    // Đổ dữ liệu vào Excel
    $row = 2;
    $totalPenalty = 0;
    if (!empty($penalties)) {
        foreach ($penalties as $penalty) {
            $sheet->setCellValue('A' . $row, $penalty['ma_doc_gia']);
            $sheet->setCellValue('B' . $row, $penalty['ten_doc_gia']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($penalty['ngay_het_han'])));
            $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($penalty['ngay_tra_sach'])));
            $sheet->setCellValue('E' . $row, $penalty['tien_phat']);
            $totalPenalty += $penalty['tien_phat'];
            $row++;
        }
    }

    // Tổng tiền phạt
    $sheet->setCellValue('D' . $row, 'Tổng cộng:');
    $sheet->setCellValue('E' . $row, $totalPenalty);
    $sheet->getStyle('D' . $row . ':E' . $row)->applyFromArray($headerStyle);

    // Xuất file Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="thong_ke_khoan_phat.xlsx"');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý phí phạt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Quản lý phí phạt</h2>
            <div>
                <a href="?export=excel" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Xuất Excel
                </a>
            </div>
        </div>
        <!-- Tìm kiếm -->
        <form action="/penalty/search" method="GET" class="row g-3 align-items-end mb-3">
            <div class="col-md-5">
                <label for="search" class="form-label">Tìm kiếm theo mã phiếu mượn, tên độc giả</label>
                <input type="text" id="search" name="search" class="form-control"
                    placeholder="Nhập từ khóa..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
                <a href="/penalties" class="btn btn-secondary">Xóa bộ lọc</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width:5%">STT</th>
                        <th style="width:10%">Mã phiếu mượn</th>
                        <th style="width:10%">Mã độc giả</th>
                        <th style="width:15%">Tên độc giả</th>
                        <th style="width:10%">Ngày hết hạn</th>
                        <th style="width:10%">Ngày trả thực tế</th>
                        <th style="width:10%">Số tiền phạt</th>
                        <th style="width:10%">Thanh toán</th>
                        <th style="width:10%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $perPage = 10;
                    $stt = ($currentPage - 1) * $perPage + 1; ?>
                    <?php
                    if (empty($penalties)): ?>
                        <tr>
                            <td colspan="12" class="text-center">Không có kết quả tìm kiếm.</td>
                        </tr>
                        <?php else:
                        $groupedPenalties = [];

                        // Nhóm theo mã phiếu mượn
                        foreach ($penalties as $penalty) {
                            $ma_phieu_muon = $penalty['ma_phieu_muon'];

                            if (!isset($groupedPenalties[$ma_phieu_muon])) {
                                $groupedPenalties[$ma_phieu_muon] = $penalty;
                            } else {
                                $groupedPenalties[$ma_phieu_muon]['tien_phat'] += $penalty['tien_phat'];
                            }
                        }

                        // Hiển thị chỉ một dòng cho mỗi mã phiếu mượn
                        foreach ($groupedPenalties as $penalty): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($stt) ?></td>
                                <td><?php echo htmlspecialchars($penalty['ma_phieu_muon']) ?> </td>
                                <td><?php echo htmlspecialchars($penalty['ma_doc_gia']) ?> </td>
                                <td><?php echo htmlspecialchars($penalty['ten_doc_gia']) ?></td>
                                <td><?php echo htmlspecialchars($penalty['ngay_het_han']) ?></td>
                                <td><?php echo htmlspecialchars($penalty['ngay_tra_sach']) ?></td>
                                <td><?= number_format($penalty['tien_phat'], 0, ',', '.') ?> VND</td>
                                <td>
                                    <?php if ($penalty['trang_thai'] == 'Đã trả'): ?>
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Chưa thanh toán</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/readers/detail/<?php echo $penalty['ma_doc_gia']; ?>" class="btn btn-info btn-sm">Xem chi tiết</a>
                                </td>
                            </tr>
                            <?php $stt++; ?>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $currentPage - 1 ?>">&laquo;</a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $currentPage + 1 ?>">&raquo;</a>
                    </li>

                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>