STEP 3 FULL ADMIN (Realtime Dashboard + Admin Code Tay)

1) Giải nén đè lên dự án fb-automation.
2) Chạy migrations:
   D:\xampp\php\php.exe artisan migrate
3) Clear cache:
   D:\xampp\php\php.exe artisan optimize:clear

4) Chạy file:
   run_project.bat

   (Sẽ mở: Laravel serve, Queue worker, Ngrok, Admin Login)

5) Đăng nhập admin:
   URL: http://localhost:8000/admin/login
   Email: admin@local.test
   Password: 123456

6) Webhook:
   - Callback URL: https://<subdomain>.ngrok-free.app/webhook/facebook
   - Verify Token: WEBHOOK_VERIFY_TOKEN trong .env
   - Object: page
   - Fields: messages, messaging_postbacks, messaging_optins, message_deliveries, message_reads

7) Dashboard realtime: tự refresh 5s (cache 10s).
8) Auto-reply: cấu hình trong config/auto_reply.php hoặc bật/tắt ở /admin/settings.
9) Broadcast: /admin/broadcasts -> Gửi -> chạy queue 'broadcast'.
10) Inbox: /{page}/inbox -> gửi/nhận tin (queue 'fb-send').
