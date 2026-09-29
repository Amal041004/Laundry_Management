<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  unset($_SESSION['old']);
}

$successMessage = "";
if (isset($_GET['success']) && isset($_SESSION['success'])) {
  $successMessage = $_SESSION['success'];
  $_SESSION['success'] = null;
  $_SESSION['old'] = [];
}

$conn = new mysqli("localhost", "root", "", "hostellaundry");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$message = "";
$errors = [];
$old = $_SESSION['old'] ?? [];

function isValidName($name) {
  return preg_match("/^[a-zA-Z\s]+$/", $name);
}
function isValidRollNumber($roll) {
  return preg_match("/^[a-zA-Z0-9]+$/", $roll);
}
function isValidPhone($phone) {
  return preg_match("/^[0-9]{10}$/", $phone);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["type"])) {
  $type = $_POST["type"];
  $name = trim($_POST["name"]);
  $password = $_POST["password"];
  $confirm = $_POST["confirm_password"];
  $old['name'] = $name;

  if (!isValidName($name)) {
    $errors['name'] = "❌ Invalid name.";
  } elseif ($password !== $confirm) {
    $errors['password'] = "❌ Passwords do not match.";
  }

  if ($type === "student") {
    $roll = $_POST["roll_number"];
    $phone = $_POST["phone_number"];
    $dept = $_POST["department"];
    $gender = $_POST["gender"] ?? '';
    $email = $roll . "@student.com";

    $old['roll_number'] = $roll;
    $old['phone_number'] = $phone;
    $old['department'] = $dept;
    $old['gender'] = $gender;

    if (!isValidRollNumber($roll)) {
      $errors['roll_number'] = "❌ Invalid roll number.";
    }
    if (!isValidPhone($phone)) {
      $errors['phone_number'] = "❌ Phone must be 10 digits.";
    }
    if (!in_array($gender, ['male', 'female'])) {
      $errors['gender'] = "❌ Please select gender.";
    }
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?#&_])[A-Za-z\d@$!%*?#&_]{8,}$/", $password)) {
      $errors['password'] = "❌ Weak password. Use upper, lower, number, special char, 8+ chars.";
    }

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
      $message = "❌ This student is already registered.";
    } else {
      if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'student')");
        $stmt->bind_param("ss", $email, $hashed);
        if ($stmt->execute()) {
          $user_id = $stmt->insert_id;
          $stmt2 = $conn->prepare("INSERT INTO students (user_id, name, roll_number, phone_number, department, gender) VALUES (?, ?, ?, ?, ?, ?)");
          $stmt2->bind_param("isssss", $user_id, $name, $roll, $phone, $dept, $gender);
          if ($stmt2->execute()) {
            $_SESSION['success'] = "✅ Student registered successfully!";
            header("Location: signup.php?success=1");
            exit();
          } else {
            $message = "❌ Student details error: " . $stmt2->error;
          }
          $stmt2->close();
        }
        $stmt->close();
      }
    }
    $check->close();

  } elseif ($type === "staff") {
    $email = $_POST["email"];
    $phone = $_POST["phone_number"];

    $old['email'] = $email;
    $old['phone_number'] = $phone;

    if (!isValidPhone($phone)) {
      $errors['phone_number'] = "❌ Phone must be 10 digits.";
    }

    if ($password !== $phone) {
      $errors['password'] = "❌ First password must be mobile number.";
    }

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
      $message = "❌ This staff account already exists.";
    } else {
      if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'staff')");
        $stmt->bind_param("ss", $email, $hashed);
        if ($stmt->execute()) {
          $user_id = $stmt->insert_id;
          $stmt2 = $conn->prepare("INSERT INTO staff (user_id, name, email, phone_number, is_admin) VALUES (?, ?, ?, ?, 0)");
          $stmt2->bind_param("isss", $user_id, $name, $email, $phone);
          if ($stmt2->execute()) {
            $_SESSION['success'] = "✅ Staff registered successfully!";
            header("Location: signup.php?success=1");
            exit();
          } else {
            $message = "❌ Staff details error: " . $stmt2->error;
          }
          $stmt2->close();
        }
        $stmt->close();
      }
    }
    $check->close();
  }

  $_SESSION['old'] = $old;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
    body {
      background: url('https://i.pinimg.com/736x/86/93/5d/86935d9765e9d71b57cc19df2b8abb7d.jpg') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .container {
      background: rgba(0,0,0,0.88);
      padding: 30px;
      border-radius: 16px;
      max-width: 480px;
      width: 100%;
      color: white;
      box-shadow: 0 0 25px rgba(0, 0, 0, 0.6);
    }
    .switcher { text-align: center; margin-bottom: 20px; }
    .switcher button {
      background: #29a329;
      color: white;
      padding: 10px 25px;
      margin: 5px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
    }
    form { display: none; }
    form.active { display: block; }
    input, select {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #444;
      border-radius: 8px;
      background: #111;
      color: #fff;
    }
    input[type="submit"] {
      background-color: #29a329;
      cursor: pointer;
    }
    .error-msg {
      color: red;
      font-size: 13px;
      margin-top: -8px;
      margin-bottom: 8px;
    }
    .msg {
      padding: 10px;
      margin: 10px 0;
      border-radius: 8px;
      background-color: #222;
      color: #fff;
    }
    .msg.success { border-left: 5px solid green; }
    .msg.error { border-left: 5px solid red; }
    .login-link {
      text-align: center;
      margin-top: 15px;
    }
    .login-link a {
      color: #4CAF50;
      text-decoration: underline;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="switcher">
    <button onclick="switchForm('student')">Student</button>
    <button onclick="switchForm('staff')">Staff</button>
  </div>

  <?php if ($successMessage): ?>
    <div class="msg success"><?= $successMessage ?></div>
  <?php elseif ($message): ?>
    <div class="msg error"><?= $message ?></div>
  <?php endif; ?>

  <!-- Student Form -->
  <form id="student" method="POST" class="active">
    <input type="hidden" name="type" value="student">
    <input type="text" name="name" placeholder="Full Name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
    <?php if (!empty($errors['name'])) echo '<div class="error-msg">'.$errors['name'].'</div>'; ?>

    <input type="text" name="roll_number" placeholder="Roll Number" value="<?= htmlspecialchars($old['roll_number'] ?? '') ?>" required>
    <?php if (!empty($errors['roll_number'])) echo '<div class="error-msg">'.$errors['roll_number'].'</div>'; ?>

    <input type="text" name="phone_number" placeholder="Phone Number" value="<?= htmlspecialchars($old['phone_number'] ?? '') ?>" required>
    <?php if (!empty($errors['phone_number'])) echo '<div class="error-msg">'.$errors['phone_number'].'</div>'; ?>

    <input type="text" name="department" placeholder="Department" value="<?= htmlspecialchars($old['department'] ?? '') ?>" required>

    <select name="gender" required>
     <option value="male" <?= ($old['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
      <option value="female" <?= ($old['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
      <option value="others" <?= ($old['gender'] ?? '') === 'others' ? 'selected' : '' ?>>others</option>
    </select>
    <?php if (!empty($errors['gender'])) echo '<div class="error-msg">'.$errors['gender'].'</div>'; ?>

    <input type="password" name="password" placeholder="Password" required>
    <?php if (!empty($errors['password'])) echo '<div class="error-msg">'.$errors['password'].'</div>'; ?>

    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    <input type="submit" value="Register as Student">
  </form>

  <!-- Staff Form -->
  <form id="staff" method="POST">
    <input type="hidden" name="type" value="staff">
    <input type="text" name="name" placeholder="Full Name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>

    <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>

    <input type="text" name="phone_number" placeholder="Phone Number" value="<?= htmlspecialchars($old['phone_number'] ?? '') ?>" required>
    <?php if (!empty($errors['phone_number'])) echo '<div class="error-msg">'.$errors['phone_number'].'</div>'; ?>

    <input type="password" name="password" placeholder="Password (must be mobile number)" required>
    <?php if (!empty($errors['password'])) echo '<div class="error-msg">'.$errors['password'].'</div>'; ?>

    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    <input type="submit" value="Register as Staff">
  </form>

  <div class="login-link">
    <span>Already registered? <a href="login.php">Login here</a></span>
  </div>
</div>

<script>
  function switchForm(type) {
    document.getElementById('student').classList.remove('active');
    document.getElementById('staff').classList.remove('active');
    document.getElementById(type).classList.add('active');
  }
</script>
</body>
</html>
