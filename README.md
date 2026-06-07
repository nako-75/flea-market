# flea-market-app
＃　新模擬案件_フリマアプリ

＃＃　Dockerビルド. 
ー　git clone
ー　docker-compose up -d —build

＃＃　環境構築. 
ー　1. docker-compose exec php bash 
ー　2. composer install
ー　3. cp .env.example .env (環境変数を適宜変更)
ー　4. php artisan key:generate
ー　5. php artisan migrate
ー　6. php artisan db:seed

＃＃　テスト環境の構築. 
※`mysql_test` を使用したフィーチャーテスト
ー　1. docker-compose exec php bash 
ー　2.  mysql -u root -p 
ー　3. CREATE DATABASE  demo_test;
ー　4. cp .env .env.testing
ー※.env.testingの内容を下記に設定してください
    APP_ENV=test 
    APP_KEY=
    DB_CONNECTION=mysql_test
    DB_DATABASE=demo_test 
    DB_USERNAME=root 
    DB_PASSWORD=root
ー　5. php artisan key:generate --env=testing
ー　6. php artisan config:clear
ー　7. php artisan migrate --env=testing
ー　8. php artisan test

＃＃　使用技術（実行環境）. 
ー　PHP: 8.1.x (fpm)
ー　Framework：Laravel 8.83.29
ー　Database：mysql:8.0.26
ー　Web Server：nginx:1.21.1
ー　OS：macOS

＃＃　ER図
<img width="572" height="571" alt="Image" src="https://github.com/user-attachments/assets/fc7b89b6-5e3a-4efc-b599-84f9732a2de0" />

＃＃　URL
ホーム画面　 http://localhost/
phpMyAdmin http://localhost:8080/

＃＃　補足
🔳.env に Stripe のテストキーを設定してください
🔳エラーメッセージは機能要件に加えて、全てのバリデーションに対し表示するように設定しています。