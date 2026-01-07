<?php
include("../include/config.php");
include("sidebar.php");
?>

<h2 class="mb-3"><i class="fa fa-search text-success me-2"></i> Order Search</h2>

<div class="card p-3 shadow-sm">
  <h5 class="mb-3">🔍 Search Orders</h5>
  <form id="searchForm" class="row g-3">
    <div class="col-md-3">
      <input type="text" name="order_id" class="form-control" placeholder="Order ID">
    </div>
    <div class="col-md-3">
      <input type="text" name="mobno" class="form-control" placeholder="Mobile Number">
    </div>
    <div class="col-md-3">
      <input type="text" name="fname" class="form-control" placeholder="Customer Name">
    </div>
    <div class="col-md-3">
      <select name="payment_status" class="form-select">
        <option value="">All Payment Status</option>
        <option value="Success">Success</option>
        <option value="Pending">Pending</option>
        <option value="Failed">Failed</option>
      </select>
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-success w-100"><i class="fa fa-search"></i> Search</button>
    </div>
  </form>
</div>

<!-- Results Table -->
<div class="card p-3 mt-4 shadow-sm">
  <h5 class="mb-3"><i class="fa fa-list text-success me-2"></i> Search Results</h5>
  <div id="resultsTable" class="table-responsive text-center text-muted">
    <p>🔍 Please enter your search criteria above and click <b>Search</b>.</p>
  </div>
</div>

<!-- Modal for Order Details -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="fa fa-box"></i> Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="orderDetailsBody" style="max-height:70vh; overflow:auto;"></div>
    </div>
  </div>
</div>

</div>
</body>

<!-- Bootstrap & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){
  $("#searchForm").on("submit", function(e){
    e.preventDefault();
    $("#resultsTable").html("<div class='p-3 text-center text-muted'><i class='fa fa-spinner fa-spin'></i> Loading orders...</div>");
    $.ajax({
      url: "search_orders_action.php",
      type: "POST",
      data: $(this).serialize(),
      success: function(res){
        $("#resultsTable").html(res);
      },
      error: function(){
        $("#resultsTable").html("<div class='alert alert-danger'>Error loading data.</div>");
      }
    });
  });
});

// Fetch and show order details in modal
function viewOrderDetails(id){
  $.ajax({
    url: "fetch_order_details.php",
    type: "POST",
    data: { id: id },
    success: function(res){
      $("#orderDetailsBody").html(res);
      new bootstrap.Modal(document.getElementById('orderModal')).show();
    },
    error: function(){
      alert("Unable to fetch details");
    }
  });
}
</script>
</html>
