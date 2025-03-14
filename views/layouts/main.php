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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #sidebar {
            width: 60px;
            min-height: 100vh;
            transition: width 0.3s;
            overflow: auto;
            text-align: center;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            z-index: 100;
        }

        #sidebar:hover {
            width: 250px;
            text-align: left;
        }

        #sidebar .nav-item {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #sidebar .nav-item a {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            transition: background-color 0.3s, padding-left 0.3s;
            color: white;
            height: 50px;
            /* Đảm bảo chiều cao */
            width: 100%;
            /* Đảm bảo chiều rộng */
        }

        #sidebar .nav-item i {
            min-width: 40px;
            text-align: center;
            transition: transform 0.3s;
            font-size: 1.2rem;
        }

        #sidebar:hover .nav-item a {
            justify-content: flex-start;
            padding-left: 15px;
        }

        #sidebar .nav-item a:hover {
            background-color: #495057;
            padding-left: 20px;
        }

        #sidebar:hover .nav-item i {
            transform: translateX(5px);
        }

        /* Điều chỉnh cho content */
        #content {
            margin-left: 60px;
            /* Đảm bảo không bị che bởi sidebar */
            transition: margin-left 0.3s;
        }

        #sidebar:hover~#content {
            margin-left: 250px;
            /* Khi sidebar mở rộng, content sẽ di chuyển qua phải */
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <?php $user =  $_SESSION['user'] ?? null; ?>
        <?php if ($user !== null): ?>

            <nav class="bg-dark text-white" id="<?php echo 'sidebar'; ?>">
                <ul class="nav flex-column mt-4">
                    <li class="nav-item"><a href="/books" class="nav-link"><i class="fas fa-home"></i> <span class="ms-2 d-none d-lg-inline">Quản lý Sách</span></a></li>
                    <li class="nav-item"><a href="/borrows" class="nav-link"><i class="fas fa-book"></i> <span class="ms-2 d-none d-lg-inline">Quản lý Mượn/Trả</span></a></li>
                    <li class="nav-item"><a href="/readers" class="nav-link"><i class="fas fa-user"></i> <span class="ms-2 d-none d-lg-inline">Quản lý độc giả</span></a></li>
                    <li class="nav-item"><a href="/penalties" class="nav-link"><i class="fas fa-money-bill"></i> <span class="ms-2 d-none d-lg-inline">Phí phạt</span></a></li>
                    <li class="nav-item"><a href="/reports" class="nav-link"><i class="fas fa-chart-bar"></i> <span class="ms-2 d-none d-lg-inline">Thống kê</span></a></li>
                    <li class="nav-item"><a href="/reports/export-excel" class="nav-link"><i class="fas fa-file-excel"></i>  <span class="ms-2 d-none d-lg-inline">Xuất Excel</span></a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-user-lock"></i> <span class="ms-2 d-none d-lg-inline"><?= htmlspecialchars($user['fullname']) ?></span></a></li>
                    <li class="nav-item"><a href="/logout" class="nav-link"><i class="fas fa-sign-out-alt"></i> <span class="ms-2 d-none d-lg-inline">Đăng xuất</span></a></li>
                </ul>
                </ul>
            </nav>
        <?php endif; ?>
        <!-- Content Area -->
        <div class="flex-grow-1 p-4 position-relative" id="content">
            <main>
                <?php echo $content; ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>