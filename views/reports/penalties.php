<?php
$title = "Danh sách phí phạt";
ob_start();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý phí phạt và gửi email nhắc nhở</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Quản lý phí phạt và gửi email nhắc nhở</h2>
    
    <div class="mb-3">
        <form method="GET" action="/penalty/search">
            <div class="input-group">
                <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm theo mã độc giả, tên, số tiền phạt" />
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Mã độc giả</th>
                    <th>Tên độc giả</th>
                    <th>Số tiền phạt</th>
                    <th>Trạng thái email</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>DG001</td>
                    <td>Nguyễn Văn A</td>
                    <td>50.000đ</td>
                    <td><span class="badge bg-danger">Chưa gửi</span></td>
                    <td>
                        <a href="/penalty/detail/DG001" class="btn btn-info btn-sm">Chi tiết</a>
                        <button class="btn btn-warning btn-sm">Gửi Email</button>
                    </td>
                </tr>
                <tr>
                    <td>DG002</td>
                    <td>Trần Thị B</td>
                    <td>30.000đ</td>
                    <td><span class="badge bg-success">Đã gửi</span></td>
                    <td>
                        <a href="/penalty/detail/DG002" class="btn btn-info btn-sm">Chi tiết</a>
                        <button class="btn btn-warning btn-sm">Gửi Email</button>
                    </td>
                </tr>
                
            </tbody>
        </table>
    </div>

    <div class="mb-3">
        <button class="btn btn-primary">Gửi email nhắc nhở hàng loạt</button>
    </div>

</div>

<!-- Modal chi tiết phí phạt -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Chi tiết phí phạt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Sách:</strong> Lập trình PHP</p>
                <p><strong>Ngày mượn:</strong> 2025-02-15</p>
                <p><strong>Ngày trả:</strong> 2025-03-05</p>
                <p><strong>Số ngày quá hạn:</strong> 5 ngày</p>
                <p><strong>Phí phạt:</strong> 50.000đ</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>