run symfony in swoole

1 install 
```text
    composer require daodao97/swoole-http-server-bundel:div-master
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

4 nginx proxy config
```text
    server {
        listen       80;
        server_name  youdomain.com;
        location / {
            proxy_connect_timeout 300;
            proxy_send_timeout 300;
            proxy_read_timeout 300;
            send_timeout 300;
            proxy_set_header X-Real-IP  $remote_addr;
            proxy_set_header Host $host;
            proxy_pass http://127.0.0.1:2345/;
            proxy_redirect off;
        }
    }
```
