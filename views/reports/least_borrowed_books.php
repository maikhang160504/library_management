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
    <div class="d-flex align-items-center justify-content-between my-4">
        <a href="/reports" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="mb-4">📉 Thống kê Sách ít được mượn</h2>
        <a href="?export=excel" class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
        </a>
    </div>

    <!-- Card container -->
    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Biểu đồ -->
            <canvas id="bookChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Table -->
    <div class="card mt-4 shadow-sm">
        <div class="card-body">
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Dữ liệu từ PHP để tạo biểu đồ
    var books = <?php echo json_encode($books); ?>;
    var bookLabels = books.map(function(book) {
        return book.ten_sach; // Tên sách
    });
    var bookData = books.map(function(book) {
        return book.so_luot_muon; // Số lượt mượn
    });

    // Cấu hình biểu đồ
    var ctx = document.getElementById('bookChart').getContext('2d');
    var bookChart = new Chart(ctx, {
        type: 'bar', // Loại biểu đồ: cột
        data: {
            labels: bookLabels, // Nhãn cho từng cột
            datasets: [{
                label: 'Số lượt mượn',
                data: bookData, // Dữ liệu: số lượt mượn của các sách
                backgroundColor: 'rgba(54, 162, 235, 0.2)', // Màu nền cột
                borderColor: 'rgba(54, 162, 235, 1)', // Màu viền cột
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false // Ẩn legend nếu không cần
                },
            },
            scales: {
                y: {
                    beginAtZero: true // Bắt đầu từ 0 cho trục y
                }
            }
        }
    });
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
