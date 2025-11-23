<?php
session_start();
include_once '../config/config.php';
include_once '../controller/ctrlgetStudDetails.php';

$details = getStudentRegById($_GET['id']);
// print_r($details);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GOVINDLAL COLLEGE</title>

    <link rel="icon" href="../assets/img/logo.jpg" type="image/x-icon">


    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
    <style>
        .card {
            padding: 40px;
            margin: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            margin-top: 30px;
            margin-left: 40px;
            margin-right: 10px;
        }

        .breadcrumb {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            list-style: none;
            background-color: #e9ecef;
            border-radius: 0.25rem;
            margin-top: 3rem;
            margin: 2rem;
            margin-right: 10px;
            margin-left: 40px;
        }

        .footer {
            margin-top: 100px;
        }

        .error {
            font-size: 12px;
            color: red;
        }

        .size {
            font-size: 8.5px;
            color: red;
        }
    </style>



</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
        </div>

        <!-- Navbar -->
        <?php include_once 'navbar.php'; ?>
        <!-- /.navbar -->

        <div class="container-fluid">
            <div class="row">
                <!-- Main Sidebar Container -->
                <div class="col-md-2">
                    <?php include_once 'sidebar.php'; ?>
                </div>
                <!-- Main Sidebar Container End -->

                <!-- Add Admin Content Start -->
                <div class="col-md-10">
                    <h4 class="mt-30 breadcrumb">Update Student</h4>
                    <div class="card">
                        <form action="../controller/ctrlStudRegistration.php" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="yearSelect" class=""><b
                                                style="color:red;">*</b>&nbsp;Surname</label>
                                        <input type="text" class="form-control" id="surname" name="surname"
                                            value="<?php echo $details['surname']; ?>" placeholder="Enter Sirname"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="acadamic_year"><b style="color:red;">*</b>&nbsp;First Namer</label>
                                        <input type="text" class="form-control" id="firstname" name="firstname"
                                            value="<?php echo $details['stud_name']; ?>" placeholder="Enter First Name"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for=""><b style="color:red;">*</b>&nbsp;Father's Name</label>
                                        <input type="text" class="form-control" id="fathersname" name="fathersname"
                                            value="<?php echo $details['fathers_name']; ?>"
                                            placeholder="Enter Father's Name" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="medium"><b style="color:red;">*</b>&nbsp;Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="<?php echo $details['email']; ?>" placeholder="Enter Email Address"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="enrolment_no">What's App Number</label>
                                        <input type="text" class="form-control" id="mobile" name="mobile"
                                            value="<?php echo $details['mobile']; ?>"
                                            placeholder="Enter What's App Number" maxlength="10" minlength="10"
                                            onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="student_name"><b style="color:red;">*</b>&nbsp;Aadhar Number</label>
                                        <input type="tel" class="form-control" id="aadhar" name="aadhar"
                                            value="<?php echo $details['aadhar_no']; ?>"
                                            placeholder="Enter Aadhar Number" maxlength="12" minlength="12"
                                            onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="dob" class="form-label">Date of Birth&nbsp;(DD/MM/YYYY)</label>
                                        <input type="date" class="form-control" id="dob" name="dob"
                                            value="<?php echo $details['dob']; ?>" required
                                            max="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="studid" value="<?php if (isset($details['stud_reg_id']))
                                echo $details['stud_reg_id']; ?>">
                            <div class="form-group">
                                <div class="col-sm-9 col-sm-offset-3">
                                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Add Admin Content End -->
            </div>
        </div> <!-- ./container -->


    </div> <!-- ./container -->

    <!-- Add Admin Content End -->

    <!-- footer start  -->

    <div class="footer">
        <?php include_once 'footer.php'; ?>
    </div>


    <!-- footer start End -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="plugins/sparklines/sparkline.js"></script>
    <!-- JQVMap -->
    <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="plugins/summernote/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="dist/js/pages/dashboard.js"></script>



    <script>
        // Function to fetch countries and populate the datalist
        async function fetchCountries() {
            try {
                const response = await fetch('https://api.first.org/data/v1/countries');
                const data = await response.json();
                const countries = Object.values(data.data);

                // Sort countries alphabetically by country name
                countries.sort((a, b) => a.country.localeCompare(b.country));

                const datalist = document.getElementById('country-datalist');
                countries.forEach(country => {
                    const option = document.createElement('option');
                    option.value = country.country;
                    datalist.appendChild(option);
                });
            } catch (error) {
                console.error('Error fetching countries:', error);
            }
        }

        // Call the function to populate the datalist when the page loads
        fetchCountries();
    </script>
    <script>
        document.getElementById('copyAddressCheckbox').addEventListener('change', function () {
            var permanentAddress = document.getElementById('permanent_address').value;
            if (this.checked) {
                document.getElementById('correspondence_address').value = permanentAddress;
            } else {
                document.getElementById('correspondence_address').value = '';
            }
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#courseAdmission').on('change', function () {
                var selectedCourse = $(this).val();
                var yearSelect = $('#yearSelect');

                yearSelect.find('option').show(); // Show all options initially

                if (selectedCourse === 'BCom') {
                    yearSelect.find('option').hide().filter('[value="fy"], [value="sy"], [value="ty"]').show();
                } else if (selectedCourse === 'MCom') {
                    yearSelect.find('option').hide().filter('[value="fy"], [value="sy"]').show();
                } else if (selectedCourse === 'DTL') {
                    yearSelect.find('option').hide().filter('[value="fy"]').show();
                } else {
                    yearSelect.find('option').hide().filter('[value=""]').show(); // Show "Select" option if no course is selected
                }

                yearSelect.val(''); // Reset the year selection
            });
        });
    </script>

    <script>
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function capitalizeInput(event) {
            const input = event.target;
            const cursorPosition = input.selectionStart; // Get current cursor position

            input.value = capitalizeFirstLetter(input.value);

            // Restore the cursor position
            input.setSelectionRange(cursorPosition, cursorPosition);
        }

        $(document).ready(function () {
            $('input[type="text"], textarea').on('input', capitalizeInput);
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#photo').on('change', function () {
                const file = this.files[0];
                const feedback = $('#photoFeedback');
                const maxSize = 500 * 1024; // 500 KB
                const minSize = 100 * 1024; // 100 KB

                if (file) {
                    const fileSize = file.size;

                    if (fileSize < minSize || fileSize > maxSize) {
                        feedback.text('File size must be between 100 KB and 500 KB.').show();
                        this.setCustomValidity('Invalid file size.');
                    } else {
                        feedback.text('').hide();
                        this.setCustomValidity('');
                    }
                } else {
                    feedback.text('').hide();
                    this.setCustomValidity('');
                }
            });

            $('#photoForm').on('submit', function (e) {
                const fileInput = $('#photo')[0];
                if (fileInput.checkValidity() === false) {
                    e.preventDefault();
                    e.stopPropagation();
                    $('#photo').addClass('is-invalid');
                } else {
                    $('#photo').removeClass('is-invalid');
                }
                $(this).addClass('was-validated');
            });
        });
    </script>
</body>

</html>