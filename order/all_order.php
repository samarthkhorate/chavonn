<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-PT6J4XZVS0"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-PT6J4XZVS0');
</script>
<?php



require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;

include "../config.php";
$api = new Api($api_key,$api_secret);
echo "<pre>";
print_r($api->order->all());
//print_r($api->order->fetch('order_PPDG0xzhxUAnXi'));
//print_r($api->order->fetch('order_PPDG0xzhxUAnXi')->payments());
echo "</pre>";


?>