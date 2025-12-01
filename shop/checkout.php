<?php
$qs = $_SERVER['QUERY_STRING'] ?? '';
parse_str($qs, $params);
$params['page'] = 5;
header('Location: index.php?' . http_build_query($params));
exit();
