run symfony in swoole

1 install 
```text
    composer require andreybolonin/swoole-http-server-bundle:dev-master
```

2 define commands in services.yaml
```text
    Swoole\HttpServerBundle\Command\RunCommand:
      tags:
          - { name: 'console.command', command: 'swoole:run' }

    Swoole\HttpServerBundle\Command\StatusCommand:
      tags:
          - { name: 'console.command', command: 'swoole:status' }

    Swoole\HttpServerBundle\Command\StopCommand:
      tags:
          - { name: 'console.command', command: 'swoole:stop' }
          
    Swoole\HttpServerBundle\Command\StartCommand:
      tags:
          - { name: 'console.command', command: 'swoole:start' }
```

2 swoole http server command
```text
    * bin/console swoole:run   --evn=dev
    * bin/console swoole:start --evn=prod
    * bin/console swoole:status
    * bin/console swoole:stop
    * bin/console swoole:reload
```

3 nginx proxy config
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
