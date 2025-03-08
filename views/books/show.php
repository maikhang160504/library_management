<?php
$title = "Chi tiết sách";
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-custom">
            <div class="card-body">
                <h2 class="card-title text-center"><?php echo $book['ten_sach']; ?></h2>
                <p class="card-text"><strong>Tác giả:</strong> <?php echo $book['ten_tac_gia']; ?></p>
                <p class="card-text"><strong>Thể loại:</strong> <?php echo $book['ten_the_loai']; ?></p>
                <p class="card-text"><strong>Năm xuất bản:</strong> <?php echo $book['nam_xuat_ban']; ?></p>
                <p class="card-text"><strong>Nhà xuất bản:</strong> <?php echo $book['nha_xuat_ban']; ?></p>
                <p class="card-text"><strong>Số lượng còn lại:</strong> <?php echo $book['so_luong']; ?></p>
                <a href="/" class="btn btn-custom w-100">Quay lại danh sách</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>