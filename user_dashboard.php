<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ctunav");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userID = $_SESSION['user_id'];

// Fetch user profile image
$stmt = $conn->prepare("SELECT profile_image FROM users WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$defaultImage = 'images/user.png';
$profileImage = (!empty($user['profile_image']) && file_exists($user['profile_image'])) ? $user['profile_image'] : $defaultImage;

// ✅ Fetch departments from DB
$departments = [];
$res = $conn->query("SELECT * FROM departments_and_offices");
while ($row = $res->fetch_assoc()) {
    $departments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>CTU-TUBURAN</title>
    <style>
        :root {
            --primary: #1a5276;
            --secondary: #2e86c1;
            --accent: #f39c12;
            --light: #ecf0f1;
            --dark: #2c3e50;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body, html { height: 100%; background: #f1ededff; }

        .navbar { height: 60px; background-color: #f3f3f3ff; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; box-shadow: 0 2px 4px rgb(0 0 0 / 0.1); border-bottom: 1px solid #ff7a00; }
        .navbar-left { display: flex; align-items: center; gap: 12px; }
        .navbar-left img { height: 40px; width: 40px; border-radius: 50%; }
        .navbar-left .logo-text { font-weight: bold; font-size: 16px; white-space: nowrap; color: #ff7a00;}
        .navbar-right { display: flex; align-items: center; gap: 20px; }
        .navbar-right img.profile { width: 34px; height: 34px; border-radius: 50%; object-fit: cover; }
        .navbar-right .icon { width: 26px; height: 26px; fill: black; cursor: pointer; transition: fill 0.2s ease; }
        .navbar-right .icon:hover { fill: #ff7a00; }

        .main-container { display: flex; height: calc(100vh - 60px); }
        .sidebar { width: 300px; background-color: #f7f5f1; border-right: 1px solid #ccc; padding: 20px; display: flex; flex-direction: column; gap: 15px; }
        .button-toggle { display: flex; gap: 15px; margin-bottom: 20px; }
        .button-toggle button { flex: 1; padding: 8px 0; border-radius: 20px; border: none; font-weight: 600; font-size: 13px; cursor: pointer; transition: background 0.3s ease; }
        .button-toggle button.active { background-color: #ff7a00; color: white; }
        .button-toggle button.inactive { background-color: #e9dcd1; color: black; }

        label { font-weight: bold; font-size: 11px; margin-left: 8px; margin-bottom: 3px; margin-top: 8px; display: block; }
        select { width: 100%; padding: 8px 12px; border-radius: 20px; border: none; background-color: #e9dcd1; cursor: pointer; font-size: 14px; color: rgb(111, 110, 110); }
        select:focus { outline: none; box-shadow: 0 0 4px #ff7a00; }

        .search-btn { background-color: #ff7a00; color: white; border: none; border-radius: 15px; padding: 8px 0; width: 100%; font-weight: bold; font-size: 13px; cursor: pointer; margin-top: 10px; transition: background 0.3s ease; }
        .search-btn:hover { background-color: #e65c00; }
        .recent-searches { margin-top: 15px; font-weight: 700; font-size: 11px; display: flex; align-items: center; gap: 5px; color: black; }
        .recent-searches svg { fill: #bbb; width: 14px; height: 14px; }

        .right-container { flex: 1; background: white; position: relative; }

        .popup {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            border: 2px solid #ff7a00;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            display: none;
            z-index: 10;
            width: 250px;
        }
        .popup h3 { margin-bottom: 8px; color: #ff7a00; }
        .popup button { background: #ff7a00; color: white; border: none; padding: 6px 10px; border-radius: 5px; cursor: pointer; margin-top: 10px; }
        .popup button:hover { background: #e65c00; }

        .label {
            color: #ff7a00;
            font-size: 14px;
            font-weight: bold;
            background: rgba(255,255,255,0.85);
            padding: 4px 8px;
            border-radius: 6px;
            white-space: nowrap;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            cursor: pointer;
        }
        .label i { margin-right: 5px; color: #ff7a00; }
    </style>
</head>
<body>
<script>
    // ✅ Pass PHP DB data to JS
    const departmentsData = <?php echo json_encode($departments); ?>;
</script>

<nav class="navbar">
    <div class="navbar-left">
        <a href="index.php"><img src="images/Logo.png" alt="Logo" /></a>
        <span class="logo-text">CTU-TUBURAN</span>
    </div>
    <div class="navbar-right">
        <a href="user_dashboard.php">
            <svg class="icon" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        </a>
        <a href="messages.php">
            <svg class="icon" viewBox="0 0 24 24"><path d="M20 2H4a2 2 0 0 0-2 2v16l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/></svg>
        </a>
        <a href="profile.php"><img class="profile" src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile" /></a>
    </div>
</nav>

<div class="main-container">
    <aside class="sidebar">
        <div class="button-toggle">
            <button id="btn-search-location" class="active">Search Location</button>
            <button id="btn-get-direction" class="inactive">Get Direction</button>
        </div>
        <form id="form-search-location">
            <label for="search-location">Search location</label>
            <select id="search-location" name="search-location" required>
                <option value="" disabled selected>Select location</option>
                <option>Sample Location 1</option>
                <option>Sample Location 2</option>
            </select>
            <button class="search-btn" type="submit">Search</button>
        </form>
        <form id="form-get-direction" style="display:none;">
            <label for="starting-point">Starting Point</label>
            <select id="starting-point" name="starting-point" required>
                <option value="" disabled selected>Select starting point</option>
                <option>Sample Start 1</option>
                <option>Sample Start 2</option>
            </select>
            <label for="ending-point">Ending Point</label>
            <select id="ending-point" name="ending-point" required>
                <option value="" disabled selected>Select ending point</option>
                <option>Sample End 1</option>
                <option>Sample End 2</option>
            </select>
            <button class="search-btn" type="submit">Search</button>
        </form>
        <div class="recent-searches">
            <span>Recent Searches</span>
            <svg viewBox="0 0 24 24"><path d="M13 3a9 9 0 1 0 8.94 8h-2.02a7 7 0 1 1-1.26-3.83L10.17 12l1.41 1.41 4.5-4.5a7 7 0 0 1 2.92 4.63H21a9 9 0 0 0-8-14zM12 8v5l4 2"/></svg>
        </div>
    </aside>

    <div class="right-container" id="viewer">
        <div class="popup" id="popup">
            <h3>Building Services</h3>
            <p id="popup-content"></p>
            <button onclick="document.getElementById('popup').style.display='none'">Close</button>
        </div>
    </div>
</div>

<script type="module">
import * as THREE from "https://esm.sh/three@0.152.2";
import { GLTFLoader } from "https://esm.sh/three@0.152.2/examples/jsm/loaders/GLTFLoader.js";
import { OrbitControls } from "https://esm.sh/three@0.152.2/examples/jsm/controls/OrbitControls.js";
import { CSS2DRenderer, CSS2DObject } from "https://esm.sh/three@0.152.2/examples/jsm/renderers/CSS2DRenderer.js";

const scene = new THREE.Scene();
scene.background = new THREE.Color(0xf1eded);

const aspect = window.innerWidth / window.innerHeight;
const d = 10;
const camera = new THREE.OrthographicCamera(-d * aspect, d * aspect, d, -d, 0.1, 1000);
camera.position.set(0, 20, 0);
camera.lookAt(0, 0, 0);

const renderer = new THREE.WebGLRenderer({ antialias: true });
renderer.setSize(window.innerWidth - 300, window.innerHeight - 60);
document.getElementById("viewer").appendChild(renderer.domElement);

const labelRenderer = new CSS2DRenderer();
labelRenderer.setSize(window.innerWidth - 300, window.innerHeight - 60);
labelRenderer.domElement.style.position = 'absolute';
labelRenderer.domElement.style.top = '60px';
labelRenderer.domElement.style.left = '300px';
labelRenderer.domElement.style.pointerEvents = 'none';
document.body.appendChild(labelRenderer.domElement);

scene.add(new THREE.HemisphereLight(0xffffff, 0x444444, 1));
const dirLight = new THREE.DirectionalLight(0xffffff, 1);
dirLight.position.set(5, 10, 7.5);
scene.add(dirLight);

const controls = new OrbitControls(camera, renderer.domElement);
controls.enableDamping = true;

const clickableObjects = [];
const loader = new GLTFLoader();

let INTERSECTED = null;

loader.load("models/educblg.gltf", (gltf) => {
    const building = gltf.scene;
    building.name = "Education Department";
    scene.add(building);

    building.traverse((child) => { 
        if (child.isMesh) {
            clickableObjects.push(child);
            child.castShadow = true;
            child.receiveShadow = true;
        }
    });

    const box = new THREE.Box3().setFromObject(building);
    const center = box.getCenter(new THREE.Vector3());
    const size = box.getSize(new THREE.Vector3());

    camera.position.set(center.x, size.y * 2, center.z);
    controls.target.copy(center);

    // ✅ DB row for this building
    const eduData = departmentsData.find(d => d.name === "Education Department");

    const div = document.createElement('div');
    div.className = 'label';
    div.innerHTML = `<i class="fas fa-book"></i> ${eduData.name}`;
    div.style.pointerEvents = "auto";
    div.addEventListener("click", (e) => {
        e.stopPropagation();
        showPopup(`${eduData.name} Services: ${eduData.description}`);
    });

    const label = new CSS2DObject(div);
    label.position.set(0, size.y + 2, 0);
    building.add(label);
});

const popup = document.getElementById("popup");
const popupContent = document.getElementById("popup-content");
function showPopup(text) {
    popupContent.innerText = text;
    popup.style.display = "block";
}

const raycaster = new THREE.Raycaster();
const mouse = new THREE.Vector2();

renderer.domElement.addEventListener("mousemove", (event) => {
    mouse.x = (event.offsetX / renderer.domElement.clientWidth) * 2 - 1;
    mouse.y = -(event.offsetY / renderer.domElement.clientHeight) * 2 + 1;
    raycaster.setFromCamera(mouse, camera);

    const intersects = raycaster.intersectObjects(clickableObjects, true);

    if (intersects.length > 0) {
        if (INTERSECTED !== intersects[0].object) {
            if (INTERSECTED) {
                INTERSECTED.material.emissive.setHex(INTERSECTED.currentHex);
            }
            INTERSECTED = intersects[0].object;
            INTERSECTED.currentHex = INTERSECTED.material.emissive.getHex();
            INTERSECTED.material.emissive.setHex(0x333333); // dark shadow highlight
            document.body.style.cursor = "pointer";
        }
    } else {
        if (INTERSECTED) INTERSECTED.material.emissive.setHex(INTERSECTED.currentHex);
        INTERSECTED = null;
        document.body.style.cursor = "default";
    }
});

renderer.domElement.addEventListener("click", () => {
    if (INTERSECTED) {
        const eduData = departmentsData.find(d => d.name === "Education Department");
        showPopup(`${eduData.name} Services: ${eduData.description}`);
    }
});

function animate() {
    requestAnimationFrame(animate);
    controls.update();
    renderer.render(scene, camera);
    labelRenderer.render(scene, camera);
}
animate();
</script>
</body>
</html>
