<?php // payment.php ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SignSense AI – Pro payment</title>
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
        <h1 class="section-title">Request Pro payment</h1>
        <p class="section-subtitle">
          Enter your UPI ID and confirm the plan. In a real deployment this page would create a real
          payment request using your PSP or payment gateway. Right now it simulates the flow and
          shows a confirmation screen.
        </p>

        <div class="payment-layout">
          <div class="payment-card">
            <h2>Pro plan – ₹499 / month</h2>
            <p class="demo-sub">
              You’ll be charged via the payment method you use with your UPI ID once your backend
              is wired up to a provider like Razorpay, Cashfree or Paytm.
            </p>

            <form action="payment_success.php" method="POST" class="payment-form" onsubmit="return validatePaymentForm();">
              <div class="form-field">
                <label for="upi_id">UPI ID</label>
                <input type="text" name="upi_id" id="upi_id" placeholder="yourname@upi" required />
                <small class="field-hint">Example: <code>username@oksbi</code> or <code>mobile@upi</code></small>
              </div>

              <div class="form-field">
                <label for="plan">Plan</label>
                <input type="text" id="plan" name="plan" value="Pro – ₹499/month" readonly />
              </div>

              <div class="form-field">
                <label for="note">Note (optional)</label>
                <textarea name="note" id="note" rows="2" placeholder="Anything we should know before onboarding?"></textarea>
              </div>

              <button type="submit" class="btn-primary">Request payment</button>
              <p class="demo-hint" style="margin-top:0.8rem;">
                This is a demo flow. Implement your own server-side payment request and
                verification before going live.
              </p>
            </form>
          </div>

          <div class="payment-card">
            <h2>What happens next?</h2>
            <ul class="pricing-list">
              <li>1. We simulate a payment request and show a confirmation screen.</li>
              <li>2. In production, you’d create a real UPI collect request or payment link.</li>
              <li>3. On success, your backend marks the account as Pro.</li>
              <li>4. You can protect certain endpoints or features based on plan.</li>
            </ul>
            <p class="demo-sub">
              This page is intentionally kept simple to help you wire it to your own gateway’s PHP SDK.
            </p>
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
