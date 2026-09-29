<?php 
session_start();

// 🛡️ Prevent browser from caching after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// ✅ Check login
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

$staff_user_id = $_SESSION['staff_id'];

// ✅ DB connection
$conn = new mysqli("localhost", "root", "", "hostellaundry");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Fetch staff name
$stmt = $conn->prepare("SELECT name FROM staff WHERE user_id = ?");
$stmt->bind_param("i", $staff_user_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

if (!$staff) {
    echo "❌ Staff profile not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f9fa;
      padding: 40px;
    }
    .dashboard {
      max-width: 800px;
      margin: auto;
      background: #ffffff;
      padding: 35px;
      border-radius: 14px;
      box-shadow: 0 0 25px rgba(0,0,0,0.1);
    }
    h2 {
      color: #34495e;
      margin-bottom: 10px;
    }
    h3 {
      color: #2c3e50;
      margin-top: 30px;
    }
    p {
      font-size: 16px;
      color: #555;
      margin-bottom: 25px;
    }
    a.button {
      display: inline-block;
      padding: 12px 25px;
      margin: 10px 10px 0 0;
      background: #3498db;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      transition: all 0.3s ease;
    }
    a.button:hover {
      background: #2980b9;
    }
    .logout-btn {
      background-color: #e74c3c !important;
    }
    #flash-message {
      background-color: #2ecc71;
      color: white;
      padding: 12px 20px;
      border-radius: 6px;
      text-align: center;
      font-weight: bold;
      margin-bottom: 20px;
      animation: fadeOut 2s ease-in-out 2s forwards;
    }
    @keyframes fadeOut {
      to { opacity: 0; transform: translateY(-20px); display: none; }
    }
  </style>
</head>
<body>
  <div class="dashboard">

    <?php if (isset($_SESSION['login_success'])): ?>
      <div id="flash-message">✅ Login successful!</div>
      <?php unset($_SESSION['login_success']); ?>
    <?php endif; ?>

    <h2>Welcome, <?php echo htmlspecialchars($staff['name']); ?> 👋</h2>
    <p>Use the options below to view student laundry requests by gender.</p>

    <a href="staff_laundry_list.php?gender=Male" class="button">👦 Boys' Laundry Requests</a>
    <a href="staff_laundry_list.php?gender=Female" class="button">👧 Girls' Laundry Requests</a>
    <a href="notifications.php" class="button">🔔 View Notifications</a>
    <a href="logout.php" class="button logout-btn">🚪 Logout</a>

  </div>
</body>
</html>
