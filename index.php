<?php
session_start();

require('./conn.php');
// Reads the variables sent via POST
$sessionId   = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$text = $_POST["text"];

// This is the first menu screen
if ($text == "") {
    $response = "CON Welcome to the NCHE Application Check System \n";
    $response .= "1. Sign Up \n";
    $response .= "2. Login \n";
    $response .= "3. Check Eligibility \n";
}

// Sign Up
else if ($text == "1") {
    $response = "CON Enter your desired username: \n";
    $_SESSION['state'] = 'signup_username';
} else if ($_SESSION['state'] == 'signup_username') {
    $username = $text;
    $response = "CON Enter your desired password: \n";
    $_SESSION['username'] = $username;
    $_SESSION['state'] = 'signup_password';
} else if ($_SESSION['state'] == 'signup_password') {
    $password = $text;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (username, userpassword) VALUES (?, ?)");
    $stmt->execute([$username, $hashedPassword]);
    $response = "END Sign-up successful!";
    $_SESSION['state'] = '';
}

// Login
else if ($text == "2") {
    $response = "CON Enter your username: \n";
    $_SESSION['state'] = 'login_username';
} else if ($_SESSION['state'] == 'login_username') {
    $username = $text;
    $response = "CON Enter your password: \n";
    $_SESSION['username'] = $username;
    $_SESSION['state'] = 'login_password';
} else if ($_SESSION['state'] == 'login_password') {
    $password = $text;
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $response = "END Login successful!";
        $_SESSION['loggedIn'] = true;
    } else {
        $response = "END Invalid username or password.";
    }
    $_SESSION['state'] = '';
}

// Check Eligibility
else if ($text == "3") {
    $response = "CON Enter your grade for the first subject: \n";
    $_SESSION['state'] = 'grade_1';
}


// Array of secondary school subjects
$subjects = [
    "Mathematics",
    "English",
    "Science",
    "History",
    "Geography",
    "Physical Education",
    "Art",
    "Music",
    "Computer Studies",
    "French",
    "Religious Studies"
];

// Loop through each subject
foreach ($subjects as $subject) {
    // Check if the user has this subject
    $response = "CON Do you have a grade for $subject? (Enter 1 for Yes, 0 for No): \n";
    echo $response;
    // Assuming you have a way to get the user's response, e.g., through a form or API call
    $userResponse = $_POST["userResponse"]; // This is a placeholder. Replace with actual method to get user response.

    if ($userResponse == "1") {
        // Prompt the user to enter the grade for this subject
        $response = "CON Enter your grade for $subject: \n";
        echo $response;
        // Assuming you have a way to get the user's grade, e.g., through a form or API call
        $userGrade = $_POST["userGrade"]; // This is a placeholder. Replace with actual method to get user grade.

        // Insert the subject and grade into the database
        $stmt = $db->prepare("INSERT INTO grades (subjectname, subjectgrade) VALUES (?, ?)");
        $stmt->execute([$subject, $userGrade]);
    } else {
        // Remove the subject from the list
        $key = array_search($subject, $subjects);
        if ($key !== false) {
            unset($subjects[$key]);
        }
    }
}

// This is the first menu screen
if ($_POST["text"] == "") {
    $response = "CON Welcome to the University Program Finder! \n";
    $response .= "Enter a paragraph describing your personality traits and interests: \n";
    $_SESSION['state'] = 'input_personality';
} else if ($_SESSION['state'] == 'input_personality') {
    $personalityDescription = $_POST["text"];
    // Here, you would use NLP to extract keywords from $personalityDescription
    // For simplicity, let's assume you have a function called extractKeywords()
    $keywords =  $personalityDescription;
    // Fetch university programs based on the keywords
    $stmt = $db->prepare("SELECT * FROM university_programs WHERE MATCH(descriptions) AGAINST(? IN BOOLEAN MODE) LIMIT 3");
    $stmt->execute([implode(' ', $keywords)]);
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format and display the results
    $response = "END Here are some university programs that might interest you: \n";
    foreach ($programs as $program) {
        $response .= "Program: " . $program['name'] . "\n";
        $response .= "Description: " . $program['description'] . "\n\n";
    }

    // Reset the session state
    $_SESSION['state'] = '';
}


//echo response
header('Content-type: text/plain');
echo $response;
