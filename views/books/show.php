<?php
$title = "Chỉnh sửa sách";
ob_start();
?>
<div class="container">
    <h2>Chỉnh sửa sách</h2>

    <form method="POST" action="/books/update">
        <input type="hidden" name="ma_sach" value="<?php echo htmlspecialchars($book['ma_sach']); ?>">

        <div class="mb-3">
            <label for="ten_sach">Tên sách</label>
            <input type="text" name="ten_sach" class="form-control" value="<?php echo htmlspecialchars($book['ten_sach']); ?>">
        </div>

        <div class="mb-3">
            <label for="ten_tac_gia">Tác giả</label>
            <input type="text" name="ten_tac_gia" class="form-control" value="<?php echo htmlspecialchars($book['ten_tac_gia']); ?>">
        </div>

        <div class="mb-3">
            <label for="ten_the_loai">Thể loại</label>
            <input type="text" name="ten_the_loai" class="form-control" value="<?php echo htmlspecialchars($book['ten_the_loai']); ?>">
        </div>

        <div class="mb-3">
            <label for="nam_xuat_ban">Năm xuất bản</label>
            <input type="number" name="nam_xuat_ban" class="form-control" value="<?php echo htmlspecialchars($book['nam_xuat_ban']); ?>">
        </div>

        <div class="mb-3">
            <label for="nha_xuat_ban">Nhà xuất bản</label>
            <input type="text" name="nha_xuat_ban" class="form-control" value="<?php echo htmlspecialchars($book['nha_xuat_ban']); ?>">
        </div>

        <div class="mb-3">
            <label for="so_luong">Số lượng</label>
            <input type="number" name="so_luong" class="form-control" value="<?php echo htmlspecialchars($book['so_luong']); ?>">
        </div>

        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="/books" class="btn btn-secondary">Hủy</a>
    </form>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
