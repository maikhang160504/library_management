<?php

use Dompdf\Dompdf;
use Dompdf\Options;

// Xuất PDF
if (isset($_GET['export']) && $_GET['export'] == 'pdf') {
    $options = new Options();
    $options->set('defaultFont', 'DejaVu Sans'); // Hỗ trợ tiếng Việt
    $dompdf = new Dompdf($options);
    $options->set('isRemoteEnabled', true);
    $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/logo_nen.png";
    $imageBase64 = "data:image/png;base64," . base64_encode(file_get_contents($imagePath));
    
    ob_start();
?>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .logo {
            width: 100px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
    <div>
        <div class="header">
            <p class="title">Phiếu Mượn Thư Viện</p>
            <img class="logo" src="<?php echo $imageBase64; ?>" alt="Logo">

        </div>
        <p><strong>Mã phiếu mượn:</strong> <?php echo $borrowDetail['ma_phieu_muon']; ?></p>
        <p><strong>Độc giả:</strong> <?php echo $borrowDetail['ten_doc_gia']; ?></p>
        <p><strong>Số điện thoại:</strong> <?php echo $borrowDetail['so_dien_thoai']; ?></p>
        <p><strong>Ngày mượn:</strong> <?php echo $borrowDetail['ngay_muon']; ?></p>
        <p><strong>Ngày trả dự kiến:</strong> <?php echo $borrowDetail['ngay_tra']; ?></p>
        <p><strong>Trạng thái:</strong> <?php echo $borrowDetail['trang_thai']; ?></p>

        <h3>Danh sách Sách Được Mượn</h3>
        <table>
            <thead>
                <tr>
                    <th>Mã sách</th>
                    <th>Tên sách</th>
                    <th>Tác giả</th>
                    <th>Số lượng</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($borrowDetail['books'] as $book): ?>
                    <tr>
                        <td><?php echo $book['ma_sach']; ?></td>
                        <td><?php echo $book['ten_sach']; ?></td>
                        <td><?php echo $book['ten_tac_gia']; ?></td>
                        <td><?php echo $book['so_luong']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php
    $html = ob_get_clean();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $dompdf->stream("phieu_muon_{$borrowDetail['ma_phieu_muon']}.pdf", ["Attachment" => true]);
    header("Location: /borrows/detail?ma_phieu_muon=" . $borrowDetail['ma_phieu_muon']);
    // echo $html;
    exit;
}
?>

<?php
$title = "Chi tiết Phiếu Mượn";
ob_start();
?>
<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-center position-relative my-4">
        <a href="/borrows" class="btn btn-outline-secondary position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="text-center">📖 Chi tiết Phiếu Mượn</h2>
        <div class="position-absolute end-0 d-flex gap-2">
            <a href="?export=pdf" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title text-primary">📌 Thông tin Phiếu Mượn</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Mã phiếu mượn:</strong> <?php echo $borrowDetail['ma_phieu_muon']; ?></li>
                <li class="list-group-item"><strong>Độc giả:</strong> <?php echo $borrowDetail['ten_doc_gia']; ?></li>
                <li class="list-group-item"><strong>Số điện thoại:</strong> <?php echo $borrowDetail['so_dien_thoai']; ?></li>
                <li class="list-group-item"><strong>Ngày mượn:</strong> <?php echo $borrowDetail['ngay_muon']; ?></li>
                <li class="list-group-item"><strong>Ngày trả dự kiến:</strong> <?php echo $borrowDetail['ngay_tra']; ?></li>
                <li class="list-group-item"><strong>Trạng thái:</strong>
                    <span class="badge bg-<?php echo ($borrowDetail['trang_thai'] == 'Đã trả') ? 'success' : 'warning'; ?>">
                        <?php echo $borrowDetail['trang_thai']; ?>
                    </span>
                </li>
            </ul>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-primary">📚 Danh sách Sách Được Mượn</h5>
            <table class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Mã sách</th>
                        <th class="text-start">Tên sách</th>
                        <th class="text-start">Tác giả</th>
                        <th>Số lượng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($borrowDetail['books'] as $book): ?>
                        <tr>
                            <td><?php echo $book['ma_sach']; ?></td>
                            <td class="text-start"><?php echo $book['ten_sach']; ?></td>
                            <td class="text-start"><?php echo $book['ten_tac_gia']; ?></td>
                            <td><strong><?php echo $book['so_luong']; ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>