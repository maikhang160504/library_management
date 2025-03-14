<?php
$title = "Sửa độc giả";
ob_start();

// Lấy dữ liệu cũ nếu có lỗi
$oldData = $_SESSION['oldData'] ?? [];
$errors = $_SESSION['errors'] ?? [];

// Xóa lỗi sau khi hiển thị để không còn tồn tại sau reload
unset($_SESSION['errors']);
unset($_SESSION['oldData']);
?>
<div class="container">
<div class="d-flex align-items-center justify-content-center position-relative my-4">
            <a href="/readers" class="btn btn-outline-secondary position-absolute start-0">
                <i class="bi bi-arrow-left-circle"></i> Quay lại
            </a>
            <h2 class="text-center"><i class="bi bi-person-lines-fill"></i> Sửa độc giả</h2>
        </div>

    <!-- Hiển thị lỗi nếu có -->
    <?php if (!empty($_SESSION['errors'])): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>
    <form method="POST" action="/readers/update/<?= $reader['ma_doc_gia'] ?>" novalidate >

        <div class="mb-3">
            <label for="ten_doc_gia" class="form-label">Tên độc giả</label>
            <input type="text" class="form-control <?= isset($errors['ten_doc_gia']) ? 'is-invalid' : '' ?>"
                   id="ten_doc_gia" name="ten_doc_gia"
                   value="<?= htmlspecialchars($oldData['ten_doc_gia'] ?? $reader['ten_doc_gia']) ?>"
                   required placeholder="Nhập tên độc giả">

            <div class="invalid-feedback"><?= $errors['ten_doc_gia'] ?? ''; ?></div>
        </div>

        <div class="mb-3">
            <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
            <input type="text" class="form-control <?= isset($errors['so_dien_thoai']) ? 'is-invalid' : '' ?>"
                   id="so_dien_thoai" name="so_dien_thoai"
                   value="<?= htmlspecialchars($oldData['so_dien_thoai'] ?? $reader['so_dien_thoai']) ?>"
                   required placeholder="Nhập số điện thoại">

            <div class="invalid-feedback"><?= $errors['so_dien_thoai'] ?? ''; ?></div>
        </div>

        <div class="mb-3">
            <label for="ngay_sinh" class="form-label">Ngày sinh</label>
            <input type="date" class="form-control <?= isset($errors['ngay_sinh']) ? 'is-invalid' : '' ?>"
                   id="ngay_sinh" name="ngay_sinh"
                   value="<?= htmlspecialchars($oldData['ngay_sinh'] ?? $reader['ngay_sinh']) ?>"
                   required>

            <div class="invalid-feedback"><?= $errors['ngay_sinh'] ?? ''; ?></div>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
