# 新模擬案件_フリマアプリ

## Dockerビルド<br>
- &nbsp;git clone git@github.com:nako-75/flea-market.git<br>
- &nbsp;cd flea-market<br>
- &nbsp;docker compose up -d --build<br>

## 環境構築<br>
1.&nbsp;docker compose exec php bash<br> 
2.&nbsp;composer install<br>
3.&nbsp;cp .env.example .env (環境変数を適宜変更)<br>
4.&nbsp;php artisan key:generate<br>
5.&nbsp;php artisan migrate<br>
6.&nbsp;php artisan db:seed<br>

## テスト環境の構築<br>
※`mysql_test` を使用したフィーチャーテスト<br>

テスト用データベース作成
- &nbsp;docker compose exec mysql bash<br> 
- &nbsp;mysql -u root -p<br> 
- &nbsp;CREATE DATABASE  demo_test<br>
- &nbsp;exit

.envの設定  
- &nbsp;docker compose exec php bash<br>
- &nbsp;cp .env .env.testing<br>
&emsp;※.env.testingの内容を下記に設定してください<br>
&emsp;&emsp;APP_ENV=test<br>
&emsp;&emsp;APP_KEY=<br>
&emsp;&emsp;DB_CONNECTION=mysql_test<br>
&emsp;&emsp;DB_DATABASE=demo_test<br>
&emsp;&emsp;DB_USERNAME=root<br>
&emsp;&emsp;DB_PASSWORD=root<br>
- &nbsp;php artisan key:generate --env=testing<br>
- &nbsp;php artisan config:clear<br>
- &nbsp;php artisan migrate --env=testing<br>
- &nbsp;php artisan test<br>

## 使用技術（実行環境）<br> 
- &nbsp;PHP: 8.1.x (fpm)<br>
- &nbsp;Framework：Laravel 8.83.29<br>
- &nbsp;Database：mysql:8.0.26<br>
- &nbsp;Web Server：nginx:1.21.1<br>
- &nbsp;SMTP Server : mailhog
- &nbsp;OS：macOS<br>

## ER図<br>
<img width="572" height="571" alt="Image" src="https://github.com/user-attachments/assets/fc7b89b6-5e3a-4efc-b599-84f9732a2de0" />

## URL<br>
ホーム画面&nbsp;&nbsp;http://localhost/<br>
phpMyAdmin&nbsp;http://localhost:8080/<br>

## 補足<br>
🔳.env に Stripe のテストキーを設定してください<br>
🔳エラーメッセージは機能要件に加えて、全てのバリデーションに対し表示するように設定しています。