<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Hostel Portal</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      font-family: 'Inter', sans-serif;
      overflow: hidden;
    }

    nav {
      position: sticky;
      top: 0;
      width: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 40px;
      z-index: 10;
    }

    nav .logo {
      font-size: 1.5em;
      font-weight: 700;
      letter-spacing: 1px;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 25px;
    }

    nav ul li {
      display: inline;
    }

    nav ul li a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s;
    }

    nav ul li a:hover {
      color: #38a169;
    }

    .slideshow {
      position: fixed;
      width: 100%;
      height: 100%;
      z-index: -2;
    }

    .slideshow img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      position: absolute;
      opacity: 0;
      transition: opacity 2s ease-in-out;
    }

    .slideshow img.active {
      opacity: 1;
    }

    .overlay {
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.6);
      z-index: -1;
    }

    .content {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      color: #ffffff;
      z-index: 1;
      padding: 20px;
    }

    .content h1 {
      font-size: 3.5em;
      margin-bottom: 25px;
      font-weight: 700;
      letter-spacing: 1px;
      text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.4);
    }

    .btn-group {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 20px;
    }

    .btn-group a {
      padding: 14px 35px;
      font-size: 1.1em;
      background: linear-gradient(145deg, #38a169, #2e8b57);
      color: white;
      text-decoration: none;
      border-radius: 12px;
      font-weight: 600;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
      transition: all 0.3s ease;
    }

    .btn-group a:hover {
      background: #256e47;
      transform: scale(1.05);
    }

    @media (max-width: 768px) {
      nav {
        flex-direction: column;
        gap: 10px;
        padding: 15px;
      }

      .content h1 {
        font-size: 2.5em;
      }

      .btn-group a {
        font-size: 1em;
        padding: 12px 28px;
      }

      nav ul {
        flex-direction: column;
        gap: 10px;
      }
    }
  </style>
</head>
<body>

  <!-- Sticky Navbar -->
  <nav>
    <div class="logo">Hostel Portal</div>
    <ul>
      <li><a href="#">Enquiry</a></li>
      <li><a href="aboutus.php">About Us</a></li>
    </ul>
  </nav>

  <!-- Slideshow -->
  <div class="slideshow">
    <img src="https://i.pinimg.com/736x/86/93/5d/86935d9765e9d71b57cc19df2b8abb7d.jpg" />
    <img src="https://i.pinimg.com/736x/40/e0/1e/40e01e712961a5f715eba05e14938479.jpg" />
    <img src="https://i.pinimg.com/736x/af/21/56/af2156be969753406aa224f843d9b27a.jpg" />
  </div>

  <!-- Overlay -->
  <div class="overlay"></div>

  <!-- Welcome Text and Button Links -->
  <div class="content">
    <h1>Welcome to St.Michael's Hostel</h1>
    <div class="btn-group">
      <a href="signup.php"> Sign Up</a>
      <a href="login.php">Login</a>
    </div>
  </div>

  <!-- JavaScript for slideshow -->
  <script>
    window.addEventListener('DOMContentLoaded', () => {
      const slides = document.querySelectorAll('.slideshow img');
      let current = 0;

      function showNextSlide() {
        slides[current].classList.remove('active');
        current = (current + 1) % slides.length;
        slides[current].classList.add('active');
      }

      slides[0].classList.add('active');
      setInterval(showNextSlide, 5000);
    });
  </script>

</body>
</html>
