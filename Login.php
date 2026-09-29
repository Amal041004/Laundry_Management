<?php
session_start();

$host     = "localhost";
$user     = "root";
$password = "";
$database = "hostellaundry";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$error       = "";
$active_role = "student"; // default selected

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $active_role   = $_POST['role'];
  $role          = $_POST['role'];
  $username      = $_POST['username'];
  $inputPassword = $_POST['password'];

  if ($role === 'student') {
    $stmt = $conn->prepare(
      "SELECT u.id AS user_id, u.password
       FROM users u
       JOIN students s ON u.id = s.user_id
       WHERE s.roll_number = ? AND u.role = 'student'"
    );
    $stmt->bind_param("s", $username);

  } elseif ($role === 'staff') {
    $stmt = $conn->prepare(
      "SELECT u.id AS user_id, u.password
       FROM users u
       JOIN staff st ON u.id = st.user_id
       WHERE st.email = ? AND u.role = 'staff'"
    );
    $stmt->bind_param("s", $username);

  } elseif ($role === 'admin') {
    $stmt = $conn->prepare(
      "SELECT u.id AS user_id, u.password
       FROM users u
       JOIN staff a ON u.id = a.user_id
       WHERE u.email = ? AND u.role = 'admin' AND a.is_admin = 1"
    );
    $stmt->bind_param("s", $username);
  }

  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (password_verify($inputPassword, $row['password'])) {
      $_SESSION['login_success'] = true;

      if ($role === 'student') {
        $_SESSION['student_id'] = $row['user_id'];
        header("Location: studentdash.php");
      } elseif ($role === 'staff') {
        $_SESSION['staff_id'] = $row['user_id'];
        $_SESSION['is_admin'] = 0;
        header("Location: staffdash.php");
      } elseif ($role === 'admin') {
        $_SESSION['staff_id'] = $row['user_id'];
        $_SESSION['is_admin'] = 1;
        header("Location: admindash.php");
      }
      exit();
    } else {
      $error = "❌ Incorrect password.";
    }
  } else {
    $error = "❌ User not found. Please sign up first.";
  }

  $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Hostel Laundry Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
    body{
      background:url('https://i.pinimg.com/736x/54/c5/d2/54c5d2d5b30bb37ed19841a6cacbb77e.jpg') no-repeat center center fixed;
      background-size:cover;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;
    }
    .container{
      width:100%;max-width:450px;background:rgba(0,0,0,0.85);border-radius:16px;padding:40px;color:#fff;
      box-shadow:0 0 20px rgba(0,0,0,0.5);
    }
    .container h2{text-align:center;margin-bottom:25px;font-weight:600;font-size:28px;color:#2ecc71;}
    .btn-group{display:flex;justify-content:space-between;gap:10px;margin-bottom:30px;}
    .btn-group button{
      flex:1;padding:12px;font-size:15px;border:none;border-radius:8px;background:#2ecc71;color:#fff;
      font-weight:bold;cursor:pointer;transition:all .3s ease;
    }
    .btn-group button.active{
      background:#1e8449;box-shadow:0 0 10px #27ae60;transform:scale(1.03);
    }
    .btn-group button:hover{background:#27ae60;transform:scale(1.03);}
    form{display:none;margin-top:10px;}
    form h3{margin-bottom:15px;font-size:20px;}
    input[type="text"],input[type="password"]{
      width:100%;padding:12px;margin:10px 0 10px;border:none;border-radius:8px;background:#222;color:#fff;
    }
    input::placeholder{color:#bbb;}
    input[type="submit"]{
      width:100%;padding:12px;border:none;border-radius:8px;background:#2ecc71;color:#fff;font-weight:bold;
      cursor:pointer;transition:all .3s ease;
    }
    input[type="submit"]:hover{background:#27ae60;}
    .error{
      color:#e74c3c;
      font-weight:bold;
      margin: -5px 0 15px;
      padding-left: 5px;
      display:flex;
      align-items:center;
      gap:6px;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Hostel Laundry Portal</h2>

  <div class="btn-group">
    <button data-role="admin"  onclick="showForm('admin')">Admin</button>
    <button data-role="staff"  onclick="showForm('staff')">Staff</button>
    <button data-role="student" onclick="showForm('student')">Student</button>
  </div>

  <!-- Admin Login -->
  <form id="admin-form" method="post">
    <h3>Admin Login</h3>
    <input type="hidden" name="role" value="admin">
    <input type="text" name="username" placeholder="Admin Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <?php if (!empty($error) && $active_role === 'admin'): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <input type="submit" value="Login">
  </form>

  <!-- Staff Login -->
  <form id="staff-form" method="post">
    <h3>Staff Login</h3>
    <input type="hidden" name="role" value="staff">
    <input type="text" name="username" placeholder="Staff Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <?php if (!empty($error) && $active_role === 'staff'): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <input type="submit" value="Login">
  </form>

  <!-- Student Login -->
  <form id="student-form" method="post">
    <h3>Student Login</h3>
    <input type="hidden" name="role" value="student">
    <input type="text" name="username" placeholder="Roll Number" required>
    <input type="password" name="password" placeholder="Password" required>
    <?php if (!empty($error) && $active_role === 'student'): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <input type="submit" value="Login">
  </form>
</div>

<script>
  function showForm(type){
    ['admin','staff','student'].forEach(r=>{
      document.getElementById(r+'-form').style.display='none';
      document.querySelector(`button[data-role="${r}"]`).classList.remove('active');
    });
    document.getElementById(type+'-form').style.display='block';
    document.querySelector(`button[data-role="${type}"]`).classList.add('active');
  }

  // select form based on server-side role
  const roleFromServer = "<?= $active_role ?>";
  window.onload = ()=> showForm(roleFromServer || 'student');
</script>

</body>
</html>
