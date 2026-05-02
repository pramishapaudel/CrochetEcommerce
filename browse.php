<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        nav {
            background-color: white;
            padding: 10px 0;
            text-align: center;
        }
        nav ul {
            display: flex;
            justify-content: center;
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }
        nav ul li {
            margin: 0 15px;
            position: relative;
        }
        nav ul li a,
        .dropbtn {
            text-decoration: none;
            color: maroon;
            font-weight: bold;
            font-size: 1.2em;
        }
        .dropbtn {
            background: linear-gradient(to right, #FF758F, #C9184A);
            border: none;
            padding: 5px 12px;
            border-radius: 10px;
            color: white;
            cursor: pointer;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            top: 30px;
            left: 0;
            background-color: #f9f9f9;
            min-width: 120px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1;
            flex-direction: column;
        }
        .dropdown:hover .dropdown-content {
            display: flex;
        }
        .cart-badge {
            background: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            position: relative;
            top: -8px;
            left: -5px;
        }
        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            padding: 40px;
            box-sizing: border-box;
            max-width: 1200px;
            margin: auto;
        }
        .product {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 16px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .image-wrapper {
            width: 100%;
            height: 220px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .image-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 8px;
        }
        .product p {
            margin: 6px 0;
        }
        .price {
            color: #28a745;
            font-weight: bold;
            font-size: 1.2em;
        }
        input[type="number"] {
            width: 60px;
            padding: 6px;
            margin-bottom: 10px;
            border: 1px solid #aaa;
            border-radius: 5px;
            text-align: center;
        }
        .action-btn {
            padding: 10px 15px;
            margin-bottom: 6px;
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
            width: 100%;
        }
        .action-btn:hover {
            background: linear-gradient(90deg, #C9184A, #FF758F);
            transform: translateY(-2px);
        }
        footer {
            background-color: #f8f8f8;
            padding: 20px;
            text-align: center;
            margin-top: 40px;
        }
        .footer-links a {
            margin: 0 10px;
            text-decoration: none;
            color: #333;
        }
        .recommended-section {
            max-width: 1200px;
            margin: 24px auto 32px;
            padding: 0 16px;
        }
        .recommended-inner {
            background: linear-gradient(145deg, #fff0f3 0%, #ffffff 45%, #fff5f7 100%);
            border: 2px solid #FF758F;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(201, 24, 74, 0.12), 0 2px 8px rgba(0,0,0,0.06);
            padding: 0 0 24px;
            overflow: hidden;
        }
        .recommended-header {
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: #fff;
            padding: 16px 24px 18px;
            text-align: center;
            position: relative;
        }
        .recommended-header h2 {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-shadow: 0 1px 2px rgba(0,0,0,0.15);
        }
        .recommended-header::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
        }
        .recommended-section .product-grid {
            padding: 20px 20px 0;
        }
        .browse-search-wrap {
            max-width: 1200px;
            margin: 16px auto 8px;
            padding: 0 16px;
        }
        .browse-search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: center;
        }
        .browse-search-form input[type="search"] {
            flex: 1;
            min-width: 200px;
            max-width: 520px;
            padding: 12px 16px;
            border: 2px solid #FF758F;
            border-radius: 10px;
            font-size: 1rem;
        }
        .browse-search-form button[type="submit"] {
            padding: 12px 22px;
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
        }
        .browse-search-form a.clear-search {
            color: #C9184A;
            font-weight: 600;
            text-decoration: none;
        }
        .browse-catalog-head {
            max-width: 1200px;
            margin: 24px auto 8px;
            padding: 0 20px;
        }
        .browse-catalog-head h2 {
            color: #C9184A;
            font-size: 1.35rem;
            margin: 0 0 6px;
        }
        .browse-hint {
            color: #666;
            font-size: 0.95rem;
            margin: 0 0 12px;
        }
        @media (max-width: 600px) {
            .recommended-header h2 {
                font-size: 1.35rem;
            }
            .container {
                padding: 10px;
                gap: 16px;
            }
            .image-wrapper {
                height: 160px;
            }
            nav ul {
                flex-direction: column;
            }
            nav ul li {
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>

    <?php
    include 'includes/connection.php';
    require_once __DIR__ . '/includes/algorithms.php';

    if (!function_exists('browse_recommend_tokenize')) {
        /**
         * Split product text into comparable keywords (lowercase, no tiny/stop words).
         */
        function browse_recommend_tokenize($text) {
            static $stop = null;
            if ($stop === null) {
                $stop = array_flip([
                    'the', 'and', 'for', 'with', 'from', 'this', 'that', 'are', 'you', 'your', 'our',
                    'has', 'was', 'were', 'been', 'have', 'will', 'can', 'all', 'any', 'not', 'but',
                    'its', 'one', 'two', 'may', 'use', 'per', 'set', 'get', 'new', 'old', 'out',
                ]);
            }
            $text = strtolower((string) $text);
            $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
            $parts = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
            $out = [];
            foreach ($parts as $p) {
                if (strlen($p) < 3) {
                    continue;
                }
                if (isset($stop[$p])) {
                    continue;
                }
                $out[] = $p;
            }
            return $out;
        }

        /**
         * Score based on unique keyword overlap between seed vocabulary and candidate text.
         */
        function browse_keyword_overlap_score(array $seed_vocab_unique, $name, $details) {
            if (empty($seed_vocab_unique)) {
                return 0.0;
            }
            $cand_tokens = browse_recommend_tokenize($name . ' ' . $details);
            $cand_set = array_flip($cand_tokens);
            $overlap = 0;
            foreach ($seed_vocab_unique as $tok) {
                if (isset($cand_set[$tok])) {
                    $overlap++;
                }
            }
            // Up to ~25 points from text match (strong signal for crochet-themed overlap)
            return min(25.0, $overlap * 4.0);
        }
    }

    if (!function_exists('browse_search_query_tokens')) {
        /**
         * Terms user typed (2+ chars). All terms must match somewhere in name or details (AND).
         */
        function browse_search_query_tokens($q) {
            $q = trim((string) $q);
            if ($q === '') {
                return [];
            }
            $q = strtolower(preg_replace('/[^a-z0-9\s]/i', ' ', $q));
            $parts = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);
            $out = [];
            foreach ($parts as $p) {
                if (strlen($p) >= 2) {
                    $out[] = $p;
                }
            }
            return array_values(array_unique($out));
        }

        /**
         * Relevance ranking: title matches weigh more than description; phrase match boosts score.
         */
        function browse_search_relevance_score($name, $details, array $tokens, $raw_query) {
            $name_l = strtolower((string) $name);
            $det_l = strtolower((string) $details);
            $combined = $name_l . ' ' . $det_l;
            $score = 0;
            $rq = strtolower(trim((string) $raw_query));
            if ($rq !== '') {
                if (strpos($name_l, $rq) !== false) {
                    $score += 50;
                } elseif (strpos($combined, $rq) !== false) {
                    $score += 22;
                }
            }
            foreach ($tokens as $t) {
                if ($t === '') {
                    continue;
                }
                if (strpos($name_l, $t) !== false) {
                    $score += 14;
                } elseif (strpos($det_l, $t) !== false) {
                    $score += 5;
                }
            }
            return $score;
        }
    }

    $search_q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
    $search_tokens = browse_search_query_tokens($search_q);
    $search_active = ($search_q !== '' && !empty($search_tokens));
    $search_invalid = ($search_q !== '' && empty($search_tokens));

    $user_id = isset($_SESSION['userID']) ? (int) $_SESSION['userID'] : 0;

    // Build recommendation seeds from user history (orders + active cart)
    $seed_product_ids = [];
    $avg_seed_price = 0.0;
    $seed_vocab_unique = [];

    if ($user_id > 0) {
        $seed_sql = "
            SELECT DISTINCT p.productId, p.productPrice
            FROM product p
            JOIN (
                SELECT productId
                FROM orders
                WHERE userId = ?
                UNION
                SELECT ci.product_id AS productId
                FROM cart c
                JOIN cart_items ci ON c.cart_id = ci.cart_id
                WHERE c.user_id = ? AND c.status = 'active'
            ) recent_products ON recent_products.productId = p.productId
        ";
        $seed_stmt = $conn->prepare($seed_sql);
        $seed_stmt->bind_param("ii", $user_id, $user_id);
        $seed_stmt->execute();
        $seed_result = $seed_stmt->get_result();

        $seed_total_price = 0.0;
        $seed_count = 0;
        while ($seed_row = $seed_result->fetch_assoc()) {
            $seed_product_ids[] = (int) $seed_row['productId'];
            $seed_total_price += (float) $seed_row['productPrice'];
            $seed_count++;
        }
        $seed_stmt->close();

        if ($seed_count > 0) {
            $avg_seed_price = $seed_total_price / $seed_count;
        }

        if (!empty($seed_product_ids)) {
            $id_list = implode(',', array_map('intval', $seed_product_ids));
            $text_res = $conn->query("SELECT productName, productDetails FROM product WHERE productId IN ($id_list)");
            if ($text_res) {
                while ($t = $text_res->fetch_assoc()) {
                    foreach (browse_recommend_tokenize($t['productName'] . ' ' . $t['productDetails']) as $tok) {
                        $seed_vocab_unique[$tok] = true;
                    }
                }
            }
            $seed_vocab_unique = array_keys($seed_vocab_unique);
        }
    }

    // Recommendation algorithm:
    // score = popularity (units sold) + price similarity + keyword overlap (name/details vs your history)
    $recommended_products = [];
    $recommend_limit = 4;
    $exclude_clause = '';
    if (!empty($seed_product_ids)) {
        $exclude_clause = " AND p.productId NOT IN (" . implode(',', array_map('intval', $seed_product_ids)) . ")";
    }

    $recommend_sql = "
        SELECT
            p.productId,
            p.productName,
            p.productDetails,
            p.productPrice,
            p.productQuantity,
            p.productImage,
            COALESCE(SUM(CASE WHEN o.status IN ('paid', 'complete') THEN o.orderQuantity ELSE 0 END), 0) AS units_sold
        FROM product p
        LEFT JOIN orders o ON o.productId = p.productId
        WHERE p.productQuantity > 0 $exclude_clause
        GROUP BY p.productId, p.productName, p.productDetails, p.productPrice, p.productQuantity, p.productImage
    ";
    $recommend_result = $conn->query($recommend_sql);
    if ($recommend_result) {
        $scored_items = [];
        while ($row = $recommend_result->fetch_assoc()) {
            $units_sold = (int) $row['units_sold'];
            $price = (float) $row['productPrice'];
            $price_similarity = 0.0;

            if ($avg_seed_price > 0) {
                $price_diff_ratio = abs($price - $avg_seed_price) / max($avg_seed_price, 1);
                $price_similarity = max(0, 20 - ($price_diff_ratio * 20));
            }

            $keyword_score = browse_keyword_overlap_score(
                $seed_vocab_unique,
                $row['productName'],
                $row['productDetails']
            );

            $row['recommend_score'] = $units_sold + $price_similarity + $keyword_score;
            $scored_items[] = $row;
        }

        // Sort by recommendation score (quick sort in includes/algorithms.php)
        $scored_items = algo_quick_sort($scored_items, function ($a, $b) {
            return $b['recommend_score'] <=> $a['recommend_score'];
        });

        $recommended_products = array_slice($scored_items, 0, $recommend_limit);
    }

    // Catalog listing: full list or search (token AND + relevance sort)
    $catalog_rows = [];
    if ($search_invalid) {
        $catalog_rows = [];
    } elseif ($search_active) {
        $where_parts = [];
        $types = '';
        $bind = [];
        foreach ($search_tokens as $t) {
            $like = '%' . $t . '%';
            $where_parts[] = '(LOWER(productName) LIKE ? OR LOWER(IFNULL(productDetails, \'\')) LIKE ?)';
            $bind[] = $like;
            $bind[] = $like;
            $types .= 'ss';
        }
        $sql = 'SELECT * FROM product WHERE ' . implode(' AND ', $where_parts);
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param($types, ...$bind);
            $stmt->execute();
            $catalog_result = $stmt->get_result();
            while ($row = $catalog_result->fetch_assoc()) {
                $catalog_rows[] = $row;
            }
            $stmt->close();
        }
        $catalog_rows = algo_quick_sort($catalog_rows, function ($a, $b) use ($search_tokens, $search_q) {
            $sa = browse_search_relevance_score(
                $a['productName'],
                $a['productDetails'] ?? '',
                $search_tokens,
                $search_q
            );
            $sb = browse_search_relevance_score(
                $b['productName'],
                $b['productDetails'] ?? '',
                $search_tokens,
                $search_q
            );
            if ($sb !== $sa) {
                return $sb <=> $sa;
            }
            return strcasecmp($a['productName'], $b['productName']);
        });
    } else {
        $all_res = $conn->query('SELECT * FROM product');
        if ($all_res) {
            while ($row = $all_res->fetch_assoc()) {
                $catalog_rows[] = $row;
            }
        }
        $catalog_rows = algo_quick_sort($catalog_rows, function ($a, $b) {
            return strcasecmp($a['productName'], $b['productName']);
        });
    }

    // Display error messages
    if (isset($_GET['error'])) {
        $error_message = '';
        switch ($_GET['error']) {
            case 'insufficient_stock':
                $error_message = 'Insufficient stock available for this product.';
                break;
            case 'missing_data':
                $error_message = 'Please select a product and quantity.';
                break;
            case 'invalid_quantity':
                $error_message = 'Please enter a valid quantity.';
                break;
            default:
                $error_message = 'An error occurred. Please try again.';
        }
        if ($error_message) {
            echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border-radius: 8px; text-align: center;">' . htmlspecialchars($error_message) . '</div>';
        }
    }
    ?>

    <div class="browse-search-wrap">
        <form class="browse-search-form" method="get" action="browse.php" role="search">
            <label for="browse-q" class="visually-hidden" style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);border:0;">Search products</label>
            <input type="search" name="q" id="browse-q" value="<?php echo htmlspecialchars($search_q, ENT_QUOTES, 'UTF-8'); ?>"
                   placeholder="Search by name or description…" autocomplete="off" maxlength="200">
            <button type="submit">Search</button>
            <?php if ($search_q !== ''): ?>
                <a class="clear-search" href="browse.php">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (!empty($recommended_products) && !$search_active): ?>
        <section class="recommended-section" aria-label="Recommended products">
            <div class="recommended-inner">
                <header class="recommended-header">
                    <h2>Recommended For You</h2>
                </header>
                <div class="product-grid">
                <?php foreach ($recommended_products as $rec): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="./admin/uploads/<?php echo htmlspecialchars($rec['productImage']); ?>" alt="<?php echo htmlspecialchars($rec['productName']); ?>">
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($rec['productName']); ?></h3>
                            <p class="desc"><?php echo htmlspecialchars($rec['productDetails']); ?></p>
                            <p class="stock">Available: <?php echo (int)$rec['productQuantity']; ?></p>
                            <p class="price">Rs. <?php echo number_format($rec['productPrice'], 2); ?></p>
                        </div>
                        <form class="add-to-cart-form" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo (int)$rec['productId']; ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo (int)$rec['productQuantity']; ?>" class="quantity-input">
                            <button type="submit" class="btn add-cart-btn">Add to Cart</button>
                        </form>
                        <form class="buy-now-form" action="buy_now.php" method="POST">
                            <input type="hidden" name="buy_now_product_id" value="<?php echo (int)$rec['productId']; ?>">
                            <input type="hidden" name="quantity" value="1" class="buy-now-quantity">
                            <button type="submit" class="btn buy-now-btn">Buy Now</button>
                        </form>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <div class="browse-catalog-head">
        <?php if ($search_invalid): ?>
            <h2>Search</h2>
            <p class="browse-hint">Enter at least 2 characters per word to search.</p>
        <?php elseif ($search_active): ?>
            <h2>Search results</h2>
            <p class="browse-hint"><?php echo count($catalog_rows); ?> product(s) found for &quot;<?php echo htmlspecialchars($search_q, ENT_QUOTES, 'UTF-8'); ?>&quot;</p>
        <?php else: ?>
            <h2>All products</h2>
        <?php endif; ?>
    </div>

    <div class="product-grid">
        <?php if ($search_active && empty($catalog_rows)): ?>
            <p style="grid-column: 1 / -1; text-align: center; color: #666; padding: 24px;">No products match your search. Try different keywords.</p>
        <?php elseif ($search_invalid): ?>
            <p style="grid-column: 1 / -1; text-align: center; color: #666; padding: 24px;">Adjust your search words above.</p>
        <?php else: ?>
            <?php foreach ($catalog_rows as $row): ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="./admin/uploads/<?php echo htmlspecialchars($row['productImage']); ?>" alt="<?php echo htmlspecialchars($row['productName']); ?>">
                </div>
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($row['productName']); ?></h3>
                    <p class="desc"><?php echo htmlspecialchars($row['productDetails']); ?></p>
                    <p class="stock">Available: <?php echo (int)$row['productQuantity']; ?></p>
                    <p class="price">Rs. <?php echo number_format($row['productPrice'], 2); ?></p>
                </div>
                <form class="add-to-cart-form" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $row['productId']; ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $row['productQuantity']; ?>" class="quantity-input">
                    <button type="submit" class="btn add-cart-btn">Add to Cart</button>
                </form>
                <form class="buy-now-form" action="buy_now.php" method="POST">
                    <input type="hidden" name="buy_now_product_id" value="<?php echo $row['productId']; ?>">
                    <input type="hidden" name="quantity" value="1" class="buy-now-quantity">
                    <button type="submit" class="btn buy-now-btn">Buy Now</button>
                </form>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function () {
            // Sync quantity between add to cart and buy now forms
            $('.quantity-input').on('change', function() {
                var quantity = $(this).val();
                var productCard = $(this).closest('.product-card');
                productCard.find('.buy-now-quantity').val(quantity);
            });
            
            $('.add-to-cart-form').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                $.post('add_to_cart.php', form.serialize(), function (response) {
                    alert('Added to cart!');
                    if (response && response.cart_count !== undefined) {
                        var count = parseInt(response.cart_count, 10) || 0;
                        var $badge = $('.cart-badge');
                        $badge.text(count);
                        if (count > 0) { $badge.removeClass('hide'); }
                    }
                }, 'json');
            });
        });
    </script>

    <footer>
        <div class="footer-container">
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="aboutus.php">About Us</a>
                <a href="terms.php">Terms & Conditions</a>
            </div>
            <p>© 2025 Crochet E-commerce. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
