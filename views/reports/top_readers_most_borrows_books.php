<?php
$title = "Thống kê Mượn Sách";
ob_start();
?>

<div class="container mt-4">
    <!-- Tiêu đề & Nút quay lại -->
    <div class="d-flex align-items-center justify-content-center position-relative my-4">
        <a href="/reports" class="btn btn-outline-secondary px-4 py-2 position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class=" text-center flex-grow-1">
    📊 Thống kê Mượn Sách từ <?php echo isset($startDate) ? $startDate : '___'; ?> đến <?php echo isset($endDate) ? $endDate : '___'; ?>
</h2>
    </div>

    <!-- Bộ lọc ngày bắt đầu & ngày kết thúc -->
    <div class="row justify-content-center mt-4">
        <div class="col-md-4">
            <label for="startDate" class="form-label">Ngày bắt đầu:</label>
            <input type="date" id="startDate" class="form-control" value="<?php echo $startDate ?? ''; ?>">
        </div>
        <div class="col-md-4">
            <label for="endDate" class="form-label">Ngày kết thúc:</label>
            <input type="date" id="endDate" class="form-control" value="<?php echo $endDate ?? ''; ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button id="filterBtn" class="btn btn-primary w-100">Lọc thống kê</button>
        </div>
    </div>

    <!-- Độc giả mượn nhiều sách nhất -->
    <div class="card shadow-sm my-4">
        <div class="card-body">
            <h5 class="card-title text-primary">📌 Độc giả mượn nhiều sách nhất</h5>
            <table class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Mã độc giả</th>
                        <th>Tên độc giả</th>
                        <th>Số lượt mượn</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($readers as $reader): ?>
                        <tr>
                            <td><?php echo $reader['ma_doc_gia']; ?></td>
                            <td class="text-start"><?php echo $reader['ten_doc_gia']; ?></td>
                            <td class="fs-5 text-success"><strong><?php echo $reader['so_luot_muon']; ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sách được mượn nhiều nhất -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-primary">📚 Sách được mượn nhiều nhất</h5>
            <table class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Mã sách</th>
                        <th class="text-start">Tên sách</th>
                        <th>Số lượt mượn</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?php echo $book['ma_sach']; ?></td>
                            <td class="text-start"><?php echo $book['ten_sach']; ?></td>
                            <td class="fs-5 text-success"><strong><?php echo $book['so_luot_muon']; ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JavaScript: Xử lý lọc thống kê -->
<script>
document.getElementById("filterBtn").addEventListener("click", function() {
    var startDate = document.getElementById("startDate").value;
    var endDate = document.getElementById("endDate").value;

    if (!startDate || !endDate) {
        alert("Vui lòng chọn cả ngày bắt đầu và ngày kết thúc!");
        return;
    }

    if (startDate > endDate) {
        alert("Ngày bắt đầu không được lớn hơn ngày kết thúc!");
        return;
    }

    var url = "/reports/top-readers-most-borrowed-book?startDate=" + startDate + "&endDate=" + endDate;
    window.location.href = url;
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
