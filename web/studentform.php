<?php
session_start();
// print_r($_SESSION);
include_once '../config/config.php';
include_once '../controller/ctrlgetStudDetails.php';

$studid = $_SESSION['id'];

$student = getStudentRegById($studid);

// print_r($student);
$status = getStudentPaymentStatusById($studid);
$name_of_board = json_decode($status['name_of_board']);
$passing_year = json_decode($status['passing_year']);
$marks_obtained = json_decode($status['marks_obtained']);
$total_marks = json_decode($status['total_marks']);
$percentage = json_decode($status['percentage']);
// print_r($status);
$enrolment_no = 10000;

$eno = 10000 + $studid;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Govindlal College</title>
    <link rel="icon" href="../assets/img/logo.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <style>
        .mt-100 {
            margin-top: 100px;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body style="background-color:#87CEFA">
    <?php
    if ($_SESSION['id'] != '' && $status['pstatus'] == 'Success') {
        ?>
        <div class="container-fluid">
            <div class="row">
                <nav class="navbar navbar-light bg-light">
                    <div class="row">
                        <div class="col-md-1">
                            <a class="navbar-brand" href="#">
                                <img src="../assets/img/logo.jpg" width="70%" alt="">
                            </a>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mt-4">GOVINDLAL KANHAIYALAL JOSHI (NIGHT) COMMERCE COLLEGE, LATUR</h6>
                        </div>
                        <div class="col-md-5">
                            <header>
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="d-flex justify-content-end mt-3">
                                            <a href="../controller/ctrlStudLogout.php" class="btn btn-primary">Logout</a>
                                        </div>
                                    </div>
                                </div>
                            </header>
                        </div>

                    </div>
                </nav>
            </div>
            <div class="container mt-100">
                <div class="row mt-3">
                    <div class="col-md-4"></div>
                    <div class="col-md-2 text-center">
                        <a href="paymentrecipt.php"><img src="../assets/img/precipt.png" width="100%" alt=""></a>
                        <span><b>Print Receipt</b></span>
                    </div>
                    <div class="col-md-2 text-center">
                        <a href="printform.php"><img src="../assets/img/forms.png" width="100%" alt=""></a>
                        <span><b>Print Form</b></span>
                    </div>
                    <div class="col-md-4"></div>
                </div>
            </div>

        </div>


        <?php
    } else {
        ?>
        <header>
            <div class="container-fluid">
                <div class="row">
                    <div class="d-flex justify-content-end mt-3">
                        <a href="../controller/ctrlStudLogout.php" class="btn btn-primary">Logout</a>
                    </div>
                </div>
            </div>
        </header>
        <div class="container">
            <div class="row">
                <div class="header mt-2">
                    <img src="../assets/img/logo.jpg" width="4%" alt="Logo">
                    <h2>Addmission Form</h2>
                </div>
                <div class="card p-4 m-3">
                    <form id="photoForm" action="../controller/ctrlStudent.php" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="courseAdmission"><b style="color:red;">*</b>&nbsp;Select Course:</label>
                                <select class="form-select" id="courseAdmission" name="courseAdmission" required>
                                    <option value="">Select</option>
                                    <option value="BCom" <?php if (isset($status['course_name']) && $status['course_name'] == "BCom")
                                        echo 'selected = "selected"'; ?>>BCom</option>

                                    <option value="MCom" <?php if (isset($status['course_name']) && $status['course_name'] == "MCom")
                                        echo 'selected = "selected"'; ?>>MCom</option>

                                    <option value="DTL" <?php if (isset($status['course_name']) && $status['course_name'] == "DTL")
                                        echo 'selected = "selected"'; ?>>DTL</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="yearSelect" class=""><b style="color:red;">*</b>&nbsp;Select Year</label>
                                <select class="form-select" id="yearSelect" name="yearSelect" required>
                                    <option value="">Select</option>

                                    <option value="fy" <?php if (isset($status['addmission_year']) && $status['addmission_year'] == "fy")
                                        echo 'selected = "selected"'; ?>>First Year</option>

                                    <option value="sy" <?php if (isset($status['addmission_year']) && $status['addmission_year'] == "sy")
                                        echo 'selected = "selected"'; ?>>Second Year
                                    </option>

                                    <option value="ty" <?php if (isset($status['addmission_year']) && $status['addmission_year'] == "ty")
                                        echo 'selected = "selected"'; ?>>Third Year</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="acadamic_year"><b style="color:red;">*</b>&nbsp;Academic Year</label>
                                <select class="form-select" id="acadamic_year" name="acadamic_year" required>
                                    <option value="">Select</option>

                                    <option value="2024-2025" <?php if (isset($status['acadamic_year']) && $status['acadamic_year'] == "2024-2025")
                                        echo 'selected = "selected"'; ?>>2024-2025
                                    </option>

                                    <!-- <option value="2025-2026" <?php if (isset($status['acadamic_year']) && $status['acadamic_year'] == "2025-2026")
                                        echo 'selected = "selected"'; ?>>2025-2026
                                    </option>

                                    <option value="2026-2027" <?php if (isset($status['acadamic_year']) && $status['acadamic_year'] == "2026-2027")
                                        echo 'selected = "selected"'; ?>>2026-2027
                                    </option> -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="enrolment_no">Enrolment No</label>
                                <input type="text" class="form-control" id="enrolment_no" name="enrolment_no"
                                    value="<?php echo $eno ?>" placeholder="Enter Enrolment No" readonly>
                            </div>
                            <div class="col-md-6">
                                <Mediu for="medium"><b style="color:red;">*</b>&nbsp;Medium</label>
                                    <select class="form-select" id="medium" name="medium" required>
                                        <option value="">Select</option>

                                        <option value="marathi" <?php if (isset($status['Medium']) && $status['Medium'] == "marathi")
                                            echo 'selected = "selected"'; ?>>Marathi</option>

                                        <option value="english" <?php if (isset($status['Medium']) && $status['Medium'] == "english")
                                            echo 'selected = "selected"'; ?>>English</option>
                                    </select>
                            </div>
                            <div class="col-md-6" id="slContainer">
							
                            <!--    <label for="">SL</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" name="sl" type="radio" value="marathi" <?php if (isset($status['SL']) && $status['SL'] == 'marathi')
                                                echo ' checked="checked"'; ?> id="marathi">
                                            <label class="form-check-label" for="marathi">Marathi</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" name="sl" type="radio" value="hindi" <?php if (isset($status['SL']) && $status['SL'] == 'hindi')
                                                echo ' checked="checked"'; ?> id="hindi">
                                            <label class="form-check-label" for="hindi">Hindi</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" name="sl" type="radio" value="sanskrit" <?php if (isset($status['SL']) && $status['SL'] == 'sanskrit')
                                                echo ' checked="checked"'; ?> id="sanskrit">
                                            <label class="form-check-label" for="sanskrit">Sanskrit</label>
                                        </div>
                                    </div>
                                </div>-->
                            </div>


                        </div>
                        <hr>
                        <center>
                            <h4>Personal Details</h4>
                        </center>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="student_name"><b style="color:red;">*</b>&nbsp;Student Name</label>
                                <input type="text" class="form-control" id="student_name" name="student_name" value="<?php
                                echo (isset($student['surname']) ? $student['surname'] : (isset($status['surname']) ? $status['surname'] : '')) . ' ' .
                                    (isset($student['stud_name']) ? $student['stud_name'] : (isset($status['stud_name']) ? $status['stud_name'] : '')) . ' ' .
                                    (isset($student['fathers_name']) ? $student['fathers_name'] : (isset($status['fathers_name']) ? $status['fathers_name'] : ''));
                                ?>" placeholder="Enter Student Name">
                            </div>
							<div class="col-md-6">
                                <label for="gender"><b style="color:red;">*</b>&nbsp;Gender</label><br>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="male"
                                                value="male" <?php if (isset($status['gender']) && $status['gender'] == 'male')
                                                    echo ' checked="checked"'; ?> required>
                                            <label class="form-check-label" for="male">Male</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="female"
                                                value="female" <?php if (isset($status['gender']) && $status['gender'] == 'female')
                                                    echo ' checked="checked"'; ?>>
                                            <label class="form-check-label" for="female">Female</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="other"
                                                value="other" <?php if (isset($status['gender']) && $status['gender'] == 'other')
                                                    echo ' checked="checked"'; ?>>
                                            <label class="form-check-label" for="other">Other</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="father_name"><b style="color:red;">*</b>&nbsp;Father's Full Name</label>
                                <input type="text" class="form-control" id="father_name" name="father_name" value="<?php if (isset($status['fathers_full_name']))
                                    echo $status['fathers_full_name']; ?>" placeholder="Enter Father's Name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="mother_name"><b style="color:red;">*</b>&nbsp;Mother's Name</label>
                                <input type="text" class="form-control" id="mother_name" name="mother_name" value="<?php if (isset($status['mothers_name']))
                                    echo $status['mothers_name']; ?>" placeholder="Enter Mother's Name" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="birth_place"><b style="color:red;">*</b>&nbsp;Place of Birth</label>
                                <input type="text" class="form-control" id="birth_place" name="birth_place" value="<?php if (isset($status['place_of_birth']))
                                    echo $status['place_of_birth']; ?>" placeholder="Enter Birth Place" required>
                            </div>

                            <div class="col-md-4">
                                <label for="marital_status"><b style="color:red;">*</b>&nbsp;Marital Status</label>
                                <select class="form-select" id="marital_status" name="marital_status" required>
                                    <option value="">Select</option>
                                    <option value="single" <?php if (isset($status['marital_status']) && $status['marital_status'] == "single")
                                        echo 'selected = "selected"'; ?>>Unmarried
                                    </option>

                                    <option value="married" <?php if (isset($status['marital_status']) && $status['marital_status'] == "married")
                                        echo 'selected = "selected"'; ?>>Married
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="date_of_birth"><b style="color:red;">*</b>&nbsp;Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                    value="<?php echo isset($student['dob']) ? $student['dob'] : (isset($status['DOB']) ? $status['DOB'] : ''); ?>"
                                    placeholder="Enter Date of Birth">
                            </div>
                            <div class="col-md-4">
                                <label for="caste"><b style="color:red;">*</b>&nbsp;Caste</label>
                                <select class="form-select" id="caste" name="caste" required>
                                    <option value="">Select </option>
                                    <option value="sc" <?php if (isset($status['caste']) && $status['caste'] == "sc")
                                        echo 'selected = "selected"'; ?>>SC</option>

                                    <option value="obc" <?php if (isset($status['caste']) && $status['caste'] == "obc")
                                        echo 'selected = "selected"'; ?>>OBC</option>

                                    <option value="sbc" <?php if (isset($status['caste']) && $status['caste'] == "sbc")
                                        echo 'selected = "selected"'; ?>>SBC</option>

                                    <option value="vjnt" <?php if (isset($status['caste']) && $status['caste'] == "vjnt")
                                        echo 'selected = "selected"'; ?>>VJNT</option>

                                    <option value="nt1" <?php if (isset($status['caste']) && $status['caste'] == "nt1")
                                        echo 'selected = "selected"'; ?>>NT1</option>

                                    <option value="nt2" <?php if (isset($status['caste']) && $status['caste'] == "nt2")
                                        echo 'selected = "selected"'; ?>>NT2</option>

                                    <option value="nt3" <?php if (isset($status['caste']) && $status['caste'] == "nt3")
                                        echo 'selected = "selected"'; ?>>NT3</option>

                                    <option value="st" <?php if (isset($status['caste']) && $status['caste'] == "st")
                                        echo 'selected = "selected"'; ?>>ST</option>

                                    <option value="sebc" <?php if (isset($status['caste']) && $status['caste'] == "sebc")
                                        echo 'selected = "selected"'; ?>>SEBC</option>

                                    <option value="open" <?php if (isset($status['caste']) && $status['caste'] == "open")
                                        echo 'selected = "selected"'; ?>>Open</option>
									<option value="EWS" <?php if (isset($status['caste']) && $status['caste'] == "EWS")
                                        echo 'selected = "selected"'; ?>>EWS</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="subcaste"><b style="color:red;">*</b>&nbsp;Subcaste</label>
                                <input type="text" class="form-control" name="subcaste" value="<?php if (isset($status['subcaste']))
                                    echo $status['subcaste']; ?>" id="subcaste" placeholder="Enter Subcaste" required>
                            </div>
                            <div class="col-md-4">
                                <label for="country-select"><b style="color:red;">*</b>&nbsp;Nationality</label>
                                <select id="country-select" name="nationality" class="form-select" required>
                                    <option value="">Select Nationality</option>

                                    <option value="India" <?php if (isset($status['nationality']) && $status['nationality'] == "India")
                                        echo 'selected = "selected"'; ?>>India</option>
                                </select>

                                <!-- Options will be populated here -->
                                </datalist>
                            </div>
                            <div class="col-md-4">
                                <label for="religion-select"><b style="color:red;">*</b>&nbsp;Religion</label>
                                <select id="religion-select" class="form-select" name="religion" required>
                                    <option value="">Select</option>
                                    <option value="Hindu" <?php if (isset($status['religion']) && $status['religion'] == "Hindu")
                                        echo 'selected = "selected"'; ?>>
                                        Hindu</option>

                                    <option value="Christian" <?php if (isset($status['religion']) && $status['religion'] == "Christian")
                                        echo 'selected = "selected"'; ?>>
                                        Christian</option>
                                    
									<option value="Islam" <?php if (isset($status['religion']) && $status['religion'] == "Islam")
                                        echo 'selected = "selected"'; ?>>Islam
                                    </option>


                                    <option value="Buddhist" <?php if (isset($status['religion']) && $status['religion'] == "Buddhist")
                                        echo 'selected = "selected"'; ?>>
                                        Buddhist</option>

                                    <option value="Sikh" <?php if (isset($status['religion']) && $status['religion'] == "Sikh")
                                        echo 'selected = "selected"'; ?>>Sikh
                                    </option>

                                    <option value="Jewish" <?php if (isset($status['religion']) && $status['religion'] == "Jewish")
                                        echo 'selected = "selected"'; ?>>Jewish
                                    </option>

                                    <option value="Bahai" <?php if (isset($status['religion']) && $status['religion'] == "Bahai")
                                        echo 'selected = "selected"'; ?>>Bahai
                                    </option>

                                    <option value="Jain" <?php if (isset($status['religion']) && $status['religion'] == "Jain")
                                        echo 'selected = "selected"'; ?>>Jain
                                    </option>

                                    <option value="Shinto" <?php if (isset($status['religion']) && $status['religion'] == "Shinto")
                                        echo 'selected = "selected"'; ?>>Shinto
                                    </option>

                                    <option value="Zoroastrian" <?php if (isset($status['religion']) && $status['religion'] == "Zoroastrian")
                                        echo 'selected = "selected"'; ?>>
                                        Zoroastrian</option>

                                    <option value="Atheist" <?php if (isset($status['religion']) && $status['religion'] == "Atheist")
                                        echo 'selected = "selected"'; ?>>
                                        Atheist</option>

                                    <option value="Agnostic" <?php if (isset($status['religion']) && $status['religion'] == "Agnostic")
                                        echo 'selected = "selected"'; ?>>
                                        Agnostic</option>

                                    <option value="Tribal Religions" <?php if (isset($status['religion']) && $status['religion'] == "Tribal Religions")
                                        echo 'selected = "selected"'; ?>>
                                        Tribal Religions</option>

                                    <option value="Parsi" <?php if (isset($status['religion']) && $status['religion'] == "Parsi")
                                        echo 'selected = "selected"'; ?>>
                                        Parsi</option>

                                    <option value="Sanamahist" <?php if (isset($status['religion']) && $status['religion'] == "Sanamahist")
                                        echo 'selected = "selected"'; ?>>
                                        Sanamahist</option>

                                    <option value="Lingayat" <?php if (isset($status['religion']) && $status['religion'] == "Lingayat")
                                        echo 'selected = "selected"'; ?>>
                                        Lingayat</option>

                                    <option value="other" <?php if (isset($status['religion']) && $status['religion'] == "other")
                                        echo 'selected = "selected"'; ?>>Other
                                    </option>
                                </select>
                                <input type="text" id="other-religion-input" class="form-control other-religion-input"
                                    name="religion" placeholder="Enter your religion">
                            </div>
                            <div class="col-md-4">
                                <label for="blood_group">Blood Group</label>
                                <select id="blood_group" class="form-control" name="blood_group">
                                    <option value="">Select</option>

                                    <option value="A+" <?php if (isset($status['blood_group']) && $status['blood_group'] == "A+")
                                        echo 'selected = "selected"'; ?>>A+
                                    </option>

                                    <option value="A-" <?php if (isset($status['blood_group']) && $status['blood_group'] == "A-")
                                        echo 'selected = "selected"'; ?>>A-
                                    </option>

                                    <option value="B+" <?php if (isset($status['blood_group']) && $status['blood_group'] == "B+")
                                        echo 'selected = "selected"'; ?>>B+
                                    </option>

                                    <option value="B-" <?php if (isset($status['blood_group']) && $status['blood_group'] == "B-")
                                        echo 'selected = "selected"'; ?>>B-
                                    </option>

                                    <option value="AB+" <?php if (isset($status['blood_group']) && $status['blood_group'] == "AB+")
                                        echo 'selected = "selected"'; ?>>AB+
                                    </option>

                                    <option value="AB-" <?php if (isset($status['blood_group']) && $status['blood_group'] == "AB-")
                                        echo 'selected = "selected"'; ?>>AB-
                                    </option>

                                    <option value="O+" <?php if (isset($status['blood_group']) && $status['blood_group'] == "O+")
                                        echo 'selected = "selected"'; ?>>O+
                                    </option>

                                    <option value="O-" <?php if (isset($status['blood_group']) && $status['blood_group'] == "O-")
                                        echo 'selected = "selected"'; ?>>O-
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="physically_challenged"><b style="color:red;">*</b>&nbsp;Physically
                                    Challenged:</label>
                                <select class="form-select" name="physically_challenged" id="physically_challenged"
                                    required>
                                    <option value="">Select</option>
                                    <option value="no" <?php if (isset($status['physically_challenged']) && $status['physically_challenged'] == "no")
                                        echo 'selected = "selected"'; ?>>
                                        No</option>

                                    <option value="yes" <?php if (isset($status['physically_challenged']) && $status['physically_challenged'] == "yes")
                                        echo 'selected = "selected"'; ?>>Yes
                                    </option>


                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="blind"><b style="color:red;">*</b>&nbsp;Blind:</label>
                                <select class="form-select" name="blind" id="blind" required>
                                    <option value="">Select</option>

                                    <option value="no" <?php if (isset($status['blind']) && $status['blind'] == "no")
                                        echo 'selected = "selected"'; ?>>No</option>

                                    <option value="yes" <?php if (isset($status['blind']) && $status['blind'] == "yes")
                                        echo 'selected = "selected"'; ?>>Yes</option>

                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="father_occupation"><b style="color:red;">*</b>&nbsp;Father's Occupation:</label>
                                <select class="form-select" name="father_occupation" id="father_occupation" required>
                                    <option value="">Select </option>

                                    <option value="farmer" <?php if (isset($status['fathers_occupation']) && $status['fathers_occupation'] == "farmer")
                                        echo 'selected = "selected"'; ?>>Farmer
                                    </option>

                                    <option value="labour" <?php if (isset($status['fathers_occupation']) && $status['fathers_occupation'] == "labour")
                                        echo 'selected = "selected"'; ?>>Labour
                                    </option>

                                    <option value="service" <?php if (isset($status['fathers_occupation']) && $status['fathers_occupation'] == "service")
                                        echo 'selected = "selected"'; ?>>Service
                                    </option>

                                    <option value="businessman" <?php if (isset($status['fathers_occupation']) && $status['fathers_occupation'] == "businessman")
                                        echo 'selected = "selected"'; ?>>
                                        Businessman</option>

                                    <option value="professional" <?php if (isset($status['fathers_occupation']) && $status['fathers_occupation'] == "professional")
                                        echo 'selected = "selected"'; ?>>
                                        Professional</option>

                                    <option value="other" <?php if (isset($status['fathers_occupation']) && $status['fathers_occupation'] == "other")
                                        echo 'selected = "selected"'; ?>>
                                        Other</option>

                                    <option value="Private" <?php if (isset($status['fathers_occupation']) && $status['fathers_occupation'] == "Private")
                                        echo 'selected = "selected"'; ?>>Private
                                    </option>

                                    <option value="Government" <?php if (isset($status['fathers_occupation']) && $status['fathers_occupation'] == "Government")
                                        echo 'selected = "selected"'; ?>>
                                        Government</option>

                                    <option value="other" <?php if (isset($status['fathers_occupation']) && $status['fathers_occupation'] == "other")
                                        echo 'selected = "selected"'; ?>>Other
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="parent_contact"><b style="color:red;">*</b>&nbsp;Parent's Contact No:</label>
                                <input type="text" class="form-control" name="parent_contact" value="<?php if (isset($status['parents_mobile']))
                                    echo $status['parents_mobile']; ?>" id="parent_contact" maxlength="10"
                                    minlength="10"
                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;"
                                    required>
                            </div>

                            <div class="col-md-4">
                                <label><b style="color:red;">*</b>&nbsp;Do you have a Voter ID?</label><br>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="voter_id" id="voter_id_yes"
                                                value="yes" <?php if (isset($status['have_voter_id']) && $status['have_voter_id'] == 'yes')
                                                    echo ' checked="checked"'; ?> required>
                                            <label class="form-check-label" for="voter_id_yes">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="voter_id" id="voter_id_no"
                                                value="no" <?php if (isset($status['have_voter_id']) && $status['have_voter_id'] == 'no')
                                                    echo ' checked="checked"'; ?>>
                                            <label class="form-check-label" for="voter_id_no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="whatsapp_number"><b style="color:red;">*</b>&nbsp;Student's WhatsApp
                                    Number:</label>
                                <input type="text" class="form-control" name="whatsapp_number" id="whatsapp_number" value="<?php echo isset($student['mobile']) ? $student['mobile'] : (isset($status['stud_whatsapp']) ? $status['stud_whatsapp'] : ''); ?>
" placeholder="Enter Student's WhatsApp Number">
                            </div>
                            <div class="col-md-4">
                                <label for="aadhar_number"><b style="color:red;">*</b>&nbsp;Student's Aadhar Number:</label>
                                <input type="text" class="form-control" name="aadhar_number" id="aadhar_number"
                                    value="<?php echo isset($student['aadhar_no']) ? $student['aadhar_no'] : (isset($status['stud_aadhar']) ? $status['stud_aadhar'] : ''); ?>"
                                    placeholder="Enter Student's Aadhar Number">
                            </div>
                            <div class="col-md-4">
                                <label for="email"><b style="color:red;">*</b>&nbsp;Student's E-mail ID:</label>
                                <input type="email" class="form-control" name="email" id="email"
                                    value="<?php echo isset($student['email']) ? $student['email'] : (isset($status['stud_email']) ? $status['stud_email'] : ''); ?>"
                                    placeholder="Enter Student's E-mail ID">
                            </div>
                            <div class="col-md-4">
                                <label for="abcid">ABC ID:</label>
                                <input type="text" class="form-control" name="abcid" id="abcid" value="<?php if (isset($status['ABC_id']))
                                    echo $status['ABC_id']; ?>" placeholder="Enter ABC ID" maxlength="12"
                                    minlength="12"
                                    onkeypress="if (isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                            </div>
                            <div class="col-md-4">
                                <br>
                                <label for="permanent_address"><b style="color:red;">*</b>&nbsp;Permanent
                                    Address:</label><br>
                                <textarea class="form-control" name="permanent_address" id="permanent_address" rows="1"
                                    required><?php if (isset($status['permanent_address']))
                                        echo $status['permanent_address']; ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="correspondence_address"><b style="color:red;">*</b>&nbsp;Address for
                                    Correspondence:</label>&nbsp;
                                <input class="form-check-input" type="checkbox" id="copyAddressCheckbox">(Use Permanent
                                Address)
                                <textarea class="form-control" name="correspondence_address" id="correspondence_address"
                                    rows="1" required><?php if (isset($status['correspondence_address']))
                                        echo $status['correspondence_address']; ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <br>
                                <label for="birth_place"><b style="color:red;">*</b>&nbsp;Photo</label>
                                <span class="size">(Between 0 KB to 500 KB)</span>
                                <input type="file" class="form-control" id="photo" name="photo" value="<?php if (isset($status['photo']))
                                    echo $status['photo']; ?>" accept="image/*" required>
                                <div id="photoFeedback" class="invalid-feedback"></div>
                            </div>
                        </div>
                        <hr>
                        <center>
                            <h4>Educational Qualifications</h4>
                        </center>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <th>Name Of Exam</th>
                                        <th>Name Of Board/Univercity</th>
                                        <th>Month & Year Of Passing</th>
                                        <th>Marks Obtained</th>
                                        <th>Total Marks</th>
                                        <th>Percentage %</th>
                                        <th>Upload Marksheet</th>
                                    </thead>
                                    <tbody id="dynamicRows">
                                        <!--<tr>
                                            <td><input type="text" class="form-control" name="nameofexam[]" value="10th"
                                                    placeholder="10th" readonly></td>

                                            <td><input type="text" class="form-control" name="nameofboard[]" value="<?php if (isset($name_of_board[0]))
                                                echo $name_of_board[0]; ?>" required></td>

                                            <td><input type="text" class="form-control" name="passingyear[]" value="<?php if (isset($passing_year[0]))
                                                echo $passing_year[0]; ?>" maxlength="4"
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;"
                                                    required></td>

                                            <td><input type="text" class="form-control obtmarks" name="obtmarks[]" value="<?php if (isset($marks_obtained[0]))
                                                echo $marks_obtained[0]; ?>" maxlength="4"
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;"
                                                    required></td>

                                            <td><input type="text" class="form-control totalmarks" name="totalmarks[]"
                                                    value="<?php if (isset($total_marks[0]))
                                                        echo $total_marks[0]; ?>" maxlength="4"
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;"
                                                    required></td>

                                            <td><input type="text" class="form-control percentage" name="percentage[]"
                                                    value="<?php if (isset($percentage[0]))
                                                        echo $percentage[0]; ?>" readonly></td>

                                            <td><input type="file" class="form-control-file photo-input" id="photo1"
                                                    name="sscmarks" required>
                                                <div id="photoFeedback1" class="invalid-feedback"></div>
                                                <span class="size">(Between 0 KB to 500 KB)</span>
                                            </td>
                                        </tr>-->
                                        <tr>
                                            <td><input type="text" class="form-control" name="nameofexam[]" value="12th"
                                                    placeholder="12th" readonly></td>

                                            <td><input type="text" class="form-control" name="nameofboard[]" value="<?php if (isset($name_of_board[1]))
                                                echo $name_of_board[1]; ?>"></td>

                                            <td><input type="text" class="form-control" name="passingyear[]" value="<?php if (isset($passing_year[1]))
                                                echo $passing_year[1]; ?>" maxlength="7" placeholder="mm-yyyy"
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                                            </td>

                                            <td><input type="text" class="form-control obtmarks" name="obtmarks[]" value="<?php if (isset($marks_obtained[1]))
                                                echo $marks_obtained[1]; ?>" maxlength="7" 
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                                            </td>

                                            <td><input type="text" class="form-control totalmarks" name="totalmarks[]"
                                                    value="<?php if (isset($total_marks[1]))
                                                        echo $total_marks[1]; ?>" maxlength="7" placeholder="mm-yyyy"
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                                            </td>

                                            <td><input type="text" class="form-control percentage" name="percentage[]"
                                                    value="<?php if (isset($percentage[1]))
                                                        echo $percentage[1]; ?>" readonly></td>

                                            <td><input type="file" class="form-control-file photo-input" id="photo2"
                                                    name="hscmarks" required>
                                                <span class="size">(Between 0 KB to 500 KB)</span>
                                                <div id="photoFeedback2" class="invalid-feedback"></div>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" class="form-control" name="nameofexam[]"
                                                    value="U.G. First Year" placeholder="U.G. First Year" readonly></td>

                                            <td><input type="text" class="form-control" name="nameofboard[]" value="<?php if (isset($name_of_board[2]))
                                                echo $name_of_board[2]; ?>"></td>

                                            <td><input type="text" class="form-control" name="passingyear[]" value="<?php if (isset($passing_year[2]))
                                                echo $passing_year[2]; ?>" maxlength="7" placeholder="mm-yyyy"
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                                            </td>

                                            <td><input type="text" class="form-control obtmarks" name="obtmarks[]" value="<?php if (isset($marks_obtained[2]))
                                                echo $marks_obtained[2]; ?>" maxlength="4"
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                                            </td>

                                            <td><input type="text" class="form-control totalmarks" name="totalmarks[]"
                                                    value="<?php if (isset($total_marks[2]))
                                                        echo $total_marks[2]; ?>" maxlength="4"
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                                            </td>

                                            <td><input type="text" class="form-control percentage" name="percentage[]"
                                                    value="<?php if (isset($percentage[2]))
                                                        echo $percentage[2]; ?>" readonly></td>

                                            <td><input type="file" class="form-control-file photo-input" id="photo3"
                                                    name="fymarks">
                                                <span class="size">(Between 0 KB to 500 KB)</span>
                                                <div id="photoFeedback3" class="invalid-feedback"></div>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" class="form-control" name="nameofexam[]"
                                                    value="U.G. Second Year" placeholder="U.G. Second Year" readonly></td>

                                            <td><input type="text" class="form-control" name="nameofboard[]" value="<?php if (isset($name_of_board[3]))
                                                echo $name_of_board[3]; ?>"></td>

                                            <td><input type="text" class="form-control" name="passingyear[]" value="<?php if (isset($passing_year[3]))
                                                echo $passing_year[3]; ?>" maxlength="7" placeholder="mm-yyyy"
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                                            </td>

                                            <td><input type="text" class="form-control obtmarks" name="obtmarks[]" value="<?php if (isset($marks_obtained[3]))
                                                echo $marks_obtained[3]; ?>" maxlength="4"
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                                            </td>

                                            <td><input type="text" class="form-control totalmarks" name="totalmarks[]"
                                                    value="<?php if (isset($total_marks[3]))
                                                        echo $total_marks[3]; ?>" maxlength="4"
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                                            </td>

                                            <td><input type="text" class="form-control percentage" name="percentage[]"
                                                    value="<?php if (isset($percentage[3]))
                                                        echo $percentage[3]; ?>" readonly></td>

                                            <td><input type="file" class="form-control-file photo-input" id="photo4"
                                                    name="symarks">
                                                <span class="size">(Between 0 KB to 500 KB)</span>
                                                <div id="photoFeedback4" class="invalid-feedback"></div>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" class="form-control" name="nameofexam[]"
                                                    value="U.G.Third Year" placeholder="U.G.Third Year" readonly></td>

                                            <td><input type="text" class="form-control" name="nameofboard[]" value="<?php if (isset($name_of_board[4]))
                                                echo $name_of_board[4]; ?>"></td>

                                            <td><input type="text" class="form-control" name="passingyear[]" value="<?php if (isset($passing_year[4]))
                                                echo $passing_year[4]; ?>" maxlength="7" placeholder="mm-yyyy"
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                                            </td>

                                            <td><input type="text" class="form-control obtmarks" name="obtmarks[]" value="<?php if (isset($marks_obtained[4]))
                                                echo $marks_obtained[4]; ?>" maxlength="7" 
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                                            </td>

                                            <td><input type="text" class="form-control totalmarks" name="totalmarks[]"
                                                    value="<?php if (isset($total_marks[4]))
                                                        echo $total_marks[4]; ?>" maxlength="7" 
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                                            </td>

                                            <td><input type="text" class="form-control percentage" name="percentage[]"
                                                    value="<?php if (isset($percentage[4]))
                                                        echo $percentage[4]; ?>" readonly></td>

                                            <td><input type="file" class="form-control-file photo-input" id="photo5"
                                                    name="tymarks">
                                                <span class="size">(Between 0 KB to 500 KB)</span>
                                                <div id="photoFeedback5" class="invalid-feedback"></div>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" class="form-control" name="nameofexam[]"
                                                    value="P.G. First Year" placeholder="P.G. First Year" readonly></td>

                                            <td><input type="text" class="form-control" name="nameofboard[]" value="<?php if (isset($name_of_board[5]))
                                                echo $name_of_board[5]; ?>"></td>

                                            <td><input type="text" class="form-control" name="passingyear[]" value="<?php if (isset($passing_year[5]))
                                                echo $passing_year[5]; ?>" maxlength="7" placeholder="mm-yyyy"
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                                            </td>

                                            <td><input type="text" class="form-control obtmarks" name="obtmarks[]" value="<?php if (isset($marks_obtained[5]))
                                                echo $marks_obtained[5]; ?>" maxlength="7" 
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                                            </td>

                                            <td><input type="text" class="form-control totalmarks" name="totalmarks[]"
                                                    value="<?php if (isset($total_marks[5]))
                                                        echo $total_marks[5]; ?>" maxlength="7" 
                                                    onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;">
                                            </td>

                                            <td><input type="text" class="form-control percentage" name="percentage[]"
                                                    value="<?php if (isset($percentage[5]))
                                                        echo $percentage[5]; ?>" readonly></td>

                                            <td><input type="file" class="form-control-file photo-input" id="photo6"
                                                    name="pgfymarks">
                                                <span class="size">(Between 0 KB to 500 KB)</span>
                                                <div id="photoFeedback6" class="invalid-feedback"></div>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    ?>

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
                // const minSize = 100 * 1024; 

                if (file) {
                    const fileSize = file.size;

                    if (fileSize > maxSize) {
                        // alert('fsdf');
                        feedback.text('File size must be between 0 KB and 500 KB.').show();
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
    <script>
        $(document).ready(function () {
            $('.photo-input').on('change', function () {
                const file = this.files[0];
                const feedback = $(this).siblings('.invalid-feedback');
                const maxSize = 500 * 1024; // 500 KB

                if (file) {
                    const fileSize = file.size;

                    if (fileSize > maxSize) {
                        feedback.text('File size must be between 0 KB and 500 KB.').show();
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
                const allFileInputs = $('.photo-input');

                allFileInputs.each(function () {
                    if (this.checkValidity() === false) {
                        e.preventDefault();
                        e.stopPropagation();
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                $(this).addClass('was-validated');
            });
        });
    </script>

    <script>
        document.getElementById('religion-select').addEventListener('change', function () {
            var otherReligionInput = document.getElementById('other-religion-input');
            if (this.value === 'Other') {
                otherReligionInput.style.display = 'inline-block';
            } else {
                otherReligionInput.style.display = 'none';
                otherReligionInput.value = ''; // Clear the input field when not in use
            }
        });
    </script>
    <script>
        document.addEventListener('input', function (event) {
            if (event.target.classList.contains('obtmarks') || event.target.classList.contains('totalmarks')) {
                var row = event.target.closest('tr');
                var obtMarksInput = row.querySelector('.obtmarks');
                var totalMarksInput = row.querySelector('.totalmarks');
                var percentageInput = row.querySelector('.percentage');

                var obtMarks = parseFloat(obtMarksInput.value) || 0;
                var totalMarks = parseFloat(totalMarksInput.value) || 0;

                if (totalMarks > 0) {
                    var percentage = (obtMarks / totalMarks) * 100;
                    percentageInput.value = percentage.toFixed(2) + '%';
                } else {
                    percentageInput.value = '';
                }
            }
        });
    </script>
    <script>
        document.getElementById('courseAdmission').addEventListener('change', function () {
            var slContainer = document.getElementById('slContainer');
            if (this.value === 'MCom' || this.value === 'DTL') {
                slContainer.classList.add('hidden');
            } else {
                slContainer.classList.remove('hidden');
            }
        });
    </script>
    <script>
        $(document).ready(function () {
            // Function to show/hide rows based on selection
            function showHideRows() {
                var course = $('#courseAdmission').val();
                var year = $('#yearSelect').val();

                // Hide all rows initially
                $('#dynamicRows tr').hide();

                // Show rows based on selection
                if (course === 'BCom' && year === 'fy') {
                    $('#dynamicRows tr:lt(2)').show(); // Show 10th and 12th rows
                } else if (course === 'BCom' && year === 'sy') {
                    $('#dynamicRows tr:lt(3)').show(); // Show 10th, 12th, U.G. First Year
                } else if (course === 'BCom' && year === 'ty') {
                    $('#dynamicRows tr:lt(4)').show(); // Show all rows
                } else if (course === 'DTL' && year === 'fy') {
                    $('#dynamicRows tr:lt(2)').show(); // Show 10th and 12th rows
                } else if (course === 'MCom' && year === 'fy') {
                    $('#dynamicRows tr:lt(5)').show(); // Show 10th, 12th, U.G. First Year
                } else if (course === 'MCom' && year === 'sy') {
                    $('#dynamicRows tr').show(); // Show all rows
                }
            }

            // Initial call to hide all rows
            showHideRows();

            // Event listeners for select elements
            $('#courseAdmission, #yearSelect').change(function () {
                showHideRows();
            });
        });
    </script>
    <script>
        document.getElementById('father_name').addEventListener('input', function () {
            var input = this.value;
            this.value = input.replace(/\b\w/g, function (char) {
                return char.toUpperCase();
            });
        });
    </script>
</body>

</html>