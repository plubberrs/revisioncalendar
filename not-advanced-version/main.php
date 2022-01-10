<!DOCTYPE html>
<html>
<?php

include 'Calendar.php';
$calendar = new Calendar();
$date = date('Y-m-d');
$subject_map = array();
$subject_map['Math'] = 'red';
$subject_map['Biology'] = 'green';
$subject_map['Chemistry'] = 'yellow';
$subject_map['Physics'] = 'blue';
$subject_map['English'] = 'orange';
$subject_map['Social'] = 'pink';
$subject_map['Geography'] = 'purple';
$subject_map['History'] = 'brown';

$servername = "localhost";
$username = "root";
$password = "EKR/qIzEovgfMO4I";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// echo "<script>alert('Connected successfully')</script>";

$sql = "SELECT * FROM revisiontimetable.input";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    // echo "username: " . $row["username"]. " - subject: " . $row["subject"]. " - topic: " . $row["topic"]. " - date learnt: " . $row["date_learnt"]. "<br>";
	$calendar->add_event($row['topic'] . '1', $row['first_review'], 1, $subject_map[$row['subject']]);
	$calendar->add_event($row['topic'] . '2', $row['second_review'], 1, $subject_map[$row['subject']]);
	$calendar->add_event($row['topic'] . '3', $row['third_review'], 1, $subject_map[$row['subject']]);
	$calendar->add_event($row['topic'] . '4', $row['fourth_review'], 1, $subject_map[$row['subject']]);
  }
} else {
  // echo "<script>alert('0 results')</script>";
}

if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['submit'])) {
	$sql = "INSERT INTO revisiontimetable.input
	(username, subject, topic, date_learnt, first_review, second_review, third_review, fourth_review)
	VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("ssssssss",
	$client_username, $client_subject, $client_topic, $date_learnt,
	$first_review, $second_review, $third_review, $fourth_review);
	
	$client_username = $_POST['username'];
	$client_subject = $_POST['subject'];
	$client_topic = $_POST['topic'];
	$date_learnt = date('Y-m-d H:i:s', $phptime);
	$first_review = date('Y-m-d H:i:s', strtotime('+1 day'));
	$second_review = date('Y-m-d H:i:s', strtotime('+3 day'));
	$third_review = date('Y-m-d H:i:s', strtotime('+7 day'));
	$fourth_review = date('Y-m-d H:i:s', strtotime('+14 day'));

	if ($stmt->execute() === TRUE) {
		// echo "<script>alert('New record created successfully')</script>";
	} else {
		echo "<script>alert('Error')</script>";
	}
	
	$calendar = new Calendar();
	$sql = "SELECT * FROM revisiontimetable.input";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
  		// output data of each row
  		while($row = $result->fetch_assoc()) {
    		// echo "username: " . $row["username"]. " - subject: " . $row["subject"]. " - topic: " . $row["topic"]. " - date learnt: " . $row["date_learnt"]. "<br>";
			$calendar->add_event($row['topic'] . '1', $row['first_review'], 1, $subject_map[$row['subject']]);
			$calendar->add_event($row['topic'] . '2', $row['second_review'], 1, $subject_map[$row['subject']]);
			$calendar->add_event($row['topic'] . '3', $row['third_review'], 1, $subject_map[$row['subject']]);
			$calendar->add_event($row['topic'] . '4', $row['fourth_review'], 1, $subject_map[$row['subject']]);
		}
	} else {
  		echo "<script>alert('0 results')</script>";
	}
}

?>

	<head>
		<meta charset="utf-8">
		<title>Revision Calendar</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link href="calendar.css" rel="stylesheet" type="text/css">
		<link href="form_style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	    <nav class="navtop">
	    	<div>
	    		<h1>Revision Calendar</h1>
	    	</div>
	    </nav>
		<div class="content home" style="display: flex;">
			<div> <?=$calendar?> </div>
			<div> 
				<form action="calendar_test.php" method="post">
					<label for="username">Username</label>
    				<input type="text" id="username" name="username" placeholder="Your username...">

   					<label for="topic">Topic</label>
   					<input type="text" id="topic" name="topic" placeholder="Today's material...">

					<label for="subject">Subject</label>
  					<select id="subject" name="subject">
  						<option value="Math">Math</option>
    					<option value="Biology">Biology</option>
						<option value="Chemistry">Chemistry</option>
						<option value="Physics">Physics</option>
    					<option value="English">English</option>
						<option value="Social">Social</option>
						<option value="Geography">Geography</option>
						<option value="History">History</option>
    				</select>

    				<input type="submit" name="submit" value="Add material" />
				</form>
			</div>
		</div>
	</body>
</html>