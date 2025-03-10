<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Quản lý Thư viện'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/styles.css">
    <!-- Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Thêm thư viện Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
    <!-- Header -->
    <header class="bg-dark text-white py-4 shadow no-print">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Quản lý Thư viện</h1>
                <nav>
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="/" class="text-white text-decoration-none hover-effect">Trang chủ</a></li>
                        <li class="list-inline-item"><a href="/borrows" class="text-white text-decoration-none hover-effect">Quản lý Mượn/Trả Sách</a></li>
                        <li class="list-inline-item"><a href="/readers" class="text-white text-decoration-none hover-effect">Quản lý độc giả</a></li>
                        <li class="list-inline-item"><a href="/penalties" class="text-white text-decoration-none hover-effect">Phí phạt</a></li>
                        <li class="list-inline-item"><a href="/reports" class="text-white text-decoration-none hover-effect">Thống Kê</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class=" my-5">
        <?php echo $content; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-auto no-print">
        <div class="container text-center">
            <p class="mb-0">&copy; 2023 Quản lý Thư viện. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS và dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <!-- Custom JS -->
    <script src="/js/scripts.js"></script>
</body>

</html>