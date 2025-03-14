<?php
$title = "Danh sách Độc giả";
ob_start();
?>
<div class="container">
    <h2 class="text-center mb-4">Danh sách Độc giả</h2>

    <?php
    // Hiển thị thông báo thành công
    if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show text-start" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
        <?php unset($_SESSION['success']);  ?>

    <?php endif; ?>

    <?php
    // Hiển thị thông báo lỗi
    if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show text-start" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>


    <div class="mb-3">
        <a href="/readers/create" class="btn btn-success">
            <i class="fas fa-plus"></i> Thêm độc giả
        </a>
        <a href="/readers/create" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Xuất Excel
        </a>
    </div>

    <div class="mb-3">
        <form method="GET" action="/readers/search">
            <div class="input-group">
                <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm theo mã, tên, số điện thoại">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </div>
        </form>
    </div>

    <div>
        <!-- Nút Quay lại -->
        <a href="/readers" class="btn btn-secondary mb-3">Quay lại danh sách đầy đủ</a>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã độc giả</th>
                    <th>Tên độc giả</th>
                    <th>Số điện thoại</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($readers)): ?>
                    <tr>
                        <td colspan="5" class="text-center">Không có kết quả tìm kiếm.</td>
                    </tr>
                <?php else: ?>
                    <?php $stt = 1; ?>
                    <?php foreach ($readers as $reader): ?>
                        <tr>
                        <td><?= $stt++; ?></td>
                            <td><?php echo $reader['ma_doc_gia']; ?></td>
                            <td><?php echo $reader['ten_doc_gia']; ?></td>
                            <td><?php echo $reader['so_dien_thoai']; ?></td>
                            <!-- <td><?php echo $reader['email']; ?></td> -->
                            <td>
                                <a href="/readers/detail/<?php echo $reader['ma_doc_gia']; ?>" class="btn btn-sm btn-info mx-2">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </a>
                                <a href="/readers/edit/<?php echo $reader['ma_doc_gia']; ?>" class="btn btn-sm btn-warning mx-2">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <a href="/readers/delete/<?php echo $reader['ma_doc_gia']; ?>" class="btn btn-sm btn-danger mx-2" data-bs-toggle="modal" data-bs-target="#delete" data-bs-id="<?php echo $reader['ma_doc_gia']; ?>">
                                    <i class=" fas fa-trash-alt"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $currentPage - 1 ?>">&laquo;</a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $currentPage + 1 ?>">&raquo;</a>
                </li>

            </ul>
        </nav>
    <?php endif; ?>

</div>

<!-- Modal -->
<div class="modal fade" id="delete" tabindex="-1" aria-labelledby="delete" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa thành viên này không?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <form action="/readers/delete" method="POST" id="deleteForm">
                    <input type="hidden" name="id" id="readerId">
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    var deleteModal = document.getElementById("delete");
    var deleteForm = document.getElementById("deleteForm");

    deleteModal.addEventListener("show.bs.modal", function (event) {
        var button = event.relatedTarget;
        var readerId = button.getAttribute("data-bs-id");

        // Gán ID vào input ẩn
        document.getElementById("readerId").value = readerId;

        // Chỉnh sửa action của form để gửi đúng route
        deleteForm.action = "/readers/delete/" + readerId;
    });
});

</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>