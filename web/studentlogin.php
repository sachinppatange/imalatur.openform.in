<?php
session_start();
// print_r($_SESSION);

?>
<script>
    // Retrieve the PHP session variable and embed it into JavaScript
    <?php if (isset($_SESSION['message'])): ?>
        var message = "<?php echo $_SESSION['message']; ?>";
        alert(message);
    <?php endif; ?>
</script>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STSVT</title>
    <link rel="icon" href="../assets/img/logo.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script>
        function showPasswordInput() {
            var passwordField = document.getElementById("passwordField");
            passwordField.style.display = "block";
        }
    </script>
    <style>
        .size {
            font-size: 12px;
        }

        .rule li {
            font-size: 13px;
            ;
        }
    </style>
</head>


<body style="background-color:#87CEFA">
    <div class="container">
        <div class="card p-4 m-3">
            <div class="row">
                <div class="col-md-6">
                    
                </div>
                <div class="col-md-6">
                    <h4>Login</h4>
                    <form id="loginForm" action="#" method="post">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <input type="text" class="form-control" id="mobile" name="mobile"
                                    placeholder="Enter Mobile Number" maxlength="10" minlength="10"
                                    onkeypress="if (isNaN(this.value + String.fromCharCode(event.keyCode))) return false;"
                                    required>
                                <span class="size">(What's App Number)</span>
                            </div>
                            <div class="col-md-12 mb-3" id="passwordField" style="display: none;">
                                <input type="hidden" id="msg" value="">
                                <input type="text" class="form-control" id="password" name="" placeholder="Enter OTP"
                                    required>
                            </div>
                        </div>
                        <center>
                            <button type="button" class="btn btn-primary mb-2" id="submitButton" onclick="sendOTP()"
                                name="submit">Get
                                OTP</button>
                        </center>
                        <span><center>You dont have an account ? Please <a href="index.php">Sign Up</a></center></span>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <script>
    let otpSent = '';
    function sendOTP() {
        const mobile = document.getElementById('mobile').value;

        if (mobile.length === 10) {
            fetch('../controller/otptest.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `mobile=${mobile}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    otpSent = data.otp; // Store the OTP sent
                    document.getElementById('msg').value = otpSent; // Set the OTP in the hidden field
                    document.getElementById('passwordField').style.display = 'block';
                    document.getElementById('submitButton').innerText = 'Login';
                    document.getElementById('submitButton').setAttribute('onclick', 'validateOTP()');
                    alert('OTP sent successfully to Your Registered WhatsApp Number. Please enter your OTP.');
                } else {
                    alert(data.message || 'Failed to send OTP. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        } else {
            alert('Please enter a valid 10-digit mobile number.');
        }
    }
</script>

   
    <script>
        function validateOTP() {
            const enteredOTP = document.getElementById('password').value;


            if (enteredOTP == otpSent) {
                var mobile = document.getElementById('mobile').value;

                window.location.href = '../controller/ctrlStudLogin.php?mobile='+ mobile; 
            } else {
                alert('Incorrect OTP. Please try again.');
            }
        }
    </script>

    <?php unset($_SESSION['message']); ?>
</body>

</html>