# API de Gerenciamento de Eventos — Laravel

API REST para cadastrar eventos e gerenciar inscrições de usuários. Não tem frontend: tudo funciona por requisições HTTP (Postman).

---

## 1. O que é usado

| Tecnologia | Para que serve |
|---|---|
| PHP 8.2+ | Linguagem |
| Composer | Instala o Laravel |
| Laravel | Framework da API |
| MySQL | Banco de dados |
| Laravel Sanctum | Login por token |
| Postman | Testes |

---

## 2. Criar o projeto Laravel

```bash
composer create-project laravel/laravel api-eventos
cd api-eventos
```

## 3. Configurar o banco de dados

Crie o banco no MySQL (phpMyAdmin, Workbench ou terminal):

```sql
CREATE DATABASE api_eventos;
```

Abra o arquivo `.env` na raiz do projeto e altere as linhas do banco:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_eventos
DB_USERNAME=root
DB_PASSWORD=
```

> Se as linhas `DB_HOST`, `DB_PORT` etc. estiverem com `#` na frente, apague o `#`.
> Em `DB_PASSWORD`, coloque a senha do seu MySQL (no XAMPP normalmente é vazia).

## 4. Instalar o Sanctum

```bash
php artisan install:api
```

Esse comando instala o Sanctum, cria a migration de tokens e o arquivo `routes/api.php`.
Se ele perguntar se deseja rodar as migrations, pode responder **yes**.

## 5. Copiar os arquivos do projeto

Copie os arquivos desta pasta para dentro do projeto, **substituindo** os que já existirem:

| Arquivo | Ação |
|---|---|
| `app/Models/User.php` | substituir |
| `app/Models/Evento.php` | novo |
| `app/Models/Inscricao.php` | novo |
| `app/Http/Requests/RegistroRequest.php` | novo |
| `app/Http/Requests/LoginRequest.php` | novo |
| `app/Http/Requests/EventoRequest.php` | novo |
| `app/Http/Resources/UserResource.php` | novo |
| `app/Http/Resources/EventoResource.php` | novo |
| `app/Http/Resources/InscricaoResource.php` | novo |
| `app/Http/Controllers/AuthController.php` | novo |
| `app/Http/Controllers/EventoController.php` | novo |
| `app/Http/Controllers/InscricaoController.php` | novo |
| `database/migrations/2026_11_11_000001_create_eventos_table.php` | novo |
| `database/migrations/2026_11_11_000002_create_inscricoes_table.php` | novo |
| `routes/api.php` | substituir |
| `bootstrap/app.php` | substituir |

> As pastas `Requests` e `Resources` não existem no Laravel novo. É só criá-las.

## 6. Executar as migrations

```bash
php artisan migrate
```

Tabelas criadas: `users`, `personal_access_tokens` (tokens do Sanctum), `eventos`, `inscricoes` e as tabelas padrão do Laravel (cache, jobs, sessions).

Para apagar tudo e recriar do zero (útil antes da apresentação):

```bash
php artisan migrate:fresh
```

## 7. Iniciar o servidor

```bash
php artisan serve
```

A API fica disponível em `http://localhost:8000/api`.

Para conferir as rotas criadas:

```bash
php artisan route:list --path=api
```

---

## 8. O que faz cada arquivo

**Migrations** — criam as tabelas no banco.
- `create_eventos_table`: tabela de eventos.
- `create_inscricoes_table`: tabela de inscrições. A linha `unique(['user_id', 'evento_id'])` impede que o mesmo usuário se inscreva duas vezes no mesmo evento, direto no banco.

**Models** — representam as tabelas e os relacionamentos.
- `User`: usuário. `HasApiTokens` permite gerar tokens. Um usuário **tem muitas** inscrições.
- `Evento`: evento. **Tem muitas** inscrições. O método `vagasDisponiveis()` calcula: máximo de participantes − inscrições.
- `Inscricao`: inscrição. **Pertence a** um usuário e **pertence a** um evento. A linha `$table = 'inscricoes'` existe porque o Laravel pluralizaria errado ("inscricaos").

**Form Requests** — validam os dados antes de chegar ao controller. Se algo estiver errado, o Laravel responde 422 automaticamente.
- `RegistroRequest`: nome, e-mail (único) e senha (mínimo 6).
- `LoginRequest`: e-mail e senha.
- `EventoRequest`: usado para criar e para atualizar evento. Nome obrigatório.

**API Resources** — definem o formato do JSON de resposta.
- `UserResource`: nunca devolve a senha.
- `EventoResource`: inclui o campo `vagas_disponiveis`.
- `InscricaoResource`: mostra a data da inscrição e os dados do evento.

**Controllers** — executam as operações.
- `AuthController`: cadastro, login, me e logout.
- `EventoController`: CRUD de eventos.
- `InscricaoController`: inscrever, listar minhas inscrições e cancelar. Aqui ficam as regras de duplicidade, lotação e dono da inscrição.

**bootstrap/app.php** — garante que **todos os erros** da API (401, 404, 422) saiam em JSON e em português, mesmo se o header `Accept` for esquecido.

**routes/api.php** — liga cada URL ao controller. Todas as rotas, exceto `register` e `login`, ficam dentro de `auth:sanctum` (só com token).

### Por que a vaga volta ao cancelar?

As vagas não ficam salvas numa coluna. Elas são **calculadas** toda vez:

```
vagas_disponiveis = max_participantes − quantidade de inscrições
```

Quando a inscrição é apagada, a contagem diminui e a vaga volta sozinha. Sem risco de o número ficar errado.

---

## 9. Rotas

| Método | Rota | Função | Precisa de token? |
|---|---|---|---|
| POST | /api/register | Cadastrar usuário | Não |
| POST | /api/login | Login | Não |
| GET | /api/me | Usuário autenticado | Sim |
| POST | /api/logout | Logout | Sim |
| GET | /api/eventos | Listar eventos | Sim |
| POST | /api/eventos | Cadastrar evento | Sim |
| GET | /api/eventos/{id} | Consultar evento | Sim |
| PUT | /api/eventos/{id} | Atualizar evento | Sim |
| DELETE | /api/eventos/{id} | Excluir evento | Sim |
| POST | /api/eventos/{id}/inscricoes | Inscrever-se | Sim |
| GET | /api/minhas-inscricoes | Minhas inscrições | Sim |
| DELETE | /api/inscricoes/{id} | Cancelar inscrição | Sim |

Recomenda-se enviar em toda requisição o header (a coleção já envia):

```
Accept: application/json
```

Nas rotas protegidas, envie também:

```
Authorization: Bearer SEU_TOKEN
```

---

## 10. Testes no Postman

### Importar a coleção

1. Abra o Postman → **Import** → selecione `postman/API-Eventos.postman_collection.json`.
2. Com `php artisan serve` rodando, clique com o botão direito na coleção → **Run collection** → **Run**.
3. Todas as 32 requisições devem aparecer com os testes em **verde**.

A coleção já salva o token, os IDs de evento e de inscrição automaticamente, e gera e-mails novos a cada execução (por isso pode ser rodada várias vezes).

### Exemplos de requisições e respostas esperadas

**Cadastro de usuário** — `POST /api/register`
```json
{ "nome": "Maria Silva", "email": "maria@teste.com", "senha": "123456" }
```
Resposta **201**:
```json
{
  "usuario": { "id": 1, "nome": "Maria Silva", "email": "maria@teste.com" },
  "token": "1|aBcD123..."
}
```

**Cadastro inválido** — Resposta **422**:
```json
{
  "message": "O campo nome é obrigatório. (and 2 more errors)",
  "errors": {
    "nome": ["O campo nome é obrigatório."],
    "email": ["Informe um e-mail válido."],
    "senha": ["O campo senha deve ter pelo menos 6 caracteres."]
  }
}
```

**Login** — `POST /api/login`
```json
{ "email": "maria@teste.com", "senha": "123456" }
```
Resposta **200**: igual ao cadastro (usuario + token).
Senha errada → **401**: `{ "mensagem": "E-mail ou senha inválidos." }`

**Usuário autenticado** — `GET /api/me` → **200**:
```json
{ "data": { "id": 1, "nome": "Maria Silva", "email": "maria@teste.com" } }
```
Sem token → **401**: `{ "mensagem": "Não autenticado. Faça login para continuar." }`

**Cadastrar evento** — `POST /api/eventos`
```json
{
  "nome": "Semana de Tecnologia",
  "descricao": "Palestras sobre desenvolvimento web",
  "local": "Auditório Principal",
  "data_evento": "2026-12-10",
  "horario": "19:00",
  "max_participantes": 50
}
```
Resposta **201**:
```json
{
  "data": {
    "id": 1,
    "nome": "Semana de Tecnologia",
    "descricao": "Palestras sobre desenvolvimento web",
    "local": "Auditório Principal",
    "data_evento": "2026-12-10",
    "horario": "19:00",
    "max_participantes": 50,
    "vagas_disponiveis": 50
  }
}
```
Sem nome → **422** com `errors.nome`.

**Listar eventos** — `GET /api/eventos` → **200**: `{ "data": [ { ...evento... } ] }`

**Consultar evento** — `GET /api/eventos/1` → **200**: `{ "data": { ...evento... } }`
(o horário vem do banco como `"19:00:00"`)

**Atualizar evento** — `PUT /api/eventos/1` com todos os campos (mesmo formato do cadastro) → **200** com o evento atualizado.

**Excluir evento** — `DELETE /api/eventos/1` → **200**:
```json
{ "mensagem": "Evento excluído com sucesso." }
```
Consultar depois → **404**: `{ "mensagem": "Registro não encontrado." }`

**Inscrever-se** — `POST /api/eventos/1/inscricoes` (sem corpo) → **201**:
```json
{
  "data": {
    "id": 1,
    "data_inscricao": "11/11/2026 10:30",
    "evento": { "id": 1, "nome": "Semana de Tecnologia", "...": "...", "vagas_disponiveis": 49 }
  }
}
```

**Inscrição duplicada** — mesma requisição de novo → **422**:
```json
{ "mensagem": "Você já está inscrito neste evento." }
```

**Evento lotado** — evento com `max_participantes: 1`, já ocupado, outro usuário tenta → **422**:
```json
{ "mensagem": "Evento lotado. Não há vagas disponíveis." }
```

**Minhas inscrições** — `GET /api/minhas-inscricoes` → **200**: `{ "data": [ { ...inscrição... } ] }`
Cada usuário vê apenas as próprias.

**Cancelar inscrição** — `DELETE /api/inscricoes/1` → **200**:
```json
{ "mensagem": "Inscrição cancelada. A vaga foi liberada." }
```
Consultar o evento depois → `vagas_disponiveis` voltou a 50.
Tentar cancelar a inscrição de outro usuário → **403**.

**Logout** — `POST /api/logout` → **200**:
```json
{ "mensagem": "Logout realizado com sucesso." }
```
Usar o mesmo token depois → **401**.

---

## 11. Erros comuns

| Erro | Solução |
|---|---|
| `Route [login] not defined` ou resposta em HTML | Confira se o `bootstrap/app.php` do projeto foi substituído |
| `SQLSTATE[HY000] [1049] Unknown database` | Crie o banco `api_eventos` no MySQL |
| `SQLSTATE[HY000] [2002] Connection refused` | O MySQL não está ligado (inicie no XAMPP/Laragon) |
| `Class "Laravel\Sanctum\HasApiTokens" not found` | Rode `php artisan install:api` |
| `Table 'api_eventos.eventos' doesn't exist` | Rode `php artisan migrate` |
| 401 em rota protegida | Faça login de novo e use o token novo |

---

## 12. Como apresentar na faculdade (roteiro de ~10 minutos)

1. **Objetivo (1 min)** — "API REST para gerenciar eventos e inscrições, sem frontend, testada pelo Postman."
2. **Banco de dados (2 min)** — Mostre as duas migrations. Destaque a `unique(user_id, evento_id)` e as chaves estrangeiras.
3. **Models (1 min)** — Mostre os relacionamentos: User → inscrições, Evento → inscrições, Inscrição → usuário e evento.
4. **Fluxo de uma requisição (2 min)** — Rota → Form Request (valida) → Controller (regra) → Model (banco) → Resource (JSON).
5. **Autenticação (1 min)** — Sanctum gera o token no login; as rotas dentro de `auth:sanctum` exigem esse token.
6. **Demonstração (3 min)** — Rode a coleção no Postman, nesta ordem:
   - cadastro e login (mostre o token);
   - criar evento sem nome (erro 422);
   - inscrição → inscrição duplicada (bloqueada);
   - evento lotado (bloqueado);
   - cancelar → vaga volta;
   - logout → token deixa de funcionar.
7. **Encerramento** — Mostre o **Collection Runner** com todos os testes verdes.

**Perguntas prováveis do professor:**
- *Por que não tem coluna de vagas?* — Porque é calculada; assim nunca fica desatualizada.
- *Onde está a validação?* — Nos Form Requests, em `app/Http/Requests`.
- *Por que usar Resource?* — Para controlar o JSON e nunca expor a senha.
- *O que impede inscrição duplicada?* — A verificação no controller e a restrição `unique` no banco.
