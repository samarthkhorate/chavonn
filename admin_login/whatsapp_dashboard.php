<?php
// whatsapp_recharge_no_store.php
// Place in admin folder. Requires: sidebar.php, ../include/config.php

include("sidebar.php");
include("../include/config.php");

// show errors while debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Chatbot link & credentials (as requested)
$chat_link = "https://agent.neotechking.com/";
$login_id = "pravarahealthcare1@gmail.com";
$login_pass = "Prasad@25";

// Wallet API
$api_url = "https://console.neotechking.com/restapi/getbalance.php?authkey=564779968434662c";
$api_response = @file_get_contents($api_url);
$wallet_data = json_decode($api_response, true);

$wallet_balance = "N/A";
$wallet_currency = "INR";
$wallet_email = "";

if ($wallet_data && isset($wallet_data['success']) && $wallet_data['success'] === true) {
    $wallet_balance = number_format((float)($wallet_data['balance'] ?? 0), 2);
    $wallet_currency = htmlspecialchars($wallet_data['currency'] ?? 'INR', ENT_QUOTES, 'UTF-8');
    $wallet_email = htmlspecialchars($wallet_data['email'] ?? '', ENT_QUOTES, 'UTF-8');
} else {
    $wallet_balance = "Error fetching";
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>WhatsApp Chatbot - Access & Recharge</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; margin:16px; color:#111; }
    .wallet-card { background: linear-gradient(90deg,#16a34a,#4ade80); color: white; border-radius:8px; padding:12px; }
    .info-box { background:#f8fafc; border-radius:8px; padding:12px; }
    .btn { padding:8px 12px; border-radius:6px; cursor:pointer; border:1px solid #ddd; display:inline-flex; gap:8px; align-items:center; text-decoration:none; color:inherit; }
    .btn-primary { background:#0d6efd; color:white; border-color:#0d6efd; }
    .btn-success { background:#16a34a; color:white; border-color:#16a34a; }
    .btn-ghost { background:transparent; color:#333; border:1px solid #ddd; }
    .modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center; }
    .modal .panel { background:white; padding:20px; border-radius:8px; width:420px; max-width:94%; box-shadow:0 10px 30px rgba(0,0,0,0.12); }
    .form-row { margin-bottom:12px; }
    input[type="number"], input[type="text"] { width:100%; padding:8px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box; }
    .small { font-size:13px; color:#666; }
    .copy-btn { background:#e6f4ea; border:1px solid #cbe9d1; padding:6px 8px; border-radius:6px; cursor:pointer; }
    .hint { font-size:13px; color:#666; margin-top:8px; }
    table.simple { width:100%; border-collapse:collapse; margin-top:12px; }
    table.simple th, table.simple td { padding:8px; border:1px solid #eee; text-align:left; }
  </style>
</head>
<body>

<div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:14px;">
  <h3 style="margin:0;"><i class="fa fa-whatsapp text-success"></i> WhatsApp Chatbot Access</h3>
  <div class="wallet-card">
    <div style="display:flex; gap:12px; align-items:center;">
      <i class="fa fa-wallet" style="font-size:20px;"></i>
      <div>
        <div style="font-size:14px; opacity:0.9;">Wallet Balance</div>
        <div style="font-weight:700; font-size:18px;"><?php echo $wallet_currency . ' ' . htmlspecialchars($wallet_balance, ENT_QUOTES, 'UTF-8'); ?></div>
        <div class="small"><?php echo $wallet_email ?: '&nbsp;'; ?></div>
      </div>
    </div>
  </div>
</div>

<div class="card" style="padding:14px; border-radius:8px; margin-bottom:12px; background:#fff; border:1px solid #eee;">
  <div style="display:flex; gap:16px; align-items:center; justify-content:space-between;">
    <div style="flex:1;">
      <div class="info-box">
        <strong>Chatbot Platform</strong><br>
        <a href="<?php echo htmlspecialchars($chat_link, ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><?php echo htmlspecialchars($chat_link, ENT_QUOTES, 'UTF-8'); ?></a>

        <div class="small" style="margin-top:8px;">
          <div style="display:flex; gap:8px; align-items:center; margin-bottom:6px;">
            <strong style="min-width:85px;">Login ID:</strong>
            <div style="flex:1;">
              <span id="showId"><?php echo htmlspecialchars($login_id, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <button class="copy-btn" data-copy-target="showId" title="Copy ID">Copy</button>
          </div>

          <div style="display:flex; gap:8px; align-items:center;">
            <strong style="min-width:85px;">Password:</strong>
            <div style="flex:1;">
              <span id="showPass"><?php echo htmlspecialchars($login_pass, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <button class="copy-btn" data-copy-target="showPass" title="Copy password">Copy</button>
          </div>
        </div>
      </div>
    </div>

    <div style="display:flex; gap:8px; align-items:center;">
      <a class="btn btn-primary" href="<?php echo htmlspecialchars($chat_link, ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><i class="fa fa-external-link-alt"></i> Open Dashboard</a>
      <button class="btn btn-success" id="openRechargeBtn"><i class="fa fa-rupee-sign"></i> Recharge Wallet</button>
    </div>
  </div>
</div>

<div class="hint">
  <strong>Note:</strong> The platform blocks embedding (iframes). Click <em>Open Dashboard</em> to open it in a new tab. Recharge opens WhatsApp with a prefilled message — you must send it manually in WhatsApp.
</div>

<!-- Modal for entering amount -->
<div id="rechargeModal" class="modal" role="dialog" aria-hidden="true">
  <div class="panel" role="document" aria-modal="true">
    <h3 style="margin-top:0; margin-bottom:6px;">Recharge Wallet</h3>
    <p class="small" style="margin-top:0; margin-bottom:12px;">Minimum amount: ₹5,000</p>

    <div class="form-row">
      <label>Username (sent in message)</label>
      <input type="text" id="modalUsername" value="<?php echo htmlspecialchars($login_id, ENT_QUOTES, 'UTF-8'); ?>" disabled>
    </div>

    <div class="form-row">
      <label>Amount (INR)</label>
      <input type="number" id="modalAmount" min="5000" step="1" placeholder="Enter amount (min 5000)">
    </div>

    <div class="form-row">
      <label>WhatsApp Number</label>
      <input type="text" id="modalWA" value="9112343121" disabled>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:10px;">
      <button class="btn btn-ghost" id="cancelBtn">Cancel</button>
      <button class="btn btn-primary" id="sendWA">Open WhatsApp</button>
    </div>

    <div id="modalError" style="color:#b91c1c; margin-top:8px; display:none;"></div>
  </div>
</div>

<!-- Optional: recent requests (non-stored) - show example of last action in client session -->
<div style="margin-top:18px;">
  <h4 style="margin-bottom:8px;">Recent Actions (this session)</h4>
  <div style="background:#fff; padding:12px; border-radius:8px;">
    <table class="simple">
      <thead><tr><th>#</th><th>Action</th><th>Details</th><th>When</th></tr></thead>
      <tbody id="recentActionsBody">
        <tr><td colspan="4" style="text-align:center; color:#666;">No actions this session.</td></tr>
      </tbody>
    </table>
  </div>
</div>

<script>
/* ---------- Copy to clipboard buttons ---------- */
document.querySelectorAll('.copy-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const targetId = btn.getAttribute('data-copy-target');
    const el = document.getElementById(targetId);
    if (!el) return;
    const text = el.textContent || el.innerText || '';
    navigator.clipboard.writeText(text.trim()).then(() => {
      btn.innerText = 'Copied';
      setTimeout(() => btn.innerHTML = 'Copy', 1400);
    }).catch(() => {
      alert('Copy failed. Please copy manually.');
    });
  });
});

/* ---------- Modal controls ---------- */
const modal = document.getElementById('rechargeModal');
const openBtn = document.getElementById('openRechargeBtn');
const cancelBtn = document.getElementById('cancelBtn');
const sendWA = document.getElementById('sendWA');
const modalAmount = document.getElementById('modalAmount');
const modalWA = document.getElementById('modalWA');
const modalError = document.getElementById('modalError');
const recentBody = document.getElementById('recentActionsBody');

openBtn.addEventListener('click', () => {
  modal.style.display = 'flex';
  modalAmount.value = '';
  modalError.style.display = 'none';
  modalAmount.focus();
});

cancelBtn.addEventListener('click', () => {
  modal.style.display = 'none';
});

// click outside to close
modal.addEventListener('click', (ev) => {
  if (ev.target === modal) modal.style.display = 'none';
});

// Send WhatsApp message (no server logging)
sendWA.addEventListener('click', () => {
  modalError.style.display = 'none';
  let amt = parseFloat(modalAmount.value || 0);
  if (isNaN(amt) || amt < 5000) {
    modalError.innerText = "Minimum amount is ₹5,000. Please enter a valid amount.";
    modalError.style.display = 'block';
    return;
  }

  const username = document.getElementById('modalUsername').value || '<?php echo htmlspecialchars($login_id, ENT_QUOTES, 'UTF-8'); ?>';
  const wa = modalWA.value.replace(/\D/g,''); // numbers only
  const message = `Please send the recharge payment link for ${username} with the amount ₹${amt.toFixed(2)}`;

  // Compose WhatsApp web/mobile URL
  const encoded = encodeURIComponent(message);
  const waWeb = `https://web.whatsapp.com/send?phone=${wa}&text=${encoded}`;
  const waMobile = `https://api.whatsapp.com/send?phone=${wa}&text=${encoded}`;

  // Open WA in new tab (prefer web on desktop)
  window.open(waWeb, '_blank');
  // Also attempt mobile URL for broader device compatibility (optional, won't harm)
  // window.open(waMobile, '_blank');

  // Close modal
  modal.style.display = 'none';

  // Add to session-only "recent actions" table (client-side only)
  addRecentAction('Recharge request opened in WhatsApp', `Amount ₹${amt.toFixed(2)} | To +${wa}`);
});

/* ---------- Recent actions (client-side) ---------- */
function addRecentAction(action, details) {
  // remove "no actions" row if present
  if (recentBody.children.length === 1 && recentBody.children[0].children.length === 1) {
    recentBody.innerHTML = '';
  }
  const row = document.createElement('tr');
  const now = new Date().toLocaleString();
  row.innerHTML = `<td style="padding:8px; border-top:1px solid #eee;">${recentBody.children.length + 1}</td>
                   <td style="padding:8px; border-top:1px solid #eee;">${escapeHtml(action)}</td>
                   <td style="padding:8px; border-top:1px solid #eee;">${escapeHtml(details)}</td>
                   <td style="padding:8px; border-top:1px solid #eee;">${escapeHtml(now)}</td>`;
  recentBody.prepend(row);
}

/* simple escaper */
function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, function(m) {
    return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m];
  });
}
</script>

</body>
</html>
