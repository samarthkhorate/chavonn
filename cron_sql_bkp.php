<?php
// Include PHPMailer
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database credentials
$host = '147.93.98.133';       // Database host
$dbname = 'vaib_db1';          // Database name
$username = 'vaib_user1';      // Database username
$password = 'Abhi235689';      // Database password

// Email credentials
$smtp_host = "smtp.gmail.com"; // Gmail SMTP server
$smtp_user = "techkinghosting@gmail.com"; // Your Gmail
$smtp_pass = "pcbmtyhdiooksoqn"; // Gmail App Password
$from_email = "techkinghosting@gmail.com";

// Recipients
$to_emails = [
    "abhisinfotech0@gmail.com"
];

// Subject with timestamp
$timestamp = date("Y-m-d H:i:s");
$subject = "BKP vaibhavdhus.com - $timestamp";

// Backup file paths
$backup_file = __DIR__ . "/backup_" . date("Y-m-d_H-i-s") . ".sql";
$zip_file = __DIR__ . "/backup_" . date("Y-m-d_H-i-s") . ".zip";

// Connect to the database (for logging)
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Function to insert logs
function log_backup($conn, $status, $message) {
    $stmt = $conn->prepare("INSERT INTO backup_logs (status, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $status, $message);
    $stmt->execute();
    $stmt->close();
}

// Start logging
log_backup($conn, "START", "Backup process started.");

// Export the database
$dump_command = "mysqldump --host=$host --user=$username --password=$password $dbname > $backup_file";
system($dump_command, $output);

// Check if SQL file exists
if (!file_exists($backup_file)) {
    log_backup($conn, "ERROR", "Backup failed: SQL file not found.");
    die("Backup failed: SQL file not found.");
} else {
    log_backup($conn, "SUCCESS", "Database backup created successfully.");
}

// Create ZIP file without password
$zip_command = "zip $zip_file $backup_file";
system($zip_command, $output);

// Check if ZIP file exists
if (!file_exists($zip_file)) {
    log_backup($conn, "ERROR", "ZIP file creation failed.");
    die("ZIP file creation failed.");
} else {
    log_backup($conn, "SUCCESS", "ZIP file created successfully.");
}

// Remove the raw SQL file after compression
unlink($backup_file);
log_backup($conn, "INFO", "Original SQL file deleted after compression.");

// Email Body
$email_body = "
Dear Team,<br><br>

Hope you are doing well.<br><br>

Please find attached the latest database backup for <strong>vaibhavdhus.com</strong> as of <strong>$timestamp</strong>.<br><br>

If you have any questions or need further assistance, feel free to reach out.<br><br>

Thank you for your continued trust in our services.<br><br>

Best Regards,<br>
<strong>Tech King Hosting</strong><br>
(A Venture by Shubhsairaj Infotech Private Limited)<br>
";

// Send email using PHPMailer
$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host       = $smtp_host;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtp_user;
    $mail->Password   = $smtp_pass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = 587; // TLS port

    // Email Content
    $mail->setFrom($from_email, 'Database Backup');

    // Add multiple recipients
    foreach ($to_emails as $email) {
        $mail->addAddress($email);
    }

    $mail->Subject = $subject;
    $mail->isHTML(true);
    $mail->Body    = $email_body;

    // Attach ZIP file
    $mail->addAttachment($zip_file);

    // Send email
    if ($mail->send()) {
        log_backup($conn, "SUCCESS", "Backup email sent successfully.");
        echo "Backup sent successfully.";
    } else {
        log_backup($conn, "ERROR", "Failed to send backup email.");
        echo "Failed to send backup.";
    }

    // Delete the zip file after sending
    unlink($zip_file);
    log_backup($conn, "INFO", "ZIP file deleted after sending email.");
} catch (Exception $e) {
    log_backup($conn, "ERROR", "Email could not be sent. Mailer Error: " . $mail->ErrorInfo);
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

// Close database connection
$conn->close();
?>
