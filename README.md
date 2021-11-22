# 環境構築手順

コンテナ作成後、コンテナ内のドキュメントルート直下で以下のコマンドを実行
```bash
laravel new portal
```
laravelのプロジェクト作成{poratl}はプロジェクト名のため可変になっているが  
ドキュメントルートを"/var/www/html/portal"を設定しているため  
プロジェクト名を変更する場合は"/app/apache/000-default.conf"を変更する必要がある

# ディレクトリ移動 
```bash
cd /var/www/html/portal
```

# 以下拡張要件
とりあえず、インストール
```bash
npm install
```
# bootstrap
基本のデザインはこれ
```bash
npm install bootstrap
```
# bootstrap icons
アイコンはこれ
```bash
npm i bootstrap-icons
```
# webpack.mix.js
以下２レコードを追加、publicから資産の呼び出しをできるように変更
```bash
mix.copy('node_modules/bootstrap-icons', 'public/bootstrap-icons');
mix.copy('node_modules/bootstrap', 'public/bootstrap');
```
# session.php
.envが優先なので修正しても反映されない可能性があります
```bash
/*
|--------------------------------------------------------------------------
| Session Cookie Name
|--------------------------------------------------------------------------
|
| Here you may change the name of the cookie used to identify a session
| instance by ID. The name specified here will get used every time a
| new session cookie is created by the framework for every driver.
|
*/

'cookie' => env(
    'SESSION_COOKIE',
    Str::slug(env('APP_NAME', 'pdflyer'), '_') . '_session'
),
```
# 以下は要件にあわせて、設定
```bash
/*
|--------------------------------------------------------------------------
| HTTPS Only Cookies
|--------------------------------------------------------------------------
|
| By setting this option to true, session cookies will only be sent back
| to the server if the browser has a HTTPS connection. This will keep
| the cookie from being sent to you when it can't be done securely.
|
*/

'secure' => env('SESSION_SECURE_COOKIE', true),
```
## 注意点
基本的には開発と本番を変更する必要はなし  
※ ただし、HTTPS Only Cookiesをtrueにしていると
httpからのアクセスができないので要注意です。  
（本番はちゃんとしたSSL証明書を設置すること）
# app.php
.envが優先なので修正しても反映されない可能性があります
```bash
/*
|--------------------------------------------------------------------------
| Application Timezone
|--------------------------------------------------------------------------
|
| Here you may specify the default timezone for your application, which
| will be used by the PHP date and date-time functions. We have gone
| ahead and set this to a sensible default for you out of the box.
|
*/

'timezone' => 'Asia/Tokyo',

/*
|--------------------------------------------------------------------------
| Application Locale Configuration
|--------------------------------------------------------------------------
|
| The application locale determines the default locale that will be used
| by the translation service provider. You are free to set this value
| to any of the locales which will be supported by the application.
|
*/

'locale' => 'ja',
```
ここまでで基本的には動作します
-

# OpenShift
### ・ DockeFile注意点抜粋
## node_modulesをインストールするためにNodeJSをいれます
```bash
RUN apt install -y nodejs
```
DockerFileに"npm install"を入れてないので、本番環境では使用していません  
※ 開発の段階で使用します（ホントはinstall環境が同じになる。。）

# Laravelで必要になるmodRewriteを有効化する
```bash
RUN mv /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled
RUN /bin/sh -c a2enmod rewrite
```
※ 詳細はこちら
https://qiita.com/Y-Kanoh/items/42af3c2f635653c4eaf7
## cpmposerのインストール
※ gitignoreでGitHubアップ対象外になっているため、インストールが必要です  
composer.jsonが存在する場所で実行が必要なため、WORKDIRで移動してます
```bash
WORKDIR /var/www/html/portal
RUN composer install
```
## chown
OpenShiftでPodを作成すると、すべてroot権限になります  
そのため、ファイル作成などの処理がパーミッションでエラーとなるため  
オーナーを"www-data"に変更します
```bash
RUN chown -R www-data:www-data ./
```
