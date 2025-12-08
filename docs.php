<?php // docs.php ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SignSense AI – Docs</title>
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
        <h1 class="section-title">Documentation</h1>
        <p class="section-subtitle">
          Understand how SignSense AI works and how to integrate it into your frontend + Python backend
          in a clean, predictable way.
        </p>

        <div class="docs-layout">
          <div class="api-box">
            <h3>Architecture overview</h3>
            <div class="svg-row">
              <svg class="flow-svg" viewBox="0 0 260 90">
                <defs>
                  <linearGradient id="gradFlow" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#06b6d4;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#4f46e5;stop-opacity:1" />
                  </linearGradient>
                </defs>
                <rect x="10" y="25" rx="8" ry="8" width="70" height="40" fill="none" stroke="rgba(148,163,184,0.7)" stroke-width="1.5"/>
                <text x="45" y="50" text-anchor="middle" fill="#e5e7eb" font-size="8">Camera</text>

                <rect x="95" y="15" rx="8" ry="8" width="70" height="30" fill="none" stroke="rgba(148,163,184,0.7)" stroke-width="1.5"/>
                <text x="130" y="34" text-anchor="middle" fill="#e5e7eb" font-size="8">MediaPipe</text>

                <rect x="95" y="45" rx="8" ry="8" width="70" height="30" fill="none" stroke="rgba(148,163,184,0.7)" stroke-width="1.5"/>
                <text x="130" y="64" text-anchor="middle" fill="#e5e7eb" font-size="8">JS client</text>

                <rect x="180" y="25" rx="8" ry="8" width="70" height="40" fill="none" stroke="rgba(148,163,184,0.7)" stroke-width="1.5"/>
                <text x="215" y="44" text-anchor="middle" fill="#e5e7eb" font-size="8">Python sign API</text>

                <path d="M80 45 H95" stroke="url(#gradFlow)" stroke-width="2" marker-end="url(#arrow)" />
                <path d="M165 45 H180" stroke="url(#gradFlow)" stroke-width="2" marker-end="url(#arrow)" />

                <defs>
                  <marker id="arrow" markerWidth="6" markerHeight="6" refX="5" refY="3" orient="auto">
                    <path d="M0,0 L0,6 L5,3 z" fill="url(#gradFlow)" />
                  </marker>
                </defs>
              </svg>
              <p class="demo-sub">
                Camera → MediaPipe Hands → JS client → Python Flask sign API.
              </p>
            </div>

            <p class="demo-sub">
              You can replace the heuristic classifier in <code>app.py</code> with
              your own ML model or connect it to an external service, while keeping the same
              front-end contract.
            </p>
          </div>

          <div class="api-box">
            <h3>Front-end API usage</h3>
            <p class="demo-sub">
              Subscribe to sign changes anywhere in your codebase:
            </p>
<pre class="code-snippet">
SignSenseAPI.onSignChange(({ sign, confidence }) =&gt; {
  console.log("Sign:", sign, "confidence:", confidence.toFixed(2));

  if (sign === "Thumbs Up") {
    // Example: unlock a feature, show success checkmark, etc.
    document.body.classList.add("sign-success");
  } else {
    document.body.classList.remove("sign-success");
  }
});
</pre>
            <p class="demo-sub">
              <code>sign</code> will be one of:
              <code>Open Hand</code>, <code>Thumbs Up</code>, <code>Fist</code>, <code>No Hand</code>, or <code>Unknown</code>.
            </p>
          </div>

          <div class="api-box">
            <h3>Python sign API contract (Flask)</h3>
            <p class="demo-sub">
              The browser sends landmarks to:
              <code>POST http://127.0.0.1:5000/api/handsign</code>
            </p>
<pre class="code-snippet">
POST http://127.0.0.1:5000/api/handsign
Content-Type: application/json

{
  "landmarks": [
    { "x": 123, "y": 45, "z": -0.1, "name": "wrist" },
    { "x": 130, "y": 12, "z": -0.2, "name": "index_finger_tip" },
    ...
  ]
}
</pre>
            <p class="demo-sub">
              The Python endpoint returns:
            </p>
<pre class="code-snippet">
{
  "success": true,
  "sign": "Thumbs Up",
  "confidence": 0.85
}
</pre>
            <p class="demo-sub">
              You can log raw landmarks or the final sign to a database, a queue or your analytics tools.
            </p>
          </div>

          <div class="api-box">
            <h3>Customising signs</h3>
            <p class="demo-sub">
              The default rules in <code>classify_sign</code> (in <code>app.py</code>) use simple distance-based thresholds.
              To add new signs, you can:
            </p>
            <ul class="pricing-list">
              <li>Capture sample landmarks for the new gesture.</li>
              <li>Compute distances/angles and derive thresholds.</li>
              <li>Add a new conditional block in <code>classify_sign</code> to map them to a sign string.</li>
            </ul>
            <p class="demo-sub">
              For advanced setups, train a small classifier (for example, a simple logistic regression)
              using the landmarks as features, and call it from Python instead of heuristics.
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
