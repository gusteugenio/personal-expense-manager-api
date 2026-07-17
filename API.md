# Documentação da API

## Base URL

| Ambiente | URL |
|---|---|
| Local | `http://localhost:8080` |
| Produção | `https://personal-expense-manager-api-production.up.railway.app` |

## Autenticação

A API utiliza autenticação via **JWT (Bearer Token)**.

Após o login, envie o token retornado no header `Authorization` de todas as requisições aos endpoints protegidos (recurso `/expenses`):

```
Authorization: Bearer <token>
```

Requisições sem token, ou com token inválido/expirado, retornam `401 Unauthorized`.

## Endpoints

### Cadastrar usuário

`POST /auth/signup`

**Corpo da requisição**
```json
{
  "email": "usuario@example.com",
  "password": "Senha@12345"
}
```

**Regras de validação**
- `email`: obrigatório, formato válido, único
- `password`: obrigatório, mínimo 10 caracteres, deve conter maiúscula, minúscula, número e caractere especial

**Resposta — 201 Created**
```json
{
  "id": 1,
  "email": "usuario@example.com"
}
```

**Resposta — 422 Unprocessable Entity** (dados inválidos)
```json
{
  "email": ["Este e-mail já está em uso."]
}
```

---

### Login

`POST /auth/login`

**Corpo da requisição**
```json
{
  "email": "usuario@example.com",
  "password": "Senha@12345"
}
```

**Resposta — 200 OK**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {
    "id": 1,
    "email": "usuario@example.com"
  }
}
```

**Resposta — 422 Unprocessable Entity** (credenciais inválidas)
```json
{
  "password": ["E-mail ou senha inválidos."]
}
```

---

### Listar despesas

`GET /expenses`

🔒 Requer autenticação.

**Query params (opcionais)**

| Parâmetro | Tipo | Descrição |
|---|---|---|
| `category` | string | `alimentação`, `transporte` ou `lazer` |
| `month` | int | Mês do filtro por período (1-12). Exige `year` junto. |
| `year` | int | Ano do filtro por período. Exige `month` junto. |
| `sort` | string | `asc` ou `desc` (padrão: `desc`), ordena por `expense_date` |
| `page` | int | Página atual (padrão: `1`) |
| `per_page` | int | Itens por página (padrão: `10`, máximo `100`) |

> `month` e `year` devem ser enviados sempre juntos. Enviar apenas um dos dois retorna `422`.

**Resposta — 200 OK**
```json
{
  "items": [
    {
      "id": 1,
      "user_id": 1,
      "description": "Supermercado",
      "category": "alimentação",
      "amount": "150.00",
      "expense_date": "2026-07-01",
      "created_at": "2026-07-17 10:00:00",
      "updated_at": "2026-07-17 10:00:00"
    }
  ],
  "pagination": {
    "total": 9,
    "page": 1,
    "per_page": 10,
    "total_pages": 1
  }
}
```

---

### Consultar despesa

`GET /expenses/{id}`

🔒 Requer autenticação. Só é possível consultar despesas do próprio usuário.

**Resposta — 200 OK**
```json
{
  "id": 1,
  "user_id": 1,
  "description": "Supermercado",
  "category": "alimentação",
  "amount": "150.00",
  "expense_date": "2026-07-01",
  "created_at": "2026-07-17 10:00:00",
  "updated_at": "2026-07-17 10:00:00"
}
```

**Resposta — 403 Forbidden** — despesa pertence a outro usuário
**Resposta — 404 Not Found** — despesa não existe

---

### Criar despesa

`POST /expenses`

🔒 Requer autenticação.

**Corpo da requisição**
```json
{
  "description": "Supermercado",
  "category": "alimentação",
  "amount": 150.00,
  "expense_date": "2026-07-01"
}
```

**Regras de validação**
- `description`: obrigatório, máximo 255 caracteres
- `category`: obrigatório, um dos valores `alimentação`, `transporte`, `lazer`
- `amount`: obrigatório, numérico, maior que zero
- `expense_date`: obrigatório, formato `YYYY-MM-DD`

**Resposta — 201 Created**: objeto da despesa criada
**Resposta — 422 Unprocessable Entity**: erros de validação por campo

---

### Atualizar despesa

`PUT /expenses/{id}` ou `PATCH /expenses/{id}`

🔒 Requer autenticação. Só é possível atualizar despesas do próprio usuário.

**Corpo da requisição** (qualquer subconjunto dos campos de criação)
```json
{
  "description": "Supermercado - atualizado"
}
```

**Resposta — 200 OK**: objeto da despesa atualizada
**Resposta — 422 Unprocessable Entity**: erros de validação
**Resposta — 403 Forbidden**: despesa pertence a outro usuário
**Resposta — 404 Not Found**: despesa não existe

---

### Excluir despesa

`DELETE /expenses/{id}`

🔒 Requer autenticação. Só é possível excluir despesas do próprio usuário.

**Resposta — 204 No Content**
**Resposta — 403 Forbidden**: despesa pertence a outro usuário
**Resposta — 404 Not Found**: despesa não existe

## Códigos HTTP utilizados

| Código | Significado |
|---|---|
| 200 | Requisição bem-sucedida |
| 201 | Recurso criado com sucesso |
| 204 | Requisição bem-sucedida, sem conteúdo de retorno |
| 401 | Não autenticado (token ausente ou inválido) |
| 403 | Autenticado, mas sem permissão sobre o recurso |
| 404 | Recurso não encontrado |
| 422 | Erro de validação |

## Categorias válidas

- `alimentação`
- `transporte`
- `lazer`
