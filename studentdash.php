<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['student_id'];

$conn = new mysqli("localhost", "root", "", "hostellaundry");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT name, roll_number FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "❌ Student profile not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      background-image: url('https://i.pinimg.com/1200x/b9/cd/f9/b9cdf9e5d3165b3292de500ef5085f3a.jpg');
      background-repeat: no-repeat;
      background-position: center center;
      background-attachment: fixed;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }
    body::before {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: rgba(0, 0, 0, 0.4);
      z-index: 0;
    }
    .dashboard {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 20px;
      max-width: 850px;
      width: 95%;
      box-shadow: 0 15px 35px rgba(0,0,0,0.3);
      position: relative;
      z-index: 1;
    }
    h2 {
      font-size: 32px;
      color: #2d3436;
      margin-bottom: 8px;
    }
    p {
      font-size: 18px;
      color: #636e72;
      margin-bottom: 30px;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }
    .button {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 18px;
      background: #2ecc71;
      color: white;
      border: none;
      border-radius: 12px;
      text-decoration: none;
      font-weight: bold;
      font-size: 16px;
      box-shadow: 0 8px 15px rgba(46, 204, 113, 0.3);
      transition: all 0.3s ease;
    }
    .button:hover {
      background-color: #27ae60;
      transform: translateY(-2px);
      box-shadow: 0 10px 18px rgba(39, 174, 96, 0.3);
    }
    .logout-btn {
      background-color: #e74c3c !important;
      box-shadow: 0 8px 15px rgba(231, 76, 60, 0.3);
    }
    .logout-btn:hover {
      background-color: #c0392b !important;
    }
    #flash-message {
      background-color: #2ecc71;
      color: white;
      padding: 12px 20px;
      border-radius: 10px;
      text-align: center;
      font-weight: bold;
      margin-bottom: 25px;
      animation: fadeOut 2s ease-in-out 2s forwards;
    }
    @keyframes fadeOut {
      to {
        opacity: 0;
        transform: translateY(-20px);
        display: none;
      }
    }
    .emoji {
      font-size: 18px;
      margin-right: 8px;
    }
  </style>
</head>
<body>
  <div class="dashboard">

    <?php if (isset($_SESSION['login_success'])): ?>
      <div id="flash-message">✅ Login successful!</div>
      <?php unset($_SESSION['login_success']); ?>
    <?php endif; ?>

    <h2>Welcome, <?= htmlspecialchars($student['name']) ?> 👋</h2>
    <p>Roll Number: <?= htmlspecialchars($student['roll_number']) ?></p>

    <div class="grid">
      <a href="createlaundry.php" class="button">🧺 Create Laundry</a>
      <a href="track_laundry.php" class="button">📦 Track Laundry</a>
      <a href="priority_request.php" class="button">⚡ Raise Priority</a>
      <a href="notifications.php" class="button">🔔 Notifications</a>
      <a href="logout.php" class="button logout-btn">🚪 Logout</a>
    </div>
  </div>
</body>
</html>
