<?php
$host = 'localhost';
$dbname = 'sandeep_portfolio';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['message'])) {
        echo "Please fill all required fields.";
        exit;
    }

    $name    = strip_tags(trim($_POST['name']));
    $email   = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST['subject']));
    $msg     = strip_tags(trim($_POST['message']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$name, $email, $subject, $msg])) {
        
        $admin_email = "jas27409@gmail.com";
        $headers  = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Sandeep Portfolio <webmaster@yourportfolio.com>" . "\r\n";

        $admin_subject = "New Inquiry: " . $subject;
        $admin_body = "
        <div style='font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px; max-width: 600px;'>
            <h2 style='color: #333;'>New Project Inquiry</h2>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Subject:</strong> {$subject}</p>
            <p style='background: #f9f9f9; padding: 15px; border-left: 4px solid #007bff;'>
                <strong>Message:</strong><br>{$msg}
            </p>
        </div>";

        @mail($admin_email, $admin_subject, $admin_body, $headers);

        $user_subject = "Thank you for contacting";
        $user_body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; line-height: 1.6;'>
            <h2 style='color: #007bff;'>Hi {$name},</h2>
            <p>Thank you for reaching out! I have received your message regarding <strong>'{$subject}'</strong>.</p>
            <p>I am a WordPress/PHP Developer, and I usually respond to inquiries within 24 hours.</p>
            <p>Best Regards,<br><strong>Sandeep Kumar</strong><br>Web Developer</p>
            <hr style='border: 0; border-top: 1px solid #eee;'>
            <small style='color: #999;'>This is an automated response. Please do not reply to this email.</small>
        </div>";

        @mail($email, $user_subject, $user_body, $headers);

        echo "success";
    } else {
        echo "Failed to save message to database.";
    }

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
?>