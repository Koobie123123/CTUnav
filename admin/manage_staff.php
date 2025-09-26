<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  /* General Styles */
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f5f5;
    margin: 0;
    padding: 20px;
    color: #333;
  }
  
  /* Card and Table Styles */
  .card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 20px;
  }
  
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
  }
  
  th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
  }
  
  /* Table Header Styles */
  thead th {
    background-color: #f39c12 !important;
    color: white;
    font-weight: 600;
    text-align: center !important;
  }
  
  th {
    background-color: #f8f9fa;
    font-weight: 600;
  }
  
  /* Action Buttons in Table */
  .action-btn {
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    margin: 0 2px;
    transition: all 0.2s;
  }
  
  .action-btn.danger {
    background-color: #f8d7da;
    color: #721c24;
  }
  
  .action-btn.danger:hover {
    background-color: #f1b0b7;
  }
  
  .edit-btn, .demote-btn {
    background-color: #e9ecef;
    color: #495057;
  }
  
  .edit-btn:hover {
    background-color: #dae0e5;
  }
  
  .demote-btn:hover {
    background-color: #fff3cd;
    color: #856404;
  }
  
  /* Modal Styles */
  .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', Tahoma, sans-serif;
  }
  
  .modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 0;
    width: 90%;
    max-width: 600px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    overflow: hidden;
  }
  
  /* Header Styles */
  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e0e0e0;
  }
  
  .modal-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
  }
  
  .close-btn {
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    color: #666;
    position: static;
  }
  
  .close-btn:hover {
    color: #333;
  }
  
  /* Cover Photo Section */
  .cover-photo {
    height: 120px;
    background-color: #e0e0e0;
    position: relative;
  }
  
  /* Profile Section */
  .profile-section {
    text-align: center;
    position: relative;
    margin-top: -60px;
    margin-bottom: 20px;
  }
  
  .profile-pic {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 12px;
  }
  
  .profile-name {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 4px;
  }
  
  .profile-email {
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
  }
  
  .upload-btn {
    display: inline-block;
    color: #f39c12;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    margin-top: -10px;
    margin-bottom: 15px;
  }
  
  .upload-btn:hover {
    text-decoration: underline;
  }
  
  /* Form Styles */
  .modal-form {
    padding: 0 24px 24px;
  }
  
  .section-divider {
    height: 1px;
    background-color: #e0e0e0;
    margin: 24px 0;
  }
  
  .name-fields {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
  }
  
  .name-field {
    flex: 1;
  }
  
  .form-group {
    margin-bottom: 20px;
  }
  
  label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    font-size: 14px;
    color: #555;
  }
  
  input[type="text"],
  input[type="email"],
  select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
  }
  
  input[type="text"]:focus,
  input[type="email"]:focus,
  select:focus {
    outline: none;
    border-color: #1976d2;
    box-shadow: 0 0 0 2px rgba(25, 118, 210, 0.2);
  }
  
  /* Action Buttons */
  .modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
  }
  
  .action-btn.save {
    background-color: #1976d2;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .action-btn.save:hover {
    background-color: #1565c0;
  }
  
  .action-btn.cancel {
    background-color: #f5f5f5;
    color: #333;
    border: 1px solid #ddd;
    padding: 10px 20px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .action-btn.cancel:hover {
    background-color: #e0e0e0;
  }
  
  .confirm-btn {
    background-color: #28a745;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .confirm-btn:hover {
    background-color: #218838;
  }
  
  /* Delete User Section */
  .delete-section {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
  }
  
  .delete-btn {
    color: #d32f2f;
    background: none;
    border: none;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    padding: 0;
  }
  
  .delete-btn:hover {
    text-decoration: underline;
  }
</style>

<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "ctunav";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Demotion logic: staff → user
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['demote_id'])) {
    $userID = intval($_POST['demote_id']);

    // Update role in users table
    $conn->query("UPDATE users SET role='user' WHERE userID = $userID");

    // Delete from staff table
    $conn->query("DELETE FROM staff WHERE userID = $userID");

    echo "<script>window.location.href='admin_dashboard.php?page=staffList';</script>";
    exit();
}

// Fetch all staff with department name
$staff = $conn->query("
    SELECT u.userID, u.first_name, u.middle_name, u.last_name, u.email, u.profile_image,
           d.name AS department_name, d.departmentID
    FROM users u
    JOIN staff s ON u.userID = s.userID
    LEFT JOIN departments_and_offices d ON s.departmentID = d.departmentID
    WHERE u.role = 'staff'
");

// Fetch departments for the edit modal
$departments = $conn->query("SELECT departmentID, name FROM departments_and_offices");

// Staff edit logic: update both users and staff
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save_staff'])) {
    $id = intval($_POST['edit_staff_id']);
    $firstName = $conn->real_escape_string($_POST['first_name']);
    $middleName = $conn->real_escape_string($_POST['middle_name']);
    $lastName = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $departmentID = intval($_POST['departmentID']);

    // Generate full name for staff table
    $fullName = trim($firstName . ' ' . $middleName . ' ' . $lastName);

    // Handle profile upload if provided
    $profilePath = null;
    if (!empty($_FILES['profile_image']['name'])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFile)) {
            $profilePath = $conn->real_escape_string("uploads/" . $fileName);
            // Update users profile path
            $conn->query("UPDATE users SET profile_image='$profilePath' WHERE userID=$id");
        }
    }

    // --- Update users table (names + email) ---
    $conn->query("UPDATE users 
                  SET first_name='$firstName', middle_name='$middleName', last_name='$lastName', email='$email'
                  WHERE userID=$id");

    // --- Update staff table ---
    $updateStaff = "UPDATE staff 
                    SET fullName='$fullName', email='$email', departmentID=$departmentID";
    
    if ($profilePath) {
        $updateStaff .= ", photo='$profilePath'";
    }
    
    $updateStaff .= " WHERE userID=$id";
    $conn->query($updateStaff);

    echo "<script>window.location.href='admin_dashboard.php?page=staffList';</script>";
    exit();
}
?>

<div class="card">
  <h2>Staff List</h2>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Profile</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Department/Office</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $staff->fetch_assoc()): ?>
      <tr>
        <td><?= $row['userID'] ?></td>
        <td>
          <?php if (!empty($row['profile_image'])): ?>
            <img src="../<?= htmlspecialchars($row['profile_image']) ?>" 
                 alt="Profile" width="40" height="40" style="border-radius:50%;">
          <?php else: ?>
            <img src="../uploads/default.png" 
                 alt="No Profile" width="40" height="40" style="border-radius:50%;">
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['department_name'] ?? 'N/A') ?></td>
        <td class="action-btns">
          <!-- Edit Button -->
          <button type="button"
                  class="action-btn edit-btn"
                  onclick="openEditStaffModal(
                    <?= $row['userID'] ?>, 
                    '<?= htmlspecialchars($row['first_name']) ?>', 
                    '<?= htmlspecialchars($row['middle_name']) ?>', 
                    '<?= htmlspecialchars($row['last_name']) ?>', 
                    '<?= htmlspecialchars($row['email']) ?>',
                    '<?= htmlspecialchars($row['profile_image']) ?>',
                    <?= $row['departmentID'] ?? 'null' ?>
                  )">
            <i class="fas fa-pen"></i>
          </button>

          <!-- Delete Button -->
          <form method="post" onsubmit="return confirm('Are you sure you want to delete this staff?');" style="display:inline;">
            <input type="hidden" name="delete_id" value="<?= $row['userID'] ?>">
            <button type="submit" class="action-btn danger" title="Delete">
              <i class="fas fa-trash"></i>
            </button>
          </form>

          <!-- Demote Button -->
          <form method="post" class="demote-form" style="display:inline;">
            <input type="hidden" name="demote_id" value="<?= $row['userID'] ?>">
            <button type="button" class="action-btn demote-btn" title="Demote to Student">
              <i class="fas fa-user-minus"></i>
            </button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Demote Modal -->
<div id="demoteModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Confirm Demotion</h3>
      <span class="close-btn" onclick="closeDemoteModal()">&times;</span>
    </div>
    <div style="padding: 20px;">
      <p>Are you sure you want to demote this staff to student?</p>
      <div class="modal-actions">
        <button id="confirmDemote" class="action-btn save">Yes</button>
        <button onclick="closeDemoteModal()" class="action-btn cancel">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Staff Modal -->
<div id="editStaffModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Edit Staff Information</h3>
      <span class="close-btn" onclick="closeEditStaffModal()">&times;</span>
    </div>

    <!-- Cover Photo -->
    <div class="cover-photo"></div>

    <!-- Staff Info Form -->
    <form method="post" enctype="multipart/form-data" class="modal-form">
      <input type="hidden" name="edit_staff_id" id="edit_staff_id">

      <!-- Profile Section -->
      <div class="profile-section">
        <img src="../uploads/default.png" 
             alt="Profile Image" 
             class="profile-pic" 
             id="staffProfilePreview">
        <div class="profile-name" id="staffProfileFullName">Full Name</div>
        <div class="profile-email" id="staffProfileEmail">user@example.com</div>
        <label for="staffProfileUpload" class="upload-btn">Change Photo</label>
        <input type="file" id="staffProfileUpload" name="profile_image" accept="image/*" hidden
               onchange="previewStaffProfile(event)">
      </div>

      <!-- Divider Line -->
      <div class="section-divider"></div>

      <!-- Name Fields (Horizontal Layout) -->
      <div class="name-fields">
        <div class="name-field">
          <label>First Name</label>
          <input type="text" name="first_name" id="edit_staff_fname" required>
        </div>
        
        <div class="name-field">
          <label>Middle Name</label>
          <input type="text" name="middle_name" id="edit_staff_mname">
        </div>
        
        <div class="name-field">
          <label>Last Name</label>
          <input type="text" name="last_name" id="edit_staff_lname" required>
        </div>
      </div>

      <!-- Email Field -->
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" id="edit_staff_email" required>
      </div>

      <!-- Department Field -->
      <div class="form-group">
        <label>Department/Office</label>
        <select name="departmentID" id="edit_staff_department" required>
          <option value="">Select Department</option>
          <?php while($dept = $departments->fetch_assoc()): ?>
            <option value="<?= $dept['departmentID'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Action Buttons -->
      <div class="modal-actions">
        <button type="button" class="action-btn cancel" onclick="closeEditStaffModal()">Cancel</button>
        <button type="submit" name="save_staff" class="action-btn save">Save changes</button>
      </div>
    </form>
  </div>
</div>

<script>
  let demoteForm = null;

  document.querySelectorAll('.demote-btn').forEach(button => {
    button.addEventListener('click', function () {
      demoteForm = this.closest('form');
      document.getElementById('demoteModal').style.display = 'flex';
    });
  });

  document.getElementById('confirmDemote').addEventListener('click', function () {
    if (demoteForm) {
      demoteForm.submit();
    }
    document.getElementById('demoteModal').style.display = 'none';
  });

  function closeDemoteModal() {
    document.getElementById('demoteModal').style.display = 'none';
    demoteForm = null;
  }

  function openEditStaffModal(userID, firstName, middleName, lastName, email, profileImage, departmentID) {
    const modal = document.getElementById("editStaffModal");
    modal.style.display = "flex";

    // Populate fields
    document.getElementById("edit_staff_id").value = userID;
    document.getElementById("edit_staff_fname").value = firstName;
    document.getElementById("edit_staff_mname").value = middleName;
    document.getElementById("edit_staff_lname").value = lastName;
    document.getElementById("edit_staff_email").value = email;
    document.getElementById("edit_staff_department").value = departmentID || '';
    
    // Update profile section
    document.getElementById("staffProfileFullName").textContent = `${firstName} ${lastName}`;
    document.getElementById("staffProfileEmail").textContent = email;
    
    // Profile image
    const profilePreview = document.getElementById("staffProfilePreview");
    profilePreview.src = profileImage ? "../" + profileImage : "../uploads/default.png";
  }

  function closeEditStaffModal() {
    document.getElementById("editStaffModal").style.display = "none";
  }

  function previewStaffProfile(event) {
    const reader = new FileReader();
    reader.onload = function(){
      document.getElementById("staffProfilePreview").src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
  }

  // Close modals when clicking outside
  window.onclick = function(event) {
    const demoteModal = document.getElementById("demoteModal");
    const editModal = document.getElementById("editStaffModal");
    
    if (event.target === demoteModal) {
      closeDemoteModal();
    }
    
    if (event.target === editModal) {
      closeEditStaffModal();
    }
  }
</script>