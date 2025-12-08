<?php // pricing.php ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SignSense AI – Pricing</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="favicon.png" href="eye1.png" />

  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <script src="https://unpkg.com/three@0.160.0/build/three.min.js"></script>
  <script src="https://unpkg.com/three@0.160.0/examples/js/controls/OrbitControls.js"></script>
</head>
<body>
  <canvas id="bg3d"></canvas>

  <div class="page">
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <main>
      <section class="section section-tight">
        <h1 class="section-title">Pricing</h1>
        <p class="section-subtitle">
          Start free, then upgrade to Pro when you’re ready to ship gesture-powered experiences.
          For custom setups, our team can help you integrate with your stack.
        </p>

        <div class="pricing-grid">
          <div class="pricing-card">
            <div class="pricing-badge">Free</div>
            <h2>Sandbox</h2>
            <p class="pricing-price">₹0<span>/month</span></p>
            <ul class="pricing-list">
              <li>Local-only detection (no external billing)</li>
              <li>Single developer / side project</li>
              <li>Full access to source code</li>
              <li>Community support via email</li>
            </ul>
            <button class="btn-ghost" disabled>Current plan</button>
          </div>

          <div class="pricing-card pricing-featured">
            <div class="pricing-badge badge-hot">Popular</div>
            <h2>Pro</h2>
            <p class="pricing-price">₹499<span>/month</span></p>
            <ul class="pricing-list">
              <li>Priority email support</li>
              <li>Guided integration call</li>
              <li>Production-ready docs & patterns</li>
              <li>Early access to new sign templates</li>
            </ul>
            <button class="btn-primary" onclick="window.location.href='payment.php'">
              Start Pro
            </button>
            <p class="pricing-note">
              “Start Pro” takes you to a UPI-based payment request page. In production, connect to
              your PSP or payment gateway.
            </p>
          </div>

          <div class="pricing-card">
            <div class="pricing-badge">Enterprise</div>
            <h2>Custom</h2>
            <p class="pricing-price">Let’s talk</p>
            <ul class="pricing-list">
              <li>On-prem or VPC deployment</li>
              <li>Custom SLAs & uptime commitments</li>
              <li>Dedicated solution engineer</li>
              <li>Compliance & security reviews</li>
            </ul>
            <a href="contact.php" class="btn-ghost btn-link">Contact sales</a>
          </div>
        </div>
      </section>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>
  </div>

  <script src="assets/js/background3d.js"></script>
  <script src="assets/js/app.js"></script>
</body>
</html>
