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
</head>
<body>
    <header class="bg-dark text-white py-3">
        <div class="container">
            <h1 class="text-center">Quản lý Thư viện</h1>
            <nav class="text-center">
                <ul class="list-inline">
                    <li class="list-inline-item"><a href="/" class="text-white text-decoration-none">Trang chủ</a></li>
                    <li class="list-inline-item"><a href="/login" class="text-white text-decoration-none">Đăng nhập</a></li>
                    <li class="list-inline-item"><a href="/logout" class="text-white text-decoration-none">Đăng xuất</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container my-5">
        <?php echo $content; ?>
    </main>

    <footer class="bg-dark text-white py-3">
        <div class="container text-center">
            <p>&copy; 2023 Quản lý Thư viện. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS và dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <!-- Custom JS -->
    <script src="/js/scripts.js"></script>
</body>
</html>