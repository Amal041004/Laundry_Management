<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
$student_id = $_SESSION['student_id'];

$conn = new mysqli("localhost", "root", "", "hostellaundry");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get student gender
$gender = 'male'; // fallback
$getGender = $conn->prepare("SELECT gender FROM students WHERE user_id = ?");
$getGender->bind_param("i", $student_id);
$getGender->execute();
$getGender->bind_result($gender);
$getGender->fetch();
$getGender->close();

// Fetch gender-specific clothing types
$regularStmt = $conn->prepare("SELECT id, name FROM clothing_types WHERE is_special = 0 AND (gender = ? OR gender = 'unisex')");
$regularStmt->bind_param("s", $gender);
$regularStmt->execute();
$regular = $regularStmt->get_result();

$specialStmt = $conn->prepare("SELECT id, name FROM clothing_types WHERE is_special = 1 AND (gender = ? OR gender = 'unisex')");
$specialStmt->bind_param("s", $gender);
$specialStmt->execute();
$special = $specialStmt->get_result();

// Handle form submission
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = date("Y-m-d");
    $laundry_number = "L" . time() . rand(10, 99);

    $stmt = $conn->prepare("INSERT INTO laundry (laundry_number, student_id, received_date, status) VALUES (?, ?, ?, 'Pending')");
    $stmt->bind_param("sis", $laundry_number, $student_id, $date);

    if ($stmt->execute()) {
        $laundry_id = $stmt->insert_id;
        $insertStmt = $conn->prepare("INSERT INTO laundry_items (laundry_id, clothing_type_id, quantity) VALUES (?, ?, ?)");
        $inserted = false;

        foreach ($_POST as $key => $val) {
            if (strpos($key, "cloth_") === 0 && is_numeric($val) && $val > 0) {
                $cloth_id = (int) str_replace("cloth_", "", $key);
                $qty = (int) $val;
                $insertStmt->bind_param("iii", $laundry_id, $cloth_id, $qty);
                $insertStmt->execute();
                $inserted = true;
            }
        }

        if ($inserted) {
            $success = "✅ Laundry request submitted successfully!";
        } else {
            $error = "⚠️ No clothes selected.";
            $conn->query("DELETE FROM laundry WHERE id = $laundry_id");
        }
    } else {
        $error = "❌ Failed to create request.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Create Laundry Request</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }
    body {
      background: linear-gradient(to right, #dff9fb, #c7ecee);
      padding: 50px;
    }
    .container {
      max-width: 700px;
      margin: auto;
      background: #fff;
      border-radius: 16px;
      padding: 35px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    h2 { margin-bottom: 20px; color: #2c3e50; }
    label { display: block; margin-top: 10px; font-weight: 600; }
    input[type="number"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    input[type="submit"] {
      margin-top: 25px;
      width: 100%;
      background: #2ecc71;
      color: white;
      border: none;
      padding: 14px;
      border-radius: 10px;
      font-weight: bold;
      cursor: pointer;
    }
    input[type="submit"]:hover {
      background: #27ae60;
    }
    .msg {
      padding: 12px;
      margin-bottom: 20px;
      border-radius: 10px;
      text-align: center;
      font-weight: bold;
    }
    .success { background: #d4efdf; color: #239b56; }
    .error { background: #fdebd0; color: #d35400; }

    .toggle-section {
      margin-top: 20px;
    }
    .toggle-btn {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      background-color: #2980b9;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }
    .toggle-btn:hover {
      background-color: #2471a3;
    }
    .clothing-list {
      display: none;
      margin-top: 10px;
      padding: 15px;
      background: #f7f9f9;
      border: 1px solid #ccc;
      border-radius: 10px;
    }
    .section-title {
      font-size: 18px;
      margin-bottom: 10px;
      color: #2c3e50;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>🧺 Create Laundry Request</h2>

    <?php if ($success): ?>
      <div class="msg success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="msg error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
      <!-- REGULAR CLOTHES -->
      <div class="toggle-section">
        <button type="button" class="toggle-btn" onclick="toggleList('regularList')">👕 Regular Clothes</button>
        <div id="regularList" class="clothing-list">
          <div class="section-title">Regular Clothes</div>
          <?php while ($row = $regular->fetch_assoc()): ?>
            <label><?= htmlspecialchars($row['name']) ?></label>
            <input type="number" name="cloth_<?= $row['id'] ?>" min="0" value="0">
          <?php endwhile; ?>
        </div>
      </div>

      <!-- SPECIAL CLOTHES -->
      <div class="toggle-section">
        <button type="button" class="toggle-btn" onclick="toggleList('specialList')">✨ Special Clothes</button>
        <div id="specialList" class="clothing-list">
          <div class="section-title">Special Clothes</div>
          <?php while ($row = $special->fetch_assoc()): ?>
            <label><?= htmlspecialchars($row['name']) ?></label>
            <input type="number" name="cloth_<?= $row['id'] ?>" min="0" value="0">
          <?php endwhile; ?>
        </div>
      </div>

      <input type="submit" value="Submit Request">
    </form>
  </div>

  <script>
    function toggleList(id) {
      const section = document.getElementById(id);
      section.style.display = section.style.display === 'block' ? 'none' : 'block';
    }
  </script>
</body>
</html>
