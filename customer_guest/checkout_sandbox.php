<?php
session_start();
include_once "dbcon.php";

if (!isset($_SESSION['auth_user']['id'])) {
    echo "<h3 style='padding:40px;text-align:center;'>Please login first</h3>";
    exit;
}

$user_id = (int)$_SESSION['auth_user']['id'];
$user_email = $_SESSION['auth_user']['email'] ?? '';

if (!isset($_GET['order_id']) && !isset($_POST['order_id'])) {
    echo "<h3 style='padding:40px;text-align:center;'>Invalid order request</h3>";
    exit;
}

$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : (int)$_GET['order_id'];

$order_q = mysqli_query($con, "
    SELECT id, order_code, user_id, total, payment_status, created_at
    FROM orders
    WHERE id = $order_id
    LIMIT 1
");

if (!$order_q || mysqli_num_rows($order_q) == 0) {
    echo "<h3 style='padding:40px;text-align:center;'>Order not found</h3>";
    exit;
}

$order = mysqli_fetch_assoc($order_q);

if ((int)$order['user_id'] !== $user_id) {
    echo "<h3 style='padding:40px;text-align:center;'>Unauthorized access</h3>";
    exit;
}

if (($order['payment_status'] ?? '') === 'PAID') {
    header("Location: order_status.php?order=" . urlencode($order['order_code']));
    exit;
}

$items_q = mysqli_query($con, "
    SELECT menu_name, quantity, price, temp, milk, syrup, addons, order_type
    FROM order_items
    WHERE order_id = $order_id
");

$order_items = [];
if ($items_q && mysqli_num_rows($items_q) > 0) {
    while ($row = mysqli_fetch_assoc($items_q)) {
        $order_items[] = $row;
    }
}

if (empty($order_items)) {
    echo "<h3 style='padding:40px;text-align:center;'>No items found for this order</h3>";
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $card_number = trim($_POST['card_number'] ?? '');
    $expiry = trim($_POST['expiry'] ?? '');
    $cvc = trim($_POST['cvc'] ?? '');
    $cardholder_name = trim($_POST['cardholder_name'] ?? '');
    $country = trim($_POST['country'] ?? '');

    $card_digits = preg_replace('/\D/', '', $card_number);
    $cvc_digits = preg_replace('/\D/', '', $cvc);

    if (
        $email === '' ||
        $card_number === '' ||
        $expiry === '' ||
        $cvc === '' ||
        $cardholder_name === '' ||
        $country === ''
    ) {
        $error = "Please fill in all payment fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($card_digits) < 16) {
        $error = "Card number must contain at least 16 digits.";
    } elseif (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry)) {
    $error = "Expiry date must be in MM/YY format.";
} else {
    [$expMonthRaw, $expYearRaw] = explode('/', $expiry);

    $expMonthInt = (int)$expMonthRaw;
    $expYearInt = 2000 + (int)$expYearRaw;

    $currentMonth = (int)date('m');
    $currentYear = (int)date('Y');

    if ($expYearInt < $currentYear || ($expYearInt === $currentYear && $expMonthInt < $currentMonth)) {
        $error = "Card expiry date is invalid or already expired.";
        } elseif (strlen($cvc_digits) < 3 || strlen($cvc_digits) > 4) {
            $error = "CVC must be 3 or 4 digits.";
        } elseif (mb_strlen($cardholder_name) < 3) {
            $error = "Please enter a valid cardholder name.";
        } else {
            $payment_method = 'Credit Card';

            $update = mysqli_query($con, "
                UPDATE orders
                SET payment_status = 'PAID',
                    payment_method = '$payment_method'
                WHERE id = $order_id
            ");

            if ($update) {
                $_SESSION['payment_success'] = 'Payment successful.';
                header("Location: order_status.php?order=" . urlencode($order['order_code']));
                exit;
            } else {
                $error = "Failed to update payment.";
            }
        }
    }
}

$order_date = !empty($order['created_at']) ? date("d/m/Y", strtotime($order['created_at'])) : 'N/A';
$order_time = !empty($order['created_at']) ? date("g:i A", strtotime($order['created_at'])) : '';
$currentYear = (int)date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #ffffff;
            color: #1f1f1f;
        }

        .checkout-page {
            min-height: 100vh;
            display: flex;
            background: #fff;
        }

        .left-panel,
        .right-panel {
            width: 50%;
            min-height: 100vh;
            padding: 52px 58px;
        }

        .left-panel {
            background: #f5f5f7;
            border-right: 1px solid #e8e8ea;
        }

        .right-panel {
            background: #ffffff;
        }

        .top-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 42px;
        }

        .back-link {
            text-decoration: none;
            color: #6b6b6b;
            font-size: 26px;
            line-height: 1;
        }

        .brand-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #2f2f2f;
            font-size: 15px;
        }

        .sandbox-tag {
            background: #0d2e4f;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 4px;
            min-width: 18px;
            min-height: 12px;
        }

        .paying-text {
            color: #666;
            font-size: 17px;
            margin-bottom: 10px;
        }

        .amount {
            font-size: 58px;
            font-weight: 700;
            margin-bottom: 18px;
            letter-spacing: -1px;
        }

        .date-box {
            color: #6d6d6d;
            font-size: 14px;
            margin-bottom: 36px;
            line-height: 1.6;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding: 16px 0;
            font-size: 17px;
            border-bottom: 1px solid #ececf0;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-name {
            color: #232323;
            line-height: 1.4;
        }

        .item-price {
            color: #232323;
            white-space: nowrap;
            font-weight: 600;
        }

        .item-meta {
            display: block;
            margin-top: 6px;
            color: #7c7c7c;
            font-size: 13px;
            line-height: 1.6;
        }

        .section-title {
            font-size: 38px;
            font-weight: 700;
            margin-bottom: 38px;
            color: #1f1f1f;
        }

        .label {
            font-size: 15px;
            font-weight: 700;
            color: #303030;
            margin-bottom: 12px;
        }

        .field-group {
            margin-bottom: 26px;
        }

        .text-input,
        .country-select {
            width: 100%;
            border: 1px solid #e7e7ea;
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 17px;
            outline: none;
            background: #fff;
        }

        .text-input:focus,
        .country-select:focus {
            border-color: #0a77d5;
        }

        .split-row {
            display: flex;
            gap: 12px;
        }

        .split-row > * {
            flex: 1;
        }

        .save-section {
            margin: 36px 0 24px;
        }

        .save-title {
            font-size: 16px;
            font-weight: 700;
            color: #2f2f2f;
            margin-bottom: 6px;
        }

        .save-desc {
            color: #6f6f6f;
            font-size: 14px;
            line-height: 1.5;
        }

        .pay-button {
            width: 100%;
            height: 66px;
            border: none;
            background: #0a77d5;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
        }

        .pay-button:hover {
            background: #086abf;
        }

        .footer-line {
            text-align: center;
            color: #777;
            font-size: 14px;
            margin-top: 26px;
        }

        .footer-line span {
            margin: 0 10px;
        }

        .error-box {
            background: #ffe9e9;
            color: #b10000;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .mini-label {
    display: block;
    font-size: 14px;
    font-weight: 700;
    color: #303030;
    margin-bottom: 8px;
}

.mini-label span {
    color: #9a9a9a;
    font-weight: 600;
    margin-left: 4px;
}

.expiry-group,
.cvc-group {
    flex: 1;
}

.expiry-wrap {
    position: relative;
}

.expiry-input {
    width: 100%;
    border: 1px solid #e7e7ea;
    border-radius: 0;
    padding: 14px 44px 14px 16px;
    font-size: 17px;
    outline: none;
    background: #fff;
    height: 54px;
}

.expiry-input:focus {
    border-color: #0a77d5;
}

.expiry-wrap.invalid .expiry-input {
    border-color: #d93025;
}

.expiry-error-icon {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #d93025;
    font-size: 20px;
    display: none;
    pointer-events: none;
}

.expiry-wrap.invalid .expiry-error-icon {
    display: block;
}

.field-error {
    min-height: 18px;
    margin-top: 6px;
    font-size: 12px;
    color: #d93025;
}

        @media (max-width: 980px) {
            .checkout-page {
                flex-direction: column;
            }

            .left-panel,
            .right-panel {
                width: 100%;
                min-height: auto;
                padding: 28px 22px;
            }

            .amount {
                font-size: 42px;
            }

            .section-title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="checkout-page">
        <div class="left-panel">
            <div class="top-row">
                <a href="order_status.php?order=<?= urlencode($order['order_code']) ?>" class="back-link">&#8592;</a>
                <div class="brand-wrap">
                    <span>Pucks Coffee</span>
                    <span class="sandbox-tag"></span>
                </div>
            </div>

            <div class="paying-text">Pay Pucks Coffee</div>
            <div class="amount">MYR <?= number_format((float)$order['total'], 2) ?></div>

            <div class="date-box">
                <div>Order Code: <?= htmlspecialchars($order['order_code']) ?></div>
                <div>Date: <?= htmlspecialchars($order_date) ?><?= $order_time ? ' | ' . htmlspecialchars($order_time) : '' ?></div>
            </div>

            <?php foreach ($order_items as $item): ?>
                <div class="item-row">
                    <div class="item-name">
                        <?= htmlspecialchars($item['menu_name']) ?>
                        <span class="item-meta">
                            Qty: <?= (int)$item['quantity'] ?><br>
                            Order Type: <?= htmlspecialchars($item['order_type'] ?? 'Dine In') ?>
                            <?php if (!empty($item['temp'])): ?> | <?= htmlspecialchars($item['temp']) ?><?php endif; ?>
                            <?php if (!empty($item['milk'])): ?> | <?= htmlspecialchars($item['milk']) ?><?php endif; ?>
                            <?php if (!empty($item['syrup'])): ?> | <?= htmlspecialchars($item['syrup']) ?><?php endif; ?>
                            <?php if (!empty($item['addons'])): ?> | <?= htmlspecialchars($item['addons']) ?><?php endif; ?>
                        </span>
                    </div>
                    <div class="item-price">
                        MYR <?= number_format((float)$item['price'] * (int)$item['quantity'], 2) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="right-panel">
            <?php if (!empty($error)): ?>
                <div class="error-box"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="section-title">Contact information</div>

            <form method="POST" action="" id="paymentForm" novalidate>
                <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">

                <div class="field-group">
                    <div class="label">Email</div>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="text-input"
                        placeholder="email@example.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? $user_email) ?>"
                        required
                    >
                </div>

                <div class="field-group">
                    <div class="label">Card information</div>
                    <input
                        type="text"
                        name="card_number"
                        id="card_number"
                        class="text-input"
                        placeholder="1234 1234 1234 1234"
                        maxlength="19"
                        inputmode="numeric"
                        value="<?= htmlspecialchars($_POST['card_number'] ?? '') ?>"
                        required
                    >

                    <div class="split-row" style="margin-top:12px;">
    <div class="expiry-group">
        <label for="expiry" class="mini-label">Expiration Date <span>(MM/YY)</span></label>
        <div class="expiry-wrap" id="expiryWrap">
            <input
                type="text"
                name="expiry"
                id="expiry"
                class="expiry-input"
                placeholder="MM/YY"
                maxlength="5"
                inputmode="numeric"
                value="<?= htmlspecialchars($_POST['expiry'] ?? '') ?>"
                required
            >
            <span class="expiry-error-icon" id="expiryErrorIcon">⚠</span>
        </div>
        <div class="field-error" id="expiryErrorText"></div>
    </div>

    <div class="cvc-group">
        <label for="cvc" class="mini-label">CVC</label>
        <input
            type="text"
            name="cvc"
            id="cvc"
            class="text-input"
            placeholder="CVC"
            maxlength="4"
            inputmode="numeric"
            value="<?= htmlspecialchars($_POST['cvc'] ?? '') ?>"
            required
        >
    </div>
</div>
                </div>

                <div class="field-group">
                    <div class="label">Cardholder name</div>
                    <input
                        type="text"
                        name="cardholder_name"
                        id="cardholder_name"
                        class="text-input"
                        placeholder="Full name on card"
                        value="<?= htmlspecialchars($_POST['cardholder_name'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="field-group">
                    <div class="label">Country or region</div>
                    <select name="country" id="country" class="country-select" required>
                        <option value="">Select country</option>
                        <option value="Malaysia" <?= (($_POST['country'] ?? '') === 'Malaysia') ? 'selected' : '' ?>>Malaysia</option>
                        <option value="Singapore" <?= (($_POST['country'] ?? '') === 'Singapore') ? 'selected' : '' ?>>Singapore</option>
                        <option value="Indonesia" <?= (($_POST['country'] ?? '') === 'Indonesia') ? 'selected' : '' ?>>Indonesia</option>
                        <option value="Thailand" <?= (($_POST['country'] ?? '') === 'Thailand') ? 'selected' : '' ?>>Thailand</option>
                    </select>
                </div>

                <div class="save-section">
                    <div class="save-title">Save my information for faster checkout</div>
                    <div class="save-desc">
                        Pay securely at Pucks Coffee and everywhere Link is accepted.
                    </div>
                </div>

                <button type="submit" class="pay-button">Pay</button>
            </form>

            <div class="footer-line">
                Powered by stripe <span>|</span> Terms <span>|</span> Privacy
            </div>
        </div>
    </div>

    <script>
    const cardInput = document.getElementById('card_number');
    const cvcInput = document.getElementById('cvc');
    const expiryInput = document.getElementById('expiry');
    const expiryWrap = document.getElementById('expiryWrap');
    const expiryErrorText = document.getElementById('expiryErrorText');
    const paymentForm = document.getElementById('paymentForm');

    cardInput.addEventListener('input', function () {
        let digits = this.value.replace(/\D/g, '').slice(0, 16);
        let groups = digits.match(/.{1,4}/g);
        this.value = groups ? groups.join(' ') : '';
    });

    cvcInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 4);
    });

    expiryInput.addEventListener('input', function () {
        let value = this.value.replace(/\D/g, '').slice(0, 4);

        if (value.length >= 3) {
            this.value = value.slice(0, 2) + '/' + value.slice(2);
        } else {
            this.value = value;
        }

        validateExpiryField(false);
    });

    function validateExpiryField(showMessage = true) {
        const value = expiryInput.value.trim();
        const pattern = /^(0[1-9]|1[0-2])\/\d{2}$/;

        if (!pattern.test(value)) {
            expiryWrap.classList.add('invalid');
            expiryErrorText.textContent = showMessage ? 'Please enter a valid expiry date.' : '';
            return false;
        }

        const parts = value.split('/');
        const expMonth = parseInt(parts[0], 10);
        const expYear = 2000 + parseInt(parts[1], 10);

        const now = new Date();
        const currentMonth = now.getMonth() + 1;
        const currentYear = now.getFullYear();

        if (expYear < currentYear || (expYear === currentYear && expMonth < currentMonth)) {
            expiryWrap.classList.add('invalid');
            expiryErrorText.textContent = showMessage ? 'This card has expired.' : '';
            return false;
        }

        expiryWrap.classList.remove('invalid');
        expiryErrorText.textContent = '';
        return true;
    }

    paymentForm.addEventListener('submit', function (e) {
        const cardDigits = cardInput.value.replace(/\D/g, '');
        const cvc = cvcInput.value.trim().replace(/\D/g, '');

        if (cardDigits.length < 16) {
            alert('Card number must contain at least 16 digits.');
            cardInput.focus();
            e.preventDefault();
            return;
        }

        if (!validateExpiryField(true)) {
            expiryInput.focus();
            e.preventDefault();
            return;
        }

        if (cvc.length < 3 || cvc.length > 4) {
            alert('CVC must be 3 or 4 digits.');
            cvcInput.focus();
            e.preventDefault();
            return;
        }
    });
</script>
</body>
</html>