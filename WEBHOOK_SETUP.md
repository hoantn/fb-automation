# Facebook Webhook Setup (ngrok-ready)

## 1) ENV
Add to your `.env`:
```env
APP_URL=https://<your-ngrok>.ngrok.io
FORCE_HTTPS=true
WEBHOOK_VERIFY_TOKEN=change-me
FACEBOOK_APP_SECRET=<your-app-secret>
```

## 2) Routes
Webhook endpoints are:
- GET  `/api/webhook/facebook` (verification)
- POST `/api/webhook/facebook` (events)

## 3) Run (dev)
```bash
php artisan serve --host=127.0.0.1 --port=8000
ngrok http 8000
php artisan config:clear && php artisan route:clear && php artisan cache:clear
php artisan route:list | grep webhook
```

## 4) Facebook App
- Webhook URL: `https://<your-ngrok>.ngrok.io/api/webhook/facebook`
- Verify token: same as `WEBHOOK_VERIFY_TOKEN`
- App secret: use `FACEBOOK_APP_SECRET` to check `X-Hub-Signature-256`

## 5) Test quickly
```bash
curl -i "https://<ngrok>/api/webhook/facebook?hub.mode=subscribe&hub.verify_token=change-me&hub.challenge=123"
curl -i -X POST "https://<ngrok>/api/webhook/facebook" -H "Content-Type: application/json" -d '{"object":"page","entry":[{"id":"1"}]}'
```
