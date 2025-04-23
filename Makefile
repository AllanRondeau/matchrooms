.PHONY: dev watch build install clean

dev:
	docker-compose exec app npm run dev

watch:
	docker-compose exec app npm run dev

build:
	docker-compose exec app npm run build

install:
	docker-compose exec app npm install

clean:
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan view:clear
