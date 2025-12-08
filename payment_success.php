<?php // payment_success.php
$upi   = isset($_POST['upi_id']) ? htmlspecialchars($_POST['upi_id']) : 'unknown@upi';
$plan  = isset($_POST['plan']) ? htmlspecialchars($_POST['plan']) : 'Unknown plan';
$note  = isset($_POST['note']) ? htmlspecialchars($_POST['note']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Payment requested – SignSense AI</title>
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
        <h1 class="section-title">Payment request created 🎉</h1>
        <p class="section-subtitle">
          In a real environment, a payment request would now be sent to your UPI app. For this demo
          we simply show what would be sent to your payment gateway.
        </p>

        <div class="api-box">
          <h3>Request summary</h3>
          <p class="demo-sub">Plan: <strong><?php echo $plan; ?></strong></p>
          <p class="demo-sub">UPI ID: <strong><?php echo $upi; ?></strong></p>
          <?php if ($note): ?>
            <p class="demo-sub">Note: <em><?php echo $note; ?></em></p>
          <?php endif; ?>
          <p class="demo-sub" style="margin-top:1rem;">
            Next step (for production): use this data on your backend to create a real payment
            request via your PSP, listen for webhooks, and update the user’s plan in your database.
          </p>
        </div>

        <button class="btn-primary" style="margin-top:1.2rem;" onclick="window.location.href='index.php'">
          Back to home
        </button>
      </section>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>
  </div>

  <script src="assets/js/background3d.js"></script>
  <script src="assets/js/app.js"></script>
</body>
</html>
