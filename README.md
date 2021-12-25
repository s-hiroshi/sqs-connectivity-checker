# symfony-messenger-playground

- https://symfony.com/doc/current/components/messenger.html
- https://symfony.com/doc/current/messenger.html

## symfony/consoleを導入

** composerをGitpod用にプロジェクトルートに配置しています。**

ref. https://symfony.com/doc/4.4/components/console.html

`Composer`がインストール済みとします。

1. `$ composer require "symfony/console"`
1. `composer.json`に`autoload`を追加
1. `$ composer dump-autoload`
1. コマンド実行ファイル`bin/console`を作成
1. `bin/console`に実行権を付与 `$ chmod u+x console`
1. `src/Command/MessageCommand.php`を作成
1. コマンド実行 `$ bin/console command:message "Hello World" --name=Hiroshi --name=Sawai` 

### composer.jsonにautoloadを追加

名前空間は`VSC\Messenger\`とします。

```json
"autoload": {
    "psr-4": {
        "VSC\\Messenger\\": "src/"
    }
},
```

### composer dump-autoload

`autoload.php`を更新する。

```sh
$ composer dump-autoload
```

### コマンド実行ファイルbin/consoleを作成

実行権を付与。

```sh
$ chmod u+x console
```

## symfony/dependency-injectionを導入

Symfony CommandをSymfony DependencyInjection Componentを使用してServiceとして実行します。

ref. 

- https://symfony.com/doc/current/service_container.html
- https://symfony.com/doc/4.4/components/dependency_injection.html


必要なコンポーネントをインストールします。

```sh
$ composer require symfony/dependency-injection
# symfony/configは必用
$ composer require symfony/config
$ composer require symfony/yaml
```

`config/service.yml`でServiceを定義します。

```yml
services:
  message.generator:
    class: VSC\Messenger\Service\MessageGenerator

  # command
  command.message:
    class: VSC\Messenger\Command\MessageCommand
    arguments:
      $messageGenerator: '@message.generator'

  # app
  app:
    class: Symfony\Component\Console\Application
    calls:
      - add: ['@command.message']
    public: true
```

`bin/console`でサービスとして実行します。

```php
$containerBuilder = new ContainerBuilder();
$loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__));
$loader->load(__DIR__ . '/../config/services.yaml');

$containerBuilder->compile(true);

$application = $containerBuilder->get('app');
$application->run();
```