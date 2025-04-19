<?php
$password = 'MyS3cur3P@ssword#123'; // Replace with your chosen password  for user 
$password1 = 'SecureP@ssw0rd2024!'; // Replace with your chosen password   for manager 
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$hashedPassword1 = password_hash($password1, PASSWORD_DEFAULT);
echo "for user" .$hashedPassword;
echo "for manager" .$hashedPassword1;
?>


