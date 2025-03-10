<?php
$title = "Danh sách sách";
ob_start();
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<div class="container">
    <?php if (!empty($success)) : ?>
        <div class="alert alert-success alert-dismissible fade show text-start" role="alert">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger alert-dismissible fade show text-start" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    <?php endif; ?>

<!-- Header với các nút quản lý -->
<div class="d-flex justify-content-between align-items-center mb-4">
    
    <h2>Danh sách sách</h2>
    <div>
        <a href="/books/add" class="btn btn-primary me-2">Thêm sách</a>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-custom table-hover">
        <thead>
            <tr>
                <th>Mã sách</th>
                <th>Tên sách</th>
                <th>Tác giả</th>
                <th>Thể loại</th>
                <th>Số lượng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
            <tr>
                <td><?php echo $book['ma_sach']; ?></td>
                <td><?php echo $book['ten_sach']; ?></td>
                <td><?php echo $book['ten_tac_gia']; ?></td>
                <td><?php echo $book['ten_the_loai']; ?></td>
                <td><?php echo $book['so_luong']; ?></td>
                <td>
                    <a href="/books/<?php echo $book['ma_sach']; ?>" class="btn btn-sm btn-custom">Xem chi tiết</a>
                    <button class="btn btn-success btn-sm update-quantity-btn" 
    data-id="<?php echo $book['ma_sach']; ?>" 
    data-current-quantity="<?php echo $book['so_luong']; ?>"
    data-name="<?= htmlspecialchars($book['ten_sach']) ?>">
    Cập nhật số lượng
</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

    <?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $currentPage - 1 ?>"><</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $currentPage + 1 ?>">></a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<!-- Modal cập nhật số lượng -->
<div class="modal fade" id="updateQuantityModal" tabindex="-1" aria-labelledby="updateQuantityModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="updateQuantityForm" method="post" action="/books/updateQuantity">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updateQuantityModalLabel">Cập nhật số lượng sách</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body">
          <!-- Mã sách ẩn -->
          <input type="hidden" name="ma_sach" id="modalMaSach" value="">
          
          <!-- Tên sách -->
          <div class="mb-3">
            <label for="modalBookName" class="form-label">Tên sách</label>
            <input type="text" class="form-control" id="modalBookName" readonly>
          </div>
          
          <!-- Số lượng cũ -->
          <div class="mb-3">
            <label for="modalOldQuantity" class="form-label">Số lượng cũ</label>
            <input type="number" class="form-control" id="modalOldQuantity" readonly>
          </div>
          
          <!-- Số lượng mới -->
          <div class="mb-3">
            <label for="modalNewQuantity" class="form-label">Số lượng mới</label>
            <input type="number" class="form-control" name="so_luong" id="modalNewQuantity" placeholder="Nhập số lượng mới">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="submit" class="btn btn-primary">Cập nhật</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Lấy tất cả các nút cập nhật
    const updateBtns = document.querySelectorAll('.update-quantity-btn');
    // Tạo modal từ Bootstrap
    const updateModal = new bootstrap.Modal(document.getElementById('updateQuantityModal'));
    
    updateBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const maSach = this.getAttribute('data-id');
            const soLuongHienTai = this.getAttribute('data-current-quantity');
            const bookName = this.getAttribute('data-name');
            
            // Gán dữ liệu vào modal
            document.getElementById('modalMaSach').value = maSach;
            document.getElementById('modalBookName').value = bookName;
            document.getElementById('modalOldQuantity').value = soLuongHienTai;
            // Làm trống ô số lượng mới khi mở modal
            document.getElementById('modalNewQuantity').value = '';
            
            // Hiển thị modal
            updateModal.show();
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>