<?php
http_response_code(503);
header('Retry-After: 3600'); // optional: suggests the client tries again in 1 hour
?>
<!DOCTYPE html>
<html>
<head><title>503 Service Unavailable</title></head>
<body>
<h1>503 - Service Unavailable</h1>
<p>The server is currently unavailable (maybe overloaded or down for maintenance).</p>
</body>
</html>
