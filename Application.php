<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=lab_application", "root", "");
} catch ( PDOException $e ){
    echo "Something went wrong.: ".$e->getMessage();
}
$insert = $db->prepare("INSERT INTO students (full_name, email, gender) VALUES (?, ?, ?)");
$isThereEmail = $db->prepare("SELECT * FROM students WHERE email = ?");
$readStudents = $db->prepare("SELECT * FROM students");

function form($name="", $email="", $gender=""){
    $male = $gender == "Male" ? "checked" : "";
    $female = $gender == "Female" ? "checked" : "";
    echo '<br><br>
<form action="Application.php" method="POST">
    <table style="border=1px solid black;">
        <tr>
            <td colspan="2">Fill in the following information:</td>
        </tr>
        <tr>
            <td>Full Name:</td>
            <td><input type="text" name="name" value="'.$name.'"></td>
        </tr>
        <tr>
            <td>Email:</td>
            <td><input type="email" name="email" value="'.$email.'"></td>
        </tr>
        <tr>
            <td>Gender:</td>
            <td>
                <input type="radio" id="male" name="gender" value="Male" '.$male.'>
                <label for="male">Male</label>
                <input type="radio" id="female" name="gender" value="Female" '.$female.'>
                <label for="female">Female</label>
            </td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" value="Submit"></td>
        </tr>
    </table>    
</form>';
}
function showData($data){
    echo '<br><br>
    <div class="data">
        <table>
            <thead>
                <td>Name</td>
                <td>Email</td>
                <td>Gender</td>
            </thead>
    ';
    foreach ($data as $datum){
        echo '
            <tr>
                <td>'.$datum["full_name"].'</td>
                <td>'.$datum["email"].'</td>
                <td>'.$datum["gender"].'</td>
            </tr>
        ';
    }
    echo '
        </table>
    </div>
    ';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Application</title>
    <style type="text/css">
        .data table{
            border: 1px solid black;
        }
        .data table thead{
            text-align: center;
        }
        .data td{
            border: 1px solid black;
            padding: 0 10px 0;
        }
    </style>
</head>
<body>
<?php
if(!($_POST))
    form();
else{
    $message = "";
    $name = $_POST["name"];
    $nameValid = preg_match("/[a-zA-ZıİğĞüÜşŞöÖçÇ]+ [a-zA-ZıİğĞüÜşŞöÖçÇ]+/", $name);
    $email = $_POST["email"];
    $emailValid = preg_match("/\w+@\w+\.\w+/", $email);
    $isThereEmail->execute(array($email));
    $gender = isset($_POST["gender"]) ? $_POST["gender"] : "";

    $message .= $nameValid ? "" : "Check the full name section!<br>";
    $message .= $emailValid ? "" : "Be sure that the email address is in the correct form!<br>";
    $message .= count($isThereEmail->fetchAll()) > 0 ? "This e-mail address is already registered.<br>" : "";
    $message .= $gender == "" ? "Select one of the gender options!<br>" : "";

    if ($message == ""){
        if ($insert->execute(array($name, $email, $_POST["gender"])))
            echo "Successfully Saved";
        else
            echo "Something went wrong. Please try again later.";
    }
    else{
        echo $message;
        form($name, $email, $gender);
    }
}
$readStudents->execute();
showData($readStudents->fetchAll());
?>
</body>
</html>
