all: docker_update

docker_update:
	docker-compose up --no-deps -d php-fpm database

	docker exec -ti neural-php sh -c "composer update"

	docker exec -ti neural-database sh -c "mysql -u root -proot -e 'create database if not exists neural;'"
	docker exec -ti neural-php sh -c "bin/console orm:schema-tool:update --dump-sql"
	docker exec -ti neural-php sh -c "bin/console orm:schema-tool:update --force"

	docker-compose up -d

	docker exec -ti neural-nodejs sh -c "bower i --allow-root"

bash:
	docker exec -ti neural-php bash

restart_websocket_server:
	docker restart neural-ws

nodejs:
	docker exec -ti neural-nodejs bash

learn_generate_samples:
	docker exec -ti neural-php sh -c "bin/console neural:sample:parse"

learn_init: learn_generate_samples
	docker exec -ti neural-php sh -c "bin/console neural:network:create"

learn_test:
	docker exec -ti neural-php sh -c "bin/console neural:network:test"

learn_train:
	docker exec -ti neural-php sh -c "bin/console neural:network:train"

learn_reset:
	docker exec -ti neural-php sh -c "bin/console orm:schema-tool:drop --force"
	docker exec -ti neural-php sh -c "bin/console orm:schema-tool:update --force"
	docker exec -ti neural-php sh -c "bin/console neural:network:create"
