<?php 

$recordMessage = "";
$recordClass = "";
$name = $email = $pass = $conpass = "";

$nameError = $emailError = $passError = $conpassError = "";

function input_data($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Validation
    $isValid = true;

    if (empty($_POST['name'])) {
        $nameError = "*Name is required";
        $isValid = false;
    } else {
        $name = input_data($_POST['name']);
        if (!preg_match("/^[a-zA-Z0-9' ]*$/", $name)) {
            $nameError = "Only letters and white space allowed";
            $isValid = false;
        }
    }

    if (empty($_POST['email'])) {
        $emailError = "*Email is required";
        $isValid = false;
    } else {
        $email = input_data($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailError = "Invalid email format";
            $isValid = false;
        }
    }

    if (empty($_POST['password'])) {
        $passError = "*Password is required";
        $isValid = false;
    } else {
        $pass = input_data($_POST['password']);
        if (strlen($pass) < 6) {
            $passError = "Password must be at least 6 characters long";
            $isValid = false;
        }
    }

    if (empty($_POST['copass'])) {
        $conpassError = "*Confirm Password is required";
        $isValid = false;
    } else {
        $conpass = input_data($_POST['copass']);
        if ($pass !== $conpass) {
            $conpassError = "Passwords do not match";
            $isValid = false;
        }
    }

    // If all validation passed, insert into DB and redirect
    if ($isValid) {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "user_data";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $query = "INSERT INTO `myform`(`name`, `email`, `password`, `confirmpass`) VALUES ('$name','$email','$pass','$conpass')";
        if ($conn->query($query) === TRUE) {
            // Redirect to login page after 3 seconds
            header("Location: http://localhost/wel_come/first.php");
            $recordMessage = "Registration successful! Redirecting to login...";
            $recordClass = "success";
        } else {
            $recordMessage = "Database error: " . $conn->error;
            $recordClass = "error";
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registration Form</title>
    <style>
        /* ... [same style as you provided, no change] ... */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --clr-primary: #6c5ce7;
            --clr-secondary: #00b894;
            --clr-bg: #f1f2f6;
            --clr-text: #2d3436;
            --radius: 0.75rem;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            --transition: 200ms ease;
            font-size: 16px;
            font-family: "Poppins", sans-serif;
            color: var(--clr-text);
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--clr-primary), var(--clr-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .main-box1 {
            width: 500px;
            height: 620px;
            background-color: transparent;
            border: 2px solid white;
            border-radius: 20px;
            margin-top: 20px;
        }

        h1 {
            text-align: center;
            text-decoration: underline;
            color: white;
            margin-top: 40px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        input {
            width: 350px;
            height: 40px;
            margin-top: 10px;
            border-radius: 20px;
            padding: 0 12px;
            border-color: white;
            background-color: transparent;
            color: white;
        }

        label {
            color: white;
        }

        ::placeholder {
            color: white;
        }

        .box-1 {
            margin-top: 23px;
            margin-left: 70px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        button[type="submit"] {
            background: var(--clr-primary);
            border: none;
            color: #fff;
            padding: 0.9rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 20px;
            cursor: pointer;
            transition: var(--transition);
            width: 350px;
            height: 40px;
        }

        button[type="submit"]:hover {
            background: var(--clr-secondary);
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
        }

        .success {
            color: green;
            display: flex;
            justify-content: start;
            font-size: 15px;
        }

        .error {
            color: red;
            font-weight: bold;
            font-size: 15px;
            display: flex;
            justify-content: start;
        }

        .meta {
            text-align: center;
            font-size: 0.875rem;
            color: white;
            margin-top: 10px;
            margin-right: 210px;
        }

        .meta a {
            text-decoration: none;
            color: white;
            transition: var(--transition);
            border-bottom: 1px solid transparent;
        }

        .meta a:hover {
            border-color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="main-box1">
            <h1>Registration Form</h1>
            <div class="box-1">
                <form method="post" action="">
                    <label for="Name">Name:</label><br>
                    <input type="text" id="name" name="name" placeholder="Enter Your Name" value="<?php echo htmlspecialchars($name); ?>"><br>
                    <span class="error"><?php echo $nameError; ?></span><br>

                    <label for="Email">Email:</label><br>
                    <input type="email" id="email" name="email" placeholder="Enter Your Email" value="<?php echo htmlspecialchars($email); ?>"><br>
                    <span class="error"><?php echo $emailError; ?></span><br>

                    <label for="Password">Password:</label><br>
                    <input type="password" id="password" name="password" placeholder="Enter Your Password" value="<?php echo htmlspecialchars($pass); ?>"><br>
                    <span class="error"><?php echo $passError; ?></span><br>

                    <label for="Confirm">Confirm Password:</label><br>
                    <input type="password" id="copass" name="copass" placeholder="Enter Your Confirm Password" value="<?php echo htmlspecialchars($conpass); ?>">
                    <span class="error"><?php echo $conpassError; ?></span><br>

                    <?php if (!empty($recordMessage)) { ?>
                        <div class="message <?php echo $recordClass; ?>"><?php echo $recordMessage; ?></div>
                    <?php } ?>
                    <br>

                    <button type="submit" name="submit">Submit</button>

                    <p class="meta">
                        Already have an account ? <a href="first.php">Sign in</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>