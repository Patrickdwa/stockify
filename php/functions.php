<?php
require 'config/conn.php';

function signup($data) {
    global $pdo; // Use the correct variable from conn.php

    // Sanitize input
    $email = strtolower(trim($data["email"]));
    $username = strtolower(trim($data["username"]));
    $password = $data["password"];
    $confirmPassword = $data["confirmPassword"];

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT email FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->fetch()) {
        echo "<script>
                alert('Email already registered.');
            </script>";
        return false;
    }

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT username FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    if ($stmt->fetch()) {
        echo "<script>
                alert('Username already registered.');
            </script>";
        return false;
    }

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "<script>
                alert('Passwords do not match.');
            </script>";
        return false;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $stmt = $pdo->prepare("INSERT INTO users (email, username, password) VALUES (:email, :username, :password)");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>
                alert('User registered successfully!');
            </script>";
        return true;
    } else {
        echo "<script>
                alert('Registration failed.');
            </script>";
        return false;
    }
}

function search($keyword) {
    global $pdo;
    $sql = "SELECT * FROM product 
            WHERE LOWER(name) LIKE LOWER(:keyword) 
            OR LOWER(category) LIKE LOWER(:keyword) 
            OR LOWER(CAST(price AS TEXT)) LIKE LOWER(:keyword) 
            OR LOWER(CAST(quantity AS TEXT)) LIKE LOWER(:keyword) 
            OR LOWER(description) LIKE LOWER(:keyword)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
