<?php
// Include the database connection file
include '../config.php';


// Fetch all orders using the primary key 'id' in descending order
$query = "SELECT * FROM tbl_old_orders ORDER BY id DESC";
$result = mysqli_query($con, $query);

// Count orders with payment status 'paid_but_need_to_fetch_info'
$count_paid_but_need_to_fetch_info_query = "SELECT COUNT(*) as count FROM tbl_old_orders WHERE payment_status = 'paid_but_need_to_fetch_info'";
$pending_to_ship_query = "SELECT COUNT(*) as count FROM tbl_old_orders WHERE payment_status = 'paid' AND (ship_shipment_id IS NULL OR ship_shipment_id = '')";

// Execute the first query
$count_result = mysqli_query($con, $count_paid_but_need_to_fetch_info_query);
$count_row = mysqli_fetch_assoc($count_result);
$count_paid_but_need_to_fetch_info = $count_row['count'];

// Execute the second query
$pending_to_ship_result = mysqli_query($con, $pending_to_ship_query);
$pending_to_ship_row = mysqli_fetch_assoc($pending_to_ship_result);
$pending_to_ship = $pending_to_ship_row['count'];


// If the form is submitted to download the selected records
if (isset($_POST['download_csv'])) {
    if (!empty($_POST['order_ids'])) {
        // Sanitize and handle the selected order IDs
        $order_ids = array_map('intval', $_POST['order_ids']);
        $order_ids_str = implode(",", $order_ids); // Convert array to comma-separated string

        // Query to fetch the selected orders
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
            $headers = ["Order ID","Razorpay Order ID" ,"Full Name", "Mobile No", "Street", "Landmark", "City", "Taluka", "District", "Pincode", "Quantity", "Payment Mode", "Value Books", "Value Charges", "Total Amount", "Order Date", "Order Time", "Payment Status", "Razorpay ID", "Bank Reference No","Shiprocket Order id","Shiprocket channal id","shiprocket shipment id","shipment status","shipment status code","shipment onboarding","shipment awb no","shipment courier id","shipment courier name","shipment new channel"];
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        // JavaScript function to handle "Select All" checkbox
        function toggleSelectAll(source) {
            const checkboxes = document.querySelectorAll('input[name="order_ids[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
            });
        }

        function openInNewTab(url) {
            window.open(url, '_blank');
        }
    </script>
    <style>
        /* Table border styling */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        /* Button Styling */
        .btn-download {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-download:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<script src="auth.js"></script>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h2>Sync Data and Process Orders</h2>
            <!-- Display the count of orders with status 'paid_but_need_to_fetch_info' -->
            <p><strong>Orders with payment status "Paid but need to fetch info": </strong> <?php echo $count_paid_but_need_to_fetch_info; ?><p>
                <strong>Orders Paid but not shipped ": </strong> <?php echo $pending_to_ship; ?>
        </p>
        
        </div>
        <div class="card-body">
            <!-- Buttons to sync data with Razorpay, send orders to Shiprocket, and retrieve tracking info -->
            <button class="btn btn-info mb-3" onclick="openInNewTab('../order/fetch_all_payment.php')">Sync Data with Razorpay</button>
            <button class="btn btn-success mb-3" onclick="openInNewTab('new_order_generate.php')">V2 Send Orders to Shiprocket</button>
            <button class="btn btn-warning mb-3" onclick="openInNewTab('fetch_all_orders_from_shiprocekt.php')">Retrieve Tracking Info from Shiprocket</button>
        </div>
    </div>

    <form method="post" class="mt-5">
        <h3>Select Orders to Download</h3>

        <!-- Button to trigger download -->
        <button type="submit" name="download_csv" class="btn-download mb-3">Download Selected Orders in CSV</button>
        
        <br><br>

        <!-- Table displaying all orders with checkboxes -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><input type="checkbox" onclick="toggleSelectAll(this)"> Select All</th>
                    <th>Action</th>
                    <th>Order ID</th>
                    <th>Full Name</th>
                    <th>Mobile No</th>
                    <th>Address</th>
                    <th>Order Details</th>
                    <th>Order Date</th>
                    <th>Payment Details</th>
                    <th>Shipment Details</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($order = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td><input type='checkbox' name='order_ids[]' value='" . $order['id'] . "'></td>";
                        echo "<td><a href='https://vaibhavdhus.com/order/thank_you.php?page_tracking=" . htmlspecialchars($order['order_id']) . "' target='_blank' class='btn btn-primary btn-sm'>Capture PYMT</a></td>";
                        echo "<td><center>" . htmlspecialchars($order['order_id']) . "<br> - <br>" . htmlspecialchars($order['id']) . "</center></td>";
                        echo "<td>" . htmlspecialchars($order['fname']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['mobno']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['street']) . "<br>" 
                                 . htmlspecialchars($order['landmark']) . "<br>" 
                                 . htmlspecialchars($order['city']) . "<br>" 
                                 . htmlspecialchars($order['taluka']) . "<br>" 
                                 . htmlspecialchars($order['district']) . "<br>" 
                                 . htmlspecialchars($order['pincode']) . "</td>";
                        echo "<td>QTY : " . htmlspecialchars($order['qty']) . "<br>";
                        echo "MODE : " . htmlspecialchars($order['mode_of_payment']) . "<br>";
                        echo "Book : " . htmlspecialchars($order['value_books']) . "<br>";
                        echo "Charges : " . htmlspecialchars($order['value_charges']) . "<br>";
                        echo "Total : " . htmlspecialchars($order['total_amount']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['order_date']) . "<br>" . htmlspecialchars($order['order_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['payment_status']) . "<br>" . htmlspecialchars($order['razorpay_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['ship_awb_code']) . "<br>" . htmlspecialchars($order['ship_courier_name']) . "<br>" . htmlspecialchars($order['ship_status']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' class='text-center'>No orders found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </form>
</div>

</body>
</html>
