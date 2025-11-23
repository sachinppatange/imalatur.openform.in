<?php
session_start();
include_once '../config/config.php';
include_once '../controller/ctrlgetStudDetails.php';

$details = getStudentByStudId($_GET['id']);
// print_r($details);

$decodedSL = json_decode($details['SL']);
// echo $details['name_of_board'];

$name_of_board = json_decode($details['name_of_board']);
$passing_year = json_decode($details['passing_year']);
$marks_obtained = json_decode($details['marks_obtained']);
$total_marks = json_decode($details['total_marks']);
$percentage = json_decode($details['percentage']);

// $LC = json_decode($details['Leaving_Certificate']);
// $Previous_Marks_Memo = json_decode($details['Previous_Marks_Memo']);
// $Caste_Certificate = json_decode($details['Caste_Certificate']);
// $NonCreamy_layer = json_decode($details['NonCreamy_layer']);
// $Gap_Certificate = json_decode($details['Gap_Certificate']);
// $Aadhar_Card = json_decode($details['Aadhar_Card']);
// $Bank_PassBook = json_decode($details['Bank_PassBook']);
// $Bonafied_Certificate = json_decode($details['Bonafied_Certificate']);
// $Income_Certificate = json_decode($details['Income_Certificate']);
// $Domacile_Certificate = json_decode($details['Domacile_Certificate']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars(getcompany(), ENT_QUOTES, 'UTF-8'); ?></title>

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

        .other-religion-input {
            display: none;
            /* margin-top: 10px; */
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
                        <form action="../controller/ctrlStudent.php" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="courseAdmission"><b style="color:red;">*</b>&nbsp;Select Course:</label>
                                        <select class="form-control" id="courseAdmission" name="courseAdmission"
                                            required>
                                            <option value="">Select</option>

                                            <option value="6th" <?php if (isset($details['course']) && $details['course'] == "6th")
                                                echo 'selected = "selected"'; ?>>6th (६ वी)
                                            </option>

                                            <option value="11th" <?php if (isset($details['course']) && $details['course'] == "11th")
                                                echo 'selected = "selected"'; ?>>11th (११ वी)
                                            </option>

                                
                                        </select>
                                    </div>
                                </div>
                                <!-- <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="yearSelect" class=""><b style="color:red;">*</b>&nbsp;Select
                                           Academic Year</label>
                                        <select class="form-control" id="yearSelect" name="yearSelect" required>

                                            <option value="">Select</option>

                                            <option value="fy" <?php if (isset($details['acadamic_year']) && $details['academic_year'] == "fy")
                                                echo 'selected = "selected"'; ?>>fy
                                                
                                            </option>

                                            <option value="sy" <?php if (isset($details['academic_year']) && $details['academic_year'] == "sy")
                                                echo 'selected = "selected"'; ?>>sy
                                                
                                            </option>

                                            <option value="ty" <?php if (isset($details['academic_year']) && $details['academic_year'] == "ty")
                                                echo 'selected = "selected"'; ?>>ty
                                                
                                            </option>
                                        </select>
                                    </div>
                                </div> -->
                                <!-- <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mother_name"><b style="color:red;">*</b>&nbsp;Admission Year</label>
                                        <input type="text" class="form-control" id="mother_name" name="admissionyear"
                                            value="<?php if (isset($details['addmission_year']))
                                                echo $details['addmission_year']; ?>" placeholder="Enter Mother's Name"
                                            required>
                                    </div>
                                </div> -->

                                <!-- <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="acadamic_year"><b style="color:red;">*</b>&nbsp;Academic
                                            Year</label>
                                        <select class="form-control" id="acadamic_year" name="acadamic_year" required>
                                            <option value="">Select</option>

                                            <option value="2024-2025" <?php if (isset($details['acadamic_year']) && $details['acadamic_year'] == "2024-2025")
                                                echo 'selected = "selected"'; ?>>
                                                2024-2025</option>

                                            <option value="2025-2026" <?php if (isset($details['acadamic_year']) && $details['acadamic_year'] == "2025-2026")
                                                echo 'selected = "selected"'; ?>>
                                                2025-2026</option>

                                            <option value="2026-2027" <?php if (isset($details['acadamic_year']) && $details['acadamic_year'] == "2026-2027")
                                                echo 'selected = "selected"'; ?>>
                                                2026-2027</option>

                                        </select>
                                        <label for="acadamic_year">Academic Year</label>
                                        <input type="text" class="form-control" id="acadamic_year" name="acadamic_year"
                                            value="<?php if (isset($details['acadamic_year']))
                                                echo $details['acadamic_year']; ?>" placeholder="Enter Academic Year">
                                    </div>
                                </div> -->
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="medium"><b style="color:red;">*</b>&nbsp;Medium</label>
                                        <select class="form-control" id="medium" name="medium" required>

                                            <option value="">Select Medium</option>

                                            <option value="marathi" <?php if (isset($details['Medium']) && $details['Medium'] == "marathi")
                                                echo 'selected = "selected"'; ?>>Marathi
                                            </option>

                                            <option value="english" <?php if (isset($details['Medium']) && $details['Medium'] == "english")
                                                echo 'selected = "selected"'; ?>>English
                                            </option>
                                        </select>
                                    </div>
                                </div>
                              
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="student_name"><b style="color:red;">*</b>&nbsp;Student's Fisrt Name</label>
                                        <input type="text" class="form-control" id="student_name" name="student_name"
                                            value="<?php if (isset($details['firstname']))
                                                echo $details['firstname']; ?>" placeholder="Enter Student Name"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="father_name"><b style="color:red;">*</b>&nbsp;Father's 
                                            Name</label>
                                        <input type="text" class="form-control" id="father_name" name="father_name"
                                            value="<?php if (isset($details['fathername']))
                                                echo $details['fathername']; ?>"
                                            placeholder="Enter Father's Name" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mother_name"><b style="color:red;">*</b>&nbsp;Mother's Name</label>
                                        <input type="text" class="form-control" id="mother_name" name="mother_name"
                                            value="<?php if (isset($details['mothername']))
                                                echo $details['mothername']; ?>" placeholder="Enter Mother's Name"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mother_name"><b style="color:red;">*</b>&nbsp;Surname</label>
                                        <input type="text" class="form-control" id="mother_name" name="surname"
                                            value="<?php if (isset($details['surname']))
                                                echo $details['surname']; ?>" placeholder="Enter Surname"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="gender"><b style="color:red;">*</b>&nbsp;Gender</label><br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="gender" id="male"
                                                        value="male" <?php if (isset($details['gender']) && $details['gender'] == 'male')
                                                            echo ' checked="checked"'; ?>
                                                        required>
                                                    <label class="form-check-label" for="male">male</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="gender"
                                                        id="female" value="female" <?php if (isset($details['gender']) && $details['gender'] == 'female')
                                                            echo ' checked="checked"'; ?>>
                                                    <label class="form-check-label" for="female">female</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="gender"
                                                        id="other" value="other" <?php if (isset($details['gender']) && $details['gender'] == 'other')
                                                            echo ' checked="checked"'; ?>>
                                                    <label class="form-check-label" for="other">Other</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>





                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="courseAdmission"><b style="color:red;">*</b>&nbsp;Category:</label>
                                        <select class="form-control" id="courseAdmission" name="category"
                                            required>
                                            <option value="">Select</option>

                                            <option value="general" <?php if (isset($details['category']) && $details['category'] == "general")
                                                echo 'selected = "selected"'; ?>>General and Obc
                                            </option>

                                            <option value="sc" <?php if (isset($details['category']) && $details['category'] == "sc")
                                                echo 'selected = "selected"'; ?>>SC/ST/VJ NT/SBC 
                                            </option>

                                
                                        </select>
                                    </div>
                                </div>
                               


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_of_birth"><b style="color:red;">*</b>&nbsp;Date of
                                            Birth</label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                            value="<?php if (isset($details['dob']))
                                                echo $details['dob']; ?>" placeholder="Enter Date of Birth" required>
                                    </div>
                                </div>
                              
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mother_name"><b style="color:red;">*</b>&nbsp;School Name </label>
                                        <input type="text" class="form-control" id="mother_name" name="schoolname"
                                            value="<?php if (isset($details['schoolname']))
                                                echo $details['schoolname']; ?>" placeholder="Enter School Name"
                                            required>
                                    </div>
                                </div>



                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="courseAdmission"><b style="color:red;">*</b>&nbsp;Select Previous Standard:</label>
                                        <select class="form-control" id="courseAdmission" name="previousstd"
                                            required>
                                            <option value="">Select</option>

                                            <option value="6th" <?php if (isset($details['previousstd']) && $details['previousstd'] == "6th")
                                                echo 'selected = "selected"'; ?>>6th (६ वी)
                                            </option>

                                            <option value="5th" <?php if (isset($details['previousstd']) && $details['previousstd'] == "11th")
                                                echo 'selected = "selected"'; ?>>5th (५ वी)
                                            </option>

                                
                                        </select>
                                    </div>
                                </div>



                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="whatsapp_number"><b style="color:red;">*</b>&nbsp;Student's WhatsApp
                                            Number:</label>
                                        <input type="text" class="form-control" name="whatsapp_number" value="<?php if (isset($details['whatsappno']))
                                            echo $details['whatsappno']; ?>" id="whatsapp_number"
                                            placeholder="Enter Student's WhatsApp Number" maxlength="10" minlength="10"
                                            onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="whatsapp_number"><b style="color:red;">*</b>&nbsp;Student's Alternate
                                            Number:</label>
                                        <input type="text" class="form-control" name="alternate_number" value="<?php if (isset($details['alternateno']))
                                            echo $details['alternateno']; ?>" id="whatsapp_number"
                                            placeholder="Enter Student's Alternate Number" maxlength="10" minlength="10"
                                            onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;"
                                            required>
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="aadhar_number"><b style="color:red;">*</b>&nbsp;Student's Aadhar
                                            Number:</label>
                                        <input type="text" class="form-control" name="aadhar_number" value="<?php if (isset($details['aadhar']))
                                            echo $details['aadhar']; ?>" id="aadhar_number"
                                            placeholder="Enter Student's Aadhar Number" maxlength="12" minlength="12"
                                            onkeypress="if ( isNaN(this.value + String.fromCharCode(event.keyCode) )) return false;"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email"><b style="color:red;">*</b>&nbsp;Student's E-mail ID:</label>
                                        <input type="email" class="form-control" name="email" value="<?php if (isset($details['email']))
                                            echo $details['email']; ?>" id="email"
                                            placeholder="Enter Student's E-mail ID" required>
                                    </div>
                                </div>



                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="father_name"><b style="color:red;">*</b>&nbsp;Grade
                                            </label>
                                        <input type="text" class="form-control" id="father_name" name="grade"
                                            value="<?php if (isset($details['grade']))
                                                echo $details['grade']; ?>"
                                             required>
                                    </div>
                                </div>



                              
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="medium"><b style="color:red;">*</b>&nbsp;Board</label>
                                        <select class="form-control" id="medium" name="board" required>

                                            <option value="">Select Board</option>

                                            <option value="cbse" <?php if (isset($details['board']) && $details['board'] == "cbse")
                                                echo 'selected = "selected"'; ?>>CBSE (सीबीएसई)
                                            </option>

                                            <option value="icse" <?php if (isset($details['board']) && $details['board'] == "icse")
                                                echo 'selected = "selected"'; ?>>ICSE (आयसीएसई)
                                            </option>
                                            <option value="maharashtra" <?php if (isset($details['board']) && $details['board'] == "maharashtra")
                                                echo 'selected = "selected"'; ?>>Maharashtra State Board (महाराष्ट्र राज्य बोर्ड)
                                            </option>
                                            <option value="other" <?php if (isset($details['board']) && $details['board'] == "other")
                                                echo 'selected = "selected"'; ?>>Other (इतर)
                                            </option>
                                        </select>
                                    </div>
                                </div>




                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="gender"><b style="color:red;">*</b>&nbsp;Language</label><br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="language" id="enlish"
                                                        value="english" <?php if (isset($details['language']) && $details['language'] == 'english')
                                                            echo ' checked="checked"'; ?>
                                                        required>
                                                    <label class="form-check-label" for="male">English</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="language"
                                                        id="female" value="marathi" <?php if (isset($details['language']) && $details['language'] == 'marathi')
                                                            echo ' checked="checked"'; ?>>
                                                    <label class="form-check-label" for="female">Marathi</label>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>




                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="medium"><b style="color:red;">*</b>&nbsp;Center</label>
                                        <select class="form-control" id="medium" name="center" required>

                                            <option value="">Select Center</option>

                                            <option value="tuljapur" <?php if (isset($details['centre']) && $details['centre'] == "tuljapur")
                                                echo 'selected = "selected"'; ?>>Tuljapur
                                            </option>

                                            <option value="latur" <?php if (isset($details['centre']) && $details['centre'] == "latur")
                                                echo 'selected = "selected"'; ?>>Latur
                                            </option>

                                            <option value="solapur" <?php if (isset($details['centre']) && $details['centre'] == "solapur")
                                                echo 'selected = "selected"'; ?>>Solapur
                                            </option>

                                            <option value="dhule" <?php if (isset($details['centre']) && $details['centre'] == "dhule")
                                                echo 'selected = "selected"'; ?>>Dhule
                                            </option>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="student_name"><b style="color:red;">*</b>&nbsp;Amount</label>
                                        <input type="text" class="form-control" id="student_name" name="amount"
                                            value="<?php if (isset($details['amount']))
                                                echo $details['amount']; ?>" 
                                            required readonly>
                                    </div>
                                </div>


                                <!--<div class="col-md-4">
                                    <div class="form-group">
                                        <label for="medium"><b style="color:red;">*</b>&nbsp;Status</label>
                                        <select class="form-control" id="medium" name="status" >

                                            <option value="">Select Status</option>

                                            <option value="fail" <?php if (isset($details['status']) && $details['status'] == "fail")
                                                echo 'selected = "selected"'; ?>>Fail
                                            </option>

                                            <option value="success" <?php if (isset($details['status']) && $details['status'] == "success")
                                                echo 'selected = "selected"'; ?>>Success
                                            </option>
                                        </select>
                                    </div>
                                </div>-->



                               
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <br>
                                        <label for="permanent_address"><b style="color:red;">*</b>&nbsp;
                                            Address:</label>
                                        <textarea class="form-control" name="address" id="permanent_address"
                                            rows="4" required> <?php if (isset($details['address']))
                                                echo $details['address']; ?></textarea>
                                    </div>
                                </div>





                                <!-- <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="birth_place"><b style="color:red;">*</b>&nbsp; Student Photo<span
                                                class="size">(Between
                                                0 KB to 500 KB)</span></label>
                                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*"
                                        <?php if (isset($details['studphoto']))
                                                echo $details['studphoto']; ?>   required>
                                  <div id="photoFeedback" class="invalid-feedback"></div>
                                    </div>
                                </div> -->

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="birth_place"><b style="color:red;">*</b>&nbsp; Student Photo<span
                                                class="size">(Between
                                                0 KB to 500 KB)</span></label>
                                        <input type="file" class="form-control" id="photo" name="studphoto" accept="image/*"
                                        <?php if (isset($details['studphoto']))
                                                echo $details['studphoto']; ?>  required>
                            <div id="photoFeedback" class="invalid-feedback"></div>
                                    </div>
                                </div>



                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="birth_place"><b style="color:red;">*</b>&nbsp; Student Adhar<span
                                                class="size">(Between
                                                0 KB to 500 KB)</span></label>
                                        <input type="file" class="form-control" id="photo" name="studaadhar" accept="image/*"
                                        <?php if (isset($details['studaadhar']))
                                                echo $details['studaadhar']; ?>  required>
                            <div id="photoFeedback" class="invalid-feedback"></div>
                                    </div>
                                </div>




                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="birth_place"><b style="color:red;">*</b>&nbsp; Student Signature<span
                                                class="size">(Between
                                                0 KB to 500 KB)</span></label>
                                        <input type="file" class="form-control" id="photo" name="studsign" accept="image/*"
                                        <?php if (isset($details['studsign']))
                                                echo $details['studsign']; ?>  required>
                            <div id="photoFeedback" class="invalid-feedback"></div>
                                    </div>
                                </div>
                               
                                <!-- <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-4">
                                                Document Type
                                                <label for="leaving_cert">Leaving Certificate:</label>
                                            </div>
                                            <div class="col-md-2">
                                                Original
                                                <div class="form-check">
                                                    <input class="form-check-input" name="Leaving_Certificate[]"
                                                        type="checkbox" value="Leaving Certificate original" <?php if (is_array($LC) && in_array('Leaving Certificate original', $LC)) {
                                                            echo 'checked';
                                                        } ?> id="leaving_cert_1">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                Xerox
                                                <div class="form-check">
                                                    <input class="form-check-input" name="Leaving_Certificate[]"
                                                        type="checkbox" value="Leaving Certificate xerox" <?php if (is_array($LC) && in_array('Leaving Certificate xerox', $LC)) {
                                                            echo 'checked';
                                                        } ?> id="leaving_cert_2">
                                                </div>
                                            </div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <label for="leaving_cert">Previous Marks Memo:</label>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="Previous_Marks_Memo[]"
                                                        value="Previous Marks Memo original" <?php if (is_array($Previous_Marks_Memo) && in_array('Previous Marks Memo original', $Previous_Marks_Memo)) {
                                                            echo 'checked';
                                                        } ?>
                                                        id="leaving_cert_1">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="Previous_Marks_Memo[]" value="Previous Marks Memo xerox"
                                                        <?php if (is_array($Previous_Marks_Memo) && in_array('Previous Marks Memo xerox', $Previous_Marks_Memo)) {
                                                            echo 'checked';
                                                        } ?>
                                                        id="leaving_cert_2">
                                                </div>
                                            </div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <label for="leaving_cert">Caste Certificate:</label>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="Caste_Certificate[]" value="Caste Certificate original"
                                                        <?php if (is_array($Caste_Certificate) && in_array('Caste Certificate original', $Caste_Certificate)) {
                                                            echo 'checked';
                                                        } ?> id="leaving_cert_1">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="Caste_Certificate[]" value="Caste Certificate xerox" <?php if (is_array($Caste_Certificate) && in_array('Caste Certificate xerox', $Caste_Certificate)) {
                                                            echo 'checked';
                                                        } ?>
                                                        id="leaving_cert_2">
                                                </div>
                                            </div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <label for="leaving_cert">Non-Creamy layer:</label>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="Non-Creamy_layer[]" value="Non-Creamy layer original"
                                                        <?php if (is_array($NonCreamy_layer) && in_array('Non-Creamy layer original', $NonCreamy_layer)) {
                                                            echo 'checked';
                                                        } ?>
                                                        id="leaving_cert_1">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="Non-Creamy_layer[]" value="Non-Creamy layer xerox" <?php if (is_array($NonCreamy_layer) && in_array('Non-Creamy layer xerox', $NonCreamy_layer)) {
                                                            echo 'checked';
                                                        } ?>
                                                        id="leaving_cert_2">
                                                </div>
                                            </div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <label for="leaving_cert"> Gap Certificate:</label>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="Gap_Certificate[]" value="Gap Certificate original" <?php if (is_array($Gap_Certificate) && in_array('Gap Certificate original', $Gap_Certificate)) {
                                                            echo 'checked';
                                                        } ?>
                                                        id="leaving_cert_1">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="Gap_Certificate[]" value="Gap Certificate xerox" <?php if (is_array($Gap_Certificate) && in_array('Gap Certificate xerox', $Gap_Certificate)) {
                                                            echo 'checked';
                                                        } ?> id="leaving_cert_2">
                                                </div>
                                            </div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                </div> -->
                                <!-- <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            Document Type
                                            <label for="leaving_cert">Aadhar Card:</label>
                                        </div>
                                        <div class="col-md-2">
                                            Original
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="Aadhar_Card[]"
                                                    value="Aadhar Card original" <?php if (is_array($Aadhar_Card) && in_array('Aadhar Card original', $Aadhar_Card)) {
                                                        echo 'checked';
                                                    } ?> id="leaving_cert_1">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            Xerox
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="Aadhar_Card[]"
                                                    value="Aadhar Card xerox" <?php if (is_array($Aadhar_Card) && in_array('Aadhar Card xerox', $Aadhar_Card)) {
                                                        echo 'checked';
                                                    } ?>
                                                    id="leaving_cert_2">
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <label for="leaving_cert">Bank Pass-Book:</label>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="Bank_Pass-Book[]"
                                                    value="Bank Pass-Book original" <?php if (is_array($Bank_PassBook) && in_array('Bank Pass-Book original', $Bank_PassBook)) {
                                                        echo 'checked';
                                                    } ?> id="leaving_cert_1">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="Bank_Pass-Book[]"
                                                    value="Bank Pass-Book xerox" <?php if (is_array($Bank_PassBook) && in_array('Bank Pass-Book xerox', $Bank_PassBook)) {
                                                        echo 'checked';
                                                    } ?> id="leaving_cert_2">
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <label for="leaving_cert">Bonafied Certificate:</label>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="Bonafied_Certificate[]" value="Bonafied Certificate original"
                                                    <?php if (is_array($Bonafied_Certificate) && in_array('Bonafied Certificate original', $Bonafied_Certificate)) {
                                                        echo 'checked';
                                                    } ?> id="leaving_cert_1">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="Bonafied_Certificate[]" value="Bonafied Certificate xerox"
                                                    <?php if (is_array($Bonafied_Certificate) && in_array('Bonafied Certificate xerox', $Bonafied_Certificate)) {
                                                        echo 'checked';
                                                    } ?>
                                                    id="leaving_cert_2">
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <label for="leaving_cert">Income Certificate:</label>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="Income_Certificate[]" value="Income Certificate original"
                                                    <?php if (is_array($Income_Certificate) && in_array('Income Certificate original', $Income_Certificate)) {
                                                        echo 'checked';
                                                    } ?>
                                                    id="leaving_cert_1">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="Income_Certificate[]" value="Income Certificate xerox" <?php if (is_array($Income_Certificate) && in_array('Income Certificate xerox', $Income_Certificate)) {
                                                        echo 'checked';
                                                    } ?>
                                                    id="leaving_cert_2">
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <label for="leaving_cert">Domacile Certificate:</label>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="Domacile_Certificate[]" value="Domacile Certificate original"
                                                    <?php if (is_array($Domacile_Certificate) && in_array('Domacile Certificate original', $Domacile_Certificate)) {
                                                        echo 'checked';
                                                    } ?> id="leaving_cert_1">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="Domacile_Certificate[]" value="Domacile Certificate xerox"
                                                    <?php if (is_array($Domacile_Certificate) && in_array('Domacile Certificate xerox', $Domacile_Certificate)) {
                                                        echo 'checked';
                                                    } ?>
                                                    id="leaving_cert_2">
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                </div> -->
                            </div>


                            <input type="hidden" name="studid" value="<?php if (isset($details['stud_id']))
                                echo $details['stud_id']; ?>">
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
                // const minSize = 100 * 1024; 

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
       document.getElementById('religion-select').addEventListener('change', function() {
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
        document.addEventListener('input', function(event) {
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
</body>

</html>