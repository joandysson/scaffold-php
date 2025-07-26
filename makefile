bash:
	docker exec -it app sh

#  make cron name=CreateNewsletter
cron:
	docker exec -it app ./cron.sh $(name)

up:
	docker compose up -d

down:
        docker compose down

# Executa todos os testes e analises no container
ci:
	docker exec -it app composer phpcs
	docker exec -it app composer phpstan
	docker exec -it app composer audit --no-interaction
	docker exec -it app composer test
