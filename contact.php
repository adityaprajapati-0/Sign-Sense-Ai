<?php // contact.php ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SignSense AI – Contact sales</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="favicon.png" href="eye1.png" />

  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- three.js (optional, for bg3d and any 3D background you use) -->
  <script src="https://unpkg.com/three@0.160.0/build/three.min.js"></script>
  <script src="https://unpkg.com/three@0.160.0/examples/js/controls/OrbitControls.js"></script>

  <!-- EmailJS browser SDK -->
  <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
</head>
<body>
  <!-- Background canvas -->
  <canvas id="bg3d"></canvas>

  <!-- Responsive background SVG orbs (same style as index.php) -->
  <svg class="bg-orb bg-orb-1" viewBox="0 0 260 260" aria-hidden="true">
    <defs>
      <radialGradient id="bgOrbGrad1" cx="30%" cy="20%" r="80%">
        <stop offset="0%" stop-color="#22d3ee" stop-opacity="0.9"/>
        <stop offset="55%" stop-color="#4f46e5" stop-opacity="0.8"/>
        <stop offset="100%" stop-color="#020617" stop-opacity="0"/>
      </radialGradient>
    </defs>
    <circle cx="130" cy="130" r="120" fill="url(#bgOrbGrad1)" />
  </svg>

  <svg class="bg-orb bg-orb-2" viewBox="0 0 260 260" aria-hidden="true">
    <defs>
      <radialGradient id="bgOrbGrad2" cx="70%" cy="10%" r="80%">
        <stop offset="0%" stop-color="#22c55e" stop-opacity="0.9"/>
        <stop offset="55%" stop-color="#0f766e" stop-opacity="0.7"/>
        <stop offset="100%" stop-color="#020617" stop-opacity="0"/>
      </radialGradient>
    </defs>
    <circle cx="130" cy="130" r="120" fill="url(#bgOrbGrad2)" />
  </svg>

  <svg class="bg-orb bg-orb-3" viewBox="0 0 260 260" aria-hidden="true">
    <defs>
      <radialGradient id="bgOrbGrad3" cx="40%" cy="80%" r="80%">
        <stop offset="0%" stop-color="#f97316" stop-opacity="0.85"/>
        <stop offset="55%" stop-color="#581c87" stop-opacity="0.8"/>
        <stop offset="100%" stop-color="#020617" stop-opacity="0"/>
      </radialGradient>
    </defs>
    <circle cx="130" cy="130" r="120" fill="url(#bgOrbGrad3)" />
  </svg>

  <div class="page">
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <main>
      <section class="section section-tight">
        <h1 class="section-title">Contact sales</h1>
        <p class="section-subtitle">
          Tell us briefly about your project, and we’ll get back with integration ideas
          and the best plan for your team.
        </p>

        <div class="contact-layout">
          <!-- LEFT: FORM -->
          <div class="contact-card">
            <h2>Send us a message</h2>

            <form id="contactForm" class="contact-form">
              <div class="form-field">
                <label for="name">Full name</label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  placeholder="Your name"
                  required
                />
              </div>

              <div class="form-field">
                <label for="email">Email</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  placeholder="you@example.com"
                  required
                />
              </div>

              <div class="form-field">
                <label for="company">Company / project (optional)</label>
                <input
                  type="text"
                  id="company"
                  name="company"
                  placeholder="Company or project name"
                />
              </div>

              <div class="form-field">
                <label for="message">How can we help?</label>
                <textarea
                  id="message"
                  name="message"
                  rows="4"
                  placeholder="Describe your use case, timeline, and team size."
                  required
                ></textarea>
              </div>

              <button type="submit" class="btn-primary" id="sendBtn">Send message</button>
              <p class="demo-hint" id="contactStatus" style="margin-top:0.7rem;"></p>
            </form>
          </div>

          <!-- RIGHT: INFO -->
          <div class="contact-card">
            <h2>What to expect</h2>
            <ul class="pricing-list">
              <li>We reply within 1–2 business days.</li>
              <li>We can review a short Loom, Figma file or GitHub repo.</li>
              <li>We’ll suggest integration patterns and recommend a plan.</li>
            </ul>
            <p class="demo-sub">
              You can also reach us directly at:
              <p>
                adityaprajapati1234567@gmail.com</p>
              </a>
            </p>
          </div>
        </div>
      </section>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>
  </div>

  <script src="assets/js/background3d.js"></script>
  <script src="assets/js/app.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const form = document.getElementById("contactForm");
      const statusEl = document.getElementById("contactStatus");
      const sendBtn = document.getElementById("sendBtn");

      // Make sure EmailJS is available
      if (window.emailjs) {
        // Initialize with the same public key as in your React component
        emailjs.init("FJ75i1g53zFfLo1_6");
      } else {
        console.warn("EmailJS SDK not loaded.");
      }

      form.addEventListener("submit", function (e) {
        e.preventDefault();

        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const company = document.getElementById("company").value.trim();
        const message = document.getElementById("message").value.trim();

        if (!window.emailjs) {
          statusEl.textContent = "Email service not loaded. Please check your internet connection.";
          statusEl.style.color = "#f97316";
          return;
        }

        if (!name || !email || !message) {
          statusEl.textContent = "Please fill in your name, email, and message.";
          statusEl.style.color = "#f97316";
          return;
        }

        statusEl.textContent = "Sending...";
        statusEl.style.color = "#e5e7eb";
        sendBtn.disabled = true;
        sendBtn.textContent = "Sending...";

        const serviceID = "service_lmhycgf";
        const templateID = "template_v16cq8t";

        const fullMessage =
          "You received a new message from: " + name + " (" + email + ")\n\n" +
          message +
          (company ? "\n\nCompany / Project: " + company : "");

        emailjs
          .send(
            serviceID,
            templateID,
            {
              from_name: name,
              from_email: "noreply@yourdomain.com",
              to_name: "Aditya",
              to_email: "adityaprajapati1234567@gmail.com",
              reply_to: email,
              message: fullMessage,
            }
          )
          .then(() => {
            statusEl.textContent = "Your message has been sent! We'll get back to you soon.";
            statusEl.style.color = "#22c55e";
            form.reset();
            sendBtn.disabled = false;
            sendBtn.textContent = "Send message";
          })
          .catch((err) => {
            console.error("EmailJS Error:", err);
            statusEl.textContent =
              "Something went wrong while sending. Please try again, or use the email link above.";
            statusEl.style.color = "#f97316";
            sendBtn.disabled = false;
            sendBtn.textContent = "Send message";
          });
      });
    });
  </script>
</body>
</html>
