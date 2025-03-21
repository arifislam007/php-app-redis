<?php
require 'config.php'; // Load DB & Redis configuration

// Fetch "Name" from Redis or DB
$nameKey = "user_name";
if ($redis->exists($nameKey)) {
    $name = $redis->get($nameKey);
    $nameFromCache = true;
} else {
    $stmt = $pdo->query("SELECT name FROM user_info ORDER BY id DESC LIMIT 1");
    $name = $stmt->fetchColumn();
    if ($name) $redis->setex($nameKey, 30, $name);
    $nameFromCache = false;
}

// Fetch "Location" from Redis or DB
$locationKey = "user_location";
if ($redis->exists($locationKey)) {
    $location = $redis->get($locationKey);
    $locationFromCache = true;
} else {
    $stmt = $pdo->query("SELECT location FROM user_info ORDER BY id DESC LIMIT 1");
    $location = $stmt->fetchColumn();
    if ($location) $redis->setex($locationKey, 60, $location);
    $locationFromCache = false;
}

// Fetch "Status" from Redis or DB
$statusKey = "user_status";
if ($redis->exists($statusKey)) {
    $status = $redis->get($statusKey);
    $statusFromCache = true;
} else {
    $stmt = $pdo->query("SELECT status FROM user_info ORDER BY id DESC LIMIT 1");
    $status = $stmt->fetchColumn();
    if ($status) $redis->setex($statusKey, 120, $status); // Cache status for 120 seconds
    $statusFromCache = false;
}
?>

<html>
<body>
    <h1>Latest User Information</h1>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?> (<?php echo $nameFromCache ? 'Cache' : 'DB'; ?>)</p>
    <p><strong>Location:</strong> <?php echo htmlspecialchars($location); ?> (<?php echo $locationFromCache ? 'Cache' : 'DB'; ?>)</p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($status); ?> (<?php echo $statusFromCache ? 'Cache' : 'DB'; ?>)</p>

    <br>
    <a href="index.php">Back to Home</a>
</body>
</html>
