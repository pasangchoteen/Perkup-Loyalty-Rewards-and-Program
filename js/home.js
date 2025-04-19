// 3D Elements for Homepage
document.addEventListener("DOMContentLoaded", () => {
  // Load Three.js and its dependencies
  loadScript("https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js", () => {
    loadScript("https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.min.js", () => {
      loadScript("https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.min.js", initThreeJS)
    })
  })
})

function loadScript(url, callback) {
  const script = document.createElement("script")
  script.src = url
  script.onload = callback
  document.head.appendChild(script)
}

function initThreeJS() {
  const container = document.getElementById("hero-3d-container")
  if (!container) return

  // Create scene
  const scene = new THREE.Scene()

  // Create camera
  const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000)
  camera.position.z = 5
  camera.position.y = 1

  // Create renderer with antialiasing for smoother edges
  const renderer = new THREE.WebGLRenderer({
    alpha: true,
    antialias: true,
    powerPreference: "high-performance",
  })
  renderer.setSize(container.clientWidth, container.clientHeight)
  renderer.setClearColor(0x000000, 0)
  renderer.setPixelRatio(window.devicePixelRatio)
  renderer.shadowMap.enabled = true
  renderer.shadowMap.type = THREE.PCFSoftShadowMap
  container.appendChild(renderer.domElement)

  // Add orbit controls for interactive rotation
  const controls = new THREE.OrbitControls(camera, renderer.domElement)
  controls.enableDamping = true
  controls.dampingFactor = 0.05
  controls.enableZoom = false
  controls.autoRotate = true
  controls.autoRotateSpeed = 0.5

  // Create lights
  const ambientLight = new THREE.AmbientLight(0xffffff, 0.5)
  scene.add(ambientLight)

  // Add directional light for shadows
  const directionalLight = new THREE.DirectionalLight(0xffffff, 1)
  directionalLight.position.set(5, 5, 5)
  directionalLight.castShadow = true
  directionalLight.shadow.mapSize.width = 1024
  directionalLight.shadow.mapSize.height = 1024
  scene.add(directionalLight)

  // Add colored point lights for dramatic effect
  const pointLight1 = new THREE.PointLight(0x00c2ff, 2, 10)
  pointLight1.position.set(2, 2, 2)
  scene.add(pointLight1)

  const pointLight2 = new THREE.PointLight(0xff4081, 2, 10)
  pointLight2.position.set(-2, -1, 2)
  scene.add(pointLight2)

  const pointLight3 = new THREE.PointLight(0x00e676, 2, 10)
  pointLight3.position.set(0, 3, -3)
  scene.add(pointLight3)

  // Create a group to hold all objects
  const group = new THREE.Group()
  scene.add(group)

  // Create a glossy platform
  const platformGeometry = new THREE.CylinderGeometry(3, 3, 0.2, 32)
  const platformMaterial = new THREE.MeshPhysicalMaterial({
    color: 0x121212,
    metalness: 0.7,
    roughness: 0.2,
    reflectivity: 1.0,
    clearcoat: 1.0,
    clearcoatRoughness: 0.2,
  })
  const platform = new THREE.Mesh(platformGeometry, platformMaterial)
  platform.position.y = -1
  platform.receiveShadow = true
  group.add(platform)

  // Create a premium loyalty card
  const cardGeometry = new THREE.BoxGeometry(3, 2, 0.1)
  const cardMaterial = new THREE.MeshPhysicalMaterial({
    color: 0x1e1e1e,
    metalness: 0.8,
    roughness: 0.2,
    reflectivity: 1.0,
    clearcoat: 1.0,
    clearcoatRoughness: 0.2,
  })
  const card = new THREE.Mesh(cardGeometry, cardMaterial)
  card.castShadow = true
  card.receiveShadow = true
  group.add(card)

  // Add a holographic effect to the card
  const holoGeometry = new THREE.PlaneGeometry(2.8, 1.8)
  const holoMaterial = new THREE.MeshPhysicalMaterial({
    color: 0xffffff,
    metalness: 1.0,
    roughness: 0.2,
    transmission: 0.9,
    transparent: true,
    opacity: 0.2,
    side: THREE.DoubleSide,
  })
  const hologram = new THREE.Mesh(holoGeometry, holoMaterial)
  hologram.position.z = 0.06
  card.add(hologram)

  // Add PerkUp logo
  const logoGeometry = new THREE.CircleGeometry(0.4, 32)
  const logoMaterial = new THREE.MeshPhysicalMaterial({
    color: 0x00c2ff,
    emissive: 0x00c2ff,
    emissiveIntensity: 0.5,
    metalness: 0.9,
    roughness: 0.1,
  })
  const logo = new THREE.Mesh(logoGeometry, logoMaterial)
  logo.position.set(-1, 0.5, 0.06)
  card.add(logo)

  // Add text "PERKUP" next to the logo
  const textMaterial = new THREE.MeshPhysicalMaterial({
    color: 0xffffff,
    emissive: 0xffffff,
    emissiveIntensity: 0.5,
    metalness: 0.9,
    roughness: 0.1,
  })

  // Simulate text with small rectangles
  const textGroup = new THREE.Group()
  textGroup.position.set(-0.5, 0.5, 0.06)
  card.add(textGroup)

  // P
  createTextSegment(0, 0, 0.1, 0.3, textMaterial, textGroup)
  createTextSegment(0.1, 0, 0.1, 0.15, textMaterial, textGroup)
  createTextSegment(0, -0.15, 0.2, 0.05, textMaterial, textGroup)

  // E
  createTextSegment(0.25, 0, 0.1, 0.3, textMaterial, textGroup)
  createTextSegment(0.35, 0, 0.1, 0.05, textMaterial, textGroup)
  createTextSegment(0.35, -0.125, 0.1, 0.05, textMaterial, textGroup)
  createTextSegment(0.35, -0.25, 0.1, 0.05, textMaterial, textGroup)

  // R
  createTextSegment(0.5, 0, 0.1, 0.3, textMaterial, textGroup)
  createTextSegment(0.6, 0, 0.1, 0.15, textMaterial, textGroup)
  createTextSegment(0.5, -0.15, 0.2, 0.05, textMaterial, textGroup)
  createTextSegment(0.6, -0.2, 0.1, 0.1, textMaterial, textGroup)

  // K
  createTextSegment(0.75, 0, 0.1, 0.3, textMaterial, textGroup)
  createTextSegment(0.85, 0, 0.1, 0.05, textMaterial, textGroup)
  createTextSegment(0.85, -0.125, 0.1, 0.05, textMaterial, textGroup)
  createTextSegment(0.85, -0.25, 0.1, 0.05, textMaterial, textGroup)

  // U
  createTextSegment(1.0, 0, 0.1, 0.25, textMaterial, textGroup)
  createTextSegment(1.1, -0.25, 0.1, 0.05, textMaterial, textGroup)
  createTextSegment(1.2, 0, 0.1, 0.25, textMaterial, textGroup)

  // P
  createTextSegment(1.35, 0, 0.1, 0.3, textMaterial, textGroup)
  createTextSegment(1.45, 0, 0.1, 0.15, textMaterial, textGroup)
  createTextSegment(1.35, -0.15, 0.2, 0.05, textMaterial, textGroup)

  // Add reward points with glowing effect
  for (let i = 0; i < 5; i++) {
    const pointGeometry = new THREE.CircleGeometry(0.15, 32)
    const pointMaterial = new THREE.MeshPhysicalMaterial({
      color: i < 3 ? 0x00e676 : 0x333333,
      emissive: i < 3 ? 0x00e676 : 0x000000,
      emissiveIntensity: i < 3 ? 0.5 : 0,
      metalness: 0.9,
      roughness: 0.1,
    })
    const point = new THREE.Mesh(pointGeometry, pointMaterial)
    point.position.set(-1.2 + i * 0.6, -0.5, 0.06)
    card.add(point)
  }

  // Add floating 3D coins
  const coinGroup = new THREE.Group()
  group.add(coinGroup)

  for (let i = 0; i < 8; i++) {
    const coinGeometry = new THREE.CylinderGeometry(0.3, 0.3, 0.05, 32)
    const coinMaterial = new THREE.MeshPhysicalMaterial({
      color: 0xffd700,
      metalness: 1.0,
      roughness: 0.1,
      reflectivity: 1.0,
    })
    const coin = new THREE.Mesh(coinGeometry, coinMaterial)

    // Position coins in a circular pattern around the card
    const angle = (i / 8) * Math.PI * 2
    const radius = 2.5
    coin.position.x = Math.cos(angle) * radius
    coin.position.z = Math.sin(angle) * radius
    coin.position.y = Math.random() * 2 - 1

    // Rotate to face center
    coin.rotation.x = Math.PI / 2
    coin.rotation.y = -angle

    coin.castShadow = true
    coin.receiveShadow = true

    // Store original position for animation
    coin.userData = {
      originalY: coin.position.y,
      originalX: coin.position.x,
      originalZ: coin.position.z,
      phase: Math.random() * Math.PI * 2,
      speed: 0.5 + Math.random() * 0.5,
    }

    coinGroup.add(coin)
  }

  // Add floating smartphone with app screen
  const phoneGroup = new THREE.Group()
  phoneGroup.position.set(0, 0.5, -2)
  phoneGroup.rotation.x = -0.2
  group.add(phoneGroup)

  // Phone body
  const phoneGeometry = new THREE.BoxGeometry(1.2, 2.2, 0.1)
  const phoneMaterial = new THREE.MeshPhysicalMaterial({
    color: 0x000000,
    metalness: 0.8,
    roughness: 0.2,
  })
  const phone = new THREE.Mesh(phoneGeometry, phoneMaterial)
  phone.castShadow = true
  phoneGroup.add(phone)

  // Phone screen
  const screenGeometry = new THREE.PlaneGeometry(1.1, 2)
  const screenMaterial = new THREE.MeshPhysicalMaterial({
    color: 0x121212,
    emissive: 0x121212,
    emissiveIntensity: 0.2,
    metalness: 0,
    roughness: 0.1,
  })
  const screen = new THREE.Mesh(screenGeometry, screenMaterial)
  screen.position.z = 0.051
  phone.add(screen)

  // Add app elements to screen
  const appHeader = new THREE.Mesh(new THREE.PlaneGeometry(1.1, 0.3), new THREE.MeshBasicMaterial({ color: 0x00c2ff }))
  appHeader.position.y = 0.85
  appHeader.position.z = 0.001
  screen.add(appHeader)

  // Add reward cards to screen
  for (let i = 0; i < 3; i++) {
    const cardElement = new THREE.Mesh(
      new THREE.PlaneGeometry(0.9, 0.4),
      new THREE.MeshBasicMaterial({ color: 0x1e1e1e }),
    )
    cardElement.position.y = 0.3 - i * 0.5
    cardElement.position.z = 0.001
    screen.add(cardElement)

    // Add reward progress to card
    const progressBar = new THREE.Mesh(
      new THREE.PlaneGeometry(0.7 * (0.3 + i * 0.3), 0.1),
      new THREE.MeshBasicMaterial({ color: 0x00e676 }),
    )
    progressBar.position.y = -0.1
    progressBar.position.z = 0.001
    progressBar.position.x = -0.1 + (0.7 * (0.3 + i * 0.3)) / 2 - 0.35
    cardElement.add(progressBar)
  }

  // Add notification particles
  const particles = new THREE.Group()
  group.add(particles)

  for (let i = 0; i < 30; i++) {
    const particleGeometry = new THREE.SphereGeometry(0.03, 8, 8)
    const particleMaterial = new THREE.MeshBasicMaterial({
      color: getRandomColor(),
      transparent: true,
      opacity: 0.7,
    })
    const particle = new THREE.Mesh(particleGeometry, particleMaterial)

    // Random positions around the scene
    particle.position.x = (Math.random() - 0.5) * 8
    particle.position.y = (Math.random() - 0.5) * 4
    particle.position.z = (Math.random() - 0.5) * 8

    // Store animation data
    particle.userData = {
      speed: 0.01 + Math.random() * 0.02,
      direction: new THREE.Vector3(
        (Math.random() - 0.5) * 0.02,
        (Math.random() - 0.5) * 0.02,
        (Math.random() - 0.5) * 0.02,
      ),
    }

    particles.add(particle)
  }

  // Animation
  const clock = new THREE.Clock()

  function animate() {
    requestAnimationFrame(animate)

    const delta = clock.getDelta()
    const elapsedTime = clock.getElapsedTime()

    // Update controls
    controls.update()

    // Animate coins
    coinGroup.children.forEach((coin, index) => {
      const userData = coin.userData
      coin.position.y = userData.originalY + Math.sin(elapsedTime * userData.speed + userData.phase) * 0.5
      coin.rotation.z = elapsedTime * 0.5

      // Slightly move in x and z for more dynamic effect
      coin.position.x = userData.originalX + Math.sin(elapsedTime * 0.3 + index) * 0.1
      coin.position.z = userData.originalZ + Math.cos(elapsedTime * 0.3 + index) * 0.1
    })

    // Animate phone
    phoneGroup.position.y = 0.5 + Math.sin(elapsedTime * 0.5) * 0.1
    phoneGroup.rotation.y = Math.sin(elapsedTime * 0.3) * 0.1

    // Animate particles
    particles.children.forEach((particle) => {
      particle.position.x += particle.userData.direction.x
      particle.position.y += particle.userData.direction.y
      particle.position.z += particle.userData.direction.z

      // Reset particles that go too far
      if (
        particle.position.x > 4 ||
        particle.position.x < -4 ||
        particle.position.y > 2 ||
        particle.position.y < -2 ||
        particle.position.z > 4 ||
        particle.position.z < -4
      ) {
        particle.position.x = (Math.random() - 0.5) * 8
        particle.position.y = (Math.random() - 0.5) * 4
        particle.position.z = (Math.random() - 0.5) * 8
      }

      // Fade particles in and out
      particle.material.opacity = 0.3 + Math.sin(elapsedTime * particle.userData.speed * 5) * 0.5
    })

    // Animate card hologram
    hologram.material.opacity = 0.1 + Math.sin(elapsedTime * 2) * 0.1

    // Animate point lights
    pointLight1.intensity = 1.5 + Math.sin(elapsedTime * 2) * 0.5
    pointLight2.intensity = 1.5 + Math.sin(elapsedTime * 2 + 2) * 0.5
    pointLight3.intensity = 1.5 + Math.sin(elapsedTime * 2 + 4) * 0.5

    renderer.render(scene, camera)
  }

  // Handle window resize
  window.addEventListener("resize", () => {
    camera.aspect = container.clientWidth / container.clientHeight
    camera.updateProjectionMatrix()
    renderer.setSize(container.clientWidth, container.clientHeight)
  })

  // Start animation
  animate()
}

// Helper function to create text segments
function createTextSegment(x, y, width, height, material, parent) {
  const geometry = new THREE.PlaneGeometry(width, height)
  const mesh = new THREE.Mesh(geometry, material)
  mesh.position.set(x + width / 2, y - height / 2, 0)
  parent.add(mesh)
}

// Helper function to get random colors for particles
function getRandomColor() {
  const colors = [0x00c2ff, 0xff4081, 0x00e676, 0xffd700]
  return colors[Math.floor(Math.random() * colors.length)]
}

// Add parallax effect to the hero section
document.addEventListener("mousemove", (e) => {
  const hero = document.querySelector(".hero")
  if (!hero) return

  const moveX = (e.clientX - window.innerWidth / 2) * 0.005
  const moveY = (e.clientY - window.innerHeight / 2) * 0.005

  hero.style.backgroundPosition = `${moveX}px ${moveY}px`

  // Move 3D elements based on mouse position
  const container = document.getElementById("hero-3d-container")
  if (container) {
    container.style.transform = `translate(${moveX * 5}px, ${moveY * 5}px)`
  }
})
