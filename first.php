<?php
/* -------------  START OF LOGIN SCRIPT ------------- */
session_start();

/* ───── 1.  Initialise variables & error holders ───── */
$recordMessage = $recordClass = '';
$email = $pass = '';
$emailError = $passError = '';

/* ───── 2.  Handle form post ───── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    /* 2‑a  Trim & sanitise helper */
    function input_data($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    /* 2‑b  Read fields */
    $email = input_data($_POST['email']  ?? '');
    $pass  =              $_POST['password'] ?? '';

    /* 2‑c  Field‑level validation */
    if ($email === '')                    $emailError = '*Email is required';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
                                          $emailError = 'Invalid email format';

    if ($pass === '')                     $passError  = '*Password is required';
    elseif (strlen($pass) < 6)            $passError  = 'Password must be at least 6 characters long';

    /* 2‑d  Stop early if validation failed */
    if ($emailError || $passError) goto OUTPUT_HTML;

    /* 2‑e  DB connection */
    $conn = new mysqli('localhost', 'root', '', 'user_data');
    if ($conn->connect_errno) {
        $recordMessage = 'Database connection failed: ' . $conn->connect_error;
        $recordClass   = 'error';
        goto OUTPUT_HTML;
    }

    /* 2‑f  Query for matching e‑mail */
    $stmt = $conn->prepare(
        'SELECT id, name, password FROM myform WHERE email = ? LIMIT 1'
    );
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        /* 2‑g  Password check  (⚠ plain‑text; switch to password_hash / password_verify) */
        if ($pass === $row['password']) {
            /* Successful login */
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['user_name'] = $row['name'];

            header('Location: homepage.html');
            exit;                     // ⬅️ stop further execution
        } else {
            /* Email exists but password wrong */
            $recordMessage = 'Incorrect password.';
            $recordClass   = 'error';
        }
    } else {
        /* No matching e‑mail at all */
        $recordMessage = 'No account found for that e‑mail.';
        $recordClass   = 'error';
    }

    /* 2‑h  House‑keeping */
    $stmt?->close();
    $conn?->close();
}
/* -------------  END OF LOGIN SCRIPT ------------- */

/* Everything below is your *unchanged* UI  */
OUTPUT_HTML:
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login Form test</title>
    <style>
        /* =========  ORIGINAL CSS (unchanged) ========= */
        *{margin:0;padding:0;box-sizing:border-box;}
        
        body{
            background-image: url(sq.jpg);
            background-size: cover; 
            background-position: center;
            background-repeat: no-repeat;
            height: 795px;
        }
        .container{
            display:flex;
            justify-content:center;
            align-items:center;
        }
        .main-box1{
            width:500px;
            height:400px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius:20px;
            margin-top:260px;
            border: 3px solid white;
            color:white
        }
        h1{
            text-align:center;
            text-decoration:underline;
            margin-top:40px;
            font-family:'Gill Sans','Gill Sans MT',Calibri,'Trebuchet MS',sans-serif;
            color: white;
        }
        label{
            color: white;
        }
        input{
            width:350px;
            height:30px;
            margin-top:10px;
            border-radius:20px;
            padding:0 12px;
            outline: none;
            background-color: transparent;
            box-shadow:inset 0 0 92px 13px rgba(0,0,0,.1);
            border: 1px solid white;
            color:white;
        }
        .box-1{
            margin-top:23px;
            margin-left:70px;
            font-family:'Gill Sans','Gill Sans MT',Calibri,'Trebuchet MS',sans-serif;
        }
        button{
            width:350px;
            height:30px;
            background-color: transparent;
            border: 1px solid white;
            border-radius:50px;
            font-family:'Gill Sans','Gill Sans MT',Calibri,'Trebuchet MS',sans-serif;
            font-size:medium; 
            margin-top: 25px;
            color: white;
        }
        .message{
            text-align:center;
            font-weight:bold;
            margin-top:10px;
        }
        .success{
            color:green;
            display:flex;
            justify-content:start;
            font-size:15px;
        }
        .error{
            color:red;
            display:flex;
            justify-content:start;
            font-size:15px;
        }
        .output-box{
            font-family:Arial,sans-serif;
            text-align:center;
            margin-top:20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="main-box1">
        <h1>Login Form</h1>
        <div class="box-1">
            <form method="post" action="">
                <!-- <label for="name">Name:</label><br>
                <input type="text" id="name" name="name" placeholder="Enter Your Name"
                       value="<?php echo htmlspecialchars($name); ?>"><br>
                <span class="error"><?php echo $nameError; ?></span><br> -->

                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" placeholder="Enter Your Email"
                       value="<?php echo htmlspecialchars($email); ?>"><br>
                        <span class="error"><?php echo $emailError; ?></span><br>

                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" placeholder="Enter Your Password"><br>
                <span class="error"><?php echo $passError; ?></span><br>

                <!-- <label for="copass">Confirm Password:</label><br>
                <input type="password" id="copass" name="copass" placeholder="(Not required for login)">
                <span class="error"><?php echo $conpassError; ?></span> -->

              
                <button type="submit" name="submit">Submit</button><br>

                  <?php if ($recordMessage): ?>
                    <div class="message <?php echo $recordClass; ?>"><?php echo $recordMessage; ?></div>
                <?php endif; ?>
                <br><br>
            </form>
        </div>
    </div>
</div>
</body>
</html>
