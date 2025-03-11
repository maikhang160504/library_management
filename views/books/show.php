<?php
$title = "Chi tiết và chỉnh sửa sách";
ob_start();
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-body">
                    <h2 class="card-title text-center">Chỉnh sửa sách: <?php echo htmlspecialchars($book['ten_sach']); ?></h2>
                    <form method="POST" action="/books/update">
                        <div class="mb-3">
                            <label for="ten_sach" class="form-label">Tên sách</label>
                            <input type="text" class="form-control" id="ten_sach" name="ten_sach" value="<?php echo htmlspecialchars($book['ten_sach']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="ten_tac_gia" class="form-label">Tác giả</label>
                            <input type="text" class="form-control" id="ten_tac_gia" name="ten_tac_gia" value="<?php echo htmlspecialchars($book['ten_tac_gia']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="ten_the_loai" class="form-label">Thể loại</label>
                            <input type="text" class="form-control" id="ten_the_loai" name="ten_the_loai" value="<?php echo htmlspecialchars($book['ten_the_loai']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="nam_xuat_ban" class="form-label">Năm xuất bản</label>
                            <input type="number" class="form-control" id="nam_xuat_ban" name="nam_xuat_ban" value="<?php echo htmlspecialchars($book['nam_xuat_ban']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="nha_xuat_ban" class="form-label">Nhà xuất bản</label>
                            <input type="text" class="form-control" id="nha_xuat_ban" name="nha_xuat_ban" value="<?php echo htmlspecialchars($book['nha_xuat_ban']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="so_luong" class="form-label">Số lượng còn lại</label>
                            <input type="number" class="form-control" id="so_luong" name="so_luong" value="<?php echo htmlspecialchars($book['so_luong']); ?>">
                        </div>
                        <!-- Giữ lại mã sách để xác định bản ghi cần cập nhật -->
                        <input type="hidden" name="ma_sach" value="<?php echo htmlspecialchars($book['ma_sach']); ?>">
                        <button type="submit" class="btn btn-dark w-100">Lưu thay đổi</button>
                    </form>
                    <a href="/books" class="btn btn-secondary w-100 mt-2">Hủy</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
