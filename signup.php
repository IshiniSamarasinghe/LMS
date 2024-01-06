<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$database = "library_test";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$update = false;
$user_id = 0;
$first_name = "";
$last_name = "";
$username = "";
$password = "";
$email = "";

if (isset($_POST['submit'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST["first_name"]);
    $last_name = mysqli_real_escape_string($conn, $_POST["last_name"]);
    $username = mysqli_real_escape_string($conn, $_POST["username"]); // Add this line
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);

    $user_id = $_POST["user_id"];

    // Check if the user ID already exists
    $checkQuery = "SELECT * FROM user WHERE user_id = '$user_id'";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        // user ID already exists, update the existing record
        $updateQuery = "UPDATE user SET first_name='$first_name', last_name='$last_name', username='$username', password='$password', email='$email' WHERE user_id = '$user_id'";

        $conn->query($updateQuery) or die($conn->error);
        $_SESSION['message'] = "Record has been updated!";
    } else {
        // user ID doesn't exist, insert a new record
        $insertQuery = "INSERT INTO user (user_id, first_name, last_name, username, password, email) VALUES ('$user_id', '$first_name', '$last_name', '$username', '$password', '$email')";
        $conn->query($insertQuery) or die($conn->error);
        $_SESSION['message'] = "Record has been saved!";
    }

    $_SESSION['msg_type'] = "warning";
    header("Location: index.php");
    exit();
}

if (isset($_GET['delete_user_id'])) {
    $user_id = $_GET['delete_user_id'];

    // Use prepared statement to avoid SQL injection
    $stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();

    $_SESSION['message'] = "Record has been deleted!";
    $_SESSION['msg_type'] = "danger";
    $stmt->close();
    header("Location: index.php");
    exit();
}


if (isset($_GET['edit_user_id'])) {
    $user_id = $_GET['edit_user_id'];
    $update = true;

    // Retrieve other details
    $first_name = $_GET['first_name'];
    $last_name = $_GET['last_name'];
    $username = $_GET['username'];
    $password = $_GET['password'];
    $email = $_GET['email'];

    // Rest of your code for editing...
}

// Add this condition to handle the update
if (isset($_POST['submit']) && isset($_GET['edit_user_id'])) {
    $user_id = $_GET['edit_user_id'];
    $first_name = mysqli_real_escape_string($conn, $_POST["first_name"]);
    $last_name = mysqli_real_escape_string($conn, $_POST["last_name"]);
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);

    // Perform the update in the database
    $updateQuery = "UPDATE user SET first_name='$first_name', last_name='$last_name', username='$username', password='$password', email='$email' WHERE user_id = '$user_id'";
    $conn->query($updateQuery) or die($conn->error);

    $_SESSION['message'] = "Record has been updated!";
    $_SESSION['msg_type'] = "warning";
    header("Location: index.php");
    exit();
}

if (isset($_POST['submit'])) {
    // ... (existing logic for handling form submission)
}

if (isset($_POST['logout'])) {
    // Logout logic
    session_start();
    $_SESSION = array();
    session_destroy();
    header("Location: User/dashboard.html");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #4caf50;
            margin: 0;
            padding: 0;
            display: grid;
            align-items: center;
            justify-content: center;
            block-size: 100vh;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            inline-size: 700px;
            margin-inline-start: 200px;

        }

        label {
            display: block;
            margin-block-end: 8px;
        }

        input {
            inline-size: 100%;
            padding: 8px;
            margin-block-end: 16px;
            box-sizing: border-box;
        }

        button {
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
            inline-size: 100%;
            block-size: 40px;
        }

        button:hover {
            background-color: #45a049;
        }

        a {
            display: block;
            margin-block-start: 20px;
            text-align: center;
            color: #333;
            text-decoration: none;
        }

        table {
            inline-size: 100%;
            border-collapse: collapse;
            margin-block-start: 10px;
            background-color: white;

        }

        th,
        td {
            padding: 30px;
            text-align: start;
            border-block-end: 1px solid black;
            height:8px;

        }

        th {
            background-color: black;
            color: white;
            font-size: 15px;
            height:8px;
            text-align:center;
        }

        th:hover {
            background-color: white;
            color: black;
        }

        .edit,
        .delete {
            background-color: #2196F3;
            color: #fff;
            padding: 8px;
            border: none;
            /* Remove borders */
            cursor: pointer;
        }

        .container-table {
            max-inline-size: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        }
        hr.new {
            border: 2px solid green;
            border-radius: 5px;
}

       
    </style>
</head>

<body>
    <form id="staffForm" action="index.php" method="post">
        <h1 style="text-align:center;">UOK LIBRARY MANAGEMENT SYSTEM</h1>
         
        <hr>
        <h2 style="text-align:center;">Login and User Registration</h2>
         
        <label for="user_id">user_id:</label>
        <input type="text" name="user_id" id="user_id" required>

        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" required>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" required>

        <label for="username">username:</label>
        <input type="text" name="username" id="username" required value="">

        <label for="password">password:</label>
        <input type="password" name="password" id="password" required value="">


        <label for="email">Email Address:</label>
        <input type="email" name="email" id="email" required>

        <button type="submit" name="submit">Register Staff</button>

        <a href="login.php">Already have an account? <b>Login here</b></a><br><hr class="new">
        <?php
            if (isset($_SESSION['username'])) {
                // If the user is logged in, show the logout link or button
                echo '<a href="dashboard.html"><b>LOG OUT FROM THE SYSTEM</b></a>';
            }
        ?>
    </form>

    <script>
        function registerStaff() {
            var user_id = document.getElementById('user_id').value;
            var first_name = document.getElementById('first_name').value;
            var last_name = document.getElementById('last_name').value;
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;
            var email = document.getElementById('email').value;

            // Validate input
            if (!user_id || !first_name || !last_name || !username || !password || !email) {
                alert('All fields are required.');
                return;
            }
            var user_idRegex = /^U\d{3}$/;
            if (!user_idRegex.test(user_id)) {
                alert('Invalid User ID format. Please use "U" followed by three digits (e.g., U001).');
                return;
            }

            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Invalid email format. Please enter a valid email address.');
                return;
            }

            if (password.length < 8) {
                alert('password must be more than 8 characters.');
                return;
            }

            // Mock API call (you can replace this with an actual API call)
            alert('Staff registered successfully:\n\nUserID: ' + user_id + '\nName: ' + first_name + ' ' + last_name + '\nusername: ' + username + '\npassword: ' + password + '\nemail: ' + email);

            // Clear form fields
            document.getElementById('user_id').value = '';
            document.getElementById('first_name').value = '';
            document.getElementById('last_name').value = '';
            document.getElementById('username').value = '';
            document.getElementById('password').value = '';
            document.getElementById('email').value = '';
        }


        function editUser(user_id, first_name, last_name, username, password, email) {
            // Fill in the form fields with the user details
            document.getElementById('user_id').value = user_id;
            document.getElementById('first_name').value = first_name;
            document.getElementById('last_name').value = last_name;
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
            document.getElementById('email').value = email;

            // Change the form action to indicate that it's an update operation
            document.getElementById('staffForm').action = 'index.php?edit_user_id=' + user_id;

            // Change the button text and function to indicate an update operation
            document.querySelector('button[name="submit"]').innerText = 'Update Staff';
            document.querySelector('button[name="submit"]').onclick = function () {
                // Implement the logic for updating a user
                updateUserData(user_id);
            };
        }





        function updateUserData(user_id) {
            // Collect updated data from the form
            var formData = new FormData(document.getElementById('staffForm'));

            // Make an AJAX request to update the user data
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'process_form.php?edit_user_id=' + user_id, true);
            xhr.onload = function () {
                // Parse the response JSON
                var response = JSON.parse(xhr.responseText);

                // Check if the update was successful
                if (response.success) {
                    // Update the table content with the new data
                    updateTable(user_id, response.updatedData);
                    alert('User data updated successfully!');
                } else {
                    alert('Failed to update user data. Please try again.');
                }
            };
            xhr.send(formData);
        }

        function updateTable(user_id, updatedData) {
            // Find the row with the matching user_id
            var row = document.querySelector('tr[data-user-id="' + user_id + '"]');

            // Update the content of the row with the new data
            row.innerHTML = updatedData;

            // Reset the form action and button text for adding new users
            document.getElementById('staffForm').action = 'process_form.php';
            document.querySelector('button[name="submit"]').innerText = 'Register Staff';
            document.querySelector('button[name="submit"]').onclick = function () {
                // Implement the logic for registering a new user
                registerStaff();
            };
        }








        function refreshTable() {
            // Make an AJAX request to fetch the updated data
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'display_Users.php', true);
            xhr.onload = function () {
                // Replace the table content with the updated data
                document.querySelector('tbody').innerHTML = xhr.responseText;
            };
            xhr.send();
            // Implement the logic to refresh the table or reload the page
            // You can use additional AJAX to fetch updated data and replace the table content
            location.reload(); // Simple method to reload the entire page
        }

        function deleteUserr(user_id) {
            if (confirm('Are you sure you want to delete this user?')) {
                // Make an AJAX request to delete the user data
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'index.php?delete_user_id=' + user_id, true);
                xhr.onload = function () {
                    // Refresh the table after successful delete
                    refreshTable();
                    alert('User data deleted successfully!');
                };
                xhr.send();
            }
        }

        function refreshTable() {
            // Implement the logic to refresh the table or reload the page
            // You can use additional AJAX to fetch updated data and replace the table content
            location.reload(); // Simple method to reload the entire page
        }
       
    </script>
    <br>
    <br>
    <br>


    <div class="container-table">
    <h2 style="text-align: center">Users List</h2>
    <table>
        <thead>

            <tr>

                <th>
                    <centre>User ID</centre>
                </th>
                <th>
                    <centre>First Name</centre>
                </th>
                <th>
                    <centre>Last Name</centre>
                </th>
                <th>
                    <centre>Username</centre>
                </th>
                <th>
                    <centre>Password</centre>
                </th>
                <th>
                    <centre>Email</centre>
                </th>
                <th>
                    <centre>Action</centre>
                </th>
            </tr>
        </thead>

        <tbody>
            
 


            <?php
         
            $servername = "localhost";
            $username = "root";
            $password = "";
            $database = "library_test";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $database);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
           


            $result = $conn->query("SELECT * FROM user");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["user_id"] . "</td>";
                    echo "<td>" . $row["first_name"] . "</td>";
                    echo "<td>" . $row["last_name"] . "</td>";
                    echo "<td>" . $row["username"] . "</td>";
                    echo "<td>" . $row["password"] . "</td>";
                    echo "<td>" . $row["email"] . "</td>";

                    echo "<td>";
                    echo "<button class='edit' onclick='editUser(\"$row[user_id]\", \"$row[first_name]\", \"$row[last_name]\", \"$row[username]\", \"$row[password]\",\"$row[email]\")'>Edit</button>&nbsp";
                    echo "<button class='delete' onclick='deleteUserr(\"" . $row['user_id'] . "\")'>Delete</button>";
                    echo "</td>";
                    echo "</tr>";
                }
            }
            


            ?>
        </tbody>
    </table>
    
    </div>
</body>

</html>