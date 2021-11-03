<h1 align="center">Flysystem Upyun (又拍云)</h1>

<p align="center">floppy_disk: 又拍云 Flysystem 实现</p>

# Requirement

- PHP >= 5.6

# 视频教程

> 视频使用教程(当然是免费的)：https://www.codecasts.com/series/awesome-laravel-packages/episodes/8

# 安装

直接可以通过 composer 来安装:

```shell

$ composer require "yzh52521/flysystem-upyun"
```

# 使用

## 1.在一般项目中使用

```php

use yzh52521\flysystem\Filesystem;
use yzh52521\flysystem\Upyun\UpyunAdapter;

$bucket = 'your-bucket-name';
$operator = 'oparator-name';
$password = 'operator-password';
$domain = 'xxxxx.b0.upaiyun.com'; // 或者 https://xxxx.b0.upaiyun.com
$protocol='https', // 服务使用的协议，如需使用 http，在此配置 http

$adapter = new UpyunAdapter($bucket, $operator, $password, $domain,$protocol);

$flysystem = new Filesystem($adapter);

```

# API 和方法调用

```php

bool $flysystem->write('file.md', 'contents');

bool $flysystem->writeStream('file.md', fopen('path/to/your/local/file.jpg', 'r'));

bool $flysystem->update('file.md', 'new contents');

bool $flysystem->updateStram('file.md', fopen('path/to/your/local/file.jpg', 'r'));

bool $flysystem->rename('foo.md', 'bar.md');

bool $flysystem->copy('foo.md', 'foo2.md');

bool $flysystem->delete('file.md');

bool $flysystem->has('file.md');

string|false $flysystem->read('file.md');

array $flysystem->listContents();

array $flysystem->getMetadata('file.md');

int $flysystem->getSize('file.md');

string $flysystem->getUrl('file.md'); 

string $flysystem->getMimetype('file.md');

int $flysystem->getTimestamp('file.md');

```

# License

MIT
