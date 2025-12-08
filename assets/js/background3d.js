/* assets/js/background3d.js
   - Renders an ambient starfield + torus-knot using THREE.js into #bg3d canvas
   - Safe no-op if THREE isn't loaded
   - Exposes setOrbForSign to slightly tint the 3D orb (optional)
*/
(function () {
  const canvas = document.getElementById('bg3d');
  if (!canvas) return;

  if (typeof THREE === 'undefined') {
    console.warn('three.js not found — background3d will do nothing');
    return;
  }

  // Scene
  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(55, window.innerWidth / window.innerHeight, 0.1, 100);
  camera.position.set(0, 0, 8);

  const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
  renderer.setSize(window.innerWidth, window.innerHeight);
  if (renderer.outputColorSpace) renderer.outputColorSpace = THREE.SRGBColorSpace;

  // Lights
  const hemi = new THREE.HemisphereLight(0xa5b4fc, 0x020617, 0.9);
  scene.add(hemi);
  const dir = new THREE.DirectionalLight(0x93c5fd, 1.1);
  dir.position.set(4, 6, 4);
  scene.add(dir);

  // Torus knot (big, subtle)
  const orbGeo = new THREE.TorusKnotGeometry(1.4, 0.34, 160, 28);
  const orbMat = new THREE.MeshStandardMaterial({
    color: 0x6366f1,
    emissive: 0x1d4ed8,
    emissiveIntensity: 1.1,
    metalness: 0.85,
    roughness: 0.25,
    flatShading: false
  });
  const orb = new THREE.Mesh(orbGeo, orbMat);
  scene.add(orb);

  // Stars
  const starCount = 900;
  const positions = new Float32Array(starCount * 3);
  for (let i = 0; i < starCount; i++) {
    const i3 = i * 3;
    positions[i3 + 0] = (Math.random() - 0.5) * 40;
    positions[i3 + 1] = (Math.random() - 0.5) * 24;
    positions[i3 + 2] = (Math.random() - 0.5) * 40;
  }
  const starsGeo = new THREE.BufferGeometry();
  starsGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
  const starsMat = new THREE.PointsMaterial({ size: 0.06, transparent: true, opacity: 0.7 });
  const stars = new THREE.Points(starsGeo, starsMat);
  scene.add(stars);

  // Mouse/Pointer parallax
  let mouseX = 0, mouseY = 0;
  window.addEventListener('pointermove', (e) => {
    mouseX = (e.clientX / window.innerWidth) * 2 - 1;
    mouseY = (e.clientY / window.innerHeight) * 2 - 1;
  });

  // Resize
  function onResize() {
    const w = window.innerWidth, h = window.innerHeight;
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    renderer.setSize(w, h);
  }
  window.addEventListener('resize', onResize);

  // Animation
  let pulseTarget = 1.0, pulseCurrent = 1.0;
  function animate(t) {
    requestAnimationFrame(animate);
    const time = t * 0.001;
    orb.rotation.y += 0.45 * 0.016;
    orb.rotation.x += 0.18 * 0.016;
    stars.rotation.y += 0.02 * 0.016;

    // camera follows pointer a little
    camera.position.x += ((mouseX * 1.3) - camera.position.x) * 0.03;
    camera.position.y += ((-mouseY * 0.7) - camera.position.y) * 0.03;
    camera.lookAt(0, 0, 0);

    pulseCurrent += (pulseTarget - pulseCurrent) * 0.07;
    const s = 1.0 + Math.sin(time * 1.3) * 0.03 * pulseCurrent;
    orb.scale.set(s, s, s);

    renderer.render(scene, camera);
  }
  animate(0);

  // Small tinting function (also exposed if needed)
  function setOrbForSign(sign) {
    try {
      if (!orbMat) return;
      if (/open hand/i.test(sign)) { orbMat.color.setHex(0x06b6d4); orbMat.emissive.setHex(0x0ea5e9); pulseTarget = 1.12; }
      else if (/thumbs up/i.test(sign)) { orbMat.color.setHex(0x22c55e); orbMat.emissive.setHex(0x16a34a); pulseTarget = 1.28; }
      else if (/fist/i.test(sign)) { orbMat.color.setHex(0xef4444); orbMat.emissive.setHex(0xb91c1c); pulseTarget = 0.9; }
      else { orbMat.color.setHex(0x6366f1); orbMat.emissive.setHex(0x1d4ed8); pulseTarget = 1.0; }
    } catch (e) { /* ignore */ }
  }
  window.setOrbForSign3D = setOrbForSign;
})();
