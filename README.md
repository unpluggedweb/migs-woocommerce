# MIGS WooCommerce

MasterCard Internet Gateway Service (MiGS) is an online payment method for accepting credit and debit cards in several countries. An account with a supported bank is required to accept payments. This WordPress plugin integrates the MiGS gateway with WooCommerce.

### How to use it
  - Download this repository as a ZIP file.<br/>![](https://i.gyazo.com/bf03993e04604bbc7ba93c2b89a8fa76.png)
  - In your WordPress dashboard, go to Plugins > Add New > Upload Plugin. And upload the ZIP file.
  - Once the plugin is uploaded, click "Activate Plugin".
  - Now a new item "MIGS WooCommerce Options" will appear in the "Settings" tabs in your WordPress dashboard.<br/>![](https://i.gyazo.com/783a18b2ccaa1b9a7293e2cf35cd75fb.png)
  - In this page, you can choose multiple currencies if your bank provided you multiple merchant ids for multiple currencies. You can optionally choose a logo that will appear in the payment options during checkout.<br/>![](https://i.gyazo.com/c17e5557e5da40d2d5a843ce0d24f25b.png)
  - Now in WooCommerce > Settings > Payments. You will find a new payment method for each currency you choose. Enable these methods using the switch.<br/>![](https://i.gyazo.com/ee60d15e58057355e691fd57d5ac7e0d.png)
  - By clicking "Manage" on one of the new methods. You can now fill the title and description that the users will see during checkout. And you can also put the "Merchant ID", "Access Code" and "Hash Secret" provided by your bank.<br/>![](https://i.gyazo.com/b6b0fa9b61b8047b75af8c222f43f691.png)
  - In the payment options during checkout, your new payment method will appear with the logo you chose. Also if you have multiple currencies, only the one that corresponds to the current WooCommerce currency will appear. (To check your current WooCommerce language, go to WooCommerce > Settings > General)<br/>![](https://i.gyazo.com/30c22361b5176ff0873999215a73273b.png)
- If the payment was successful, the WooCommerce order status will turn from "Pending" to "Processing".
- If the payment was not successful, the message returned from the bank will be logged in the order notes:<br/>![](https://i.gyazo.com/327f742b55d0cdb56442f40da5ea09ae.png)
- Also if the payment was not successful, the order status will be "Pending payment". The customer can still pay for the order for the amount of time specified in your WooCommerce settings (WooCommerce > Settings > Products > Inventory). After that period the order status will change to "Canceled".