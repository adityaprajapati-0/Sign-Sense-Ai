var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });

// index.js
var index_default = {
  async fetch(request, env, ctx) {
    if (request.method === "OPTIONS") {
      return new Response(null, {
        headers: {
          "Access-Control-Allow-Origin": "*",
          "Access-Control-Allow-Methods": "POST, OPTIONS",
          "Access-Control-Allow-Headers": "Content-Type"
        }
      });
    }
    if (request.method !== "POST") {
      return new Response("SignSense API: Send POST with landmarks", {
        status: 200
      });
    }
    try {
      const data = await request.json();
      const landmarks = data.landmarks;
      if (!Array.isArray(landmarks) || landmarks.length < 21) {
        return new Response(
          JSON.stringify({ success: false, error: "NO_LANDMARKS" }),
          {
            status: 200,
            headers: {
              "Content-Type": "application/json",
              "Access-Control-Allow-Origin": "*"
            }
          }
        );
      }
      const { sign, confidence, debug } = classifySign(landmarks);
      return new Response(
        JSON.stringify({ success: true, sign, confidence, debug }),
        {
          status: 200,
          headers: {
            "Content-Type": "application/json",
            "Access-Control-Allow-Origin": "*"
          }
        }
      );
    } catch (e) {
      return new Response(
        JSON.stringify({
          success: false,
          error: "SERVER_ERROR",
          message: e.message
        }),
        {
          status: 500,
          headers: {
            "Content-Type": "application/json",
            "Access-Control-Allow-Origin": "*"
          }
        }
      );
    }
  }
};
function dist(a, b) {
  if (!a || !b) return 0;
  return Math.hypot((a.x || 0) - (b.x || 0), (a.y || 0) - (b.y || 0));
}
__name(dist, "dist");
function classifySign(landmarks) {
  const byName = {};
  landmarks.forEach((lm) => {
    if (lm.name) byName[lm.name] = lm;
  });
  const g = /* @__PURE__ */ __name((name, idx) => byName[name] || landmarks[idx] || null, "g");
  const wrist = g("wrist", 0);
  const thumbTip = g("thumb_tip", 4);
  const thumbIp = g("thumb_ip", 3);
  const indexTip = g("index_finger_tip", 8);
  const indexPip = g("index_finger_pip", 6);
  const middleTip = g("middle_finger_tip", 12);
  const middlePip = g("middle_finger_pip", 10);
  const ringTip = g("ring_finger_tip", 16);
  const ringPip = g("ring_finger_pip", 14);
  const pinkyTip = g("pinky_finger_tip", 20);
  const pinkyPip = g("pinky_finger_ip", 18);
  if (!wrist)
    return { sign: "Unknown", confidence: 0.1, debug: { error: "NO_WRIST" } };
  const isExtended = /* @__PURE__ */ __name((tip, pip) => {
    if (!tip || !pip) return false;
    return dist(tip, wrist) > dist(pip, wrist) + 0.04;
  }, "isExtended");
  const fingers = {
    thumb: isExtended(thumbTip, thumbIp),
    index: isExtended(indexTip, indexPip),
    middle: isExtended(middleTip, middlePip),
    ring: isExtended(ringTip, ringPip),
    pinky: isExtended(pinkyTip, pinkyPip)
  };
  const count = Object.values(fingers).filter(Boolean).length;
  const pinchD = dist(thumbTip, indexTip);
  if (fingers.pinky && !fingers.thumb && !fingers.index && !fingers.middle && !fingers.ring) {
    return {
      sign: "Pinky",
      confidence: 0.88,
      debug: { ...fingers, reason: "TOILET" }
    };
  }
  if (fingers.middle && !fingers.thumb && !fingers.index && !fingers.ring && !fingers.pinky) {
    return {
      sign: "Middle",
      confidence: 0.88,
      debug: { ...fingers, reason: "ANGRY" }
    };
  }
  if (fingers.ring && fingers.middle && fingers.pinky && !fingers.thumb && !fingers.index) {
    return {
      sign: "Ring Pinky Middle",
      confidence: 0.9,
      debug: { ...fingers, reason: "AWESOME" }
    };
  }
  if (thumbTip && indexTip && pinchD < 0.06) {
    return {
      sign: "Pinch",
      confidence: 0.86,
      debug: { ...fingers, pinch_distance: pinchD }
    };
  }
  if (fingers.index && !fingers.middle && !fingers.ring && !fingers.pinky) {
    return {
      sign: "Point",
      confidence: 0.87,
      debug: { ...fingers, reason: "POINT" }
    };
  }
  if (fingers.index && fingers.middle && !fingers.ring && !fingers.pinky) {
    return {
      sign: "Peace",
      confidence: 0.88,
      debug: { ...fingers, reason: "PEACE" }
    };
  }
  if (fingers.index && fingers.pinky && !fingers.middle && !fingers.ring) {
    return {
      sign: "Rock",
      confidence: 0.88,
      debug: { ...fingers, reason: "ROCK" }
    };
  }
  if (fingers.thumb && count === 1) {
    return {
      sign: "Thumbs Up",
      confidence: 0.9,
      debug: { ...fingers, reason: "THUMBS_UP" }
    };
  }
  if (count === 0) {
    return {
      sign: "Fist",
      confidence: 0.85,
      debug: { ...fingers, reason: "FIST" }
    };
  }
  if (count === 5) {
    return {
      sign: "Open Hand",
      confidence: 0.92,
      debug: { ...fingers, reason: "OPEN_HAND" }
    };
  }
  if (count === 4)
    return { sign: "Four Fingers", confidence: 0.82, debug: { ...fingers } };
  if (count === 3)
    return { sign: "Three Fingers", confidence: 0.8, debug: { ...fingers } };
  return { sign: "Unknown", confidence: 0.3, debug: { ...fingers } };
}
__name(classifySign, "classifySign");
export {
  index_default as default
};
//# sourceMappingURL=index.js.map
