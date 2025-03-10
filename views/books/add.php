<?php
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];


// Clear session ngay sau khi lấy dữ liệu!
unset($_SESSION['errors'], $_SESSION['old']);
?>

<?php $title = "Thêm sách mới"; ob_start(); ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-body">
                    <h2 class="card-title text-center">Thêm sách</h2>

                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
                    <?php endif; ?>

                    <form action="/books/store" method="post" autocomplete="off">
                        <?php
                        $fields = [
                            'ten_sach' => 'Tên sách',
                            'ten_tac_gia' => 'Tên tác giả',
                            'ten_the_loai' => 'Thể loại',
                            'nam_xuat_ban' => 'Năm xuất bản',
                            'nha_xuat_ban' => 'Nhà xuất bản',
                            'so_luong' => 'Số lượng'
                        ];
                        ?>

                        <?php foreach ($fields as $name => $label): ?>
                            <div class="mb-3">
                                <label for="<?= $name ?>" class="form-label"><?= $label ?></label>

                                <input
                                    type="<?= ($name === 'nam_xuat_ban' || $name === 'so_luong') ? 'number' : 'text' ?>"

                                    class="form-control <?= isset($errors[$name]) ? 'is-invalid' : '' ?>"

                                    id="<?= $name ?>"
                                    name="<?= $name ?>"
                                    <?= $name === 'nam_xuat_ban' ? 'min="1800" max="2100"' : '' ?>
                                    value="<?= htmlspecialchars($old[$name] ?? '') ?>"
                                >

                                <?php if (isset($errors[$name])): ?>
                                    <span class="text-danger"><?= htmlspecialchars($errors[$name]) ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <button type="submit" class="btn btn-custom w-100">Thêm sách</button>
                    </form>

                    <a href="/books" class="btn btn-secondary w-100 mt-2">Quay lại danh sách</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
