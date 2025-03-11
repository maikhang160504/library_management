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

?>

<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-center position-relative my-4 no-print">
        <a href="/reports" class="btn btn-outline-secondary position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="text-center text-primary "><i class="bi bi-bar-chart-line"></i> Thống kê Khoản Phạt - <?= $filterText ?></h2>
        <button class="btn btn-success position-absolute end-0" onclick="printReport()">
            <i class="bi bi-printer"></i> In Báo Cáo
        </button>
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
                            <th>Mã Độc Giả</th>
                            <th class="text-start">Tên Độc Giả</th>
                            <th>Ngày hết hạn</th>
                            <th>Ngày trả sách</th>
                            <th>Số tiền phạt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($penalties)) : ?>
                            <?php foreach ($penalties as $penalty): ?>
                                <tr>
                                    <td class="text-center"><?= $penalty['ma_doc_gia'] ?></td>
                                    <td class="text-start fw-medium"><?= $penalty['ten_doc_gia'] ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($penalty['ngay_het_han'])) ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($penalty['ngay_tra_sach'])) ?></td>
                                    <td class="text-center text-danger"><?= number_format($penalty['tien_phat'], 0, ',', '.') ?> VND</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Không có dữ liệu tiền phạt.</td>
                            </tr>
                        <?php endif; ?>

                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <!-- Biểu đồ thống kê -->
    <div class="card shadow-sm mt-4 border-0 no-print">
        <div class="card-body">
            <h5 class="card-title text-secondary"><i class="bi bi-graph-up"></i> Biểu đồ Thống kê Phạt</h5>
            <canvas id="penaltyChart"></canvas>
        </div>
    </div>
</div>

<style>
    #penaltyChart {
        max-height: 300px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('penaltyChart').getContext('2d');
        var penaltyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?php foreach ($penalties as $penalty) {
                                echo '"' . addslashes($penalty['ten_doc_gia']) . '",';
                            } ?>],
                datasets: [{
                    label: 'Số tiền phạt',
                    data: [<?php foreach ($penalties as $penalty) {
                                echo $penalty['tien_phat'] . ',';
                            } ?>],
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 10000
                        }
                    }
                }
            }
        });
    });

    function printReport() {
        window.print();
    }
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>