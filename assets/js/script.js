function confirmOrder(vID,uID) {
    if (confirm('Confirm Rent Order?')) {
        const vehicleID = vID;
        const userID = uID;
        const rentDate = document.getElementById('rentDate').value;
        if(rentDate){
            $.ajax({
                type: "POST",
                url: "./includes/confirm_order.php",
                data: {
                    vehicleID: vehicleID,
                    userID: userID,
                    rentDate: rentDate
                },
                success: function(response) {
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    alert("An error occurred: " + xhr.responseText);
                }
            });
        }else{
            alert("enter appropraite date");
        }
    }
}


function updateOrderStatus(action, orderID) {
    if(confirm("Are you sure you want to " + action + " this order?")) {
        $.ajax({
            url: '../admin/includes/update_order_status.php',
            type: 'POST',
            data: { orderID: orderID, action: action },
            success: function(response) {
                if(response === 'success') {
                    alert("Order " + action + "d successfully!");
                    location.reload();
                } else {
                    alert("Error: " + response);
                }
            },
            error: function(xhr, status, error) {
                alert("An error occurred: " + xhr.responseText);
            }
        });
    }
}