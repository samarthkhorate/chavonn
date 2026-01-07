function logAction(action) {
  fetch('log_action.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=' + encodeURIComponent(action)
  });
}
