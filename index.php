<?php
session_start();
$message = "";

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        // Connect to SQLite database
        $db = new PDO('sqlite:database.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare statement to check user
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email AND password = :password");
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $password); // In production, store hashed passwords!
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Valid login
            $_SESSION['user'] = $user;
            header("Location: generate.html");
            exit;
        } else {
            $message = "Invalid credentials";
        }
    } catch (Exception $e) {
        $message = "Database error";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{ box-sizing:border-box; margin:0; padding:0; font-family:Inter,Segoe UI,sans-serif; }
body{ height:100vh; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#141e30,#243b55); padding:20px; }
.login-container{ width:100%; max-width:400px; padding:50px 40px; border-radius:18px; background:rgba(255,255,255,0.08); backdrop-filter:blur(20px); box-shadow:0 20px 60px rgba(0,0,0,0.6); color:white; transition:0.3s; }
h2{ text-align:center; margin-bottom:40px; font-weight:500; letter-spacing:1px; }
.form-group{ position:relative; margin-bottom:28px; }
input{ width:100%; height:50px; padding:14px 40px 14px 14px; border-radius:10px; border:1px solid rgba(255,255,255,0.2); background:transparent; color:white; font-size:16px; outline:none; transition:all .2s; }
input::placeholder{ color:rgba(255,255,255,0.6); }
input:focus{ border-color:#4facfe; box-shadow:0 0 0 2px rgba(79,172,254,0.25); }
.toggle-password{ position:absolute; right:8px; top:50%; transform:translateY(-50%); width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:none; background:transparent; color:white; font-size:16px; cursor:pointer; opacity:.8; transition:none; }
.toggle-password:hover{ opacity:1; }
button{ width:100%; height:48px; border:none; border-radius:10px; font-size:16px; font-weight:600; cursor:pointer; color:white; background:linear-gradient(135deg,#4facfe,#00f2fe); transition:.2s; }
button:not(.toggle-password):hover{ transform:translateY(-1px); box-shadow:0 10px 25px rgba(0,0,0,0.3); }
#errorMsg{ margin-top:14px; text-align:center; color:#ff6b6b; font-size:14px; }
/* Responsive */
@media(max-width:768px){ .login-container{ padding:40px 30px; } input,button{ height:45px; font-size:15px; } }
@media(max-width:480px){ .login-container{ padding:30px 20px; } input,button{ height:42px; font-size:14px; } .toggle-password{ font-size:16px; } }
</style>
</head>
<body>

<div class="login-container">
  <h2>Sign In</h2>
  <form method="POST">
      <div class="form-group">
        <input type="email" name="email" placeholder="Email Address" required>
      </div>
      <div class="form-group">
        <input type="password" name="password" id="password" placeholder="Password" required>
        <button type="button" class="toggle-password" id="togglePassword"><i class="fa-solid fa-eye"></i></button>
      </div>
      <button type="submit">Login</button>
  </form>
  <div id="errorMsg"><?php echo $message; ?></div>
</div>

<script>
// Toggle password visibility
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');
togglePassword.addEventListener('click', ()=>{
  const icon = togglePassword.querySelector('i');
  if(passwordInput.type==='password'){
    passwordInput.type='text';
    icon.classList.replace('fa-eye','fa-eye-slash');
  } else {
    passwordInput.type='password';
    icon.classList.replace('fa-eye-slash','fa-eye');
  }
});
</script>

</body>
</html>
