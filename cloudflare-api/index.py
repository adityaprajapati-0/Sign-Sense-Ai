from js import Response, JSON
import math

def dist(a, b):
    if not a or not b:
        return 0.0
    ax, ay = float(a.get("x", 0.0)), float(a.get("y", 0.0))
    bx, by = float(b.get("x", 0.0)), float(b.get("y", 0.0))
    return math.hypot(ax - bx, ay - by)

def classify_sign(landmarks):
    if not landmarks or len(landmarks) < 21:
        return "Unknown", 0.2, {"error": "NOT_ENOUGH_LANDMARKS"}

    by_name = {lm.get("name"): lm for lm in landmarks if lm.get("name")}
    
    def g(name, idx):
        return by_name.get(name, landmarks[idx] if idx < len(landmarks) else None)

    wrist = g("wrist", 0)
    thumb_tip = g("thumb_tip", 4)
    thumb_ip = g("thumb_ip", 3)
    index_tip = g("index_finger_tip", 8)
    index_pip = g("index_finger_pip", 6)
    middle_tip = g("middle_finger_tip", 12)
    middle_pip = g("middle_finger_pip", 10)
    ring_tip = g("ring_finger_tip", 16)
    ring_pip = g("ring_finger_pip", 14)
    pinky_tip = g("pinky_finger_tip", 20)
    pinky_pip = g("pinky_finger_ip", 18)

    if not wrist:
        return "Unknown", 0.1, {"error": "NO_WRIST"}

    def extended(tip, pip):
        if not tip or not pip: return False
        return dist(tip, wrist) > dist(pip, wrist) + 0.04

    fingers = {
        "thumb": extended(thumb_tip, thumb_ip),
        "index": extended(index_tip, index_pip),
        "middle": extended(middle_tip, middle_pip),
        "ring": extended(ring_tip, ring_pip),
        "pinky": extended(pinky_tip, pinky_pip),
    }

    count = sum(fingers.values())
    pinch_d = dist(thumb_tip, index_tip)

    if fingers["pinky"] and not any([fingers["thumb"], fingers["index"], fingers["middle"], fingers["ring"]]):
        return "Pinky", 0.88, {**fingers, "reason": "TOILET"}
    if fingers["middle"] and not any([fingers["thumb"], fingers["index"], fingers["ring"], fingers["pinky"]]):
        return "Middle", 0.88, {**fingers, "reason": "ANGRY"}
    if fingers["ring"] and fingers["middle"] and fingers["pinky"] and not fingers["thumb"] and not fingers["index"]:
        return "Ring Pinky Middle", 0.90, {**fingers, "reason": "AWESOME"}
    if thumb_tip and index_tip and pinch_d < 0.06:
        return "Pinch", 0.86, {**fingers, "pinch_distance": pinch_d}
    if fingers["index"] and not any([fingers["middle"], fingers["ring"], fingers["pinky"]]):
        return "Point", 0.87, {**fingers, "reason": "POINT"}
    if fingers["index"] and fingers["middle"] and not any([fingers["ring"], fingers["pinky"]]):
        return "Peace", 0.88, {**fingers, "reason": "PEACE"}
    if fingers["index"] and fingers["pinky"] and not any([fingers["middle"], fingers["ring"]]):
        return "Rock", 0.88, {**fingers, "reason": "ROCK"}
    if fingers["thumb"] and count == 1:
        return "Thumbs Up", 0.90, {**fingers, "reason": "THUMBS_UP"}
    if count == 0:
        return "Fist", 0.85, {**fingers, "reason": "FIST"}
    if count == 5:
        return "Open Hand", 0.92, {**fingers, "reason": "OPEN_HAND"}
    if count == 4: return "Four Fingers", 0.82, {**fingers}
    if count == 3: return "Three Fingers", 0.80, {**fingers}

    return "Unknown", 0.30, {**fingers}

async def on_fetch(request):
    if request.method == "OPTIONS":
        return Response.new(None, status=204, headers={
            "Access-Control-Allow-Origin": "*",
            "Access-Control-Allow-Methods": "POST, OPTIONS",
            "Access-Control-Allow-Headers": "Content-Type"
        })

    if request.method != "POST":
        return Response.new("SignSense API: Send POST with landmarks", status=200)

    try:
        data = await request.json()
        landmarks = data.get("landmarks")
        
        sign, confidence, debug = classify_sign(landmarks)
        
        response_data = {
            "success": True,
            "sign": sign,
            "confidence": confidence,
            "debug": debug
        }
        
        return Response.new(JSON.stringify(response_data), headers={
            "Content-Type": "application/json",
            "Access-Control-Allow-Origin": "*"
        })
    except Exception as e:
        return Response.new(JSON.stringify({"success": False, "error": str(e)}), status=500, headers={
            "Content-Type": "application/json",
            "Access-Control-Allow-Origin": "*"
        })
