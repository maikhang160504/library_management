<?php
$title = "Thống kê đọcgiả mượn sách trong năm";
ob_start();
$year = date('Y'); // Lấy năm hiện tại
?>

<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-center position-relative my-4">
        <a href="/reports" class="btn btn-outline-secondary px-4 py-2 position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="text-center mb-4 no-print">📊 Thống kê đọc giả Mượn Sách trong Năm <?php echo $year; ?></h2>
        <button class="btn btn-success position-absolute end-0" onclick="printReport()">
            <i class="bi bi-printer"></i> In Báo Cáo
        </button>
    </div>
    <div class="d-none" id="printTitle"> 
        <h2 class="text-center">BÁO CÁO THỐNG KÊ ĐỌC GIẢ MƯỢN SÁCH NĂM <?php echo date('Y'); ?></h2>
    </div>

    <!-- Thông tin tổng quan -->
    <div class="card shadow-sm mb-4">
        <div class="card-body text-center">
            <h5 class="card-title text-primary">📌 Tổng số đọc giả đã mượn sách</h5>
            <p class="fs-3 fw-bold text-success"><?php echo $stats['SoDocGiaMuon']; ?> đọc giả</p>
        </div>
    </div>

    <!-- Danh sách đọcgiả mượn sách -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-primary">📋 Chi tiết đọc giả Mượn Sách</h5>
            <table class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Mã đọc giả</th>
                        <th class="text-start">Tên đọc giả</th>
                        <th>Số lần mượn</th>
                        <th class="text-start">Thể loại sách mượn nhiều nhất</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($details as $reader): ?>
                    <tr>
                        <td><?php echo $reader['ma_doc_gia']; ?></td>
                        <td class="text-start"><?php echo $reader['ten_doc_gia']; ?></td>
                        <td class="fs-5 text-success"><strong><?php echo $reader['so_lan_muon']; ?></strong></td>
                        <td class="text-start"><?php echo $reader['the_loai_muon_nhieu_nhat']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Biểu đồ thống kê -->
    <div class="card shadow-sm mt-4 no-print">
        <div class="card-body">
            <h5 class="card-title text-primary">📈 Biểu đồ Thống kê</h5>
            <canvas id="readerChart"></canvas>
        </div>
    </div>
</div>

<style>
    #readerChart {
        max-height: 300px;
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
        #readerChart{
            max-height: 300px;
        }
     
    }
</style>

<!-- Thêm thư viện Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('readerChart').getContext('2d');
    var readerChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [<?php foreach ($details as $reader) { echo '"' . addslashes($reader['ten_doc_gia']) . '",'; } ?>],
            datasets: [{
                label: 'Số lần mượn',
                data: [<?php foreach ($details as $reader) { echo $reader['so_lan_muon'] . ','; } ?>],
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    function printReport() {
        window.print();
    }
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
