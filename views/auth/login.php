
<?php
$title = "Login";
ob_start();

// Lấy lỗi từ session (nếu có)
$errors = $_SESSION['errors'] ?? ['usernameErr' => '', 'passwordErr' => '', 'loginErr' => ''];
unset($_SESSION['errors']);
?>

<link rel="stylesheet" href="/css/styles.css">

<body>
    <div class="auth-container container d-flex justify-content-center align-items-center mt-5">
        <form action="/" class="border border-2 rounded header-border needs-validation" style="width: 30%;" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="m-3 fw-bold d-flex justify-content-center">
                <h1 class="text-muted">Login</h1>
            </div>

            <!-- Thông báo đăng ký thành công -->
            <?php if (!empty($_SESSION['success_message'])): ?>
                <div class="d-flex justify-content-between alert alert-success mx-3">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (!empty($errors['loginErr'])): ?>
                <div class="d-flex justify-content-between alert alert-danger mx-3">
                    <?= $errors['loginErr'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="mb-4 mx-3">
                <input type="text" class="form-control <?php echo !empty($errors['usernameErr']) ? 'is-invalid' : ''; ?>"
                    name="username" placeholder="Enter your username."
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : (isset($_COOKIE['username']) ? htmlspecialchars($_COOKIE['username']) : ''); ?>" />
                <div class="invalid-feedback">
                    <?= $errors['usernameErr'] ?>
                </div>
            </div>

            <div class="input-group-password mb-4 mx-3">
                <input type="password" class="form-control <?php echo !empty($errors['passwordErr']) ? 'is-invalid' : ''; ?>"
                    name="password" placeholder="Enter your password." />
                <div class="invalid-feedback">
                    <?= $errors['passwordErr'] ?>
                </div>
            </div>

            <div class="mb-4 mx-3">
                <input class="checkbox" type="checkbox" id="remember-me" name="remember-me">
                <label for="remember-me">Remember me</label>
            </div>

            <div class="mx-3">
                <button type="submit" class="login btn auth-btn">Login</button>
            </div>
        </form>
    </div>
</body>

<script>
    (function() {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');

        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
