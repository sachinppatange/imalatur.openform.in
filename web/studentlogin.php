<?php
session_start();
?>
<script>
    <?php if (isset($_SESSION['message'])): ?>
        var message = "<?php echo addslashes($_SESSION['message']); ?>";
        alert(message);
    <?php endif; ?>
</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="../assets/img/logo.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <style>
        .size { font-size: 12px; }
        .rule li { font-size: 13px; }
    </style>
</head>
<body style="background-color:#87CEFA">
    <div class="container">
        <div class="card p-4 m-3">
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <h4>Login</h4>
                    <form id="loginForm" action="#" method="post" onsubmit="return false;">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <input type="text" class="form-control" id="mobile" name="mobile"
                                    placeholder="Enter Mobile Number" maxlength="10" minlength="10"
                                    onkeypress="if (isNaN(this.value + String.fromCharCode(event.keyCode))) return false;">
                                <span class="size">(WhatsApp Number)</span>
                            </div>

                            <!-- Hidden: holds user id if mobile exists in user table -->
                            <input type="hidden" id="user_id" name="user_id" value="">

                            <div class="col-md-12 mb-3" id="passwordField" style="display: none;">
                                <input type="hidden" id="msg" value="">
                                <input type="text" class="form-control" id="password" name="otp" placeholder="Enter OTP">
                            </div>

                            <div class="col-md-12 mb-2" id="userInfo" style="display:none;">
                                <div class="alert alert-info" id="userInfoText"></div>
                            </div>

                        </div>
                        <center>
                            <button type="button" class="btn btn-primary mb-2" id="submitButton" onclick="checkUserAndSendOTP()">Get OTP</button>
                        </center>
                        <span><center>You don't have an account? Please <a href="index.php">Sign Up</a></center></span>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
// store OTP (if returned by otptest) for client-side validation (if needed)
let otpSent = '';

function checkUserAndSendOTP() {
    const mobile = document.getElementById('mobile').value.trim();
    if (mobile.length !== 10 || !/^[0-9]{10}$/.test(mobile)) {
        alert('Please enter a valid 10-digit mobile number.');
        return;
    }

    // First: check user exists in `user` table
    fetch('../controller/checkUser.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'mobile=' + encodeURIComponent(mobile)
    })
    .then(resp => resp.json())
    .then(data => {
        if (data.success) {
            // user exists -> show info and send OTP
            document.getElementById('userInfo').style.display = 'block';
            const name = data.user.participant_name || (data.user.surname ? (data.user.surname + ' ' + (data.user.firstname||'')) : '');
            const email = data.user.email || '';
            document.getElementById('userInfoText').innerText = 'Account found: ' + (name || '(No name)') + (email ? ' | ' + email : '');

            // store user id
            document.getElementById('user_id').value = data.user.id || '';

            // Now call otptest to send OTP
            sendOTP(mobile);
        } else {
            // user not found - ask to signup
            document.getElementById('userInfo').style.display = 'block';
            document.getElementById('userInfoText').innerText = data.message || 'Account not found. Please Sign Up.';
            document.getElementById('user_id').value = '';
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error checking user. Please try again.');
    });
}

function sendOTP(mobile) {
    fetch('../controller/otptest.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'mobile=' + encodeURIComponent(mobile)
    })
    .then(resp => resp.json())
    .then(data => {
        if (data.success) {
            otpSent = data.otp || ''; // if otptest returns otp (for testing)
            document.getElementById('msg').value = otpSent;
            document.getElementById('passwordField').style.display = 'block';
            document.getElementById('submitButton').innerText = 'Login';
            document.getElementById('submitButton').setAttribute('onclick', 'validateOTP()');
            alert('OTP sent successfully to your WhatsApp number. Please enter OTP.');
        } else {
            alert(data.message || 'Failed to send OTP. Please try again.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error sending OTP. Please try again.');
    });
}

function validateOTP() {
    const enteredOTP = document.getElementById('password').value.trim();
    // client-side check only if OTP returned earlier; otherwise skip and submit to server
    if (otpSent !== '' && enteredOTP !== otpSent) {
        alert('Incorrect OTP. Please try again.');
        return;
    }

    const mobile = document.getElementById('mobile').value.trim();
    const userId = document.getElementById('user_id').value;

    // Redirect to server-side login handler (existing)
    // Pass mobile and user id (if available) via GET
    let url = '../controller/ctrlStudLogin.php?mobile=' + encodeURIComponent(mobile);
    if (userId) url += '&userid=' + encodeURIComponent(userId);
    window.location.href = url;
}
</script>

<?php unset($_SESSION['message']); ?>
</body>
</html>