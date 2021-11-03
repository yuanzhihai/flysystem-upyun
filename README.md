<h1 align="center">Flysystem Upyun (又拍云)</h1>

<p align="center">floppy_disk: 又拍云 Flysystem 实现</p>

# Requirement

- PHP >= 7.2
- League/Filesystem >=2.0 


# 安装

直接可以通过 composer 来安装:

```shell

$ composer require yzh52521/flysystem-upyun 2.0
```

# 使用

## 1.在一般项目中使用

```php

use League\Flysystem\Filesystem;
use yzh52521\Flysystem\Upyun\UpyunAdapter;

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

bool $flysystem->copy('foo.md', 'foo2.md');

bool $flysystem->delete('file.md');

bool $flysystem->has('file.md');

string|false $flysystem->read('file.md');

array $flysystem->listContents();

array $flysystem->getMetadata('file.md');

int $flysystem->getSize('file.md');

string $flysystem->getUrl('file.md'); 

string $flysystem->getMimetype('file.md');


```

# License

MIT
