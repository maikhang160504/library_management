<?php
$title = "Danh sách sách";
ob_start();
?>

<h2 class="text-center mb-4">Danh sách sách</h2>
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