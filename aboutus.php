<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>About Us - Hostel Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: #121212;
      color: #ffffff;
    }

    .header {
      background: rgba(0, 0, 0, 0.85);
      padding: 40px 20px;
      text-align: center;
    }

    .header h1 {
      font-size: 3em;
      color: #2ecc71;
      margin-bottom: 10px;
    }

    .header p {
      font-size: 1.2em;
      max-width: 800px;
      margin: 0 auto;
      line-height: 1.6;
    }

    .gallery {
      padding: 40px 20px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .gallery img {
      width: 100%;
      border-radius: 12px;
      height: 220px;
      object-fit: cover;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .gallery img:hover {
      transform: scale(1.05);
      box-shadow: 0 0 15px rgba(46, 204, 113, 0.7);
    }

    @media (max-width: 600px) {
      .header h1 {
        font-size: 2em;
      }

      .header p {
        font-size: 1em;
      }
    }
  </style>
</head>
<body>

  <div class="header">
    <h1>About Our Hostel</h1>
    <p>
      Welcome to our Hostel Portal! We are committed to providing a clean, secure, and friendly environment
      for students and staff. Our hostel facilities include modern laundry services, spacious rooms, Wi-Fi, mess facilities,
      and 24/7 support. This portal also helps you manage laundry requests, track priorities, and contact staff easily.
    </p>
  </div>

  <div class="gallery">
    <img src="https://i.pinimg.com/736x/86/93/5d/86935d9765e9d71b57cc19df2b8abb7d.jpg" alt="Hostel Room">
    <img src="https://i.pinimg.com/736x/40/e0/1e/40e01e712961a5f715eba05e14938479.jpg" alt="Common Area">
    <img src="https://i.pinimg.com/736x/af/21/56/af2156be969753406aa224f843d9b27a.jpg" alt="Laundry Room">
    <img src="https://i.pinimg.com/736x/f7/25/22/f72522831bb617fb66df42a09e0332a2.jpg" alt="Cafeteria">
    <img src="https://i.pinimg.com/736x/3c/46/3e/3c463e3e5d3c8f50a5ddbbd1657b35b7.jpg" alt="Outdoor Space">
    <img src="https://i.pinimg.com/736x/e5/e6/3e/e5e63e09904bc5f5cdbfdc1d5b354c53.jpg" alt="Study Hall">
  </div>

</body>
</html>
