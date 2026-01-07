<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test</title>
    <link rel="stylesheet" href="https://checkout-ui.shiprocket.com/assets/styles/shopify.css">
</head>
<body>
    <button id="buyNow">Checkout</button>

<script>
    const button = document.getElementById('buyNow');

    button.addEventListener('click', async (e) => {
        try {
            const response = await fetch("token.php"); // Fetch token from backend
            const data = await response.json();

            console.log("Token Response:", data); // Debugging log

            if (data.status_code === 200 && data.response.ok) {
                const token = data.response.result.token;
                console.log("Generated Token:", token); // Debugging log

                // Proceed with checkout
                HeadlessCheckout.addToCart(event, token, { fallbackUrl: "https://your.fallback.com?product=123" });
            } else {
                throw new Error("Invalid token response");
            }
        } catch (error) {
            console.error("Error fetching token:", error);
            alert("An error occurred while fetching token.");
        }
    });
</script>


    <script src="https://checkout-ui.shiprocket.com/assets/js/channels/shopify.js"></script>
</body>
</html>
