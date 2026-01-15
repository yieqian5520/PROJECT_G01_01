<?php
session_start();
include_once __DIR__ . "/includes/header.php";
include_once __DIR__ . "/dbcon.php";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect input values safely
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($message)) {
        $_SESSION['contact_error'] = "Please fill in all required fields.";
    } else {
        // Prepare SQL statement
        $stmt = mysqli_prepare(
            $con,
            "INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            // Prepare failed — show SQL error for debugging
            $_SESSION['contact_error'] = "Database error: " . mysqli_error($con);
        } else {
            // Bind parameters and execute
            mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $phone, $subject, $message);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['contact_success'] = "Thank you! Your message has been sent successfully.";
            } else {
                $_SESSION['contact_error'] = "Failed to send message: " . mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        }
    }

    // Redirect to avoid form resubmission
    header("Location: contact.php");
    exit;
}
?>

<!-- HERO BANNER -->
<section class="contact-hero">
  <div class="contact-hero__overlay">
    <div class="contact-hero__content">
      <p class="contact-hero__subtitle">We're here to hear from you</p>
      <h1 class="contact-hero__title">CONTACT</h1>
      <p class="contact-hero__desc">Nice to hear from you</p>
    </div>
  </div>
</section>

<!-- MAIN CONTENT -->
<section class="contact-page container">
  <div class="contact-grid">

    <!-- LEFT: GET IN TOUCH -->
    <div class="contact-card">
      <h3 class="contact-card__title">GET IN TOUCH</h3>
      <p>You can leave us a message and we will get back to you as soon as possible.</p>

      <?php if (!empty($_SESSION['contact_success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['contact_success']); unset($_SESSION['contact_success']); ?></div>
      <?php elseif (!empty($_SESSION['contact_error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['contact_error']); unset($_SESSION['contact_error']); ?></div>
      <?php endif; ?>

      <form class="contact-form" method="POST" autocomplete="on">
        <div class="contact-field">
          <label for="name">Name</label>
          <input id="name" type="text" name="name" placeholder="Your Name" required>
        </div>

        <div class="contact-field">
          <label for="email">Email</label>
          <input id="email" type="email" name="email" placeholder="Your Email" required>
        </div>

        <div class="contact-field">
          <label for="phone">Phone</label>
          <input id="phone" type="tel" name="phone" placeholder="Your Phone" required>
        </div>

        <div class="contact-field">
          <label for="subject">Subject</label>
          <input id="subject" type="text" name="subject" placeholder="Subject">
        </div>

        <div class="contact-field">
          <label for="message">Message</label>
          <textarea id="message" name="message" placeholder="Your Message..." rows="6" required></textarea>
        </div>

        <button type="submit" class="contact-btn">SEND MESSAGE</button>
      </form>
    </div>

    <!-- RIGHT: FIND US -->
    <div class="contact-card">
      <h3 class="contact-card__title">FIND US</h3>

      <div class="contact-info">
        <p>Address: Back Portion, 180, Jalan Tun H S Lee, Kuala Lumpur City Centre, 50000 Kuala Lumpur</p>
        <p>Phone: 012-253 0132</p>
        <p>Email: contact@puckscoffee.com</p>
      </div>

      <div class="map-box">
        <iframe
          title="Pucks Coffee Kuala Lumpur"
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3975.460327384316!2d101.6949252!3d3.1435798!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc493510da19bb%3A0x8cf52a0e3d40d8d4!2sPucks%20Coffee!5e0!3m2!1sen!2smy!4v1700000000000!5m2!1sen!2smy"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
    </div>

  </div>
</section>

<?php
include_once __DIR__ . "/includes/footer.php";
?>
