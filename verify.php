<?php

    require_once 'DBConnection.php';

    if(isset($_GET['Email']) && !empty($_GET['Email']) && isset($_GET['hash']) && !empty($_GET['hash'])){
        $conn = Database::getInstance()->getConnection();
        $email = htmlspecialchars($_GET['Email']);
        $hash = htmlspecialchars($_GET['hash']);
        if(!$conn){
            die('Connection not Established');
        }
        $stmt = $conn->prepare("SELECT * FROM Data WHERE Email = ? AND hash = ?");
        $stmt->bind_param("ss", $Email, $hash);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $stmt = $conn->prepare("UPDATE Data SET verify = 1 WHERE Email = ? AND hash = ?");
            $stmt->bind_param("ss", $Email, $hash);
            $stmt->execute();
            if($stmt->affected_rows > 0){
                echo "
                    <script>alert('Verification done successfully');
                    window.location.href = 'index.php';
                    </script>
                ";
            } else {
                if($stmt->errno == 0){
                    echo "Already Verified";
                } else {
                    echo "Verificaiton Failed";
                }
            }
        } else {
            echo "Email Not Found.";
        }
    }