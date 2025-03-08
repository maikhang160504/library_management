<?php
$title = "Xác nhận trả sách";
ob_start();
?>
<div class="">
    <h2>Xác nhận trả sách</h2>
    <form method="POST" action="/returns/return">
        <div class="mb-3">
            <label for="ma_ctpm" class="form-label">Mã chi tiết phiếu mượn</label>
            <input type="text" class="form-control" id="ma_ctpm" name="ma_ctpm" required>
        </div>
        <div class="mb-3">
            <label for="ngay_tra_sach" class="form-label">Ngày trả sách</label>
            <input type="date" class="form-control" id="ngay_tra_sach" name="ngay_tra_sach" required>
        </div>
        <button type="submit" class="btn btn-primary">Xác nhận trả sách</button>
    </form>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>