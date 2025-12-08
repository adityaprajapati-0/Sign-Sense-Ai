/* assets/js/app.js
   - Uses MediaPipe Hands (hands.js / global Hands) for hand landmarks
   - Draws landmarks on overlay canvas
   - Sends landmarks to Python Flask API at http://127.0.0.1:5000/api/handsign
   - Updates UI sign text, confidence, keyword, orb, debug panel
   - Handles background orbs + small UI helpers
*/

(function () {
  // ----------------- helpers -----------------
  function $(sel, root = document) { return root.querySelector(sel); }
  function $all(sel, root = document) { return Array.from((root || document).querySelectorAll(sel)); }

  // ----------------- public event API -----------------
  const signListeners = [];
  window.SignSenseAPI = {
    onSignChange(cb) {
      if (typeof cb === "function") signListeners.push(cb);
    }
  };
  function broadcastSign(sign, confidence) {
    signListeners.forEach(fn => {
      try { fn({ sign, confidence }); } catch (e) { console.error(e); }
    });
  }

  // ----------------- label → keyword mapping -----------------
  function keywordForSign(label) {
    switch ((label || "").toLowerCase()) {
      case "open hand": return "HELLO";
      case "thumbs up": return "YES";
      case "fist": return "STOP";
      case "peace": return "OK";
      case "rock": return "ROCK";
      case "point": return "LOOK";
      case "pinch": return "SMALL";
      case "four fingers": return "FOUR";
      case "three fingers": return "THREE";
      case "pinky": return "TOILET";
      case "middle": return "ANGRY";
      case "ring pinky middle": return "AWESOME";
      case "no hand":
      case "unknown":
      default: return "—";
    }
  }

  // ----------------- orb SVG helpers -----------------
  function hexToRgba(hex, a = 1) {
    try {
      const c = hex.replace("#", "");
      const bigint = parseInt(c, 16);
      const r = (bigint >> 16) & 255;
      const g = (bigint >> 8) & 255;
      const b = bigint & 255;
      return `rgba(${r}, ${g}, ${b}, ${a})`;
    } catch {
      return `rgba(99,102,241,${a})`;
    }
  }

  function updateOrbSVG(sign) {
    try {
      const svg = $(".hero-orb-svg");
      if (!svg) return;

      let mainColor = "#6366f1";
      let emissive = "#1d4ed8";
      let pulse = 1.0;

      if (/thumbs up/i.test(sign)) {
        mainColor = "#22c55e";
        emissive = "#16a34a";
        pulse = 1.25;
      } else if (/fist/i.test(sign)) {
        mainColor = "#ef4444";
        emissive = "#b91c1c";
        pulse = 0.85;
      } else if (/open hand/i.test(sign)) {
        mainColor = "#06b6d4";
        emissive = "#0ea5e9";
        pulse = 1.12;
      } else if (/middle/i.test(sign)) {
        mainColor = "#dc2626";
        emissive = "#7f1d1d";
        pulse = 1.2;
      } else if (/ring pinky middle/i.test(sign)) {
        mainColor = "#8b5cf6";
        emissive = "#ec4899";
        pulse = 1.18;
      } else if (/peace|rock|point|four fingers|three fingers|pinch|pinky/i.test(sign)) {
        mainColor = "#0ea5e9";
        emissive = "#22c55e";
        pulse = 1.1;
      }

      const circles = $all("circle", svg);
      const ellipses = $all("ellipse", svg);
      const central = circles[0] || ellipses[0];

      if (central) central.setAttribute("fill", mainColor);
      if (ellipses[0]) ellipses[0].setAttribute("stroke", mainColor);
      if (ellipses[1]) ellipses[1].setAttribute("stroke", emissive);

      if (circles[1]) circles[1].setAttribute("fill", "#22c55e");
      if (circles[2]) circles[2].setAttribute("fill", "#06b6d4");
      if (circles[3]) circles[3].setAttribute("fill", "#f97316");

      svg.style.transition = "transform 260ms ease, filter 260ms ease";
      svg.style.transformOrigin = "50% 50%";
      svg.style.transform = `scale(${pulse})`;
      svg.style.filter = `drop-shadow(0 8px 30px ${hexToRgba(mainColor, 0.18)})`;

      const labelText = Array.from($all("text", svg))
        .find(t => /SIGN|HELLO|YES|STOP|OK|ROCK|SMALL|LOOK|FOUR|THREE|TOILET|AWESOME|ANGRY/i.test(t.textContent));
      if (labelText) {
        const kw = keywordForSign(sign);
        labelText.textContent = (sign && kw !== "—") ? kw : "SIGN ORB";
      }
    } catch (e) {
      console.warn("updateOrbSVG failed", e);
    }
  }

  window.updateSignVisual = updateOrbSVG;
  window.__SignSense_updateOrbSVG = updateOrbSVG;

  function setDotColorFor(label) {
    const dot = $("#signDot");
    if (!dot) return;

    let gradient = "linear-gradient(135deg,#4b5563,#6b7280)";
    if (/open hand/i.test(label)) {
      gradient = "linear-gradient(135deg,#06b6d4,#0ea5e9)";
    } else if (/thumbs up/i.test(label)) {
      gradient = "linear-gradient(135deg,#22c55e,#16a34a)";
    } else if (/fist/i.test(label)) {
      gradient = "linear-gradient(135deg,#ef4444,#b91c1c)";
    } else if (/middle/i.test(label)) {
      gradient = "linear-gradient(135deg,#dc2626,#7f1d1d)";
    } else if (/ring pinky middle/i.test(label)) {
      gradient = "linear-gradient(135deg,#8b5cf6,#ec4899)";
    } else if (/pinky|peace|rock|point|four fingers|three fingers|pinch/i.test(label)) {
      gradient = "linear-gradient(135deg,#38bdf8,#6366f1)";
    }

    dot.style.background = gradient;
  }

  // ----------------- background orbs parallax -----------------
  function initBackgroundOrbs() {
    const orbs = $all(".bg-orb");
    if (!orbs.length) return;
    const depths = [10, 18, 26];
    orbs.forEach((o, i) => o.dataset.depth = depths[i] || 16);

    function onMove(e) {
      const xNorm = e.clientX / window.innerWidth - 0.5;
      const yNorm = e.clientY / window.innerHeight - 0.5;
      orbs.forEach((orb) => {
        const d = parseFloat(orb.dataset.depth || 16);
        const tx = -xNorm * d;
        const ty = -yNorm * d * 0.6;
        orb.style.transform = `translate3d(${tx}px, ${ty}px, 0)`;
      });
    }

    function onScroll() {
      const scrollY = window.scrollY || window.pageYOffset;
      const maxScroll = Math.max(1, document.body.scrollHeight - window.innerHeight);
      const ratio = scrollY / maxScroll;
      orbs.forEach((orb, idx) => {
        const base = 0.55 - idx * 0.08;
        const osc = Math.sin(ratio * Math.PI * 2 + idx) * 0.06;
        orb.style.opacity = (Math.max(0.25, Math.min(0.75, base + osc))).toFixed(2);
      });
    }

    window.addEventListener("pointermove", onMove);
    window.addEventListener("scroll", onScroll);
    onScroll();
  }

  // ----------------- hand detection (MediaPipe Hands) -----------------
  async function initHandDetection() {
    const video = $("#camera");
    const overlay = $("#overlayCanvas");
    if (!video || !overlay) return;
    const ctx = overlay.getContext("2d");

    const signText = $("#signText");
    const signConfidence = $("#signConfidence");
    const signKeywordEl = $("#signKeyword");
    const apiDebug = $("#apiDebug");
    const apiStatusTag = $("#apiStatusTag");

    // check global Hands from hands.js
    if (typeof Hands === "undefined") {
      console.error("MediaPipe Hands (Hands) not loaded. Check hands.js script tag.");
      if (signText) signText.textContent = "AI model failed to load";
      return;
    }

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      if (signText) signText.textContent = "Camera not supported";
      return;
    }

    // camera
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } });
      video.srcObject = stream;
      await new Promise(resolve => { video.onloadedmetadata = () => { video.play(); resolve(); }; });
      if (signText) signText.textContent = "Camera ready — show a hand";
    } catch (e) {
      console.error("camera error", e);
      if (signText) signText.textContent = "Camera permission denied";
      return;
    }

    // overlay sizing
    function resizeOverlay() {
      const rect = video.getBoundingClientRect();
      overlay.width = rect.width;
      overlay.height = rect.height;
    }
    window.addEventListener("resize", resizeOverlay);
    resizeOverlay();

    // MediaPipe Hands instance
    const hands = new Hands({
      locateFile: file => `https://cdn.jsdelivr.net/npm/@mediapipe/hands@0.4/${file}`
    });

    hands.setOptions({
      maxNumHands: 1,
      modelComplexity: 1,
      minDetectionConfidence: 0.5,
      minTrackingConfidence: 0.5
    });

    const lmNames = [
      "wrist",
      "thumb_cmc", "thumb_mcp", "thumb_ip", "thumb_tip",
      "index_finger_mcp", "index_finger_pip", "index_finger_dip", "index_finger_tip",
      "middle_finger_mcp", "middle_finger_pip", "middle_finger_dip", "middle_finger_tip",
      "ring_finger_mcp", "ring_finger_pip", "ring_finger_dip", "ring_finger_tip",
      "pinky_finger_mcp", "pinky_finger_pip", "pinky_finger_dip", "pinky_finger_tip"
    ];

    function drawLandmarks(lms) {
      if (!ctx || !overlay.width || !overlay.height) return;
      ctx.clearRect(0, 0, overlay.width, overlay.height);
      ctx.fillStyle = "rgba(56,189,248,0.95)";
      lms.forEach((lm) => {
        const x = (1 - lm.x) * overlay.width; // mirror horizontally
        const y = lm.y * overlay.height;
        ctx.beginPath();
        ctx.arc(x, y, 3, 0, Math.PI * 2);
        ctx.fill();
      });
    }

    async function sendToPythonAPI(lms) {
      if (!Array.isArray(lms) || !lms.length) return;

      const landmarks = lms.map((lm, idx) => ({
        x: lm.x,
        y: lm.y,
        z: lm.z ?? 0,
        name: lmNames[idx] || ""
      }));

      try {
        if (apiStatusTag) {
          apiStatusTag.textContent = "calling python...";
          apiStatusTag.style.color = "#e5e7eb";
        }

        const res = await fetch("http://127.0.0.1:5000/api/handsign", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ landmarks })
        });

        const json = await res.json();
        if (apiDebug) apiDebug.textContent = JSON.stringify(json, null, 2);

        if (json && json.success) {
          if (apiStatusTag) {
            apiStatusTag.textContent = "ok";
            apiStatusTag.style.color = "#22c55e";
          }
          const label = json.sign || "Unknown";
          const conf = Number(json.confidence || 0);
          updateUI(label, conf);
        } else {
          if (apiStatusTag) {
            apiStatusTag.textContent = "error";
            apiStatusTag.style.color = "#f97316";
          }
          updateUI("Unknown", 0);
        }
      } catch (e) {
        console.error("python api error", e);
        if (apiStatusTag) {
          apiStatusTag.textContent = "server down";
          apiStatusTag.style.color = "#f97316";
        }
        updateUI("Unknown", 0);
      }
    }

    let lastLabel = null;
    function updateUI(label, confidence) {
      if (signText) signText.textContent = label;
      if (signConfidence) signConfidence.textContent =
        `Confidence: ${confidence.toFixed(2)}`;
      if (signKeywordEl) signKeywordEl.textContent = keywordForSign(label);
      setDotColorFor(label);
      updateOrbSVG(label);
      if (label !== lastLabel) {
        lastLabel = label;
        broadcastSign(label, confidence ?? 0);
      }
    }

    hands.onResults((results) => {
      const lmsList = results.multiHandLandmarks || [];
      if (lmsList.length > 0) {
        const lms = lmsList[0];
        drawLandmarks(lms);
        sendToPythonAPI(lms);
      } else {
        if (ctx) ctx.clearRect(0, 0, overlay.width, overlay.height);
        updateUI("No Hand", 0);
        if (apiStatusTag) {
          apiStatusTag.textContent = "no-hand";
          apiStatusTag.style.color = "#9ca3af";
        }
      }
    });

    async function loop() {
      try {
        await hands.send({ image: video });
      } catch (e) {
        console.error("hands.send error", e);
      }
      requestAnimationFrame(loop);
    }

    requestAnimationFrame(loop);
  }

  // ----------------- payment UPI checker -----------------
  function validatePaymentForm() {
    const upi = $("#upi_id");
    if (!upi) return true;
    const v = upi.value.trim();
    if (!v || !v.includes("@")) {
      alert("Please enter a valid UPI ID (example: name@upi)");
      return false;
    }
    return true;
  }
  window.validatePaymentForm = validatePaymentForm;

  // ----------------- smooth scroll helper -----------------
  window.scrollToSection = function (id) {
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: "smooth" });
  };

  // ----------------- DOM ready -----------------
  document.addEventListener("DOMContentLoaded", () => {
    const yearEl = $("#year");
    if (yearEl) yearEl.textContent = new Date().getFullYear();

    initBackgroundOrbs();

    if ($("#camera") && $("#overlayCanvas")) {
      initHandDetection().catch(err => console.error("initHandDetection failed", err));
    }

    const heroWrapper = $(".hero-orb-wrapper");
    if (heroWrapper) {
      heroWrapper.addEventListener("pointermove", (e) => {
        const rect = heroWrapper.getBoundingClientRect();
        const x = (e.clientX - rect.left) / rect.width - 0.5;
        const y = (e.clientY - rect.top) / rect.height - 0.5;
        const svg = $(".hero-orb-svg");
        if (svg) svg.style.transform =
          `translate3d(${x * 6}px, ${y * 6}px, 0) scale(1.02)`;
      });
      heroWrapper.addEventListener("pointerleave", () => {
        const svg = $(".hero-orb-svg");
        if (svg) svg.style.transform = "";
      });
    }
  });

})();
