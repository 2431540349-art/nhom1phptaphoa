<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../Admin/config.php';

function getCategories() {
    global $conn;
    $categories = [];
    $result = $conn->query("SELECT c.id, c.name, c.icon_emoji, COUNT(p.id) AS count FROM categories c LEFT JOIN products p ON p.category_id = c.id GROUP BY c.id, c.name, c.icon_emoji ORDER BY c.name ASC");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'icon' => $row['icon_emoji'] ?? '',
                'count' => $row['count']
            ];
        }
    }
    return $categories;
}

function getProducts($limit = 200) {
    global $conn;
    $products = [];
    $res = $conn->query("SELECT * FROM products LIMIT " . intval($limit));
    if ($res && $res->num_rows > 0) {
        while ($p = $res->fetch_assoc()) {
            $products[] = $p;
        }
    }
    return $products;
}

function getRecentProducts($limit = 18) {
    global $conn;
    $products = [];
    $sql = "SELECT * FROM products ORDER BY id DESC LIMIT " . intval($limit);
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        while ($p = $res->fetch_assoc()) {
            $products[] = $p;
        }
    }
    return $products;
}

function getProductsCount($search = null, $categoryID = 0) {
    global $conn;
    $where = [];
    if ($categoryID > 0) $where[] = "category_id = " . intval($categoryID);
    if ($search !== null && $search !== '') {
        $s = $conn->real_escape_string($search);
        $where[] = "name LIKE '%" . $s . "%'";
    }
    $sql = "SELECT COUNT(*) AS total FROM products" . (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) return intval($row['total']);
    return 0;
}

function getProductsPaginated($limit = 30, $offset = 0, $search = null, $categoryID = 0) {
    global $conn;
    $products = [];
    $where = [];
    if ($categoryID > 0) $where[] = "category_id = " . intval($categoryID);
    if ($search !== null && $search !== '') {
        $s = $conn->real_escape_string($search);
        $where[] = "name LIKE '%" . $s . "%'";
    }
    $sql = "SELECT * FROM products" . (count($where) ? ' WHERE ' . implode(' AND ', $where) : '') . " ORDER BY id DESC LIMIT " . intval($offset) . "," . intval($limit);
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        while ($p = $res->fetch_assoc()) {
            $products[] = $p;
        }
    }
    return $products;
}

function getCartCount() {
    global $conn;
    $uid = intval($_SESSION['user_id'] ?? 0);
    if ($uid > 0) {
        $stmt = $conn->prepare("SELECT COALESCE(SUM(quantity),0) AS total FROM carts WHERE user_id = ?");
        if (!$stmt) return 0;
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return intval($row['total'] ?? 0);
    }
    $count = 0;
    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $it) {
            $count += intval($it['qty'] ?? 1);
        }
    }
    return $count;
}

function addToCart($productId) {
    global $conn;
    $id = intval($productId);
    $stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE id = ? LIMIT 1");
    if (!$stmt) return;
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res || $res->num_rows == 0) { $stmt->close(); return; }
    $product = $res->fetch_assoc();
    $stmt->close();

    $user_id = intval($_SESSION['user_id'] ?? 0);
    if ($user_id > 0) {
        $userCheck = $conn->prepare("SELECT id FROM user_custom WHERE id = ? LIMIT 1");
        if (!$userCheck) {
            error_log('Prepare failed (helpers user validation): ' . $conn->error);
            return;
        }
        $userCheck->bind_param('i', $user_id);
        $userCheck->execute();
        $userRes = $userCheck->get_result();
        if (!$userRes || $userRes->num_rows == 0) {
            error_log('User ID ' . $user_id . ' not found in user_custom table');
            $userCheck->close();
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['qty'] += 1;
            } else {
                $_SESSION['cart'][$id] = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'qty' => 1
                ];
            }
            return;
        }
        $userCheck->close();

        $check = $conn->prepare("SELECT id, quantity FROM carts WHERE user_id = ? AND product_id = ? LIMIT 1");
        if (!$check) {
            error_log('Prepare failed (helpers check carts): ' . $conn->error);
            return;
        }
        $check->bind_param('ii', $user_id, $id);
        $check->execute();
        $cres = $check->get_result();
        $now = date('Y-m-d H:i:s');
        if ($cres && $cres->num_rows > 0) {
            $crow = $cres->fetch_assoc();
            $upd = $conn->prepare("UPDATE carts SET quantity = quantity + 1, added_at = ? WHERE id = ?");
            if ($upd) {
                $upd->bind_param('si', $now, $crow['id']);
                if (!$upd->execute()) error_log('Execute failed (helpers update carts): ' . $upd->error);
                $upd->close();
            } else {
                error_log('Prepare failed (helpers update carts): ' . $conn->error);
            }
        } else {
            $ins = $conn->prepare("INSERT INTO carts (user_id, product_id, price_at_add, quantity, added_at) VALUES (?, ?, ?, ?, ?)");
            if (!$ins) {
                error_log('Prepare failed (helpers insert carts): ' . $conn->error);
            } else {
                $price = (float)$product['price'];
                $qty = 1;
                $ins->bind_param('iidis', $user_id, $id, $price, $qty, $now);
                if (!$ins->execute()) error_log('Execute failed (helpers insert carts): ' . $ins->error);
                $ins->close();
            }
        }
        $check->close();
        return;
    }

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['qty'] += 1;
    } else {
        $_SESSION['cart'][$id] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'qty' => 1
        ];
    }
}

if (isset($_GET['add_to_cart'])) {
    $id = intval($_GET['add_to_cart']);
    addToCart($id);
    $redirect = 'index.php';
    if (isset($_GET['page'])) {
        $redirect .= '?page=' . intval($_GET['page']);
        $sep = '&';
        if (isset($_GET['categoryID'])) $redirect .= '&categoryID=' . intval($_GET['categoryID']);
        if (isset($_GET['id'])) $redirect .= '&id=' . intval($_GET['id']);
    }
    header('Location: ' . $redirect);
    exit;
}

?>
