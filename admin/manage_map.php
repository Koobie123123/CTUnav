<?php
$conn = new mysqli("localhost","root","","ctunav");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

$departments = [];
$res = $conn->query("SELECT * FROM departments_and_offices");
while($row=$res->fetch_assoc()) $departments[]=$row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Map | CTU-TUBURAN</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body, html{margin:0;padding:0;height:100%;overflow:hidden;font-family:Arial;}
.sidebar{position:fixed;top:0;left:0;width:250px;height:100vh;background:#7c0000;color:white;padding:20px;box-sizing:border-box;overflow-y:auto;}
.sidebar h2{text-align:center;margin-bottom:20px;}
.sidebar a{display:block;padding:10px;color:white;text-decoration:none;margin:5px 0;border-radius:6px;}
.sidebar a:hover{background:#5e0000;}
#viewer{position:absolute;top:0;left:250px;width:calc(100% - 250px);height:100%;background:#f1eded;}
.label{color:#ff7a00;font-size:14px;font-weight:bold;background:rgba(255,255,255,0.85);padding:4px 8px;border-radius:6px;white-space:nowrap;box-shadow:0 2px 6px rgba(0,0,0,0.2);cursor:pointer;}
.label i{margin-right:5px;color:#ff7a00;}
.popup{position:absolute;top:20px;left:50%;transform:translateX(-50%);background:white;border:2px solid #ff7a00;border-radius:10px;padding:15px;box-shadow:0 5px 15px rgba(0,0,0,0.2);display:none;width:250px;z-index:10;}
.popup button{background:#ff7a00;color:white;border:none;padding:6px 10px;border-radius:5px;cursor:pointer;margin-top:10px;}
.popup button:hover{background:#e65c00;}
</style>
</head>
<body>

<div class="sidebar">
  <div class="nav-left">
    <img src="Logo.png" alt="CTU Logo" />
  </div>
  <h2>ADMIN</h2>
 <a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
  <a href="admin_dashboard.php?page=manageAccount"><i class="fas fa-user-cog"></i> Manage Account</a>
  <a href="admin_dashboard.php?page=staffList"><i class="fas fa-users"></i> Manage Staffs</a>
  <a href="admin_dashboard.php?page=manageOffices"><i class="fas fa-building"></i> Manage Offices</a>
  <a href="admin_dashboard.php?page=manageMap"><i class="fas fa-map-marked-alt"></i> Manage Map</a>
  <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div id="viewer">
  <div class="popup" id="popup">
    <h3>Building Services</h3>
    <p id="popup-content"></p>
    <button onclick="document.getElementById('popup').style.display='none'">Close</button>
  </div>
</div>

<script>
  // pass PHP departments to JS
  const departmentsData = <?php echo json_encode($departments); ?>;
</script>

<script type="module">
import * as THREE from "https://esm.sh/three@0.152.2";
import { GLTFLoader } from "https://esm.sh/three@0.152.2/examples/jsm/loaders/GLTFLoader.js";
import { OrbitControls } from "https://esm.sh/three@0.152.2/examples/jsm/controls/OrbitControls.js";
import { CSS2DRenderer, CSS2DObject } from "https://esm.sh/three@0.152.2/examples/jsm/renderers/CSS2DRenderer.js";

window.addEventListener('DOMContentLoaded', () => {
    const viewer = document.getElementById("viewer");
    if (!viewer) {
        console.error("No #viewer div found!");
        return;
    }

    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0xf1eded);

    const aspect = window.innerWidth / window.innerHeight;
    const d = 10;
    const camera = new THREE.OrthographicCamera(-d * aspect, d * aspect, d, -d, 0.1, 1000);
    camera.position.set(0, 20, 0);
    camera.lookAt(0, 0, 0);

    const renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(window.innerWidth - 300, window.innerHeight - 60);
    viewer.appendChild(renderer.domElement);

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

    loader.load("/CTUnavA2/models/educblg.gltf", (gltf) => {
        const building = gltf.scene;
        building.name = "Education Department";

        // Add basic material if black
        building.traverse((child) => {
            if (child.isMesh) {
                if (!child.material) child.material = new THREE.MeshStandardMaterial({color:0xaaaaaa});
                clickableObjects.push(child);
                child.castShadow = true;
                child.receiveShadow = true;
            }
        });

        scene.add(building);

        const box = new THREE.Box3().setFromObject(building);
        const center = box.getCenter(new THREE.Vector3());
        const size = box.getSize(new THREE.Vector3());

        camera.position.set(center.x, size.y * 2, center.z);
        controls.target.copy(center);

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
                if (INTERSECTED) INTERSECTED.material.emissive.setHex(INTERSECTED.currentHex);
                INTERSECTED = intersects[0].object;
                INTERSECTED.currentHex = INTERSECTED.material.emissive.getHex();
                INTERSECTED.material.emissive.setHex(0x333333);
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
});
</script>

</body>
</html>
