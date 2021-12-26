# SQS-Connectivity-Checker

ECRとSQSの疎通を確認します。
タスクとして実行することを想定しています。


## 環境構築

```shell
$ composer install
```

## ローカル Dockerを使用して確認

1. `docker build`でイメージ作成
1. `docker run`でSymfony コマンド実行

```shell
# シェル変数にローカルの`default`のクレデンシャルを設定
AWS_REGION=$(aws configure get region)
AWS_ACCESS_KEY_ID=$(aws configure get aws_access_key_id)
AWS_SECRET_ACCESS_KEY=$(aws configure get aws_secret_access_key)
```

### 送信確認

Symfony Command：sqs:send

```shell
# -eでSQSキューのURLと↑でシェル変数に格納したクレデンシャルをコンテナに環境変数として渡す
$ docker run -e QUEUE_URL=https://sqs.ap-northeast-1.amazonaws.com/{{アカウントID}}/{{キューURL}} \
             -e AWS_REGION=$AWS_REGION \
             -e AWS_ACCESS_KEY_ID=$AWS_ACCESS_KEY_ID \
             -e AWS_SECRET_ACCESS_KEY=$AWS_SECRET_ACCESS_KEY \
             bin/console sqs:send
````

### 受信確認

Symfony Command：sqs:receive

```shell
# -eでSQSキューのURLと↑でシェル変数に格納したクレデンシャルをコンテナに環境変数として渡す
$ docker run -e QUEUE_URL=https://sqs.ap-northeast-1.amazonaws.com/{{アカウントID}}/{{キューURL}} \
             -e AWS_REGION=$AWS_REGION \
             -e AWS_ACCESS_KEY_ID=$AWS_ACCESS_KEY_ID \
             -e AWS_SECRET_ACCESS_KEY=$AWS_SECRET_ACCESS_KEY \
             bin/console sqs:send
````

## AWS ECSを使用して確認

1. ECRにリポジトリを作成＆イメージをPush
1. （必要なら）クラスタを作成
1. タスク定義を作成
1. タスクを実行

タスクを実行する際に以下のようにコンテナを上書きします（例:sqs:receive）

![コンテナを上書き](aws-ecs.png)