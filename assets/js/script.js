function confirmOrder(vID, uID) {
    if (confirm('Confirm Rent Order?')) {
        const rentDate = document.getElementById('rentDate').value;
        
        if (rentDate) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "includes/confirm_order.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            alert("haha aayo");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        alert(xhr.responseText); // Show success/error message
                        location.reload(); // Reload to reflect changes
                    } else {
                        alert("Error: " + xhr.responseText);
                    }
                }
            };

            // Send data to the server
            xhr.send(`vehicleID=${vID}&userID=${uID}&rentDate=${rentDate}`);
        } else {
            alert("Please enter an appropriate date.");
        }
    }
}


function updateOrderStatus(action, orderID) {
    if (confirm("Are you sure you want to " + action + " this order?")) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../admin/includes/update_order_status.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    if (xhr.responseText.trim() === 'success') {
                        alert("Order " + action + "d successfully!");
                        location.reload();
                    } else {
                        alert("Error: " + xhr.responseText);
                    }
                } else {
                    alert("An error occurred: " + xhr.responseText);
                }
            }
        };

        const data = `orderID=${encodeURIComponent(orderID)}&action=${encodeURIComponent(action)}`;
        xhr.send(data);
    }
}
