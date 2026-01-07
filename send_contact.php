<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-PT6J4XZVS0"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-PT6J4XZVS0');
</script><?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'config.php';  // Adjust path as needed
require 'mail/src/Exception.php';
require 'mail/src/PHPMailer.php';
require 'mail/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $mobile = htmlspecialchars(trim($_POST['mobile']));
    $message = htmlspecialchars(trim($_POST['message']));

    if (!$email) {
        echo "<script>alert('Invalid email format!');</script>";
        exit;
    }

    // Insert data using prepared statements for security
    $stmt = $con->prepare("INSERT INTO tbl_contact_us (cust_query, cust_name, cust_email, cust_mob, timestamp) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $message, $name, $email, $mobile);

    if ($stmt->execute()) {
        try {
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'system@vaibhavdhus.com';  // Replace with a secure method
            $mail->Password   = 'System#$@2178';           // Replace with environment variable
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Recipients
            $mail->setFrom('system@vaibhavdhus.com', 'Vaibhav Dhus');
            $mail->addAddress($email, $name);   // User email
            $mail->addAddress('vaibhavdhus@gmail.com', 'Vaibhav Dhus');  // Company email
            $mail->addReplyTo('vaibhavdhus@gmail.com', 'Vaibhav Dhus');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Contact Us Query - ' . $name;
            $mail->Body    = "<p>You have received a new contact query from <b>$name</b>.</p>
                              <p><strong>Message:</strong> $message</p>
                              <p><strong>Contact Details:</strong></p>
                              <p>Email: $email</p>
                              <p>Mobile: $mobile</p>";

            $mail->send();
            echo "<script>
        alert('Your message has been successfully sent. We will get back to you shortly!');
                    window.location.href = 'index.php';
                  </script>";
        } catch (Exception $e) {
            // Log the error for internal review, avoid exposing error details
            error_log("Email Error: {$mail->ErrorInfo}");
            echo "<script>alert('Error sending email. Please try again later.');</script>";
        }
    } else {
        // Log the database error
        error_log("Database Insert Error: {$con->error}");
        echo "<script>alert('Error saving data to the database!');</script>";
    }
    $stmt->close();
}
?>
