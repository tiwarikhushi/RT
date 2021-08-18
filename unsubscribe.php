<?php

    require_once 'DBConnection.php';

    if(isset($_GET['email']) && !empty($_GET['Email']) && isset($_GET['hash']) && !empty($_GET['hash'])) {
        $Email = $_GET['Email'];
        $hash = $_GET['hash'];
        $conn = Database::getInstance()->getConnection();
        if(!$conn){
            die('Connection not Established');
        }
        $verify = 1;
        $stmt = $conn->prepare("SELECT * FROM Data WHERE Email = ? AND hash = ? AND verify = ?");
        $stmt->bind_param("ssi", $Email, $hash, $verify);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $stmt = $conn->prepare("DELETE FROM Data WHERE Email = ? AND hash = ?");
            $stmt->bind_param("ss", $Email, $hash);
            $stmt->execute();
            if($stmt->affected_rows > 0){
                echo "
                    <script>alert('Unsubscribe done successfully');
                    window.location.href = 'index.php';
                    </script>
                ";
            } else {
                echo "Unsubscribe Failed";
            }
        } else {
            echo "User Not Found.";
        }

    }