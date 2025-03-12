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
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Thư viện Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
    // Khởi tạo biến đếm sách (global)
    let soLuongSach = 1;
    console.log(soLuongSach);
    // Hàm thêm sách vào form
    function themSach() {
        const danhSachSach = document.getElementById('danh_sach_sach');

        // Tạo div chứa sách
        const div = document.createElement('div');
        div.className = 'mb-3 book-item';
        div.dataset.index = soLuongSach; // Lưu chỉ mục để kiểm soát

        // Tạo nội dung cho mỗi sách
        div.innerHTML = `
            <label class="form-label">Sách</label>
            <select class="form-select select2 book-select" name="danh_sach_sach[${soLuongSach}][ma_sach]" required>
                <option value="">Chọn sách</option>
                <?php foreach ($books as $book): ?>
                    <option value="<?php echo $book['ma_sach']; ?>">
                        <?php echo $book['ma_sach'] . ' - ' . $book['ten_sach']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Vui lòng chọn sách.</div>

            <label class="form-label mt-2">Số lượng</label>
            <input type="number" class="form-control book-quantity" name="danh_sach_sach[${soLuongSach}][so_luong]" min="1" required>
            <div class="invalid-feedback">Vui lòng nhập số lượng.</div>

            <button type="button" class="btn btn-danger btn-sm mt-2 remove-book" onclick="xoaSach(this)">
                <i class="fas fa-trash"></i> Xóa
            </button>
        `;

        // Thêm div vào danh sách sách
        danhSachSach.appendChild(div);

        // Khởi tạo Select2 cho dropdown mới
        $(div).find('.select2').select2({
            placeholder: "Chọn một tùy chọn",
            allowClear: true
        });

        // Tăng biến đếm lên để tạo chỉ mục cho sách tiếp theo
        soLuongSach++; // Tăng chỉ mục sau khi thêm sách
        console.log(soLuongSach);
    }

    // Hàm xóa sách
    function xoaSach(button) {
        button.parentElement.remove(); // Xóa sách
        capNhatIndex(); // Cập nhật lại chỉ mục khi xóa
    }

    // Hàm cập nhật lại chỉ mục của các sách trong form khi có sách bị xóa
    function capNhatIndex() {
        let index = 0;
        document.querySelectorAll('.book-item').forEach(div => {
            div.dataset.index = index;
            // Cập nhật lại name của các input
            div.querySelector('.book-select').setAttribute('name', `danh_sach_sach[${index}][ma_sach]`);
            div.querySelector('.book-quantity').setAttribute('name', `danh_sach_sach[${index}][so_luong]`);
            index++;
        });
        // Cập nhật lại biến đếm sách
        soLuongSach = index;
    }

    // Khởi tạo Select2 khi trang tải
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Chọn một tùy chọn",
            allowClear: true,
            tags: true
        });
    });

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