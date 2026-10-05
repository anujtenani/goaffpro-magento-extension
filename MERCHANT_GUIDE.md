# Goaffpro Affiliate Marketing — Merchant Guide

This guide explains, in plain language, what the **Goaffpro Affiliate Marketing** extension for
Magento does, what it needs, how to set it up, and what information it sends to Goaffpro. It is
written for store owners and store administrators. A developer or Magento partner may be needed
for the command-line installation steps.

---

## 1. What this extension does

The extension connects your Magento store to [Goaffpro](https://goaffpro.com), an affiliate and
influencer marketing platform. With Goaffpro you can run your own affiliate program — recruit
affiliates and influencers, hand them referral links and coupon codes, and reward them for the
sales they bring in.

The extension is the **bridge** between your store and Goaffpro. It does not change how your
storefront looks or how customers shop. It:

1. **Registers your store with Goaffpro.** On installation it creates a unique *Public Key* for
   your store and tells Goaffpro your store name and store currency.
2. **Adds the Goaffpro tracking script to every storefront page.** This is what recognises an
   affiliate's referral link or cookie when a visitor arrives, so that sales can be attributed
   to the right affiliate.
3. **Reports orders to Goaffpro.** When a customer places an order — and whenever an order is
   updated in the Magento admin — the order's details are sent to Goaffpro so commissions and
   reports stay accurate.
4. **Marks the order on the "Thank you" page.** A small hidden element containing the order
   number is added to the checkout success page so Goaffpro can match the purchase to an
   affiliate.
5. **Disconnects cleanly when removed.** Uninstalling the extension notifies Goaffpro that this
   store is no longer connected.

Everything is controlled from **Stores → Configuration → goaffpro → Affiliate Marketing**.

### At a glance

```
Customer clicks an affiliate link
        │
        ▼
Goaffpro tracking script sets a referral cookie   (loaded on every storefront page)
        │
        ▼
Customer places an order   ──►  Goaffpro is notified (order.created)
        │
        ▼
Order is saved in Admin    ──►  Goaffpro is notified (order.updated)
        │
        ▼
Goaffpro attributes the sale and calculates commission
```

---

## 2. What your customers see

Nothing new. The extension adds **no** buttons, banners, pop-ups or layout changes. It only adds:

- an invisible tracking script, and
- a hidden element on the checkout success page.

Any affiliate-facing elements (referral links, coupon codes, affiliate dashboard) live in
Goaffpro, not in your storefront theme.

---

## 3. Requirements

- Magento Open Source or Adobe Commerce **2.4.9** (also compatible with the 2.4.7+ release line)
- PHP **8.3, 8.4 or 8.5**
- Outbound HTTPS access from your server to `https://api.goaffpro.com`
- An active Goaffpro account

---

## 4. Installation

Installation is done from the server command line. Ask your developer or hosting provider if you
are not comfortable running commands.

### Option A — Composer (recommended)

From your Magento root directory:

```bash
composer require goaffpro/affiliatemarketing
bin/magento setup:upgrade
bin/magento cache:flush
```

### Option B — Zip upload

1. Download the module zip from https://goaffpro.com/goaffpro-affiliate_marketing-latest.zip
2. Unzip it into `app/code` so the path becomes `app/code/Goaffpro/AffiliateMarketing`.
3. Run:

```bash
bin/magento setup:upgrade
bin/magento cache:flush
```

During `setup:upgrade` the extension generates your store's Public Key and sends your store name
and currency to Goaffpro. There is nothing to type in by hand.

> Upgrading from an older release? Your existing Public Key is preserved automatically — the
> extension will never overwrite it on upgrade.

---

## 5. Connecting your store to Goaffpro

1. Create an account or sign in at https://goaffpro.com.
2. In your Magento admin, open **Stores → Configuration → goaffpro → Affiliate Marketing →
   General Settings** and copy the **Public Key**.
3. Use that key in Goaffpro to link this store. Goaffpro's dashboard guides you through the
   remaining steps.

The Public Key is unique to this store and is displayed read-only — it is generated
automatically and should not be edited.

---

## 6. Settings

Go to **Stores → Configuration → goaffpro → Affiliate Marketing → General Settings**.

| Setting | What it does | Default |
| --- | --- | --- |
| **Enabled** | Switches the storefront tracking script on or off. | Yes |
| **Public Key** | Your store's unique Goaffpro identifier. Read-only. | Generated on install |

### Disabling the extension

Setting **Enabled = No** stops the tracking script from loading, so no new referrals are tracked
on the storefront. The extension still notifies Goaffpro about order events, which keeps your
existing affiliate data consistent. To stop all communication, uninstall the extension
(see section 9).

---

## 7. What is sent to Goaffpro, and when

All requests go to `https://api.goaffpro.com` over HTTPS.

| When | Endpoint | Data sent |
| --- | --- | --- |
| Extension installed | `…/magento/webhook/app.installed/{publicKey}` | Store name, store currency |
| Customer places an order | `…/magento/webhook/order.created/{publicKey}` | Full order details (see below) |
| Order saved in the admin (invoice, shipment, status change, etc.) | `…/magento/webhook/order.updated/{publicKey}` | Full order details (see below) |
| Extension uninstalled | `…/magento/webhook/app.uninstalled/{publicKey}` | Public Key only |

### Order payload

The order webhooks send the order's IDs and number, its money amounts, currency and date, the
customer's details, any coupon codes, one entry per product line, and the raw Magento order
record:

```json
{
    "id": 1001,
    "increment_id": "0000001001",
    "number": "#0000001001",
    "total": 1000,
    "subtotal": 850,
    "discount": 50,
    "tax": 100,
    "shipping": 50,
    "currency": "USD",
    "date": "2021-04-27T17:06:55.000Z",
    "customer": {
        "first_name": "John",
        "last_name": "Doe",
        "email": "johndoe@example.com",
        "phone": "+1 555-111-222",
        "is_new_customer": true
    },
    "coupons": ["EASY10OFF"],
    "line_items": [
        {
            "name": "Product A",
            "quantity": 2,
            "price": 450,
            "sku": "PD-110-1",
            "product_id": "21413232",
            "tax": 0,
            "discount": 50
        }
    ],
    "raw": {
        "...": "all attributes Magento stores on the order record"
    }
}
```

Field notes:

| Field | Meaning |
| --- | --- |
| `id` | Magento's internal order ID (`sales_order.entity_id`) |
| `increment_id` | The order number as Magento stores it, e.g. `0000001001` |
| `number` | The human-readable order number (the increment ID prefixed with `#`) |
| `total` | Final amount the customer paid (order grand total) |
| `subtotal` | Order total minus shipping and tax (i.e. net of any discount) |
| `discount` | Discount the customer received |
| `tax` | Tax charged on the order |
| `shipping` | Shipping charged on the order |
| `currency` | ISO-4217 code of the currency the order was placed in |
| `date` | Order date, in UTC and ISO-8601 format |
| `customer.phone` | Billing address phone (falls back to the shipping address) |
| `customer.is_new_customer` | `true` when the customer has no earlier orders (guest orders count as new) |
| `coupons` | Coupon codes applied to the order |
| `line_items` | One entry per product line; `price` is the unit price and `discount` is the line's total discount |
| `raw` | The unmodified Magento order record (every attribute Magento holds on the order) |

Your store also exposes a small **public** JSON endpoint at `/goaffpro/config/index` that returns
the Public Key, store name and store currency. The Goaffpro tracking script uses this to identify
your store.

### Privacy

The order webhooks include **customer personal data**: first name, last name, email address and
phone number. The `raw` field contains Magento's full order record, which also includes the
customer's email, name, date of birth (if captured), customer note and the IP address the order
was placed from. Postal addresses and payment/card details are **not** sent.

Make sure this is covered by your store's privacy policy and, where the GDPR or similar rules
apply, by your agreement with Goaffpro as a data processor.

---

## 8. Verifying that it works

- Browse your storefront and view the page source. You should find a script pointing to
  `https://api.goaffpro.com/loader.js?shop=…`. If it is there, tracking is active.
- Place a test order and check that it appears in your Goaffpro dashboard.
- Place a test order through an affiliate's referral link and confirm the sale is attributed to
  that affiliate.

If the script is missing, confirm **Enabled** is set to **Yes** and flush the cache
(`bin/magento cache:flush`).

---

## 9. Uninstalling

```bash
bin/magento module:disable Goaffpro_AffiliateMarketing
bin/magento module:uninstall Goaffpro_AffiliateMarketing
bin/magento cache:flush
```

On uninstall, the extension notifies Goaffpro so the store is disconnected.

---

## 10. Troubleshooting

| Symptom | Likely cause / fix |
| --- | --- |
| The `loader.js` script is not in the page source | The extension is disabled, or the cache was not flushed. Set **Enabled = Yes** and run `bin/magento cache:flush`. |
| Referrals are not being tracked | A conflict with a Content Security Policy or a browser script blocker. The extension whitelists `api.goaffpro.com` and `static.goaffpro.com`; if you use a custom CSP, allow those hosts for `script-src` and `connect-src`. |
| Orders do not appear in Goaffpro | Your server cannot reach `https://api.goaffpro.com`. Check outbound HTTPS and firewall rules. |
| The Public Key field looks wrong or "greyed out" | This is by design — the field is read-only. Reinstall, or contact Goaffpro support to re-register the store. |

---

## 11. FAQ

**Do I need to edit my theme?**
No. The extension adds its script and the success-page marker automatically.

**Will it slow down my store?**
No. The tracking script loads asynchronously from Goaffpro's servers and does not block page
rendering.

**Can I run multiple stores?**
Yes. The **Enabled** setting can differ per store view, so you can enable tracking on some stores
and not others. The Public Key is a single value for the Magento instance — contact Goaffpro if
you need fully separate keys per store.

**Does it work with a headless / PWA storefront?**
The tracking script and success-page marker target the standard Magento storefront. Headless
setups would use Goaffpro's own integration instead.

**Is customer personal data shared?**
Yes, some. The order webhooks send the customer's first name, last name, email address and phone
number, plus the full raw Magento order record (`raw`), which also includes the customer email,
name, date of birth (if captured), customer note and the IP address the order was placed from.
Postal addresses and payment/card details are never sent. Make sure this is covered by your
privacy policy and your agreement with Goaffpro.

---

## 12. Getting help

- Goaffpro documentation: https://docs.goaffpro.com
- Goaffpro support: admin@goaffpro.com
