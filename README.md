## Portal de Cotações / IntegraHub

## Instalação

### Pré-requisitos
- Docker
- Docker Compose
- Git
- [mkcert](https://github.com/FiloSottile/mkcert)

### Passo a passo

1. Clone o repositório
2. Copie o arquivo `.env.example` para `.env`
3. Crie um arquivo chamado `.npmrc` na raiz do projeto com o conteúdo abaixo
4. Gere o certificado para HTTPS com o comando `mkcert -key-file docker/web/tls/key.pem -cert-file docker/web/tls/cert.pem oceanpact.dev`
5. Execute o comando `docker-compose up -d`
6. Instale as dependências do composer com o comando `docker-compose exec app composer install`
7. Instale as dependências do npm com o comando `docker-compose exec node npm install`
8. Compile os arquivo do front-end com o comando `docker-compose exec node npm run build`
9. Rode as migrations com o comando `docker-compose exec app php artisan migrate`
10. Crie um usuário para acesso `docker-compose exec app php artisan make:filament-user`
11. Gere a APP_KEY com o comando `docker-compose exec app php artisan key:generate`
12. Adicione o domínio `oceanpact.dev` no arquivo `/etc/hosts`
    ```
    127.0.0.1        oceanpact.dev
    ::1              oceanpact.dev
    ```
13. Acesse o sistema em `https://oceanpact.dev`

#### Arquivo .npmrc
```
@fortawesome:registry=https://npm.fontawesome.com/
//npm.fontawesome.com/:_authToken=<PARA ADQUIRIR O TOKEN ENTRE EM CONTATO COM O ADMINISTRADOR DO PROJETO>
```

## Dando acesso super_admin para o seu usuário no ambiente local

Após se conectar ao banco de dados, execute os seguintes comandos no seu editor de SQL:

```sql
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES(1, 'super_admin', 'web', '2023-01-17 09:34:25', '2023-01-17 09:34:25');

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES(1, 'App\\Models\\User', 1);
```
