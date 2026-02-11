<?php
// Bypass Laravel: use this to confirm the PHP server responds.
// Open http://127.0.0.1:8000/health.php — if you see "pong", the server is fine and the delay is inside Laravel.
header('Content-Type: text/plain');
echo 'pong';
