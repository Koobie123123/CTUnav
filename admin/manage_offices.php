<link rel="stylesheet" href="styles.css">


<?php
// Add
if (isset($_POST['add_office'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $desc = $conn->real_escape_string($_POST['description']);
    $loc = $conn->real_escape_string($_POST['location']);
    $conn->query("INSERT INTO departments_and_offices (name, description, location) VALUES ('$name', '$desc', '$loc')");
    header("Location: admin_dashboard.php?page=manageOffices");
    exit();
}

// Delete
if (isset($_POST['delete_office'])) {
    $id = intval($_POST['delete_office']);
    $conn->query("DELETE FROM departments_and_offices WHERE departmentID=$id");
    header("Location: admin_dashboard.php?page=manageOffices");
    exit();
}

// Update
if (isset($_POST['update_office'])) {
    $id = intval($_POST['update_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $desc = $conn->real_escape_string($_POST['description']);
    $loc = $conn->real_escape_string($_POST['location']);
    $conn->query("UPDATE departments_and_offices SET name='$name', description='$desc', location='$loc' WHERE departmentID=$id");
    header("Location: admin_dashboard.php?page=manageOffices");
    exit();
}

// Fetch data
$offices = $conn->query("SELECT * FROM departments_and_offices");

// Edit check
$editing = false;
$editData = null;
if (isset($_GET['edit'])) {
    $editing = true;
    $editId = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM departments_and_offices WHERE departmentID=$editId");
    if ($result->num_rows > 0) {
        $editData = $result->fetch_assoc();
    }
}
?>

<div class="manage-wrapper">
  <!-- Table Left -->
  <div class="office-table">
    <div class="card">
      <h3>Offices and Departments List</h3>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Location</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $offices->fetch_assoc()): ?>
          <tr>
            <td><?= $row['departmentID'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= htmlspecialchars($row['location']) ?></td>
            <td>
              <a href="admin_dashboard.php?page=manageOffices&edit=<?= $row['departmentID'] ?>" title="Edit"><i class="fas fa-pen"></i></a>
              <form method="post" style="display:inline;" onsubmit="return confirm('Delete this entry?');">
                <input type="hidden" name="delete_office" value="<?= $row['departmentID'] ?>">
                <button type="submit" title="Delete"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
    
  <!-- Form Right -->
  <div class="office-form">
    <h3><?= $editing ? "Edit Office/Department" : "Add New Office/Department" ?></h3>
    <form method="post">
     <input type="text" name="name" placeholder="Name" value="<?= $editing ? htmlspecialchars($editData['name']) : '' ?>" required>

    <textarea name="description" placeholder="Description" ><?= $editing ? htmlspecialchars($editData['description']) : '' ?></textarea>

    <input type="text" name="location" placeholder="Location" value="<?= $editing ? htmlspecialchars($editData['location']) : '' ?>" >

      <?php if ($editing): ?>
        <input type="hidden" name="update_id" value="<?= $editData['departmentID'] ?>">
        <button type="submit" name="update_office">Update</button>
        <a href="admin_dashboard.php?page=manageOffices">Cancel</a>
      <?php else: ?>
        <button type="submit" name="add_office">Add</button>
      <?php endif; ?>
    </form>
  </div>
</div>
