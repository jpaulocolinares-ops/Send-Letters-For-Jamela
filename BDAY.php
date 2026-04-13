<?php
$file = 'messages.json';
// Load the messages
$messages = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
// Optional: reverse so the newest messages appear first
if (is_array($messages)) { $messages = array_reverse($messages, true); }

$view_id = isset($_GET['msg']) ? $_GET['msg'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jamela's Birthday Vault 💜</title>
    <style>
        :root {
            --accent: #bf77f6;
            --bg: #0f051d;
            --card-bg: rgba(255, 255, 255, 0.08);
        }

        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: radial-gradient(circle at center, #2d134d 0%, var(--bg) 100%);
            color: #f3e5f5;
            margin: 0; 
            padding: 20px;
            display: flex; 
            flex-direction: column; 
            align-items: center;
            min-height: 100vh;
        }

        .header { 
            text-align: center; 
            margin: 60px 0 40px 0; 
        }

        .header p {
            letter-spacing: 5px; 
            color: var(--accent);
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .header h1 { 
            font-size: clamp(1.8rem, 6vw, 3rem); 
            text-shadow: 0 0 20px rgba(191, 119, 246, 0.5);
            margin: 0;
            line-height: 1.2;
        }

        /* The Grid Layout */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px; 
            width: 100%; 
            max-width: 1000px;
            padding: 20px;
        }

        /* The Individual Letter Cards */
        .card {
            background: var(--card-bg);
            aspect-ratio: 1/1; 
            border-radius: 20px;
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center;
            text-decoration: none; 
            color: white;
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(5px);
            text-align: center;
            padding: 10px;
        }

        .card:hover { 
            transform: translateY(-10px) scale(1.05); 
            border-color: var(--accent); 
            background: rgba(191,119,246,0.2);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .card span { font-size: 0.65rem; opacity: 0.6; margin-bottom: 5px; }
        .card strong { font-size: 1.1rem; word-break: break-word; }

        /* Modal / Letter Overlay */
        .overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.9); 
            display: flex; justify-content: center; align-items: center;
            z-index: 1000; 
            backdrop-filter: blur(12px);
            padding: 20px;
            box-sizing: border-box;
        }

        .modal {
            background: #1a082e; 
            padding: 30px; 
            border-radius: 30px;
            max-width: 500px; 
            width: 100%; 
            text-align: center;
            border: 1px solid var(--accent);
            box-shadow: 0 0 40px rgba(191, 119, 246, 0.2);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal img { 
            width: 100%; 
            border-radius: 20px; 
            max-height: 350px; 
            object-fit: cover; 
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .modal h2 { margin: 10px 0; color: var(--accent); }
        .modal p { line-height: 1.6; color: #e0d0f0; margin-bottom: 20px; }

        .close-btn { 
            display: inline-block; 
            padding: 12px 30px; 
            background: var(--accent); 
            color: white; 
            text-decoration: none; 
            border-radius: 50px; 
            font-weight: bold;
            transition: 0.3s;
        }

        .close-btn:hover { background: #a356e0; transform: scale(1.05); }

    </style>
</head>
<body>

    <div class="header">
        <p>MEMORIES & WISHES</p>
        <h1>HAPPY BIRTHDAY <br> JAMELA FAYE OLIVERA</h1>
    </div>

    <div class="grid">
        <?php if (empty($messages)): ?>
            <div style="grid-column: 1/-1; text-align: center; opacity: 0.5;">
                <p>The vault is waiting for its first letter... ✨</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $index => $msg): ?>
                <a href="?msg=<?php echo $index; ?>" class="card">
                    <span>FROM</span>
                    <strong><?php echo htmlspecialchars($msg['from']); ?></strong>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($view_id !== null && isset($messages[$view_id])): 
        $m = $messages[$view_id]; ?>
        <div class="overlay" onclick="window.location.href='?'">
            <div class="modal" onclick="event.stopPropagation()">
                <?php if(!empty($m['image'])): ?>
                    <img src="<?php echo htmlspecialchars($m['image']); ?>" alt="Birthday Memory">
                <?php endif; ?>
                
                <h2><?php echo htmlspecialchars($m['title']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($m['content'])); ?></p>
                
                <div style="margin-bottom: 25px; font-style: italic; opacity: 0.8;">
                    — Love, <?php echo htmlspecialchars($m['from']); ?>
                </div>
                
                <a href="?" class="close-btn">Back to Vault</a>
            </div>
        </div>
    <?php endif; ?>

</body>
</html>