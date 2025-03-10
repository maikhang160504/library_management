<?php
$title = "Danh sách Độc giả";
ob_start();
?>

<h2 class="text-center mb-4">Danh sách Độc giả</h2>
<div class="mb-3">
    <a href="/readers/create" class="btn btn-success">Thêm độc giả</a>
</div>

<div class="mb-3">
    <form method="GET" action="/readers/search">
        <div class="input-group">
            <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm theo mã, tên, số điện thoại">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-custom table-hover">
        <thead>
            <tr>
                <th>Mã độc giả</th>
                <th>Tên độc giả</th>
                <th>Số điện thoại</th>
                <th>Email</th>
                <th>Số sách đang mượn</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($readers as $reader): ?>
            <tr>
                <td><?php echo $reader['ma_doc_gia']; ?></td>
                <td><?php echo $reader['ten_doc_gia']; ?></td>
                <td><?php echo $reader['so_dien_thoai']; ?></td>
                <td><?php echo $reader['email']; ?></td>
                <td><?php echo $reader['so_sach_dang_muon']; ?></td>
                <td>
                    <a href="/readers/<?php echo $reader['ma_doc_gia']; ?>" class="btn btn-sm btn-info">Xem chi tiết</a>
                    <a href="/readers/<?php echo $reader['ma_doc_gia']; ?>/edit" class="btn btn-sm btn-warning">Sửa</a>
                    <a href="/readers/<?php echo $reader['ma_doc_gia']; ?>/delete" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa độc giả này không?');">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>