<?php
$title = "Thêm độc giả";
ob_start();

// Lấy dữ liệu cũ nếu có lỗi
$oldData = $_SESSION['oldData'] ?? [];
$errors = $_SESSION['errors'] ?? [];

// Xóa lỗi sau khi hiển thị để không còn tồn tại sau reload
unset($_SESSION['errors']);
unset($_SESSION['oldData']);
?>
<div class="container">
    <div class="">
        <div class="d-flex align-items-center justify-content-center position-relative my-4">
            <a href="/readers" class="btn btn-outline-secondary position-absolute start-0">
                <i class="bi bi-arrow-left-circle"></i> Quay lại
            </a>
            <h2 class="text-center"><i class="bi bi-person-lines-fill"></i> Thêm độc giả</h2>
        </div>

        <form method="POST" action="/readers/store">
            <!-- Tên độc giả -->
            <div class="mb-3">
                <label for="ten_doc_gia" class="form-label">Tên độc giả</label>
                <input type="text" class="form-control <?= isset($errors['ten_doc_gia']) ? 'is-invalid' : '' ?>"
                    value="<?= htmlspecialchars($oldData['ten_doc_gia'] ?? '') ?>"
                    id="ten_doc_gia" name="ten_doc_gia" placeholder="Nhập tên độc giả">

                <div class="invalid-feedback"><?= $errors['ten_doc_gia'] ?? ''; ?></div>
            </div>

            <!-- Số điện thoại -->
            <div class="mb-3">
                <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control <?= isset($errors['so_dien_thoai']) ? 'is-invalid' : '' ?>"
                    value="<?= htmlspecialchars($oldData['so_dien_thoai'] ?? '') ?>"
                    id="so_dien_thoai" name="so_dien_thoai" placeholder="Nhập số điện thoại">

                <div class="invalid-feedback"><?= $errors['so_dien_thoai'] ?? ''; ?></div>
            </div>

            <!-- Ngày sinh -->
            <div class="mb-3">
                <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                <input type="date" class="form-control <?= isset($errors['ngay_sinh']) ? 'is-invalid' : '' ?>"
                    value="<?= htmlspecialchars($oldData['ngay_sinh'] ?? '') ?>"
                    id="ngay_sinh" name="ngay_sinh" placeholder="Ngày sinh">

                <div class="invalid-feedback"><?= $errors['ngay_sinh'] ?? ''; ?></div>
            </div>

            <button type="submit" class="btn btn-primary">Thêm độc giả</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>