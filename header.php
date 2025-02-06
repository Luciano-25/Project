<header class="top-header">
    <div class="header-container">
        <h1 class="site-title">BookHaven</h1>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="products.php">Browse</a>
            <a href="cart.php">Cart (<?php echo array_sum($_SESSION['cart'] ?? []); ?>)</a>
            <a href="#">Profile</a>
        </nav>
    </div>
</header>
