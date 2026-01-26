<?php
session_start();
include_once __DIR__ . "/dbcon.php";

// Protect page
if (!isset($_SESSION['authenticated'])) {
    $_SESSION['status'] = "Please login to leave feedback.";
    header("Location: login.php");
    exit();
}

// Handle feedback submit
if (isset($_POST['submit_feedback'])) {

    $user_id = $_SESSION['auth_user']['id'];
    $rating  = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5) {
        $_SESSION['status'] = "Invalid rating.";
    } elseif (empty($comment)) {
        $_SESSION['status'] = "Comment cannot be empty.";
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = $con->prepare("INSERT INTO feedback_message (user_id, rating, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $rating, $comment);
        if ($stmt->execute()) {
            $_SESSION['status'] = "Thank you for your feedback ❤️";
        } else {
            $_SESSION['status'] = "Failed to submit feedback. Please try again.";
        }
        $stmt->close();
    }

    header("Location: feedback.php");
    exit();
}

include_once __DIR__ . "/includes/header.php";
?>

<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <?php if(isset($_SESSION['status'])): ?>
                    <div class="alert alert-info">
                        <?= $_SESSION['status']; unset($_SESSION['status']); ?>
                    </div>
                <?php endif; ?>

                <!-- FEEDBACK FORM -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Leave Your Feedback</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">

                            <div class="mb-3">
                                <label>Rating</label>
                                <select name="rating" class="form-control" required>
                                    <option value="">-- Select Rating --</option>
                                    <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                                    <option value="4">⭐⭐⭐⭐ (Good)</option>
                                    <option value="3">⭐⭐⭐ (Average)</option>
                                    <option value="2">⭐⭐ (Poor)</option>
                                    <option value="1">⭐ (Very Bad)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Comment</label>
                                <textarea name="comment" class="form-control" rows="4" required></textarea>
                            </div>

                            <button type="submit" name="submit_feedback" class="btn btn-warning text-dark">
                                Submit Feedback
                            </button>

                        </form>
                    </div>
                </div>

                <!-- SHOW FEEDBACK -->
                <?php
                $query = "
                    SELECT f.*, u.name, u.profile_image
                    FROM feedback_message f
                    JOIN users u ON f.user_id = u.id
                    ORDER BY f.created_at DESC
                ";
                $result = mysqli_query($con, $query);

                if (!$result) {
                    echo "<div class='alert alert-danger'>Error fetching feedback: " . mysqli_error($con) . "</div>";
                }

                while ($row = mysqli_fetch_assoc($result)):
                ?>
                    <div class="card mb-3">
                        <div class="card-body d-flex gap-3">

                            <img src="<?= !empty($row['profile_image']) ? htmlspecialchars($row['profile_image']) : 'https://via.placeholder.com/60' ?>"
                                 width="60" height="60"
                                 class="rounded-circle"
                                 style="object-fit:cover;">

                            <div>
                                <h6 class="mb-1"><?= htmlspecialchars($row['name']) ?></h6>

                                <!-- STARS -->
                                <div class="mb-1">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <?= $i <= $row['rating'] ? '⭐' : '☆' ?>
                                    <?php endfor; ?>
                                </div>

                                <p class="mb-1"><?= htmlspecialchars($row['comment']) ?></p>

                                <small class="text-muted">
                                    <?= date("d M Y, h:i A", strtotime($row['created_at'])) ?>
                                </small>
                            </div>

                        </div>
                    </div>
                <?php endwhile; ?>

            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . "/includes/footer.php"; ?>
