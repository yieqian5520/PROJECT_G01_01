<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/forgot.css">
</head>

<body>
  <div class="container">
    <div class="form-box">
      <form method="post" action="../pages/send-password-reset.php">
        <h2>Forgot Password</h2>
        <input type="email" id="email" placeholder="Enter your email address" name="email" required>
        <button type="submit" name="send">Send</button>
      </form>
    </div>
  </div>
  
</body>
</html>