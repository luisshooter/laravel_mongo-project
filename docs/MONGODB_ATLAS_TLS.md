# Erro de TLS ao conectar no MongoDB Atlas

Se aparecer **ConnectionTimeoutException** ou **TLS handshake failed** ao usar MongoDB Atlas (ex.: com PHP 8.5 no Windows), tente na ordem abaixo.

## 1. Liberar acesso por IP no Atlas

No [MongoDB Atlas](https://cloud.mongodb.com) → seu projeto → **Network Access**:

- Clique em **Add IP Address**.
- Use **Allow Access from Anywhere** (`0.0.0.0/0`) para desenvolvimento.
- Salve e aguarde 1–2 minutos.

## 2. Workaround de TLS no .env (só para desenvolvimento)

No `.env`, adicione:

```env
DB_TLS_ALLOW_INVALID=true
```

Isso faz a conexão aceitar certificados inválidos e costuma contornar falhas de handshake com PHP 8.5 + OpenSSL. **Não use em produção.**

Depois rode:

```bash
php artisan config:clear
```

e teste o login de novo.

## 3. Incluir o banco na URI (recomendado)

Deixe a URI no `.env` com o nome do banco no caminho, por exemplo:

```env
DB_URI="mongodb+srv://USUARIO:SENHA@cluster.xxxxx.mongodb.net/laravel_orders?retryWrites=true&w=majority"
```

Substitua `USUARIO`, `SENHA` e o host do cluster. O trecho `/laravel_orders` é o nome do banco (pode ser outro, desde que igual a `DB_DATABASE`).

## 4. Usar PHP 8.2 ou 8.3

Se o erro continuar com PHP 8.5, use PHP 8.2 ou 8.3 (por exemplo com [XAMPP](https://www.apachefriends.org), [Laravel Herd](https://herd.laravel.com) ou [php.net](https://www.php.net/downloads)). PHP 8.5 é muito novo e em alguns ambientes o driver MongoDB + Atlas ainda pode falhar no TLS.

## 5. Conferir usuário e senha do Atlas

No Atlas → **Database Access** → o usuário usado na URI deve existir e ter permissão de leitura/escrita no banco (ex.: **Read and write to any database**).

---

Depois de qualquer alteração no `.env`, execute:

```bash
php artisan config:clear
```

e teste novamente.
