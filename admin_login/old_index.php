<?php
// Include the database connection file
include '../config.php';

// Fetch all orders using the primary key 'id' in descending order
$query = "SELECT * FROM tbl_old_orders ORDER BY id DESC";
$result = mysqli_query($con, $query);

// If the form is submitted to download the selected records
if (isset($_POST['download_csv'])) {
    if (!empty($_POST['order_ids'])) {
        // Sanitize and handle the selected order IDs (use 'id' instead of 'order_id')
        $order_ids = array_map('intval', $_POST['order_ids']); // Ensures all values are integers
        $order_ids_str = implode(",", $order_ids); // Convert array to comma-separated string

        // Query to fetch the selected orders using 'id'
        $download_query = "SELECT * FROM tbl_old_orders WHERE id IN ($order_ids_str) ORDER BY id DESC";
        $download_result = mysqli_query($con, $download_query);

        if ($download_result) {
            // Set the file name for the download
            $filename = "orders_" . date('Y-m-d_H-i-s') . ".csv";

            // Set headers to prompt file download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Open the output stream for CSV
            $output = fopen('php://output', 'w');

            // Add column headers to CSV
            $headers = ["Order ID", "Full Name", "Mobile No", "Street", "Landmark", "City", "Taluka", "District", "Pincode", "Quantity", "Payment Mode", "Value Books", "Value Charges", "Total Amount", "Order Date", "Order Time", "Payment Status", "Razorpay ID", "Bank Reference No"];
            fputcsv($output, $headers);

            // Populate data
            while ($order = mysqli_fetch_assoc($download_result)) {
                fputcsv($output, $order);
            }

            // Close output stream
            fclose($output);
            exit;
        } else {
            echo "Error fetching data: " . mysqli_error($con);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders List</title>
</head>
<body>

    <!-- Form for selecting orders and downloading -->
    <form method="post">
        <h3>Select Orders to Download</h3>
        
        <!-- Button to trigger download -->
        <button type="submit" name="download_csv">Download Selected Orders in CSV</button>
        
        <br><br>

        <!-- Table displaying all orders with checkboxes -->
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th><input type="checkbox" onclick="toggleSelectAll(this)"> Select All</th>
                    <th>Action</th>
                    <th>Razorpay Order ID</th>
                    <th>System Order ID</th>
                    <th>Full Name</th>
                    <th>Mobile No</th>
                    <th>Street</th>
                    <th>Landmark</th>
                    <th>City</th>
                    <th>Taluka</th>
                    <th>District</th>
                    <th>Pincode</th>
                    <th>Quantity</th>
                    <th>Payment Mode</th>
                    <th>Value Books</th>
                    <th>Value Charges</th>
                    <th>Total Amount</th>
                    <th>Order Date</th>
                    <th>Order Time</th>
                    <th>Payment Status</th>
                    <th>Razorpay ID</th>
                    <th>Bank Reference No</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($order = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td><input type='checkbox' name='order_ids[]' value='" . $order['id'] . "'></td>";
                        echo "<td><a href='https://vaibhavdhus.com/order/thank_you.php?page_tracking=" . htmlspecialchars($order['order_id']) . "' target='_blank'>Capture PYMT</a></td>";

                        echo "<td>" . htmlspecialchars($order['order_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['fname']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['mobno']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['street']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['landmark']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['city']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['taluka']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['district']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['pincode']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['qty']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['mode_of_payment']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['value_books']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['value_charges']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['total_amount']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['order_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['order_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['payment_status']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['razorpay_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['bank_ref_no']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='18'>No orders found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </form>
<script src="auth.js"></script>
    <!-- JavaScript to handle select/deselect all checkboxes -->
   

</body>
</html>
