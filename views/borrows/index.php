<?php
$title = "Quản lí phiếu mượn Sách";
ob_start();
?>

<div class="container">
    <!-- Header với các nút quản lý -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Danh sách phiếu mượn</h2>
        <div>
            <a href="/borrows" class="btn btn-primary me-2">Quản lý Mượn Sách</a>
            <a href="/returns" class="btn btn-warning">Quản lý Trả Sách</a>
        </div>
    </div>

    <!-- Bảng danh sách phiếu mượn -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mã phiếu mượn</th>
                <th>Mã độc giả</th>
                <th>Ngày mượn</th>
                <th>Ngày trả</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
                <th>Chi tiết</th> <!-- Thêm cột mới cho nút xem chi tiết -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($borrows as $borrow): ?>
            <tr>
                <td><?php echo $borrow['ma_phieu_muon']; ?></td>
                <td><?php echo $borrow['ma_doc_gia']; ?></td>
                <td><?php echo $borrow['ngay_muon']; ?></td>
                <td><?php echo $borrow['ngay_tra']; ?></td>
                <td><?php echo $borrow['trang_thai']; ?></td>
                <td>
                    <?php if ($borrow['trang_thai'] === 'Đang mượn'): ?>
                        <a href="/returns/return?ma_phieu_muon=<?php echo $borrow['ma_phieu_muon']; ?>" class="btn btn-success btn-sm">Trả sách</a>
                    <?php else: ?>
                        <span class="text-muted">Đã trả</span>
                    <?php endif; ?>
                </td>
                <td>
                    <!-- Nút xem chi tiết -->
                    <a href="/borrows/detail/<?php echo $borrow['ma_phieu_muon']; ?>" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> Xem chi tiết
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mb-3 d-flex align-items-center">
        <!-- Nút tạo phiếu mượn -->
        <a href="/borrows/create" class="btn btn-primary">Tạo phiếu mượn</a>
    <button type="button" class="btn btn-outline-primary me-3" onclick="xuatQR()">
        <i class="fas fa-qrcode"></i> Xuất mã QR
    </button>
    <div id="qrCodeContainer"></div>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    function xuatQR() {
        const qrContainer = document.getElementById("qrCodeContainer");
        qrContainer.innerHTML = ""; // Xóa mã QR cũ (nếu có)
        new QRCode(qrContainer, {
            text: window.location.origin + "/borrows/create",
            width: 128,
            height: 128
        });
    }
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>