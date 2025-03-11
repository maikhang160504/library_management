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
    <div class="d-flex justify-content-end ">
        
        <div class="pt-2 ms-2">
            <a href="/books/add" class="btn btn-dark">Thêm sách</a>
        </div>
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
                <th style="width: 10%;">Mã sách</th>
                <th style="width: 25%;">Tên sách</th>
                <th style="width: 20%;">Tác giả</th>
                <th style="width: 20%;">Thể loại</th>
                <th style="width: 10%;">Số lượng</th>
                <th style="width: 10%;">Hành động</th>
            </tr>
        </thead>
        <tbody id="books-table-body">
            <?php foreach ($books as $book): ?>
            <tr>
                <td><?php echo $book['ma_sach']; ?></td>
                <td><?php echo $book['ten_sach']; ?></td>
                <td><?php echo $book['ten_tac_gia']; ?></td>
                <td><?php echo $book['ten_the_loai']; ?></td>
                <td><?php echo $book['so_luong']; ?></td>
                <td>
                    <a href="/books/<?php echo $book['ma_sach']; ?>" class="btn btn-sm btn-dark">Chi tiết</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

