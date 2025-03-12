<?php
$title = "Thống kê sách";
ob_start();
?>
<div class="container mt-4">
    <h2 class="text-center">Thống kê sách theo <?= ($type === 'day') ? 'Ngày' : (($type === 'month') ? 'Tháng' : 'Năm'); ?></h2>

    <!-- Form chọn kiểu thống kê (sử dụng POST) -->
    <form action="/books/statistics" method="POST" class="d-flex justify-content-center align-items-center mb-3">
        <select name="type" class="form-select me-3" style="width: 200px;" onchange="this.form.submit()">
            <option value="day" <?= $type === 'day' ? 'selected' : '' ?>>Theo Ngày</option>
            <option value="month" <?= $type === 'month' ? 'selected' : '' ?>>Theo Tháng</option>
            <option value="year" <?= $type === 'year' ? 'selected' : '' ?>>Theo Năm</option>
        </select>
        <!-- Nút xuất Excel, giữ GET (bạn có thể chuyển sang POST nếu muốn) -->
        <a href="/books/exportStatistics?type=<?= $type; ?>" class="btn btn-success">Xuất Excel</a>
    </form>

    <!-- Bảng thống kê chi tiết -->
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th class="text-center">Mã sách</th>
                <th class="text-center">Tên sách</th>
                <th class="text-center">Tác giả</th>
                <th class="text-center">Thể loại</th>
                <th class="text-center">Số lượng</th>
                <th class="text-center"><?= ($type === 'day') ? 'Ngày' : (($type === 'month') ? 'Tháng' : 'Năm'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($booksDetail)): ?>
                <?php foreach ($booksDetail as $book): ?>
                    <tr>
                        <td class="text-center"><?= htmlspecialchars($book['ma_sach']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($book['ten_sach']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($book['ten_tac_gia']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($book['ten_the_loai']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($book['so_luong']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($book['period']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Không có dữ liệu thống kê.</td>
                </tr>
            <?php endif; ?>
            <tr class="table-info fw-bold">
                <td colspan="4" class="text-center">Tổng cộng</td>
                <td class="text-center"><?= htmlspecialchars($total); ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
