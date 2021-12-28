# Para subir o projeto, execute os comandos
* docker compose run --rm --service-ports hyperf-skeleton
* composer install
* php bin/hyperf.php vendor:publish hyperf/watcher

# Para executar o projeto, execute o comando:

* php bin/hyperf.php server:watch

O serviço roda em CLI, entao ele vai estar disponivel na rota http://0.0.0.0:9501
POC construida seguindo o tutorial  https://leocarmo.dev/hyperf-php-coroutine-framework-baseado-em-swoole