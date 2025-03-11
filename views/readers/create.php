<?php
$title = "Thêm độc giả";
ob_start();
?>
<div class="container">
    <div class="">
        <h2 class="text-center mb-4">Thêm độc giả</h2>

        <div class="mt-3 mb-3">
            <a href="/readers" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <form method="POST" action="/readers/store">

            <div class="mb-3">
                <label for="ten_doc_gia" class="form-label">Tên độc giả</label>
                <input type="text" class="form-control" id="ten_doc_gia" name="ten_doc_gia" required placeholder="Nhập tên độc giả">
            </div>

            <div class="mb-3">
                <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai" required placeholder="Nhập số điện thoại">
            </div>

            <div class="mb-3">
                <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                <input type="date" class="form-control" id="ngay_sinh" name="ngay_sinh" required placeholder="Ngày sinh">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="Nhập email">
            </div>

            <button type="submit" class="btn btn-primary">Thêm độc giả</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>