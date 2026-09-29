<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Hostel Portal Intro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
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

    body {
      background: url('https://i.pinimg.com/736x/a4/8c/db/a48cdb7232df88ec2ccb1d1947b9e98a.jpg') no-repeat center center fixed;
      background-size: cover;
      position: relative;
    }

    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      z-index: 0;
    }

    .container {
      position: relative;
      z-index: 1;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
      animation: fadeInDown 2s ease-in-out forwards;
    }

    .video-box {
      max-width: 500px;
      width: 90%;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 0 20px rgba(0,0,0,0.7);
      margin-bottom: 30px;
    }

    video {
      width: 100%;
      height: auto;
      display: block;
    }

    .enter-btn {
      display: inline-block;
      padding: 15px 40px;
      background: #2ecc71;
      color: white;
      text-decoration: none;
      font-size: 1.1em;
      font-weight: 600;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      transition: background 0.3s, transform 0.3s;
      opacity: 0;
      animation: fadeIn 2s ease-in-out 2.5s forwards;
    }

    .enter-btn:hover {
      background: #27ae60;
      transform: scale(1.05);
    }

    @keyframes fadeInDown {
      from {
        transform: translateY(-30px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    @keyframes fadeIn {
      to {
        opacity: 1;
      }
    }
  </style>
</head>
<body>

  <div class="overlay"></div>

  <div class="container">
    <div class="video-box">
      <video autoplay muted playsinline>
        <source src="Logo.mp4" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    </div>
    <a href="hostelfront.php" class="enter-btn">Visit</a>
  </div>

</body>
</html>
