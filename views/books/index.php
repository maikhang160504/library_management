<?php
$title = "Danh sách sách";
ob_start();
function flash($key) {
    if (isset($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}
$success = flash('success');
$error = flash('error');

$selectedCategoryName = 'Lọc theo thể loại';
if (isset($selectedCategory) && $selectedCategory !== '') {
    foreach ($categories as $cate) {
        if ($cate['ma_the_loai'] == $selectedCategory) {
            $selectedCategoryName = $cate['ten_the_loai'];
            break;
        }
    }
}
// Nếu không có dữ liệu thống kê được truyền vào, thiết lập mặc định
$type = isset($type) ? $type : 'day';  // mặc định theo ngày
$stats = isset($stats) ? $stats : [];    // mảng thống kê
$total = isset($total) ? $total : 0;      // tổng sách theo thống kê
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

    <!-- Phần thống kê -->


<!-- Header với các nút quản lý -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Danh sách sách</h2>
    <div class="d-flex justify-content-end ">
        
        
        <div class="dropdown pt-2 ms-3">
            
            <form method="POST" action="/books" id="filterSearchForm" class="mb-3 d-flex">
            <button type="button" class="btn btn-secondary ms-2" id="resetFilterBtn">Xóa lọc</button>
                <div class="btn-group me-2">
                    <button class="btn btn-secondary dropdown-toggle ms-3" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= htmlspecialchars($selectedCategoryName) ?>
                    </button>
                    <ul class="dropdown-menu" style="max-height: 300px; overflow-y: auto;">
                        <li><a class="dropdown-item" href="#" data-category="">Tất cả</a></li>
                        <?php foreach ($categories as $cate): ?>
                            <li>
                                <a class="dropdown-item" href="#" data-category="<?= htmlspecialchars($cate['ma_the_loai']) ?>">
                                    <?= htmlspecialchars($cate['ten_the_loai']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <input type="hidden" name="category" id="filterCategory" value="<?= htmlspecialchars($selectedCategory) ?>">
                </div>
                
                <div class="flex-grow-1 ms-3">
                    <input type="text" name="query" id="bookSearch" class="form-control shadow-none border-dark" 
                        placeholder="Nhập từ khóa tìm kiếm..." 
                        value="<?= htmlspecialchars($searchQuery) ?>">
                </div>
                
            </form>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-custom table-hover">
        <thead>
            <tr>
                <th style="width: 10%;" class="text-center">Mã sách</th>
                <th style="width: 25%;" class="text-center">Tên sách</th>
                <th style="width: 20%;" class="text-center">Tác giả</th>
                <th style="width: 20%;" class="text-center">Thể loại</th>
                <th style="width: 10%;" class="text-center">Số lượng</th>
                <th style="width: 10%;" class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody id="books-table-body">
            <?php foreach ($books as $book): ?>
            <tr>
                <td class="text-center"><?php echo $book['ma_sach']; ?></td>
                <td class="text-center"><?php echo $book['ten_sach']; ?></td>
                <td class="text-center"><?php echo $book['ten_tac_gia']; ?></td>
                <td class="text-center"><?php echo $book['ten_the_loai']; ?></td>
                <td class="text-center"><?php echo $book['so_luong']; ?></td>
                <td class="text-center">
                    <a href="/books/<?php echo $book['ma_sach']; ?>" class="btn btn-sm btn-dark">Chi tiết</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-between">
    <nav>
        <ul class="pagination">
            <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $currentPage - 1 ?>&query=<?= urlencode($searchQuery) ?>&category=<?= urlencode($selectedCategory) ?>">«</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&query=<?= urlencode($searchQuery) ?>&category=<?= urlencode($selectedCategory) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $currentPage + 1 ?>&query=<?= urlencode($searchQuery) ?>&category=<?= urlencode($selectedCategory) ?>">»</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="pt-2 ms-2">
            <a href="/books/add" class="btn btn-dark">Thêm sách</a>
            <a href="/books/statisticsView" class="btn btn-dark">Thống kê</a>
            <form action="/books/export" method="GET" class="d-inline-block">
                <input type="hidden" name="query" value="<?= htmlspecialchars($searchQuery) ?>">
                <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
                <button type="submit" class="btn btn-success">Xuất Excel</button>
            </form>
        </div>
    </div>
</div>

<script>
    setTimeout(function(){
    const alertElement = document.querySelector('.alert');
    if (alertElement) {
        alertElement.remove();
    }
}, 2000);

document.querySelectorAll('.dropdown-item').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        const categoryId = this.getAttribute('data-category');
        document.getElementById('filterCategory').value = categoryId;
        document.getElementById('categoryDropdown').innerText = this.textContent;

        document.getElementById('filterSearchForm').submit();
    });
});

// Nếu muốn tự động search khi gõ
function debounce(func, delay) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}

document.getElementById('bookSearch').addEventListener('keyup', debounce(function(e) {
    const query = this.value.trim();
    if (query.length >= 2 || query === "") {
        sessionStorage.setItem('shouldFocus', 'true');
        document.getElementById('filterSearchForm').submit();
    }
}, 300));

// Nếu cần focus lại vào ô tìm kiếm
window.addEventListener('DOMContentLoaded', function() {
    if (sessionStorage.getItem('shouldFocus') === 'true') {
        const searchInput = document.getElementById('bookSearch');
        if (searchInput) {
            searchInput.focus();
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
        }
        sessionStorage.removeItem('shouldFocus');
    }
});
document.getElementById('resetFilterBtn').addEventListener('click', function() {
    document.getElementById('filterCategory').value = '';
    document.getElementById('categoryDropdown').innerText = 'Lọc theo thể loại';

    document.getElementById('bookSearch').value = '';

    document.getElementById('filterSearchForm').submit();
});
document.querySelectorAll('.dropdown-menu a').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        const selectedType = this.getAttribute('data-type');

        document.getElementById('statTypeInput').value = selectedType;

        document.getElementById('statTypeForm').submit();
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

