<?php
http_response_code(401);
header('WWW-Authenticate: Basic realm="Access Denied"');
?>
<!DOCTYPE html>
<html>
<head><title>401 Unauthorized</title></head>
<body>
<h1>401 - Unauthorized</h1>
<p>You are not authorized to view this page.</p>
</body>
</html>
