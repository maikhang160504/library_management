<?php
$title = "Quản lí phiếu mượn Sách";
ob_start();

// Thiết lập phân trang
$limit = 8; // Số phiếu mượn hiển thị trên mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Lọc theo trạng thái
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$search_name = isset($_GET['search']) ? trim($_GET['search']) : '';


// Lọc theo tên độc giả (nếu có)
$filteredBorrows = array_filter($borrows, function ($borrow) use ($search_name) {
    return empty($search_name) || stripos($borrow['ten_doc_gia'], $search_name) !== false;
});

$totalRecords = count($filteredBorrows);
$totalPages = ceil($totalRecords / $limit);

// Lọc danh sách phiếu mượn theo trang hiện tại
$borrowsPaginated = array_slice($filteredBorrows, $offset, $limit);
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Danh sách phiếu mượn</h2>
        <div>
            <a href="/borrows" class="btn btn-primary me-2">Quản lý Mượn Sách</a>
            <a href="/returns" class="btn btn-warning">Quản lý Trả Sách</a>
        </div>
    </div>

    <!-- Tìm kiếm -->
    <form action="/borrows" method="GET" class="row g-3 align-items-end mb-3">
        <!-- Ô nhập tìm kiếm -->
        <div class="col-md-5">
            <label for="search" class="form-label">Tìm theo tên độc giả</label>
            <input type="text" id="search" name="search" class="form-control"
                placeholder="Nhập tên độc giả..." value="<?php echo htmlspecialchars($search_name); ?>">
        </div>

        <!-- Bộ lọc trạng thái -->
        <div class="col-md-4">
            <label for="status" class="form-label">Trạng thái</label>
            <select id="status" name="status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                <option value="Đang mượn" <?php echo ($filter_status === 'Đang mượn') ? 'selected' : ''; ?>>Đang mượn</option>
                <option value="Đã trả" <?php echo ($filter_status === 'Đã trả') ? 'selected' : ''; ?>>Đã trả</option>
            </select>
        </div>

        <!-- Nút tìm kiếm & Xóa bộ lọc -->
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-success w-50">Tìm kiếm</button>
            <a href="/borrows" class="btn btn-secondary w-50">Xóa bộ lọc</a>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mã phiếu mượn</th>
                <th>Mã độc giả</th>
                <th>Tên độc giả</th>
                <th>Ngày mượn</th>
                <th>Ngày trả</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($borrowsPaginated as $borrow): ?>
                <tr>
                    <td><?php echo $borrow['ma_phieu_muon']; ?></td>
                    <td><?php echo $borrow['ma_doc_gia']; ?></td>
                    <td><?php echo $borrow['ten_doc_gia']; ?></td>
                    <td><?php echo $borrow['ngay_muon']; ?></td>
                    <td><?php echo $borrow['ngay_tra']; ?></td>
                    <td><?php echo $borrow['trang_thai']; ?></td>
                    <td>
                        <a href="/borrows/detail/<?php echo $borrow['ma_phieu_muon']; ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                        <?php if ($borrow['trang_thai'] === 'Đang mượn'): ?>
                            <a href="/returns/return?ma_phieu_muon=<?php echo $borrow['ma_phieu_muon']; ?>" class="btn btn-success btn-sm">Trả sách</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Phân trang -->
    <nav>
        <ul class="pagination justify-content-end">
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo urlencode($filter_status); ?>&search=<?php echo urlencode($search_name); ?>">Trước</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo urlencode($filter_status); ?>&search=<?php echo urlencode($search_name); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo urlencode($filter_status); ?>&search=<?php echo urlencode($search_name); ?>">Sau</a>
            </li>
        </ul>
    </nav>

    <a href="/borrows/create" class="btn btn-primary">Tạo phiếu mượn</a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
