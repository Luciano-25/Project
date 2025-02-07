</head>
<body>
<header class="top-header">
        <div class="header-container">
            <h1 class="site-title">BookHaven</h1>
            <nav class="nav-links">
    <a href="index.php">Home</a>
    <a href="products.php">Browse</a>
    <div class="nav-right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php" class="profile-link">
                <i class="fas fa-user"></i>
                <?php echo $_SESSION['username']; ?>
            </a>
        <?php else: ?>
            <a href="login.php" class="login-link">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </a>
        <?php endif; ?>
        <a href="cart.php" class="cart-icon">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count"><?php echo empty($_SESSION['cart']) ? '0' : count($_SESSION['cart']); ?></span>
        </a>
    </div>
</nav>
        </div>
    </header>