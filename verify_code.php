<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_code = $_POST['code'];

    if ($entered_code == $_SESSION['reset_code']) {
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Incorrect code. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Code - BookHaven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="profile.css">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            background-color: #f4f6f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .profile-container {
            width: 100%;
            min-height: 100vh;
            padding: 50px;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .profile-card {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
        }
        .profile-header h1 {
            font-size: 32px;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .profile-header p {
            color: #000;
            font-size: 15px;
            margin-bottom: 25px;
        }
        .edit-form label {
            font-weight: 600;
            color: #2c3e50;
            display: block;
            margin-bottom: 6px;
        }
        .edit-form input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            background-color: #fafafa;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .edit-form input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 6px rgba(52, 152, 219, 0.3);
            background-color: #fff;
        }
        .edit-profile-btn {
            background-color: #3498db;
            color: white;
            padding: 12px 22px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: background-color 0.3s ease;
        }
        .edit-profile-btn:hover {
            background-color: #2980b9;
        }
        .error {
            background-color: #fcebea;
            color: #cc1f1a;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <h1>Verify Code</h1>
            <p>Enter the 6-digit code sent to your email.</p>
        </div>

        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="POST" class="edit-form">
            <label for="code">Reset Code</label>
            <input type="text" name="code" id="code" required maxlength="6">

            <button type="submit" class="edit-profile-btn">
                <i class="fas fa-check-circle"></i> Verify Code
            </button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
