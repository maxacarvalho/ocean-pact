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
3. Crie um arquivo chamado `.npmrc` na raiz do projeto com o conteúdo abaixo:
   ```
    @fortawesome:registry=https://npm.fontawesome.com/
    //npm.fontawesome.com/:_authToken=<PARA ADQUIRIR O TOKEN ENTRE EM CONTATO COM O ADMINISTRADOR DO PROJETO>
    ```
4. Gere o certificado para HTTPS com o comando `mkcert -key-file key.pem -cert-file cert.pem oceanpact.dev`
5. Execute o script `docker/bin/build`. Esse script vai construir as imagens Docker do projetos
6. Inicie os containers com o script `docker/bin/start`
7. Instale as dependências do composer com o script `docker/bin/composer install`
8. Instale as dependências do npm com o script `docker/bin/npm install`
9. Compile os arquivo do front-end com o script `docker/bin/npm run build`
10. Rode as migrations com o comando `docker/bin/artisan migrate`
11. Crie um usuário para acesso `docker/bin/artisan make:filament-user`
12. Gere a APP_KEY com o comando `docker/bin/artisan key:generate`
13. Adicione o domínio `oceanpact.dev` no arquivo `/etc/hosts`
    ```
    127.0.0.1        oceanpact.dev
    ::1              oceanpact.dev
    ```
14. Acesse o sistema em `https://oceanpact.dev`

## Dando acesso super_admin para o seu usuário no ambiente local

Após se conectar ao banco de dados, execute os seguintes comandos no seu editor de SQL:

```sql
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES(1, 'super_admin', 'web', '2023-01-17 09:34:25', '2023-01-17 09:34:25');

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES(1, 'App\\Models\\User', 1);
```
