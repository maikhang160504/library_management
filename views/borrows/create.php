<?php
$title = "Tạo phiếu mượn";
ob_start();
?>

<div class="">
    <h2>Tạo phiếu mượn sách</h2>
    <form method="POST" action="/borrows/store">
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
        </div>

        <!-- Ngày mượn -->
        <div class="mb-3">
            <label for="ngay_muon" class="form-label">Ngày mượn</label>
            <input type="date" class="form-control" id="ngay_muon" name="ngay_muon" required>
        </div>

        <!-- Ngày trả -->
        <div class="mb-3">
            <label for="ngay_tra" class="form-label">Ngày trả</label>
            <input type="date" class="form-control" id="ngay_tra" name="ngay_tra" required>
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
                <label for="so_luong" class="form-label">Số lượng</label>
                <input type="number" class="form-control" name="danh_sach_sach[0][so_luong]" required>
            </div>
        </div>

        <!-- Nút thêm sách -->
        <button type="button" class="btn btn-secondary" onclick="themSach()">Thêm sách</button>
        <button type="submit" class="btn btn-primary">Tạo phiếu mượn</button>
    </form>
</div>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Khởi tạo Select2
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Chọn một tùy chọn",
            allowClear: true
        });
    });

    // Thêm sách
    let soLuongSach = 1;
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
            <label for="so_luong" class="form-label">Số lượng</label>
            <input type="number" class="form-control" name="danh_sach_sach[${soLuongSach}][so_luong]" required>
        `;
        document.getElementById('danh_sach_sach').appendChild(div);

        // Khởi tạo Select2 cho dropdown mới
        $(div).find('.select2').select2({
            placeholder: "Chọn một tùy chọn",
            allowClear: true
        });

        soLuongSach++;
    }
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>