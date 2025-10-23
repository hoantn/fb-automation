# FB Automation – Step 3 Fix Patch

Nội dung:
- `app/Models/Setting.php`: lưu tương thích mọi schema (`value`, `value_json`, `type` NOT NULL).
- `app/Services/FacebookService.php`: thêm `ensurePageSubscription()`, log lỗi gửi tin nhắn.
- `app/Http/Controllers/WebhookController.php`: luôn 200, chỉ dispatch khi có job.
- `app/Providers/AppServiceProvider.php`: tự động subscribe khi Page được lưu/cập nhật token.
- `app/Console/Commands/FbResubscribePages.php`: lệnh `php artisan fb:resubscribe`.

Sau khi giải nén ghi đè:
1) `php artisan optimize:clear`
2) (tuỳ chọn) `php artisan fb:resubscribe`
