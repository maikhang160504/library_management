<?php
$title = "Thống kê Sách Mượn";
ob_start();
$filter = $_GET['filter'] ?? 'this_month'; // Mặc định là tháng này
$filterText = [
    'today' => 'Hôm nay',
    'this_week' => 'Tuần này',
    'this_month' => 'Tháng này',
    'this_year' => 'Năm nay'
][$filter];
?>

<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-center position-relative my-4 no-print">
        <a href="/reports" class="btn btn-outline-secondary position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="text-center text-primary "><i class="bi bi-bar-chart-line"></i> Thống kê Sách Mượn - <?= $filterText ?></h2>
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
            <h5 class="card-title text-secondary"><i class="bi bi-journal-bookmark-fill"></i> Tổng số sách đã mượn</h5>
            <p class="display-5 fw-bold text-success"><?= $total_borrow ?> quyển</p>
        </div>
    </div>

    <!-- Danh sách sách được mượn -->
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
                            <td class="text-center fs-5 text-primary"><strong><?= $book['so_lan_muon'] ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Biểu đồ thống kê -->
    <div class="card shadow-sm mt-4 border-0 no-print">
        <div class="card-body">
            <h5 class="card-title text-secondary"><i class="bi bi-graph-up"></i> Biểu đồ Thống kê</h5>
            <canvas id="borrowChart"></canvas>
        </div>
    </div>
</div>

<style>
    #borrowChart{
        max-height: 300px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var ctx = document.getElementById('borrowChart').getContext('2d');
        var borrowChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?php foreach ($details as $book) { echo '"' . addslashes($book['ten_sach']) . '",'; } ?>],
                datasets: [{
                    label: 'Số lần mượn',
                    data: [<?php foreach ($details as $book) { echo $book['so_lan_muon'] . ','; } ?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
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