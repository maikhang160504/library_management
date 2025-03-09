<?php
$title = "Tạo phiếu mượn";
ob_start();
?>

<div class="container mt-4">
<div class="d-flex align-items-center justify-content-center position-relative my-4">
    <a href="/borrows" class="btn btn-outline-secondary px-4 py-2 position-absolute start-0">
        <i class="bi bi-arrow-left-circle"></i> Quay lại
    </a>
    <h2 class="mb-4">Tạo phiếu mượn sách</h2>
</div>

   
    <form method="POST" action="/borrows/store" class="needs-validation" novalidate>
        <!-- Chọn độc giả -->
        <div class="mb-3">
            <label for="ma_doc_gia" class="form-label">Độc giả</label>
            <select class="form-select select2" id="ma_doc_gia" name="ma_doc_gia" required>
                <option value="">Chọn độc giả</option>
                <?php foreach ($readers as $reader): ?>
                    <option value="<?php echo $reader['ma_doc_gia']; ?>">
                        <?php echo $reader['ma_doc_gia'] . ' - ' . $reader['ten_doc_gia']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Vui lòng chọn độc giả.</div>
        </div>

        <!-- Ngày mượn -->
        <div class="mb-3">
            <label for="ngay_muon" class="form-label">Ngày mượn</label>
            <input type="date" class="form-control" id="ngay_muon" name="ngay_muon" required>
            <div class="invalid-feedback">Vui lòng chọn ngày mượn.</div>
        </div>

        <!-- Ngày trả -->
        <div class="mb-3">
            <label for="ngay_tra" class="form-label">Ngày trả</label>
            <input type="date" class="form-control" id="ngay_tra" name="ngay_tra" required>
            <div class="invalid-feedback">Vui lòng chọn ngày trả.</div>
        </div>

        <!-- Danh sách sách -->
        <div id="danh_sach_sach">
            <div class="mb-3">
                <label for="ma_sach" class="form-label">Sách</label>
                <select class="form-select select2" name="danh_sach_sach[0][ma_sach]" required>
                    <option value="">Chọn sách</option>
                    <?php foreach ($books as $book): ?>
                        <option value="<?php echo $book['ma_sach']; ?>">
                            <?php echo $book['ma_sach'] . ' - ' . $book['ten_sach']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Vui lòng chọn sách.</div>
                <label for="so_luong" class="form-label mt-2">Số lượng</label>
                <input type="number" class="form-control" name="danh_sach_sach[0][so_luong]" required>
                <div class="invalid-feedback">Vui lòng nhập số lượng.</div>
            </div>
        </div>

        <!-- Nút thêm sách -->
        <div class="mb-3">
            <button type="button" class="btn btn-outline-secondary" onclick="themSach()">
                <i class="fas fa-plus"></i> Thêm sách
            </button>
        </div>

        <!-- Nút tạo phiếu mượn -->
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Tạo phiếu mượn
            </button>
        </div>
    </form>
</div>

<script>
    // Khởi tạo Select2
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Chọn một tùy chọn",
            allowClear: true,
            tags: true // Cho phép nhập giá trị mới
        });
    });

    // Thêm sách
    let soLuongSach = 1; // Khai báo biến toàn cục
    function themSach() {
        const div = document.createElement('div');
        div.className = 'mb-3';
        div.innerHTML = `
            <label for="ma_sach" class="form-label">Sách</label>
            <select class="form-select select2" name="danh_sach_sach[${soLuongSach}][ma_sach]" required>
                <option value="">Chọn sách</option>
                <?php foreach ($books as $book): ?>
                    <option value="<?php echo $book['ma_sach']; ?>">
                        <?php echo $book['ma_sach'] . ' - ' . $book['ten_sach']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Vui lòng chọn sách.</div>
            <label for="so_luong" class="form-label mt-2">Số lượng</label>
            <input type="number" class="form-control" name="danh_sach_sach[${soLuongSach}][so_luong]" required>
            <div class="invalid-feedback">Vui lòng nhập số lượng.</div>
        `;
        document.getElementById('danh_sach_sach').appendChild(div);

        // Khởi tạo Select2 cho dropdown mới
        $(div).find('.select2').select2({
            placeholder: "Chọn một tùy chọn",
            allowClear: true,
            tags: true // Cho phép nhập giá trị mới
        });

        soLuongSach++; // Tăng biến đếm
    }

    // Bootstrap validation
    (function () {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>