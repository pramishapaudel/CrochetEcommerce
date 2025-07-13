<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
<?php
session_start();
require('./includes/connection.php');

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['userID'];
// Get active cart
$cart_sql = "SELECT cart_id FROM cart WHERE user_id=? AND status='active' ORDER BY cart_id DESC LIMIT 1";
$stmt = $conn->prepare($cart_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($cart_id);
$stmt->fetch();
$stmt->close();

if (!$cart_id) {
    echo "<h2>Your cart is empty!</h2>";
    exit();
}

// Get cart items
$sql = "SELECT ci.cart_item_id, p.productName, p.productPrice, ci.quantity, p.productImage
        FROM cart_items ci
        JOIN product p ON ci.product_id = p.productId
        WHERE ci.cart_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Your Cart</h2>
<form id="cart-form" action="checkout.php" method="POST">
    <table>
        <tr>
            <th>Select</th>
            <th>Image</th>
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Subtotal</th>
            <th>Remove</th>
        </tr>
        <?php
        while ($row = $result->fetch_assoc()) {
            $subtotal = $row['productPrice'] * $row['quantity'];
            $imgPath = './admin/uploads/' . htmlspecialchars($row['productImage']);
            ?>
            <tr data-cart-item-id="<?php echo $row['cart_item_id']; ?>">
                <td><input type="checkbox" class="item-checkbox" name="selected_items[]" value="<?php echo $row['cart_item_id']; ?>" checked></td>
                <td><img src="<?php echo $imgPath; ?>" alt="" style="width:50px;height:50px;object-fit:cover;"></td>
                <td><?php echo htmlspecialchars($row['productName']); ?></td>
                <td class="item-price"><?php echo $row['productPrice']; ?></td>
                <td>
                    <input type="number" class="qty-input" name="quantities[<?php echo $row['cart_item_id']; ?>]" value="<?php echo $row['quantity']; ?>" min="1" style="width:40px;">
                </td>
                <td class="item-subtotal"><?php echo $subtotal; ?></td>
                <td>
                    <button type="button" class="remove-btn" data-cart-item-id="<?php echo $row['cart_item_id']; ?>">Remove</button>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td colspan="5">Total</td>
            <td id="cart-total"></td>
            <td></td>
        </tr>
    </table>
    <button type="submit">Checkout</button>
</form>

<!-- Khalti JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://khalti.com/static/khalti-checkout.js"></script>
<script>
function recalculateTotal() {
    let total = 0;
    $('tr[data-cart-item-id]').each(function() {
        let $row = $(this);
        let checked = $row.find('.item-checkbox').is(':checked');
        let price = parseFloat($row.find('.item-price').text());
        let qty = parseInt($row.find('.qty-input').val());
        let subtotal = checked ? price * qty : 0;
        $row.find('.item-subtotal').text(subtotal);
        if (checked) total += subtotal;
    });
    $('#cart-total').text(total);
}

$(function() {
    recalculateTotal();

    $('.qty-input').on('input', function() {
        let $row = $(this).closest('tr');
        let cart_item_id = $row.data('cart-item-id');
        let quantity = $(this).val();
        $.post('update_cart_quantity.php', {
            cart_item_id: cart_item_id,
            quantity: quantity
        }, function(response) {
            recalculateTotal();
        });
    });

    $('.item-checkbox').on('change', function() {
        recalculateTotal();
    });

    $('.remove-btn').on('click', function() {
        var btn = $(this);
        var cart_item_id = btn.data('cart-item-id');
        $.post('remove_from_cart.php', { cart_item_id: cart_item_id }, function(response) {
            btn.closest('tr').remove();
            recalculateTotal();
        });
    });
});
</script>

</body>
</html>
