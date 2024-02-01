<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Submission</title>
</head>
<body>
    <p>First Name: {{ $data['first_name'] }}</p>
    <p>Last Name: {{ $data['last_name'] }}</p>
    <p>Email: {{ $data['email'] }}</p>
    <p>Phone Number: {{ $data['phone_number'] }}</p>
    <p>Message: {{ $data['message'] }}</p>
</body>
</html>
