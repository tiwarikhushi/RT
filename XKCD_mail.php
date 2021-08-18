<?php

    require_once 'DBConnection.php';

    $conn = Database::getInstance()->getConnection();
    if(!$conn){
        die('Connection not Established');
    }

    $url_random_xkcd = "https://c.xkcd.com/random/comic/";
    $data = file_get_contents($url_random_xkcd);

    preg_match('/<meta property="og:url" content="https:\/\/xkcd.com\/\d*/', $data, $match);

    $img = substr($match[0], strrpos($match[0], '/') + 1);

    $url_xkcd = "https://xkcd.com/".$img."/info.0.json";
    $data = file_get_contents($url_xkcd);
    $json_data = json_decode($data, true);

    $img_url = $json_data['img'];

    file_put_contents($img_path, file_get_contents($img_url));

    $subject = $json_data['safe_title'];

    $verify = 1;

    $stmt = $conn->prepare("SELECT * FROM Data WHERE verify = ?");
    $stmt->bind_param("i", $verify);
    $stmt->execute();
    $results = $stmt->get_result();

    foreach($results as $row ){

        $to = $row['Email'];
        $hash = $row['hash'];

        $htmlContent = "
                <html>
                    <head>
                        <title>XKCD Emoticon</title>
                    </head>
                    <body>
                        <center>
                            <h1>" . $json_data['title'] . "</h1>
                            <img src='". $img_url ."' alt='". $json_data['alt'] ."'>
                            <a href='http://xkcd.in/unsubscribe.php?email=$to&hash=$hash'><h3>Unsubscribe XKCD</h3></a>
                        </center><br/>
                    </body>
                </html>
            ";

        $headers = "From: khushitiwari2001@gmail.com";

        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

        $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
            "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n";

        if(!empty($img_path)){
            if(file_exists($img_path)) {
                $message .= "--{$mime_boundary}\n";
                $fp = @fopen($img_path, "rb");
                $data = @fread($fp, filesize($img_path));

                @fclose($fp);
                $data = chunk_split(base64_encode($data));
                $message .= "Content-Type: application/octet-stream; name=\"" . basename($img_path) . "\"\n" .
                    "Content-Description: " . basename($img_path) . "\n" .
                    "Content-Disposition: attachment;\n" . " filename=\"" . basename($img_path) . "\"; size=" . filesize($img_path) . ";\n" .
                    "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
            } else {
                echo "File Not Found";
            }
        }else{
            echo "Image Url Empty";
        }

        $message .= "--{$mime_boundary}--";
        $mail_result = mail($to, $subject, $message, $headers);
        echo $mail_result?"<h1>Email Sent Successfully!</h1>":"<h1>Email sending failed.</h1>";

    }

