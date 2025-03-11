<div class="dropdown mb-3">
            <form method="POST" action="/books" id="filterForm" class="mb-3">
                <div class="btn-group">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= htmlspecialchars($selectedCategoryName) ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-category="">Tất cả</a></li>
                        <?php foreach ($categories as $cate): ?>
                            <li>
                                <a class="dropdown-item" href="#" data-category="<?= htmlspecialchars($cate['ma_the_loai']) ?>">
                                    <?= htmlspecialchars($cate['ten_the_loai']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <input type="hidden" name="category" id="filterCategory" value="">
            </form>
        </div>


        setTimeout(function(){
            // Tìm phần tử alert và loại bỏ lớp 'show' để ẩn đi (Bootstrap sẽ thực hiện fade-out)
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
            // Cập nhật text hiển thị của nút dropdown
            document.getElementById('categoryDropdown').innerText = this.textContent;
            document.getElementById('filterForm').submit();
        });
    });