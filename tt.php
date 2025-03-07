<?php
// Database connection
$servername = "localhost";
$username = "root"; // default XAMPP MySQL username
$password = ""; // default XAMPP MySQL password (empty by default)
$dbname = "journal_db"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Default message
$message = "You didn't write anything that day.";

// If form is submitted to save an entry
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $entry = $_POST['entry'];

    // Insert the journal entry into the database
    $stmt = $conn->prepare("INSERT INTO entries (date, entry) VALUES (?, ?)");
    $stmt->bind_param("ss", $date, $entry);
    $stmt->execute();
    $stmt->close();
}

// Fetch the journal entry for the selected date
if (isset($_GET['date'])) {
    $selectedDate = $_GET['date'];

    // Retrieve the entry from the database
    $stmt = $conn->prepare("SELECT entry FROM entries WHERE date = ?");
    $stmt->bind_param("s", $selectedDate);
    $stmt->execute();
    $stmt->bind_result($journalEntry);

    if ($stmt->fetch()) {
        $message = $journalEntry;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Journal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7ece1;
            padding: 20px;
        }
        .journal-container {
            border: 2px solid pink;
            padding: 20px;
            background-color: white;
            margin-top: 20px;
        }
        .message {
            padding: 10px;
            border: 1px dashed #f7c7e7;
        }
        input[type="date"], textarea {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background-color: pink;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h1>Journal Entry</h1>

<!-- Form to save a journal entry -->
<form method="POST">
    <label for="date">Choose a date to write:</label><br>
    <input type="date" name="date" required><br>

    <label for="entry">Write your entry:</label><br>
    <textarea name="entry" rows="4" placeholder="Write something here..." required></textarea><br>

    <input type="submit" value="Save Entry">
</form>

<h2>View Journal Entry</h2>
<!-- Form to view the journal entry for a specific date -->
<form method="GET">
    <label for="date">Select a date to view your entry:</label><br>
    <input type="date" name="date" required><br>

    <input type="submit" value="View Entry">
</form>

<div class="journal-container">
    <p class="message"><?php echo $message; ?></p>
</div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
