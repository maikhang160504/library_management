<?php
$title = "Tạo độc giả";
ob_start();
?>

<div class="">
    <h2>Tạo độc giả</h2>
    <form method="POST" action="/readers/store">
        <div class="mb-3">
            <label for="ten_doc_gia" class="form-label">Tên độc giả</label>
            <input type="text" class="form-control" id="ten_doc_gia" name="ten_doc_gia" required placeholder="Nhập tên độc giả">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required placeholder="Nhập email">
        </div>

        <div class="mb-3">
            <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai" required placeholder="Nhập số điện thoại">
        </div>

        <div class="mb-3">
            <label for="dia_chi" class="form-label">Địa chỉ</label>
            <input type="text" class="form-control" id="dia_chi" name="dia_chi" required placeholder="Nhập địa chỉ">
        </div>

        <button type="submit" class="btn btn-primary">Tạo độc giả</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
