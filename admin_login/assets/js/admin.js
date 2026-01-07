// admin.js
function logAction(action){
  try{
    fetch('log_action.php', {
      method:'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: 'action='+encodeURIComponent(action)
    }).catch(e=>console.warn('log failed',e));
  }catch(e){console.warn(e)}
}

// simple auto-logout warning: this is only UX; server enforces logout
let lastActivity = Date.now();
const TIMEOUT = 15 * 60 * 1000; // 15 min
const WARNING_BEFORE = 60 * 1000; // 1 min

['click','mousemove','keydown','scroll','touchstart'].forEach(evt=>{
  document.addEventListener(evt, ()=> lastActivity = Date.now());
});

setInterval(()=>{
  const idle = Date.now() - lastActivity;
  if (idle > (TIMEOUT - WARNING_BEFORE) && idle < TIMEOUT) {
    if (!document.getElementById('idle-warning')) {
      let w = document.createElement('div'); w.id='idle-warning';
      w.style.position='fixed';w.style.right='20px';w.style.bottom='80px'; w.style.background='#fff';w.style.padding='12px'; w.style.borderRadius='8px'; w.style.boxShadow='0 6px 18px rgba(0,0,0,0.12)';
      w.innerHTML = '<b>Idle</b> You will be logged out in 60 seconds due to inactivity. <button onclick="stayLoggedIn()">Stay</button>';
      document.body.appendChild(w);
    }
  }
  if (idle >= TIMEOUT) {
    // forced redirect to logout page
    window.location = 'logout.php';
  }
}, 20000);

function stayLoggedIn(){
  lastActivity = Date.now();
  let w = document.getElementById('idle-warning'); if (w) w.remove();
  logAction('Stayed Logged In via warning');
}
