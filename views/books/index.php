<?php
$title = "Danh sách sách";
ob_start();
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

?>
<div class="container">
    <?php if (!empty($success)) : ?>
        <div class="alert alert-success alert-dismissible fade show text-start" role="alert">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    <?php endif; ?>

<!-- Header với các nút quản lý -->
<div class="d-flex justify-content-between align-items-center mb-4">
    
    <h2>Danh sách sách</h2>
    <div>
        <a href="/add" class="btn btn-primary me-2">Thêm sách</a>
        <a href="/borrows" class="btn btn-success me-2">Cập nhật sách</a>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-custom table-hover">
        <thead>
            <tr>
                <th>Mã sách</th>
                <th>Tên sách</th>
                <th>Tác giả</th>
                <th>Thể loại</th>
                <th>Số lượng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
            <tr>
                <td><?php echo $book['ma_sach']; ?></td>
                <td><?php echo $book['ten_sach']; ?></td>
                <td><?php echo $book['ten_tac_gia']; ?></td>
                <td><?php echo $book['ten_the_loai']; ?></td>
                <td><?php echo $book['so_luong']; ?></td>
                <td>
                    <a href="/books/<?php echo $book['ma_sach']; ?>" class="btn btn-sm btn-custom">Xem chi tiết</a>
                    <a href="" class="btn btn-sm btn-danger">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>