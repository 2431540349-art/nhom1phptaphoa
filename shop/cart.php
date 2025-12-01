<?php
$qs = $_SERVER['QUERY_STRING'] ?? '';
parse_str($qs, $params);
$params['page'] = 4;
header('Location: index.php?' . http_build_query($params));
exit();
