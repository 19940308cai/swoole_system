# Nginx配置
```
server {
    listen       8787;
    server_name  view.swoole_client.com;
    root /Users/caijiang/work/swooleProject;
    index index.html;
}


upstream wsbackend {
  server 127.0.0.1:9093;
}

server {
      listen 8088;
      server_name www.swoole_client.com;
      index index.php index.html;
      add_header Access-Control-Allow-Origin 'http://view.swoole_client.com:8787';
      add_header Access-Control-Allow-Credentials true;
      add_header Access-Control-Allow-Methods *;

      location /ws {
           proxy_pass http://wsbackend;
           proxy_http_version 1.1;
           proxy_set_header Upgrade $http_upgrade;
           proxy_set_header Connection "upgrade";
      }

      location / {
          # First attempt to serve request as file, then
          # as directory, then fall back to displaying a 404.
          try_files $uri $uri/ =404;

          if (!-e $request_filename) {
              rewrite ^(.*)$ /index.php?s=$1 last;
          }
      }

      root /Users/caijiang/work/swooleProject;
      location ~ \.php {
          fastcgi_pass   127.0.0.1:9000;
          fastcgi_index  index.php;
          fastcgi_split_path_info ^(.+\.php)(.*)$;     #增加这一句
          fastcgi_param PATH_INFO $fastcgi_path_info;    #增加这一句
          fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
          include fastcgi_params;
          include fastcgi.conf;
      }
}
```
