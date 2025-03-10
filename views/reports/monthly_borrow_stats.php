<?php
$title = "Thống kê sách mượn trong tháng";
ob_start();
?>

<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-center position-relative my-4 no-print">
        <a href="/reports" class="btn btn-outline-secondary position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="text-center  text-primary "><i class="bi bi-bar-chart-line"></i> Thống kê Sách Mượn trong Tháng</h2>
        <button class="btn btn-success position-absolute  end-0" onclick="printReport()">
            <i class="bi bi-printer"></i> In Báo Cáo
        </button>
    </div>
    <div class="d-none" id="printTitle"> 
        <h2 class="text-center">BÁO CÁO THỐNG KÊ SÁCH MƯỢN THÁNG <?php echo date('m/Y'); ?></h2>
    </div>

    <!-- Thông tin tổng quan -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body text-center">
            <h5 class="card-title text-secondary"><i class="bi bi-journal-bookmark-fill"></i> Tổng số sách đã mượn</h5>
            <p class="display-5 fw-bold text-success"><?php echo $stats['SoSachMuon']; ?> quyển</p>
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
                            <th class="text-start">Thể loại</th> <!-- Thêm cột Thể loại -->
                            <th>Số lần mượn</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $book): ?>
                        <tr>
                            <td class="text-center"><?php echo $book['ma_sach']; ?></td>
                            <td class="text-start fw-medium"><?php echo $book['ten_sach']; ?></td>
                            <td class="text-start fst-italic"><?php echo $book['ten_tac_gia']; ?></td>
                            <td class="text-start"><?php echo $book['ten_the_loai']; ?></td> <!-- Hiển thị thể loại -->
                            <td class="text-center fs-5 text-primary"><strong><?php echo $book['so_lan_muon']; ?></strong></td>
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
    #borrowChart {
        max-height: 300px; /* Giới hạn chiều cao */
    }

    @media print {
        body {
            margin: 20mm 15mm;
            font-size: 14px;
        }
        .container {
            width: 100%;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid black !important;
            padding: 8px !important;
            font-size: 12px;
        }
        .btn {
            display: none;
        }
        .no-print{
            display: none !important;
        }
        #printTitle {
            display: block !important;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        #borrowChart{
            max-height: 300px;
        }

    }
</style>

<!-- Thêm thư viện Chart.js -->
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
        document.body.setAttribute('data-print-time', new Date().toLocaleString());
        window.print();
    }
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
