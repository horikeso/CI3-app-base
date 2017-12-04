codeigniter初期設定（このdoc以外がcodeigniterを設置して微調整したものです）
doc以外を捨ててしまった場合に1から作りだす手順（サーバー側の設定は行う必要があります）
(実装した基本機能は触れません)

## codeigniter設置

https://www.codeigniter.com/download

codeigniterを配置しておく
(言語ファイルもDLしてjapaneseをsystem\languageに配置してconfigもlanguageをjapaneseにする)

## フォルダ構成調整

不要フォルダ・ファイル削除

```
user_guide
readme.rst
contributing.md
```

管理画面クラス格納用

```
application/controllers/backend
```

テンプレートファイル格納用

```
application/views/templates
application/views/templates/backend
application/views/templates_c
```

ルートの.gitignoreに以下を追加

```
application/views/templates_c/*
```

## PHPUnit導入

```
composer require phpunit/phpunit:6.0.*
composer require kenjis/ci-phpunit-test --dev
php vendor/kenjis/ci-phpunit-test/install.php
```

composer.json

```
"scripts": {
    "test": "phpunit -c /home/public/app/application/tests"
},
```

ルートの.gitignoreに以下を追加

```
application/tests/build/*
```

## Smartyの導入

ルートのディレクトリに移動する

composer.json

```
"require": {
    "smarty/smarty": "^3.1"
},
```

Smarty読み込み用ライブラリ追加

```
cp doc/Smarty.php system/libraries/Smarty.php
```

application/config/autoload.php

```
$autoload['libraries'] = array('smarty');
```

application/config/config.php

```
$config['composer_autoload'] = './vendor/autoload.php';
```

## ログの設定

application/config/config.php

```
$config['log_threshold'] = 3;// Informational Messages
$config['log_path'] = 'application/logs/';
$config['log_file_extension'] = '.log';
```

ログファイルをログレベル毎に分ける

```
cp doc/MY_Log.php application/core/MY_Log.php
```

出力は方法は

```
log_message('info', 'wellcom');
```

## Nginx用の設定（nginxでは.htaccessは使えない）

CI_ENV developmentの設定をproductionにすると画面エラーは出なくなります。
アプリケーションログは記録されるので問題ありません。
fatalログはアプリ側でキャッチできないので/var/log/nginxのログを確認します。

application/config/config.php

```
$config['index_page'] = '';
```

nginx.conf

```
        # 413 Request Entity Too Large
        client_max_body_size 20M;

        root   /home/public/app;
        index  index.php index.html index.htm;

        location / {
            try_files $uri $uri/ /index.php;
        }

        location ~ application/.* { deny all; }
        location ~ doc/.* { deny all; }
        location ~ system/.* { deny all; }

        if ($request_filename ~* composer.json) {
            return 403;
        }
        if ($request_filename ~* .gitignore) {
            return 403;
        }
        if ($request_filename ~* license.txt) {
            return 403;
        }

        error_page   403 404 /404.html;

        location ~ \.php$ {
            root           /home/public/app;
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
            fastcgi_param CI_ENV development;
            include        fastcgi_params;
        }
```

```
nginx -s reload
```

## 画面へのプロファイラの出力方法

```
$this->output->enable_profiler(TRUE);
```

## セキュリティの設定

```
$config['csrf_protection'] = TRUE;
```

# テストの仕方

アプリのルートディレクトリで

```
composer test
```

もしくは

```
vendor/bin/phpunit -c application/tests
```

# DB初期構築

アプリのルートディレクトリで

```
mysql -u root -p[password] < doc/app.sql
mysql -u root -p[password] < doc/app_test.sql
```

# DB設定

application/config/autoload.php

```
$autoload['libraries'] = array('smarty', 'database');
```

application/config/database.phpを設定する

## DBエラーを扱えるようにする

application/config/database.php

```
$db['default']['db_debug'] = FALSE;
```

この設定をしないとDBエラーの際にその場で止まり先の処理も行われない。
DBエラー自体はExceptionでキャッチできないのでエラー処理がうまくできない。
この設定をすることによってその場で止まらなくなるので先の処理にたどり着けるので

```
$this->db->affected_rows();
```

やらでうまく扱うことができるようになる。
また、エラー内容を取得したい場合は

```
$this->db->error();
```

で取得できる

```
例）エラーがある場合
array(2) {
  ["code"]=>
  int(1048)
  ["message"]=>
  string(32) "Column 'password' cannot be null"
}

例）エラーがない場合
array(2) {
  ["code"]=>
  int(0)
  ["message"]=>
  string(0) ""
}
```

# memchached設定

application/config/autoload.php

```
$autoload['drivers'] = array('cache');
```

# その他設定

application/config/autoload.php

```
$autoload['helper'] = array('url');
```

どこでも```redirect()```が使えるようになります

# 基本機能のみ作成済み

バックエンド（初回時 adminユーザー登録画面 初回以降 ログイン画面）
/backend/login

JSON API
GET /backend/api/user?page=3&unit=3

フロント
/

