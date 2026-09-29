<?php
session_start();
if (!isset($_SESSION['staff_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];
$conn = new mysqli("localhost", "root", "", "hostellaundry");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch admin name
$adminQuery = $conn->prepare("SELECT name FROM staff WHERE user_id = ?");
$adminQuery->bind_param("i", $staff_id);
$adminQuery->execute();
$admin = $adminQuery->get_result()->fetch_assoc();

$successMessage = "";

// Handle actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['staff_id'];

    if (isset($_POST['update'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $stmt = $conn->prepare("UPDATE staff SET name=?, email=? WHERE id=? AND is_admin=0");
        $stmt->bind_param("ssi", $name, $email, $id);
        $stmt->execute();
        $stmt->close();
        $successMessage = "✔️ Staff details updated successfully.";
    }

    if (isset($_POST['reset_password'])) {
        $stmt = $conn->prepare("SELECT phone_number FROM staff WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $phone = $row['phone_number'];
            $hashed = password_hash($phone, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE users SET password=? WHERE id=(SELECT user_id FROM staff WHERE id=?)");
            $updateStmt->bind_param("si", $hashed, $id);
            $updateStmt->execute();
            $updateStmt->close();
            $successMessage = "🔐 Password reset to staff's phone number.";
        }
        $stmt->close();
    }

    if (isset($_POST['delete'])) {
        $conn->begin_transaction();
        try {
            $getUser = $conn->prepare("SELECT user_id FROM staff WHERE id=? AND is_admin=0");
            $getUser->bind_param("i", $id);
            $getUser->execute();
            $userRes = $getUser->get_result()->fetch_assoc();
            $userId = $userRes['user_id'];
            $getUser->close();

            $delStaff = $conn->prepare("DELETE FROM staff WHERE id=?");
            $delStaff->bind_param("i", $id);
            $delStaff->execute();
            $delStaff->close();

            $delUser = $conn->prepare("DELETE FROM users WHERE id=?");
            $delUser->bind_param("i", $userId);
            $delUser->execute();
            $delUser->close();

            $conn->commit();
            $successMessage = "🗑️ Staff deleted successfully.";
        } catch (Exception $e) {
            $conn->rollback();
            $successMessage = "❌ Failed to delete staff.";
        }
    }
}

// Get non-admin staff list
$result = $conn->query("SELECT id, name, email FROM staff WHERE is_admin = 0");
$staffList = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f2f5f7;
      padding: 30px;
    }

    .dashboard {
      max-width: 1000px;
      margin: auto;
      background: #fff;
      padding: 40px;
      border-radius: 14px;
      box-shadow: 0 0 30px rgba(0,0,0,0.1);
    }

    h2 {
      margin-bottom: 20px;
      color: #2c3e50;
    }

    a.button {
      display: inline-block;
      padding: 12px 24px;
      margin: 10px 5px 30px 0;
      background: #3498db;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
    }

    a.button:hover {
      background: #2980b9;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background: #2c3e50;
      color: white;
    }

    td input[type="text"] {
      width: 95%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    form {
      display: flex;
      gap: 6px;
      flex-wrap: wrap;
    }

    button {
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }

    button[name="update"] { background-color: #27ae60; color: white; }
    button[name="reset_password"] { background-color: #f39c12; color: white; }
    button[name="delete"] { background-color: #e74c3c; color: white; }

    button:hover { opacity: 0.9; }

    #flash-message {
      background-color: #2ecc71;
      color: white;
      padding: 12px;
      border-radius: 6px;
      margin-bottom: 20px;
      text-align: center;
      font-weight: bold;
    }

    .logout {
      background-color: #c0392b !important;
    }
  </style>
</head>
<body>
  <div class="dashboard">
    <?php if (!empty($successMessage)): ?>
      <div id="flash-message"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <h2>Welcome, <?= htmlspecialchars($admin['name']) ?> (Admin)</h2>

    <a href="priority_requests.php" class="button">Priority Requests</a>
    <a href="notifications.php" class="button">Notifications</a>
    <a href="logout.php" class="button logout">Logout</a>

    <?php if (!empty($staffList)): ?>
      <h3>👥 Staff Management</h3>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th style="width: 260px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($staffList as $staff): ?>
            <tr>
              <form method="POST">
                <input type="hidden" name="staff_id" value="<?= $staff['id'] ?>">
                <td><input type="text" name="name" value="<?= htmlspecialchars($staff['name']) ?>"></td>
                <td><input type="text" name="email" value="<?= htmlspecialchars($staff['email']) ?>"></td>
                <td>
                  <button name="update">Save</button>
                  <button name="reset_password">Reset</button>
                  <button name="delete" onclick="return confirm('Are you sure you want to delete this staff?')">Delete</button>
                </td>
              </form>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No staff accounts found.</p>
    <?php endif; ?>
  </div>
</body>
</html>
