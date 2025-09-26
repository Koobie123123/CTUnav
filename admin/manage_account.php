<?php
// manage_account.php
?>
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
  
  /* Table Header Styles */
  thead th {
    background-color: #f39c12 !important;
    color: white;
    font-weight: 600;
    text-align: left;
    margin-left: 15px;
  }
  
  /* Rest of your existing CSS remains the same */
  th, td {
    padding: 12px 15px;
    border-bottom: 1px solid #e0e0e0;
  }
  
  th {
    font-weight: 600;
  }
  
  /* Keep table body cells left-aligned */
  tbody td {
    text-align: left;
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
  
  /* Role select dropdown */
  form select {
    padding: 6px 12px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: #fff;
    color: #333;
    outline: none;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  
  form select:hover {
    border-color: #ff7a00;
    box-shadow: 0 0 6px rgba(124, 0, 0, 0.2);
  }
  
  form select:focus {
    border-color: #ff7a00;
    box-shadow: 0 0 8px rgba(124, 0, 0, 0.3);
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
  
  .upload-btn {
    display: inline-block;
    color: #f39c12;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    margin-top: -25px; /* Move button upward */
    margin-bottom: 15px; /* Adjust spacing below */
  }

  /* Adjust profile email margin to maintain spacing */
  .profile-email {
    font-size: 14px;
    color: #666;
    margin-bottom: 10px; /* Reduced to compensate for button movement */
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
  input[type="email"] {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
  }
  
  input[type="text"]:focus,
  input[type="email"]:focus {
    outline: none;
    border-color: #f39c12;
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
    background-color: #f39c12;
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
    background-color: #e79512ff;
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
$host = "localhost";
$user = "root";
$pass = "";
$db = "ctunav";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch all users
$users = $conn->query("SELECT userID, first_name, middle_name, last_name, email, role, created_at, profile_image FROM users");

// Fetch departments
$departments = $conn->query("SELECT departmentID, name FROM departments_and_offices");

// Handle role update and staff department assignment
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Case 1: Role update triggered
    if (isset($_POST['update_role'])) {
        $id = intval($_POST['user_id']);
        $newRole = $conn->real_escape_string($_POST['role']);

        // Update role in users table
        $conn->query("UPDATE users SET role='$newRole' WHERE userID=$id");

        // If promoted to staff, show department assignment
        if ($newRole === 'staff') {
            $userResult = $conn->query("SELECT first_name, middle_name, last_name, email FROM users WHERE userID=$id");
            if ($userResult && $userResult->num_rows > 0) {
                $user = $userResult->fetch_assoc();
                $fullName = $conn->real_escape_string(trim($user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name']));
                $email = $conn->real_escape_string($user['email']);

                // Check if staff already exists
                $check = $conn->query("SELECT * FROM staff WHERE userID = $id");
                if ($check->num_rows === 0) {
                    // Store temporary staff info in session and show modal for department selection
                    session_start();
                    $_SESSION['new_staff'] = [
                        'userID' => $id,
                        'fullName' => $fullName,
                        'email' => $email
                    ];
                    // Redirect to same page to trigger modal
                    echo "<script>window.location.href='admin_dashboard.php?page=manageAccount&assignDept=1';</script>";
                    exit();
                }
            }
        } else {
            // For roles not staff, redirect back
            echo "<script>window.location.href='admin_dashboard.php?page=manageAccount';</script>";
            exit();
        }
    }

    // Case 2: Department assignment submitted
    if (isset($_POST['assign_department'])) {
        session_start();
        $departmentID = intval($_POST['departmentID']);
        if (isset($_SESSION['new_staff'])) {
            $staff = $_SESSION['new_staff'];
            $conn->query("INSERT INTO staff (userID, fullName, email, departmentID) 
                         VALUES ({$staff['userID']}, '{$staff['fullName']}', '{$staff['email']}', $departmentID)");
            unset($_SESSION['new_staff']);
        }  
        echo "<script>window.location.href='admin_dashboard.php?page=manageAccount';</script>";
        exit();
    }

    // Case 3: Delete user
    if (isset($_POST['delete_id'])) {
        $del_id = intval($_POST['delete_id']);
        $conn->query("DELETE FROM users WHERE userID=$del_id");
        $conn->query("DELETE FROM staff WHERE userID=$del_id");
        echo "<script>window.location.href='admin_dashboard.php?page=manageAccount';</script>";
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save_user'])) {
    $id = intval($_POST['edit_user_id']);
    $firstName = $conn->real_escape_string($_POST['first_name']);
    $middleName = $conn->real_escape_string($_POST['middle_name']);
    $lastName = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);

    // Generate full name for staff table
    $fullName = trim($firstName . ' ' . $middleName . ' ' . $lastName);

    // Handle profile upload if provided
    $profilePath = null;
    if (!empty($_FILES['profile_image']['name'])) {
        // Web-accessible path (what goes in DB, relative to project root)
        $targetDir = "uploads/";

        // File system path (used by PHP to move file, since manage_account.php is in admin/)
        $fileSystemDir = "../" . $targetDir;

        // Make sure folder exists
        if (!is_dir($fileSystemDir)) mkdir($fileSystemDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $fileSystemPath = $fileSystemDir . $fileName; // ../uploads/12345_image.png
        $webPath = $targetDir . $fileName;           // uploads/12345_image.png

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $fileSystemPath)) {
            $profilePath = $conn->real_escape_string($webPath);

            // Save web path in DB (not ../)
            $conn->query("UPDATE users SET profile_image='$profilePath' WHERE userID=$id");
        }
    }

    // Update user info (3 name fields + email)
    $sql = "UPDATE users 
            SET first_name='$firstName', middle_name='$middleName', last_name='$lastName', email='$email' 
            WHERE userID=$id";
    $conn->query($sql);

    // --- Update staff table if user is a staff ---
    $checkStaff = $conn->query("SELECT staffID FROM staff WHERE userID=$id");
    if ($checkStaff && $checkStaff->num_rows > 0) {
        $updateStaff = "UPDATE staff 
                        SET fullName='$fullName', email='$email'";

        // If profile uploaded, also update staff photo
        if ($profilePath) {
            $updateStaff .= ", photo='$profilePath'";
        }

        $updateStaff .= " WHERE userID=$id";
        $conn->query($updateStaff);
    }

    echo "<script>window.location.href='admin_dashboard.php?page=manageAccount';</script>";
    exit();
}
?>

<div class="card">
  <h2>Manage Accounts</h2>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Profile</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Created At</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $users->fetch_assoc()): ?>
      <tr>
        <td><?= $row['userID'] ?></td>
        <td>
          <?php if (!empty($row['profile_image'])): ?>
              <img src="../<?= htmlspecialchars($row['profile_image']) ?>" 
                 alt="Profile" width="40" height="40" style="border-radius:50%;">
          <?php else: ?>
            <img src="uploads/default.png" 
                 alt="No Profile" width="40" height="40" style="border-radius:50%;">
          <?php endif; ?>
        </td>

        <td><?= htmlspecialchars(trim($row['first_name'].' '.$row['middle_name'].' '.$row['last_name'])) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td>
          <form method="post" style="display:inline;">
            <input type="hidden" name="user_id" value="<?= $row['userID'] ?>">
            <select name="role" onchange="this.form.submit()" class="role-select">
              <option value="user" <?= $row['role'] === 'user' ? 'selected' : '' ?>>User</option>
              <option value="staff" <?= $row['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
              <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            <input type="hidden" name="update_role" value="1">
          </form>
        </td>
        <td><?= date("M. d, Y h:i A", strtotime($row['created_at'])) ?></td>

        <td>
          <!-- Edit User -->
          <button type="button" class="action-btn" 
                  onclick="openEditModal(
                      <?= $row['userID'] ?>, 
                      '<?= htmlspecialchars($row['first_name']) ?>', 
                      '<?= htmlspecialchars($row['middle_name']) ?>', 
                      '<?= htmlspecialchars($row['last_name']) ?>', 
                      '<?= htmlspecialchars($row['email']) ?>',
                      '<?= htmlspecialchars($row['profile_image']) ?>'
                  )">
              <i class="fas fa-pen"></i>
          </button>

          <!-- Delete User -->
          <form method="POST" style="display:inline;" 
                onsubmit="return confirm('Are you sure you want to delete this account?');">
              <input type="hidden" name="delete_id" value="<?= $row['userID'] ?>">
              <button type="submit" class="action-btn danger">
                  <i class="fas fa-trash"></i>
              </button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Edit User Modal -->
<div class="modal" id="editUserModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Edit User</h3>
      <span class="close-btn" onclick="closeEditModal()">&times;</span>
    </div>

    <!-- Cover Photo -->
    <div class="cover-photo"></div>

    <!-- User Info Form -->
    <form method="post" enctype="multipart/form-data" class="modal-form">
      <input type="hidden" name="edit_user_id" id="edit_user_id">

      <!-- Profile Section -->
      <div class="profile-section">
        <img src="uploads/default.png" 
             alt="Profile Image" 
             class="profile-pic" 
             id="profilePreview">
        <div class="profile-name" id="profileFullName">Full Name</div>
        <div class="profile-email" id="profileEmail">user@example.com</div>
        <label for="profileUpload" class="upload-btn">Change Photo</label>
        <input type="file" id="profileUpload" name="profile_image" accept="image/*" hidden
               onchange="previewProfile(event)">
      </div>

      <!-- Divider Line -->
      <div class="section-divider"></div>

      <!-- Name Fields (Horizontal Layout) -->
      <div class="name-fields">
        <div class="name-field">
          <label>First Name</label>
          <input type="text" name="first_name" id="edit_first_name">
        </div>
        
        <div class="name-field">
          <label>Middle Name</label>
          <input type="text" name="middle_name" id="edit_middle_name">
        </div>
        
        <div class="name-field">
          <label>Last Name</label>
          <input type="text" name="last_name" id="edit_last_name">
        </div>
      </div>

      <!-- Email Field -->
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" id="edit_email">
      </div>

      <!-- Action Buttons -->
      <div class="modal-actions">
        <button type="button" class="action-btn cancel" onclick="closeEditModal()">Cancel</button>
        <button type="submit" name="save_user" class="action-btn save">Save changes</button>
      </div>
      
      <!-- Delete User Section -->
      <div class="delete-section">
        <button type="button" class="delete-btn" onclick="deleteUser()">Delete user</button>
      </div>
    </form>
  </div>
</div>

<!-- Department Assignment Modal -->
<?php if (isset($_GET['assignDept']) && isset($_SESSION['new_staff'])): ?>
<div id="departmentModal" class="modal" style="display: flex;">
  <div class="modal-content" style="max-width: 400px;">
    <div class="modal-header">
      <h3>Select Department for New Staff</h3>
      <span class="close-btn" onclick="closeDepartmentModal()">&times;</span>
    </div>
    <form method="post" style="padding: 20px;">
      <div class="form-group">
        <label>Department</label>
        <select name="departmentID" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
          <?php
          $departments = $conn->query("SELECT departmentID, name FROM departments_and_offices");
          while($dept = $departments->fetch_assoc()){
              echo "<option value='{$dept['departmentID']}'>{$dept['name']}</option>";
          }
          ?>
        </select>
      </div>
      <div class="modal-actions">
        <button type="button" class="action-btn cancel" onclick="closeDepartmentModal()">Cancel</button>
        <button type="submit" name="assign_department" class="action-btn save">Assign</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<script>
function openEditModal(userID, firstName, middleName, lastName, email, profileImage) {
    const modal = document.getElementById("editUserModal");
    modal.style.display = "flex";

    // Populate fields
    document.getElementById("edit_user_id").value = userID;
    document.getElementById("edit_first_name").value = firstName;
    document.getElementById("edit_middle_name").value = middleName;
    document.getElementById("edit_last_name").value = lastName;
    document.getElementById("edit_email").value = email;
    
    // Update profile section
    document.getElementById("profileFullName").textContent = `${firstName} ${lastName}`;
    document.getElementById("profileEmail").textContent = email;
    
    // Profile image
    const profilePreview = document.getElementById("profilePreview");
    profilePreview.src = profileImage ? "../" + profileImage : "uploads/default.png";
}

function closeEditModal() {
    document.getElementById("editUserModal").style.display = "none";
}

function previewProfile(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById("profilePreview").src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}

function deleteUser() {
    if (confirm('Are you sure you want to delete this user?')) {
        const userId = document.getElementById("edit_user_id").value;
        // You'll need to implement the actual deletion logic here
        console.log("Delete user with ID:", userId);
    }
}

function closeDepartmentModal() {
    document.getElementById("departmentModal").style.display = "none";
}

// Close modals when clicking outside
window.onclick = function(event) {
    const editModal = document.getElementById("editUserModal");
    const deptModal = document.getElementById("departmentModal");
    
    if (event.target === editModal) {
        closeEditModal();
    }
    
    if (event.target === deptModal) {
        closeDepartmentModal();
    }
}
</script>