STEP 2 - Webhook + Inbox + Send Message (Overlay)

1) .env (add)
FACEBOOK_APP_SECRET=your_app_secret
WEBHOOK_VERIFY_TOKEN=dev_verify_token

2) Migrate + queue
php artisan migrate
php artisan queue:work --queue=fb-webhook,fb-send

3) Config
php artisan config:clear

4) Facebook App Webhook:
Callback URL: http://localhost:8000/webhook/facebook
Verify Token: <WEBHOOK_VERIFY_TOKEN>
Object: page; Fields: messages,messaging_postbacks,messaging_optins,message_deliveries,message_reads

5) Inbox:
GET /{page_id}/inbox
GET /{page_id}/inbox/{customer_id}
POST /{page_id}/inbox/{customer_id}/send

6) Subscribe page (optional helper):
POST /{page_id}/subscribe
