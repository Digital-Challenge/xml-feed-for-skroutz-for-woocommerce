=== XML Feed for Skroutz & BestPrice for WooCommerce ===
Contributors: dichagr, theogk
Tags: skroutz, bestprice, xml, feed, marketplace
Stable tag: 1.0.3
Requires at least: 5.6
Tested up to: 6.7.2
WC requires at least: 6.2.0
WC tested up to: 9.6.1
Requires PHP: 7.4
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

This plugin helps you create an XML feed for Skroutz and BestPrice marketplaces.

== Description ==

XML Feed for Skroutz & BestPrice for WooCommerce is the ultimate tool for WooCommerce store owners looking to maximize their exposure on Greece's leading product marketplaces, Skroutz.gr and BestPrice.gr.

This plugin generates a fully compliant XML product feed, ensuring your store’s products are displayed accurately on these marketplaces.

With automatic updates for product additions, deletions, and modifications, this plugin removes the hassle of manually managing feeds, saving you time and effort.

The plugin provides many settings in the admin panel to create the product feed tailored to your needs. In addition, it provides developer-friendly ways to furtherly customize the feed to match specific advanced requirements.

This plugin will help you to easily pass the strict and challenging Skroutz XML validation process.

= Key Features =

- Fully compliant with Skroutz and BestPrice specifications.
- Full support for product variations with the new format required for Skroutz marketplace. Variations are grouped by size attributes. "Non-size" variations like color etc. appear in the feed as separate products, as per Skroutz requirements.
- Exclude or include products in the feed based on category, and/or tag from the plugin settings. If you need more control, there is a developer-friendly way to include/exclude products programmatically.
- Hide out of stock products from the feed, or products on backorder.
- Choose between long and short descriptions for product display. If one description is empty, the other is used as fallback.
- Use the new native WooCommerce v9.2+ field for EAN/Barcode, or let this plugin create a new custom field for you.
- Select multiple attributes for manufacturer, color, and size fields.
- Skroutz availability management at global level, and at the product/variation level.
- Select the attributes to include in the product name from the plugin settings.
- Add a fixed shipping rate.
- Developer-friendly ways to customize **ALL** feed data fields, like ID, product title, SKU, availability, images, description, category, price, quantity, weight etc.
- Schedule automatic XML generation using WP Cron or server cron jobs from Plesk/cPanel/SiteGround/GoDaddy or whatever platform you use.
- ZIP format available for huge product feeds.
- WP CLI support for generating the XML file.

= Really Fast & Lightweight =
- Optimized for speed, as it generates the XML way **faster** than any other "Skroutz feed" plugin, and also using far **less memory**, minimizing the stress on the server during the XML creation process.
- Optimized to run on servers with low resources. The minimum PHP memory officially required is 256MB, but it can run with even lower memory for small-sized eshops. The recommended memory for optimal performance is 512MB-1024MB+.

= Documentation =
[Documentation for Users/Shop managers](https://doc.clickup.com/2582906/p/h/2eubu-58615/d0b94a4b2c5331e/2eubu-58675)
[Documentation for Developers](https://doc.clickup.com/2582906/p/h/2eubu-58615/d0b94a4b2c5331e/2eubu-58695)

= Minimum Requirements =

* WooCommerce 6.2.0 or later
* WordPress 5.6 or later
* Minimum server PHP memory limit: 256MB (Recommended: 512MB)

== Screenshots ==

1. Plugin settings.

== Frequently Asked Questions ==

 = If it's so good, why it's free? =
We use this plugin for years in our customers eshops with great results and 100% success rate in XML validation and approval from Skroutz and BestPrice.
We just want to give something for free to the greek ecommerce community. If you insist on giving us money, you can buy some of our [premium plugins & addons](https://www.dicha.gr/plugins/).

 = I use a similar plugin for generating the Skroutz/BestPrice XML, but I have problems. Can I switch easily to this plugin? =
Yes, you can switch to this plugin, but please follow carefully the [migration guide](https://doc.clickup.com/2582906/p/h/2eubu-58615/d0b94a4b2c5331e/2eubu-58775) that corresponds to your previous plugin.
If there is no guide available, please contact us to help you with the switch.

 = Is the XML feed fully compliant with Skroutz.gr and BestPrice.gr specifications? =
Yes, the plugin adheres to all requirements and specifications of Skroutz.gr and BestPrice.gr, and it can help you to easily pass the strict and challenging Skroutz XML validation process.

 = Does the plugin support product variations? =
Yes, the plugin supports variations! Variations are grouped by size attributes. "Non-size" variations like color etc. appear in the feed as separate products, as per Skroutz requirements.

 = Can I exclude specific products or categories from the feed? =
Absolutely. You can exclude or include products based on their categories and tags using the plugin settings. If you need more control, there is a developer-friendly way to include/exclude products programmatically.
Please read the [documentation for developers](https://doc.clickup.com/2582906/p/h/2eubu-58615/d0b94a4b2c5331e/2eubu-58695) to find out how.

 = Can I customize the product IDs in the XML feed? =
Yes, you can use custom product IDs and SKUs which is particularly helpful if you are migrating from another e-commerce CMS, or an old WooCommerce installation.
Please read the [documentation for developers](https://doc.clickup.com/2582906/p/h/2eubu-58615/d0b94a4b2c5331e/2eubu-58695) to find out how.

 = How often is the XML feed updated? =
The plugin allows you to schedule automatic updates using the native WP Cron, or server cron jobs from Plesk/cPanel/SiteGround/GoDaddy or whatever platform you use. You can customize the update frequency to meet your needs.

 = Can I use WP CLI or a server script to generate the XML? =
Yes, you can use the WP CLI or a server script to generate the XML.
Please read the [documentation for developers](https://doc.clickup.com/2582906/p/h/2eubu-58615/d0b94a4b2c5331e/2eubu-58695) to find out how.

 = What are the system requirements for using this plugin? =
The plugin requires WooCommerce 6.2.0 or later and WordPress 5.6 or later.
It is optimized to run on servers with low resources and the minimum PHP memory officially required is 256MB, but it can run with even lower memory for small-sized eshops. The recommended memory for optimal performance is 512MB-1024MB+.

== Installation ==

= Installation via the WordPress interface =
1. Download the plugin from [Official WP Plugin Repository](https://wordpress.org/plugins/xml-feed-for-skroutz-for-woocommerce/).
2. Upload Plugin from your WP Dashboard ( Plugins>Add New>Upload Plugin ) the xml-feed-for-skroutz-for-woocommerce.zip file.
3. Activate the plugin through the 'Plugins' menu in WordPress Dashboard.
4. Setup the plugin settings navigating through the left main menu: Digital Challenge > Skroutz/BestPrice XML

== Changelog ==

= 1.0.3 =
*Release Date - 07 Feb 2025*
* Feature: Add support for native WooCommerce Brands (added recently in WooCommerce core v9.6.0).
* Feature: Add missing support for native Woo EAN/Barcode field.
* Improvement: Easier XML generation via cron. More details on the Doc for developers.
* Improvement: Better instructions for the checkbox that enables the extra EAN/Barcode field.
* Fix: Fix availability column width in admin product list, caused by a deprecated class name.
* Compatibility: Checked with the latest WordPress and WooCommerce versions.

= 1.0.1 =
*Release Date - 26 Dec 2024*
* Feature: Support for XML generation via WP-CLI.

= 1.0.0 =
*Release Date - 20 Dec 2024*
* Initial public release.