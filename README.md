# OnePay Frontend + Native PHP Backend

This is a demo project integrating with OnePay API endpoints as defined in the provided Postman collection.

## Project Structure
```
onepay-frontend-php/
├── public/
│   ├── index.html          # Payment form (creates order)
│   └── assets/
│       ├── css/style.css
│       └── js/script.js
├── api/
│   ├── accountInfo.php
│   ├── invoiceList.php
│   ├── config.php
│   ├── api_client.php
│   ├── createOrder.php
│   ├── checkOrder.php
│   └── logs/
├── .env.example
├── .gitignore
└── README.md
```

## Install & Run
1. Copy `public/` contents to your web root (e.g., `public_html`).
2. Place `api/` folder at the same level or above web root (ensure `api/config.php` can read `.env`).
3. Copy `.env.example` to `.env` and set:
```
ONEPAY_TOKEN=your_onepay_api_token_here
ONEPAY_BASE_URL=https://one-pay.info/api/v2
```
4. Make `api/logs` writable:
```
chmod 755 api/logs
```
5. Open `public/index.html` in browser and test.

## Notes for developers
- `api/config.php` reads `.env` file for `ONEPAY_TOKEN` and `ONEPAY_BASE_URL`.
- `api/api_client.php` handles `onepay_post` and `onepay_get`. It concatenates base URL and path, so we call `onepay_post('createorder', $payload)`.
- `public/assets/js/app.js` uses Fetch API to call the local PHP endpoints (`/api/createOrder.php` and `/api/checkOrder.php`). It stores some values in `localStorage` for convenience.

## Security
- Never commit your `.env` or `ONEPAY_TOKEN` to public repos.
- Use HTTPS in production.
- Limit access to `api/` if needed.

## If you need
- I can also produce a PR that applies these updates directly to your GitHub repo (if you provide it), or push the ZIP for manual upload.
