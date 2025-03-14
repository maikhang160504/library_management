<?php
$title = "Thống kê sách";
ob_start();
$categories = [];
foreach ($booksDetail as $book) {
    $category = $book['ten_the_loai'];
    if (!isset($categories[$category])) {
        $categories[$category] = 0;
    }
    $categories[$category] += $book['so_luong'];
}

// Chuẩn bị dữ liệu cho biểu đồ
$labels = array_keys($categories); // Thể loại sách
$data = array_values($categories); // Tổng số sách theo thể loại
?>
<div class="container mt-4">
    <!-- Nút quay lại -->
        <div class="d-flex">
            <div class="col-5">
                <a href="/reports" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left-circle"></i> Quay lại
                </a>
            </div>
            <div class="col-7">
                <h2 class="text-center mb-4 d-inline-block">Thống kê sách</h2>
            </div>
        </div>

    

    <!-- Form chọn kiểu thống kê (sử dụng POST) -->
    <div class="d-flex justify-content-between align-items-center mb-4">
    <form action="/reports/statistics" method="POST" class="row g-3 mb-0 col-12 d-flex align-items-center">
        <div class="col-auto">
            <label for="month" class="col-form-label">Chọn tháng:</label>
        </div>
        <div class="col-auto">
            <select name="month" id="month" class="form-select">
                <option value="">-- Tất cả tháng --</option>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>" <?= ($month == $i) ? 'selected' : '' ?>>Tháng <?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="col-auto">
            <label for="year" class="col-form-label">Chọn năm:</label>
        </div>
        <div class="col-auto">
            <select name="year" id="year" class="form-select">
                <option value="">-- Tất cả năm --</option>
                <?php 
                $currentYear = date('Y');
                for ($y = $currentYear; $y >= ($currentYear - 10); $y--): ?>
                    <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>>Năm <?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="col-auto">
            <label for="category" class="col-form-label">Thể loại:</label>
        </div>
        <div class="col-auto">
            <select name="category" id="category" class="form-select">
                <option value="">Tất cả</option>
                <?php foreach ($categoriesList as $cat): // Sử dụng 1 mảng riêng cho dropdown ?>
                    <option value="<?= $cat['ma_the_loai'] ?>" <?= (isset($categoryId) && $categoryId == $cat['ma_the_loai']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['ten_the_loai']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Lọc</button>
        </div>
        <div class="col-auto">
            <a href="/reports/exportExcelStatistic?month=<?= $month ?>&year=<?= $year ?>&category=<?= $categoryId ?>" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Xuất Excel
            </a>
        </div>
    </form>
    
</div>

    </div>



    <!-- Card chứa bảng thống kê chi tiết -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <!-- Bảng thống kê chi tiết -->
            <table class="table table-bordered table-striped table-hover text-start">
                <thead class="table-dark">
                    <tr>
                        <th class="text-start">Mã sách</th>
                        <th class="text-start" style="width: 30%;">Tên sách</th>
                        <th class="text-start">Tác giả</th>
                        <th class="text-start">Thể loại</th>
                        <th class="text-start">Số lượng</th>
                        <th class="text-start">Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($booksDetail)): ?>
                        <?php foreach ($booksDetail as $book): ?>
                            <tr>
                                <td class="text-start"><?= htmlspecialchars($book['ma_sach']); ?></td>
                                <td class="text-start" style="word-wrap: break-word;"><?= htmlspecialchars($book['ten_sach']); ?></td>
                                <td class="text-start"><?= htmlspecialchars($book['ten_tac_gia']); ?></td>
                                <td class="text-start"><?= htmlspecialchars($book['ten_the_loai']); ?></td>
                                <td class="text-start"><?= htmlspecialchars($book['so_luong']); ?></td>
                                <td class="text-start"><?= htmlspecialchars($book['period']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-start">Không có dữ liệu thống kê.</td>
                        </tr>
                    <?php endif; ?>
                    <tr class="table-info fw-bold">
                        <td colspan="4" class="text-start">Tổng cộng</td>
                        <td class="text-start"><?= htmlspecialchars($total); ?></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Biểu đồ thống kê -->
    <div class="card shadow-sm mt-4">
    <div class="card-body">
        <h5 class="card-title text-primary">📊 Biểu đồ Sách Nhập theo thể</h5>
        <canvas id="booksChart" style="max-height: 300px;"></canvas>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Dữ liệu cho biểu đồ
let labels = <?= json_encode($labels); ?>; // Thể loại sách
let data = <?= json_encode($data); ?>; // Số lượng sách theo thể loại

// Tạo biểu đồ
var ctx = document.getElementById('booksChart').getContext('2d');
var booksChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels, // Thể loại sách
        datasets: [{
            label: 'Số lượng sách',
            data: data, // Tổng số sách theo thể loại
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return tooltipItem.raw + ' sách';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
