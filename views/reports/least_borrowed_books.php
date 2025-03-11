<?php
$title = "Thống kê sách ít được mượn";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Nếu có yêu cầu xuất Excel
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
    ];
    $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

    // Tiêu đề cột
    $sheet->setCellValue('A1', 'Mã sách');
    $sheet->setCellValue('B1', 'Tên sách');
    $sheet->setCellValue('C1', 'Tác giả');
    $sheet->setCellValue('D1', 'Số lượt mượn');

    // Đổ dữ liệu vào Excel
    $row = 2;
    foreach ($books as $book) {
        $sheet->setCellValue('A' . $row, $book['ma_sach']);
        $sheet->setCellValue('B' . $row, $book['ten_sach']);
        $sheet->setCellValue('C' . $row, $book['ten_tac_gia']);
        $sheet->setCellValue('D' . $row, $book['so_luot_muon']);
        $row++;
    }

    // Xuất file Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="thong_ke_sach_it_muon.xlsx"');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

ob_start();
?>
<div class="container mt-4">
<div class="d-flex align-items-center justify-content-center position-relative my-4 no-print">
        <a href="/reports" class="btn btn-outline-secondary position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="mb-4">📉 Thống kê Sách ít được mượn</h2>
        <a href="?export=excel" class="btn btn-success position-absolute end-0">
            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
        </a>
    </div>

    <table class="table table-bordered table-hover text-center">
        <thead class="table-dark">
            <tr>
                <th>Mã sách</th>
                <th class="text-start">Tên sách</th>
                <th class="text-start">Tác giả</th>
                <th>Số lượt mượn</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
            <tr>
                <td><?php echo $book['ma_sach']; ?></td>
                <td class="text-start"><?php echo $book['ten_sach']; ?></td>
                <td class="text-start"><?php echo $book['ten_tac_gia']; ?></td>
                <td><?php echo $book['so_luot_muon']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
