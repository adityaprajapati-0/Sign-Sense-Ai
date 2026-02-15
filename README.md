# SignSense AI - Hand Sign Recognition

A real-time hand sign detection web application powered by MediaPipe Hands and Cloudflare Workers.

## 🚀 Live Demo

**Frontend**: [Deployed on Netlify](https://your-site.netlify.app) _(Update after deployment)_  
**Backend API**: [https://signsense-api.adi-ai-chatbot.workers.dev](https://signsense-api.adi-ai-chatbot.workers.dev)

## ✨ Features

- **Real-time hand detection** using MediaPipe Hands
- **Instant sign classification** via Cloudflare Workers edge network
- **10+ recognized signs**: Open Hand, Peace, Rock, Thumbs Up, Fist, Pinch, and more
- **Luxury UI** with glassmorphism, smooth animations, and premium design
- **Ultra-thin zoom slider** for precise camera control
- **Responsive design** optimized for desktop and mobile
- **Global edge deployment** for <50ms latency worldwide

## 🛠️ Tech Stack

### Frontend

- Pure HTML5, CSS3, and Vanilla JavaScript
- MediaPipe Hands for real-time hand tracking
- Glassmorphism and modern UI design patterns
- Hosted on **Netlify**

### Backend

- Cloudflare Workers (JavaScript runtime)
- Global edge network deployment
- Zero cold starts
- REST API: `POST https://signsense-api.adi-ai-chatbot.workers.dev`

## 🎯 Recognized Hand Signs

- ✋ **Open Hand** - All 5 fingers extended
- 👍 **Thumbs Up** - Thumb only
- ✌️ **Peace** - Index + middle fingers
- 🤘 **Rock** - Index + pinky fingers
- ✊ **Fist** - No fingers extended
- ☝️ **Point** - Index finger only
- 🤏 **Pinch** - Thumb + index close together
- 🖕 **Middle** - Middle finger only
- 🤙 **Pinky** - Pinky finger only
- 🤟 **Ring Pinky Middle** - Three fingers (awesome sign)
- ✋ **Four Fingers** - Four fingers extended
- 🤚 **Three Fingers** - Three fingers extended

## 📦 Local Development

```bash
# Clone the repository
git clone https://github.com/adityaprajapati-0/Sign-Sense-Ai.git
cd Sign-Sense-Ai

# Serve locally
python -m http.server 3000

# Open browser
http://localhost:3000
```

## 🚀 Deploy to Netlify

### Option 1: GitHub Integration (Recommended)

1. Push your code to GitHub
2. Go to [app.netlify.com](https://app.netlify.com)
3. Click "New site from Git"
4. Select your repository
5. Deploy settings:
   - **Build command**: _(leave empty)_
   - **Publish directory**: `/`
6. Deploy!

### Option 2: Netlify CLI

```bash
npm install -g netlify-cli
netlify login
netlify deploy --prod
```

## 🔧 API Documentation

### Endpoint

```
POST https://signsense-api.adi-ai-chatbot.workers.dev
Content-Type: application/json
```

### Request Body

```json
{
  "landmarks": [
    { "x": 0.5, "y": 0.5, "z": 0.1, "name": "wrist" },
    { "x": 0.6, "y": 0.4, "z": 0.2, "name": "thumb_tip" }
    // ... 21 total landmarks
  ]
}
```

### Response

```json
{
  "success": true,
  "sign": "Open Hand",
  "confidence": 0.92,
  "debug": {
    "thumb": true,
    "index": true,
    "middle": true,
    "ring": true,
    "pinky": true,
    "reason": "OPEN_HAND"
  }
}
```

## 🎨 UI Features

- **Luxury Zoom Slider** - Ultra-thin volume-style control
- **Camera Switch Animation** - 360° rotating icon
- **Toast Notifications** - Premium glassmorphic error feedback
- **Smooth Navigation** - Zero-jitter page transitions
- **Background Orbs** - Interactive parallax animations
- **Responsive Layout** - Optimized for all devices

## 📄 License

This project is licensed under the MIT License.

## 👨‍💻 Author

**Aditya Prajapati**

- Email: adityaprajapati1234567@gmail.com
- GitHub: [@adityaprajapati-0](https://github.com/adityaprajapati-0)

## 🙏 Acknowledgments

- MediaPipe Hands by Google
- Cloudflare Workers
- Netlify

---

Made with ❤️ for accessible communication
