run symfony in swoole

1 install 
```text
    composer require daodao/swoole-http-server-bundel:div-master
```

2 register in AppKernel
```php
    $bundles = [
        ...
        new Swoole\HttpServerBundle\SwooleHttpServerBundle(),
        ...
    ];
```

3 swoole http server command
```text
    * bin/consoel swoole:run   --evn=dev
    * bin/consoel swoole:start --evn=prod
    * bin/consoel swoole:status
    * bin/consoel swoole:stop
    * bin/consoel swoole:reload
```
