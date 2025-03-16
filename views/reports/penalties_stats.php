<?php
$title = "Thống kê Khoản Phạt";
ob_start();
$filter = $_GET['filter'] ?? 'this_month';
$filterText = [
    'today' => 'Hôm nay',
    'this_week' => 'Tuần này',
    'this_month' => 'Tháng này',
    'this_year' => 'Năm nay'
][$filter] ?? 'Tháng này';

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

<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-center position-relative my-4 no-print">
        <a href="/reports" class="btn btn-outline-secondary position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="text-center text-primary"><i class="bi bi-bar-chart-line"></i> Thống kê Khoản Phạt - <?= $filterText ?></h2>
        <a href="?export=excel&filter=<?= $filter ?>" class="btn btn-success position-absolute end-0">
            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
        </a>
    </div>


    <!-- Bộ lọc thống kê -->
    <div class="d-flex justify-content-center gap-2 mb-4">
        <a href="?filter=today" class="btn <?= $filter == 'today' ? 'btn-primary' : 'btn-outline-primary' ?>">Hôm nay</a>
        <a href="?filter=this_week" class="btn <?= $filter == 'this_week' ? 'btn-primary' : 'btn-outline-primary' ?>">Tuần này</a>
        <a href="?filter=this_month" class="btn <?= $filter == 'this_month' ? 'btn-primary' : 'btn-outline-primary' ?>">Tháng này</a>
        <a href="?filter=this_year" class="btn <?= $filter == 'this_year' ? 'btn-primary' : 'btn-outline-primary' ?>">Năm nay</a>
    </div>


    <!-- Thông tin tổng quan -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body text-center">
            <h5 class="card-title text-secondary"><i class="bi bi-journal-bookmark-fill"></i> Tổng số tiền phạt</h5>
            <?php $total_penalty = $total_penalty ?? 0; ?>
            <p class="display-5 fw-bold text-danger"><?= number_format($total_penalty, 0, ',', '.') ?> VND</p>
        </div>
    </div>

    <!-- Danh sách khoản phạt -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title text-secondary"><i class="bi bi-credit-card"></i> Chi tiết Khoản Phạt</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>STT</th>
                            <th>Mã phiếu mượn</th>
                            <th>Mã Độc Giả</th>
                            <th class="text-start">Tên Độc Giả</th>
                            <th>Ngày hết hạn</th>
                            <th>Ngày trả sách</th>
                            <th>Số tiền phạt</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                        <?php if (!empty($penalties)) : ?>
                            <?php
                            // Group penalties by borrow ticket ID (ma_phieu_muon)
                            $groupedPenalties = [];
                            foreach ($penalties as $penalty) {
                                $groupedPenalties[$penalty['ma_phieu_muon']][] = $penalty;
                            }
                            ?>
                            <?php 
                        $perPage = 10;
                        $stt = ($currentPage - 1) * $perPage + 1; 
                    ?>
                            <?php foreach ($groupedPenalties as $ticketId => $penaltyGroup): ?>
                                <tr>
                                    <td> <?php echo(htmlspecialchars($stt))?></td>
                                    <td class="text-center"><?= $ticketId ?></td>
                                    <td class="text-center"><?= $penaltyGroup[0]['ma_doc_gia'] ?></td>
                                    <td class="text-start fw-medium"><?= $penaltyGroup[0]['ten_doc_gia'] ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($penaltyGroup[0]['ngay_het_han'])) ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($penaltyGroup[0]['ngay_tra_sach'])) ?></td>
                                    <td class="text-center text-danger">
                                        <?php
                                        $totalPenalty = array_sum(array_column($penaltyGroup, 'tien_phat'));
                                        echo number_format($totalPenalty, 0, ',', '.') . ' VND';
                                        ?>
                                    </td>
                                </tr>
                                <?php $stt++; ?>
                            <?php endforeach; ?>
                            
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Không có dữ liệu tiền phạt.</td> <!-- Adjusted colspan to 6 -->
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
        <?php if ($totalPages > 1 ): ?>
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

    <!-- Biểu đồ thống kê -->
    <div class="card shadow-sm mt-4 border-0 no-print">
        <div class="card-body">
            <h5 class="card-title text-secondary"><i class="bi bi-graph-up"></i> Biểu đồ Thống kê Phạt theo mã độc giả</h5>
            <canvas id="penaltyChart"></canvas>
        </div>
    </div>
</div>

<style>
    #penaltyChart {
        max-height: 300px;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
            font-size: 14px;
        }

        .table th,
        .table td {
            border: 1px solid black !important;
            padding: 8px !important;
            text-align: center !important;
        }

        .table thead {
            background-color: #000 !important;
            color: white !important;
        }

        body::before {
            content: "Thống kê Khoản Phạt - <?= $filterText ?> - In lúc: " attr(data-print-time);
            display: block;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }

        @page {
            size: A4 landscape;
            margin: 20mm;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('penaltyChart').getContext('2d');
    var penaltyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chartLabels); ?>,
            datasets: [{
                label: 'Tổng tiền phạt',
                data: <?php echo json_encode($chartData); ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>