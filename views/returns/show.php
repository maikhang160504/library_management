<?php

use Dompdf\Dompdf;
use Dompdf\Options;

if (isset($_GET['export_pdf'])) {
    // Lấy đường dẫn tuyệt đối của ảnh
    $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/logo_nen.png";

    // Kiểm tra file ảnh tồn tại
    if (file_exists($imagePath)) {
        $imageBase64 = "data:image/png;base64," . base64_encode(file_get_contents($imagePath));
    } else {
        $imageBase64 = ""; // Tránh lỗi nếu ảnh không tồn tại
    }

    // Tạo nội dung HTML
    $html = '
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: DejaVu Sans, sans-serif; }
            h2 { text-align: center; color: #d9534f; }
            .logo { width: 100px;}
            .table { width: 100%; border-collapse: collapse; }
            .table, .table th, .table td { border: 1px solid black; }
            .table th, .table td { padding: 8px; text-align: left; }
            .text-center { text-align: center; }
        </style>
    </head>
    <body>
        <h2>📄 Chi Tiết Phiếu Trả</h2>
       <div style="text-align: center;">
    <img style="width: 100px; display: block; margin: 0 auto;" src="' . $imageBase64 . '" alt="Logo">
</div>
        <p><strong>Mã phiếu trả:</strong> ' . $returnDetail['ma_phieu_tra'] . '</p>
        <p><strong>Độc giả:</strong> ' . $returnDetail['ten_doc_gia'] . '</p>
        <p><strong>Ngày trả thực tế:</strong> ' . $returnDetail['ngay_tra_sach'] . '</p>
        <p><strong>Tiền phạt:</strong> ' . number_format($returnDetail['tien_phat']) . ' VNĐ</p>

        <h3>📚 Danh Sách Sách Trả</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Mã sách</th>
                    <th>Tên sách</th>
                    <th>Tác giả</th>
                    <th>Số lượng</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($returnDetail['books'] as $book) {
        $html .= '<tr>
                    <td>' . $book['ma_sach'] . '</td>
                    <td>' . $book['ten_sach'] . '</td>
                    <td>' . $book['ten_tac_gia'] . '</td>
                    <td class="text-center">' . $book['so_luong'] . '</td>
                </tr>';
    }

    $html .= '</tbody></table></body></html>';

    // Cấu hình Dompdf
    $options = new Options();
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Xuất file PDF
    $dompdf->stream("Phieu_Tra_" . $returnDetail['ma_phieu_tra'] . ".pdf", ["Attachment" => true]);
    exit;
}


$title = "Chi tiết Phiếu Trả";
ob_start();
?>

<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-center position-relative my-4">
        <a href="/returns" class="btn btn-outline-secondary px-4 py-2 position-absolute start-0">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <h2 class="mb-4 text-center">📄 Chi tiết Phiếu Trả</h2>
        <a href="?ma_phieu_muon=<?php echo $returnDetail['ma_phieu_muon']; ?>&export_pdf=1" class="btn btn-danger position-absolute end-0">
            <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
        </a>
    </div>

    <!-- Thông tin phiếu trả -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title text-primary">📌 Thông tin Phiếu Trả</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong> Mã phiếu trả:</strong> <?php echo $returnDetail['ma_phieu_tra']; ?></li>
                <li class="list-group-item"><strong> Mã phiếu mượn:</strong> <?php echo $returnDetail['ma_phieu_muon']; ?></li>
                <li class="list-group-item"><strong> Độc giả:</strong> <?php echo $returnDetail['ten_doc_gia']; ?> </li>
                <li class="list-group-item"><strong> Số điện thoại:</strong> <?php echo $returnDetail['so_dien_thoai']; ?> </li>
                <li class="list-group-item"><strong> Ngày mượn:</strong> <?php echo $returnDetail['ngay_muon']; ?></li>
                <li class="list-group-item"><strong> Ngày trả dự kiến:</strong> <?php echo $returnDetail['ngay_tra']; ?></li>
                <li class="list-group-item"><strong> Ngày trả thực tế:</strong> <?php echo $returnDetail['ngay_tra_sach']; ?></li>
                <li class="list-group-item"><strong> Tiền phạt:</strong> <span class="badge bg-danger fs-5"><?php echo number_format($returnDetail['tien_phat']); ?> VNĐ</span></li>
            </ul>
        </div>
    </div>

    <!-- Danh sách sách được trả -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-primary">📚 Danh sách Sách Được Trả</h5>
            <table class="table table-bordered table-hover">
                <thead class="table-dark text-center">
                    <tr>
                        <th> Mã sách</th>
                        <th class="text-start"> Tên sách</th>
                        <th class="text-start"> Tác giả</th>
                        <th> Số lượng</th>
                    </tr>
                </thead>
                <tbody class="align-middle text-center">
                    <?php foreach ($returnDetail['books'] as $book): ?>
                        <tr>
                            <td><?php echo $book['ma_sach']; ?></td>
                            <td class="text-start fw-bold"><?php echo $book['ten_sach']; ?></td>
                            <td class="text-start"><?php echo $book['ten_tac_gia']; ?></td>
                            <td class="fs-5 text-success"><strong><?php echo $book['so_luong']; ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php

$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>