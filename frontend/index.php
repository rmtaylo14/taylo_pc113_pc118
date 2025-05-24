<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>FoodHub - Delicious Meals Delivered</title>
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;700&display=swap" rel="stylesheet" />
  
  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  
  <!-- Bootstrap Icons -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css"
    rel="stylesheet"
  />
  
  <style>
    /* General */
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #fefefe;
      color: #333;
      scroll-behavior: smooth;
    }

    /* Navbar */
    .navbar {
      background: #1f1f1f;
      box-shadow: 0 2px 10px rgba(0,0,0,0.5);
      transition: background 0.4s ease;
      padding: 1rem 0;
    }
    .navbar-brand {
      font-weight: 700;
      font-size: 1.75rem;
      letter-spacing: 2px;
      color: #ffc107 !important;
      text-transform: uppercase;
      transition: color 0.3s ease;
    }
    .navbar-brand:hover {
      color: #ffca2c !important;
    }
    .nav-link {
      font-weight: 600;
      color: #eee !important;
      font-size: 1rem;
      transition: color 0.3s ease;
    }
    .nav-link:hover,
    .nav-link.active {
      color: #ffc107 !important;
      text-decoration: underline;
    }
    .btn-outline-warning {
      border-width: 2.5px;
      font-weight: 600;
      color: #ffc107;
      transition: all 0.3s ease;
    }
    .btn-outline-warning:hover {
      background: #ffc107;
      color: #212529;
      box-shadow: 0 0 10px #ffc107aa;
    }
    .btn-warning {
      font-weight: 700;
      padding: 0.85rem 2.5rem;
      background: linear-gradient(45deg, #ffc107, #ffca2c);
      border: none;
      box-shadow: 0 6px 15px #ffbf007a;
      transition: all 0.3s ease;
    }
    .btn-warning:hover {
      background: linear-gradient(45deg, #ffca2c, #ffc107);
      box-shadow: 0 8px 25px #ffbf0080;
      transform: translateY(-3px);
    }

    /* Header */
    header.py-5 {
      height: 650px;
      position: relative;
      background: linear-gradient(
          135deg,
          rgba(0, 0, 0, 0.6) 30%,
          rgba(40, 40, 40, 0.65) 100%
        ),
        url('assets/food.jpg') center/cover no-repeat;
      border-radius: 0 0 60px 60px;
      display: flex;
      align-items: center;
      box-shadow: inset 0 0 80px rgba(0,0,0,0.9);
    }
    header .container {
      position: relative;
      z-index: 10;
    }
    header h1 {
      font-size: 3.8rem;
      font-weight: 800;
      color: #ffc107;
      text-shadow: 0 4px 15px rgba(255, 193, 7, 0.7);
      margin-bottom: 1.2rem;
      letter-spacing: 4px;
      text-transform: uppercase;
    }
    header p.lead {
      font-size: 1.4rem;
      max-width: 520px;
      color: #ffe066dd;
      line-height: 1.7;
      text-shadow: 0 2px 8px rgba(0,0,0,0.75);
      margin-bottom: 2rem;
    }

    /* Buttons container in header */
    .header-buttons {
      display: flex;
      gap: 1.5rem;
    }

    /* Features Section */
    #features {
      padding: 6rem 0 5rem;
      background: #fff;
    }
    #features .feature {
      background: white;
      border-radius: 25px;
      padding: 2.8rem 2.5rem;
      box-shadow: 0 15px 25px rgba(0,0,0,0.1);
      transition: transform 0.4s ease, box-shadow 0.4s ease;
      cursor: default;
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }
    #features .feature:hover {
      transform: translateY(-15px);
      box-shadow: 0 30px 45px rgba(0,0,0,0.15);
    }
    #features .feature img {
      height: 70px;
      margin-bottom: 1.8rem;
      filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
    }
    #features h2 {
      font-weight: 700;
      font-size: 1.8rem;
      color: #222;
      margin-bottom: 1rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
    }
    #features p {
      flex-grow: 1;
      color: #555;
      font-size: 1.1rem;
      margin-bottom: 1.5rem;
      line-height: 1.6;
    }
    #features a.text-decoration-none {
      font-weight: 700;
      color: #ffc107;
      font-size: 1.1rem;
      transition: color 0.3s ease;
    }
    #features a.text-decoration-none:hover {
      color: #e0a800;
      text-decoration: underline;
    }

    /* Footer */
    footer {
      background: #1c1c1c;
      color: #bbb;
      padding: 2rem 0;
      text-align: center;
      position: relative;
      font-weight: 400;
      font-size: 0.9rem;
    }
    footer p {
      margin: 0;
    }
    footer .social-icons {
      margin: 1rem 0 0;
    }
    footer .social-icons a {
      color: #ffc107;
      margin: 0 12px;
      font-size: 1.4rem;
      transition: color 0.3s ease;
    }
    footer .social-icons a:hover {
      color: #ffca2c;
    }

    /* Responsive */
    @media (max-width: 991px) {
      header h1 {
        font-size: 3rem;
      }
      header p.lead {
        max-width: 100%;
        font-size: 1.2rem;
      }
      #features {
        padding: 4rem 1rem 4rem;
      }
    }

    @media (max-width: 575px) {
      header.py-5 {
        height: 480px;
        border-radius: 0 0 40px 40px;
      }
      header h1 {
        font-size: 2.4rem;
        letter-spacing: 2px;
      }
      .header-buttons {
        flex-direction: column;
        gap: 1rem;
      }
      .btn-warning,
      .btn-outline-warning {
        width: 100%;
        padding: 0.8rem 0;
      }
      #features .feature {
        padding: 2rem 1.5rem;
      }
    }

    /* Simple fade-in animation */
    .fade-in {
      opacity: 0;
      transform: translateY(20px);
      animation-fill-mode: forwards;
      animation-name: fadeInUp;
      animation-duration: 1s;
      animation-timing-function: ease-out;
      animation-delay: 0.2s;
    }
    .fade-in.delay-1 {
      animation-delay: 0.4s;
    }
    .fade-in.delay-2 {
      animation-delay: 0.6s;
    }
    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container px-5">
      <a class="navbar-brand" href="#!">FoodHub</a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
          <li class="nav-item ms-lg-3">
            <a class="btn btn-outline-warning me-2 px-4" href="login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-warning px-4" href="register.php">Register</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Header -->
  <header class="py-5">
    <div class="container text-center text-md-start">
      <div class="row justify-content-center justify-content-md-start">
        <div class="col-lg-6 fade-in">
          <h1>FoodHub</h1>
          <p class="lead">
            Our diverse menu offers a wide selection of mouthwatering dishes
            crafted from the freshest ingredients, all prepared with love and
            care. Explore our menu, discover new favorites, and enjoy the
            convenience of easy online ordering.
          </p>
          <div class="header-buttons">
            <a class="btn btn-warning btn-lg" href="#features">Get Started</a>
            <!-- Uncomment if you want another button -->
            <a class="btn btn-outline-warning btn-lg" href="#learn-more">Learn More</a>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Features Section -->
  <section id="features" class="fade-in delay-1">
    <div class="container px-5 my-5">
      <div class="row gx-5 text-center text-lg-start">
        <div class="col-lg-4 mb-5 mb-lg-0">
          <div class="feature">
            <img
              src="assets/feature6.jpg"
              alt="Fresh Ingredients"
              class="img-fluid"
            />
            <h2>Fresh Ingredients</h2>
            <p>
              We source only the freshest and highest quality ingredients to
              ensure every bite bursts with flavor and nutrition.
            </p>
            <a class="text-decoration-none" href="#!">
              <!-- Learn More <i class="bi bi-arrow-right"></i> -->
            </a>
          </div>
        </div>
        <div class="col-lg-4 mb-5 mb-lg-0">
          <div class="feature">
            <img
              src="assets/feature1.jpg"
              alt="Fast Delivery"
              class="img-fluid"
            />
            <h2>Fast Delivery</h2>
            <p>
              Our efficient delivery system ensures your meals arrive hot and
              fresh right to your doorstep, every time.
            </p>
            <a class="text-decoration-none" href="#!">
              <!-- Learn More <i class="bi bi-arrow-right"></i> -->
            </a>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="feature">
            <img
              src="assets/feature2.jpg"
              alt="Easy Ordering"
              class="img-fluid"
            />
            <h2>Easy Ordering</h2>
            <p>
              Browse our intuitive menu and place your order quickly with just
              a few clicks – convenience at your fingertips.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

    <!-- Learn More Section (Simple) -->
<section id="learn-more" class="py-5 bg-light">
  <div class="container text-center">
    <h2 class="mb-4">Learn More About FoodHub</h2>
    <p class="lead">At FoodHub, we’re more than just a food delivery service – we’re a community passionate about delivering happiness, one meal at a time.</p>
    <p>Founded in 2020, our mission has always been to bring people together through the love of food. We partner with local farmers and trusted suppliers to ensure every dish is made from the freshest and finest ingredients.</p>
    <p>Our innovative online platform makes it easy to explore diverse menus, place orders seamlessly, and track deliveries in real time. Whether it’s a cozy dinner for two or a celebration with friends, FoodHub is here to make your dining experience exceptional.</p>
    <p>With fast delivery, customizable meal plans, and a dedicated support team, we’re committed to making every customer’s journey delightful and satisfying.</p>
    <a href="about.php" class="btn btn-warning btn-lg mt-3">Read More</a>
  </div>
</section>


  <!-- Footer -->
  <footer>
    <div class="container">
      <p>&copy; 2025 FoodHub. All rights reserved.</p>
      <div class="social-icons">
        <a href="#!" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
        <a href="#!" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
        <a href="#!" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>document.querySelector('.btn-outline-warning').addEventListener('click', function(e) {
  e.preventDefault();  // Prevent default link behavior (optional)
  window.location.href = 'login.php';
});
</script>
</body>