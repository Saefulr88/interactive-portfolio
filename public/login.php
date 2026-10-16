<?php
// simple static login page
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login Kasir</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="container">
    <h2>Login Kasir</h2>
    <form id="f">
      <input id="username" placeholder="Username" required><br><br>
      <input id="password" type="password" placeholder="Password" required><br><br>
      <button type="submit">Login</button>
    </form>
    <p>Default admin: admin / admin123</p>
  </div>
<script>
document.getElementById('f').addEventListener('submit', async (e) => {
  e.preventDefault();
  const username = document.getElementById('username').value;
  const password = document.getElementById('password').value;
  const r = await fetch('../api/login.php', { 
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({username,password})
  });
  const data = await r.json();
  if (r.ok) {
    window.location = 'index.php';
  } else {
    alert(data.error || 'Login gagal');
  }
});
</script>
</body>
</html>
