<?php
// Cloudinary Configuration
$cloud_name = "dlalxofgg";
$api_key = "116957151731237";
$api_secret = "H7jZD5fVw-ZcdDQdWqo5R4-4az8";
$file = 'messages.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $file_path = $_FILES['photo']['tmp_name'];
        $timestamp = time();
        
        // Cloudinary Signature Logic
        $params_to_sign = "timestamp=$timestamp" . $api_secret;
        $signature = sha1($params_to_sign);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/$cloud_name/image/upload");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Helps on some local servers
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'file' => new CURLFile($file_path),
            'api_key' => $api_key,
            'timestamp' => $timestamp,
            'signature' => $signature
        ]);
        
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (isset($response['secure_url'])) {
            $image_url = $response['secure_url'];
            
            // Read existing, append, and save
            $current_data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
            if (!is_array($current_data)) $current_data = [];
            
            $current_data[] = [
                "from" => htmlspecialchars($_POST['from']),
                "title" => htmlspecialchars($_POST['title']),
                "content" => htmlspecialchars($_POST['content']),
                "image" => $image_url,
                "date" => date("Y-m-d H:i")
            ];
            
            file_put_contents($file, json_encode($current_data, JSON_PRETTY_PRINT));
            $success = "Letter Sent to the Cloud! 💜";
        } else {
            $error = "Upload failed. Error: " . ($response['error']['message'] ?? 'Unknown Error');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birthday Vault | Sender</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #0f051d; color: #f3e5f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: rgba(255,255,255,0.05); padding: 30px; border-radius: 20px; border: 1px solid #bf77f6; max-width: 400px; width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        h1 { font-size: 1.5rem; text-align: center; color: #bf77f6; }
        input, textarea, button { width: 100%; padding: 12px; margin: 10px 0; border-radius: 10px; box-sizing: border-box; font-size: 1rem; }
        input, textarea { background: rgba(0,0,0,0.4); border: 1px solid #3b185f; color: white; }
        button { background: #bf77f6; color: white; border: none; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 20px; }
        button:hover { background: #a356e0; transform: scale(1.02); }
        .loading { display: none; color: #bf77f6; font-style: italic; text-align: center; margin-top: 10px; }
        .alert { padding: 10px; border-radius: 10px; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Send Love to Jamela 💜</h1>
        
        <?php if(isset($success)): ?>
            <div class="alert" style="background: rgba(0,255,0,0.1); color: #00ff00; border: 1px solid #00ff00;"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert" style="background: rgba(255,0,0,0.1); color: #ff4444; border: 1px solid #ff4444;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" onsubmit="document.getElementById('loader').style.display='block'; document.getElementById('submitBtn').disabled = true;">
            <input type="text" name="from" placeholder="Your Name" required>
            <input type="text" name="title" placeholder="Letter Title" required>
            <textarea name="content" rows="4" placeholder="Your Message..." required></textarea>
            <label style="font-size: 0.8rem; opacity: 0.8;">Upload a Memory (Photo):</label>
            <input type="file" name="photo" accept="image/*" required>
            <div id="loader" class="loading">✨ Sending to the stars...</div>
            <button type="submit" id="submitBtn">Upload & Send ✨</button>
        </form>
    </div>
</body>
</html>