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
                    alert(response);
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