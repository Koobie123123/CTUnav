<style>
.sidebar {
  position: fixed;       /* keeps it steady */
  top: 0;                /* pin to top */
  left: 0;               /* pin to left */
  width: 250px;
  height: 100vh;         /* full viewport height */
  background-color: #7c0000;
  color: white;
  padding: 20px;
  box-sizing: border-box;
  overflow-y: auto;      /* add scroll if content is taller than screen */
}

  .sidebar h2 {
    text-align: center;
    margin-bottom: 20px;
  }
  .sidebar a {
    display: block;
    padding: 10px;
    color: white;
    text-decoration: none;
    margin: 5px 5px;
    border-radius: 6px;
    transition: background 0.3s;
  }

  .sidebar a i {
    margin-right: 10px;
  }
  .sidebar a:hover {
    background-color: #5e0000;
  }
  .nav-left img {
    display: block;
    margin: 0 auto 10px;
    width: 100px;
    height: 100px;
  }

</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


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
