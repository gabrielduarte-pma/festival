# Festival Aracati — sistema Laravel

App público (PWA) servido em `/` com dados do banco (`/dados.js`), painel do administrador em `/painel`
e área do expositor em `/meu-stand`. Login em `/login` (cadastro aberto desativado; acesso por convite).

## Primeira instalação (local)
```bash
php artisan migrate --seed        # cria tabelas, 119 stands, programação e o admin (.env ADMIN_*)
php artisan storage:link          # uploads de fotos/cardápios
php artisan festival:admin seu@email.com SuaSenha --nome="Seu Nome"   # (opcional) criar/atualizar admin
composer dev                      # ou: php artisan serve + npm run dev
php artisan test
```

## Fluxo
1. Admin entra em `/login` → `/painel`.
2. **Expositores → Novo**: nome, e-mail e stands → o sistema mostra o **link de convite** (válido 14 dias). Envie por WhatsApp.
3. O expositor abre o link, cria a senha e cai em `/meu-stand`, onde edita sobre, horário, Instagram, WhatsApp, cardápio (link ou PDF) e fotos. Vale na hora no app.
4. Esqueceu a senha? Em Expositores → Editar → **Gerar novo link**.
5. **Mapa/GPS**: informe lat/lng de INÍCIO e FIM (mín. 2 pontos; 3+ melhora).

## Produção (EasyPanel + MySQL)
- Crie um serviço MySQL e um serviço "App" a partir do repositório (usa o `Dockerfile`).
- Variáveis: `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY` (php artisan key:generate --show), `APP_URL=https://seu-dominio`,
  `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT=3306`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`,
  `SESSION_DRIVER=database`, `ADMIN_EMAIL`, `ADMIN_PASSWORD`, `MAIL_*` (para recuperação de senha, opcional).
- Monte um **volume persistente** em `/var/www/html/storage/app/public` (fotos e cardápios enviados).
- Depois do primeiro deploy, no terminal do serviço: `php artisan db:seed --force` (apenas uma vez).
