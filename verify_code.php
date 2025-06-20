<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Code</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form method="POST">
        <h2>Verify Code</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <label>Enter the 6-digit code sent to your email:</label>
        <input type="text" name="code" required maxlength="6">
        <button type="submit">Verify</button>
    </form>
</body>
</html>
