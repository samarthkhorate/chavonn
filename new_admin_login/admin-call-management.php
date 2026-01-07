<?php
// ======================================================
// Call Management Dashboard (Admin Panel)
// ======================================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../include/config.php");
include("sidebar.php"); // ✅ Uses your existing header + sidebar
date_default_timezone_set("Asia/Kolkata");

// Fetch all agents/channels
$agentQuery = "SELECT channel_id, channel_name FROM tbl_channel WHERE channel_active = 1 ORDER BY channel_name ASC";
$agents = $con->query($agentQuery);

// Fetch all call requests
$query = "SELECT cr.*, ch.channel_name 
          FROM tbl_call_requests cr
          LEFT JOIN tbl_channel ch ON cr.agent_no = ch.channel_id
          WHERE (cr.status IS NULL OR cr.status = '' OR cr.status <> 'Completed')
          ORDER BY cr.id DESC";



$result = $con->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Call Management Dashboard</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #f1f5f9;
  margin: 0;
}
.main-container {
  margin-left: 250px; /* Matches your sidebar width */
  padding: 30px;
  transition: 0.3s;
}
.card {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 3px 15px rgba(0,0,0,0.08);
  padding: 20px;
  overflow-x: auto;
}
.card h2 {
  font-size: 22px;
  color: #1e293b;
  border-bottom: 2px solid #2563eb;
  padding-bottom: 8px;
  margin-bottom: 20px;
}
.table-wrapper {
  width: 100%;
  overflow-x: auto;
}
table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  font-size: 14px;
}
th, td {
  padding: 12px;
  border-bottom: 1px solid #e2e8f0;
  text-align: center;
}
th {
  background: #2563eb;
  color: white;
  font-weight: 500;
}
tr:nth-child(even) {
  background: #f8fafc;
}
select, button {
  padding: 6px 10px;
  border-radius: 6px;
  border: 1px solid #cbd5e1;
  font-size: 13px;
}
button {
  background: #2563eb;
  color: white;
  border: none;
  cursor: pointer;
  transition: 0.3s;
}
button:hover {
  background: #1d4ed8;
}
.status {
  font-weight: bold;
}
.status.Pending { color: #b91c1c; }
.status['In Progress'] { color: #f59e0b; }
.status.Completed { color: #16a34a; }
@media (max-width: 768px) {
  .main-container {
    margin-left: 0;
    padding: 15px;
  }
  th, td {
    font-size: 12px;
    padding: 8px;
  }
  h2 {
    font-size: 18px;
  }
}
</style>
</head>
<body>

<div class="main-container">
  <div class="card">
    <h2>📞 Call Management Dashboard</h2>

    <div class="table-wrapper">
      <table>
        <tr>
          <th>ID</th>
          <th>User Name</th>
          <th>Mobile</th>
          <th>Created At</th>
          <th>Agent (Channel)</th>
          <th>Status</th>
          <th>Call Time</th>
          <th>Action</th>
        </tr>

        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <form method="POST" action="update_call_status.php">
              <td><?= $row['id']; ?></td>
              <td><?= htmlspecialchars($row['user_name']); ?></td>
              <td><?= htmlspecialchars($row['user_mobile']); ?></td>
              <td><?= date("d M Y h:i A", strtotime($row['created_at'])); ?></td>

              <td>
                <select name="agent_no" required>
                  <option value="">Select Agent</option>
                  <?php
                    mysqli_data_seek($agents, 0);
                    while ($agent = $agents->fetch_assoc()):
                  ?>
                    <option value="<?= $agent['channel_id']; ?>" <?= ($row['agent_no'] == $agent['channel_id']) ? 'selected' : ''; ?>>
                      <?= $agent['channel_id'] . " - " . htmlspecialchars($agent['channel_name']); ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </td>

              <td>
                <select name="status" required>
                  <?php
                    $statuses = ['Pending','In Progress','Completed','Cancelled','No Answer','Call Back Later'];
                    foreach ($statuses as $status) {
                        $selected = ($row['status'] === $status) ? 'selected' : '';
                        echo "<option value='$status' $selected>$status</option>";
                    }
                  ?>
                </select>
              </td>

              <td><?= $row['call_attended_datetime'] ? date("d M Y h:i A", strtotime($row['call_attended_datetime'])) : '-'; ?></td>
              <td>
                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                <button type="submit">Update</button>
              </td>
            </form>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8">No call requests found.</td></tr>
        <?php endif; ?>
      </table>
    </div>
  </div>
</div>

</body>
</html>
