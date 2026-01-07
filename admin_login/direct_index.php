<?php
include '../config.php'; // Database connection

if (isset($_POST['search'])) {
    $conditions = [];

    $fields = [
        'id', 'order_id', 'fname', 'mobno', 'pincode', 'qty', 'razorpay_id',
        'ship_awb_code', 'ship_courier_id', 'ship_courier_name', 'mode_of_payment',
        'value_books'
    ];

    foreach ($fields as $field) {
        if (!empty($_POST[$field])) {
            $conditions[] = "$field LIKE '%" . mysqli_real_escape_string($con, $_POST[$field]) . "%'";
        }
    }

    // Handle date
    if (!empty($_POST['order_date'])) {
        $date = DateTime::createFromFormat('d-m-Y', $_POST['order_date']);
        $formatted_date = $date->format('d-m-Y');
        $conditions[] = "order_date = '$formatted_date'";
    }

    if (!empty($_POST['language'])) {
        $conditions[] = "language = '" . mysqli_real_escape_string($con, $_POST['language']) . "'";
    }

    if (!empty($_POST['payment_status'])) {
        $conditions[] = "payment_status = '" . mysqli_real_escape_string($con, $_POST['payment_status']) . "'";
    }

    if (!empty($_POST['shiprocket_account'])) {
        $conditions[] = "shiprocket_account = '" . mysqli_real_escape_string($con, $_POST['shiprocket_account']) . "'";
    }

    if (!empty($_POST['meta'])) {
        $conditions[] = "meta = '" . mysqli_real_escape_string($con, $_POST['meta']) . "'";
    }

    if (empty($conditions)) {
        $error = "Please fill at least one field.";
    } else {
        $query = "SELECT * FROM tbl_old_orders WHERE " . implode(" AND ", $conditions);
        $result = mysqli_query($con, $query);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Premium Order Search</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .container-fluid { padding: 20px; }
        .search-card { box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); border-radius: 12px; padding: 20px; }
        .table th { background: #007bff; color: white; }
        .table-hover tbody tr:hover { background: #f1f1f1; }
        .btn-search { background: #007bff; color: white; }
        .btn-search:hover { background: #0056b3; }
        .btn-download { background: #28a745; color: white; }
        .btn-download:hover { background: #218838; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="card search-card">
        <h3 class="text-center mb-3"><i class="fa fa-search"></i> Search Orders</h3>

        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php } ?>

        <form method="post">
          <div class="row g-2">
    <?php
    $search_fields = [
        'id', 'order_id', 'fname', 'mobno', 'pincode', 'qty', 'razorpay_id',
        'ship_awb_code', 'ship_courier_id', 'ship_courier_name', 'mode_of_payment',
        'value_books'
    ];

    foreach ($search_fields as $field) {
        echo '<div class="col-md-3">';
        
        if ($field == 'mode_of_payment') {
            echo '<select name="mode_of_payment" class="form-select">
                    <option value="">Select Mode of Payment</option>
                    <option value="cod">COD</option>
                    <option value="prepaid">Prepaid</option>
                  </select>';
        } else {
            echo '<input type="text" name="' . $field . '" class="form-control" placeholder="' . ucfirst(str_replace("_", " ", $field)) . '">';
        }

        echo '</div>';
    }
    ?>
    <div class="col-md-3">
        <input type="text" name="order_date" class="form-control datepicker" placeholder="Order Date (DD-MM-YYYY)">
    </div>

    <div class="col-md-3">
        <select name="language" class="form-select">
            <option value="">Select Language</option>
            <?php
            // Fetch product SKUs and names
            $query1 = "SELECT product_sku_code, product_name FROM tbl_products WHERE product_name IS NOT NULL AND product_sku_code IS NOT NULL";
            $result1 = mysqli_query($con, $query1);
            while ($row1 = mysqli_fetch_assoc($result1)) { ?>
            <option value="<?php echo $row1['product_sku_code']; ?>"><?php echo $row1['product_name'];?></option>
        <?php }
            ?>
            <option value="Marathi_Part2">Marathi - Part 2</option>
            <option value="Hindi">Hindi - Part 1</option>
        </select>
    </div>

    <div class="col-md-3">
        <select name="payment_status" class="form-select">
            <option value="">Payment Status</option>
            <option value="Paid">Paid</option>
            <option value="Pending">Pending</option>
        </select>
    </div>
</div>


               <!--  <div class="col-md-3">
                    <select name="shiprocket_account" class="form-select">
                        <option value="">Shiprocket Account</option>
                        <option value="1">orders@vaibhavdhus.com</option>
                        <option value="2">admin@vaibhavdhus.com</option>
                    </select>
                </div> -->
<!-- 
                <div class="col-md-3">
                    <select name="meta" class="form-select">
                        <option value="">Meta (Ads Account)</option>
                        <option value="1">Ads Account managed by Kiran</option>
                        <option value="2">Ads Account managed by Vikram</option>
                    </select>
                </div> -->
            </div>

            <div class="text-center mt-4">
                <button type="submit" name="search" class="btn btn-search"><i class="fa fa-search"></i> Search</button>
                <?php if (isset($result) && mysqli_num_rows($result) > 0) { ?>
                    <a href="download_orders.php?query=<?php echo urlencode($query); ?>" class="btn btn-download"><i class="fa fa-download"></i> Download CSV</a>
                <?php } ?>
            </div>
        </form>
    </div>

    <?php if (isset($result) && mysqli_num_rows($result) > 0) { ?>
        <div class="card mt-4 p-3">
            <h5 class="card-header bg-primary text-white">Order Details</h5>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <?php
                            $display_fields = [
                                'id', 'order_id', 'fname', 'mobno', 'pincode', 'qty', 'razorpay_id',
                                'ship_awb_code', 'ship_courier_id', 'ship_courier_name', 'mode_of_payment',
                                'value_books', 'value_charges', 'total_amount', 'order_time', 'bank_ref_no',
                                'ship_odr_id', 'ship_ch_odr_id', 'ship_shipment_id', 'ship_status',
                                'ship_status_code', 'ship_onboarding', 'ship_new_channel', 'affilate_id',
                                'order_date', 'language', 'payment_status', 'shiprocket_account',
                                'ship_last_updated', 'meta'
                            ];
                            foreach ($display_fields as $field) {
                                echo "<th>" . ucfirst(str_replace("_", " ", $field)) . "</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <?php
                                foreach ($display_fields as $field) {
                                    $value = isset($row[$field]) ? $row[$field] : '';
                                    echo "<td>" . ($field == 'order_date' ? date('d-m-Y', strtotime($value)) : $value) . "</td>";
                                }
                                ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } elseif (isset($_POST['search']) && empty($error)) { ?>
        <div class="alert alert-warning mt-3 text-center"><i class="fa fa-exclamation-triangle"></i> No records found.</div>
    <?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        flatpickr(".datepicker", { dateFormat: "d-m-Y" });
    });
</script>

</body>
</html>
