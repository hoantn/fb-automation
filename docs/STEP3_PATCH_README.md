# FB Automation – Step 3 Patch (Pivot + Token Sync + Auto Subscribe)

Hướng dẫn:
1) Giải nén đè vào project.
2) Chạy:
   php artisan migrate
   php artisan optimize:clear

Tác dụng:
- Ghi kép token (pages.access_token + page_tokens).
- Tự động attach user<->page vào pivot `page_user` khi Connect.
- Trang Home hiển thị page qua pivot.
- Tự động subscribe sau khi connect.
