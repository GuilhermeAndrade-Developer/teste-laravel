
# Teste Laravel




## Stack utilizada

**Back-end:** Laravel 11,
PHP 8.3,
MySQL 8.0,
Docker,
Docker Compose

## Postman

https://planetary-astronaut-424013.postman.co/workspace/Team-Workspace~34f0d673-ac8b-4693-87b9-0edf619a79f0/request/13156911-2b6ff457-0cd5-4a84-858e-fb5b4c21bced


## Instalação

Clone o projeto:

```bash
    git clone https://github.com/GuilhermeAndrade-Developer/teste-laravel.git
    cd teste-laravel
```
Configurar o Arquivo .env:

```bash
    cp .env.example .env
```

Atualize as configurações do banco de dados no arquivo .env:

```bash
    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=usuario_laravel
    DB_PASSWORD=teste_laravel
```
Construir e Iniciar os Contêineres:

```bash
    docker compose up -d --build
```

Instalar as Dependências do Composer:

```bash
    docker compose run --rm app composer install
```

Gerar a Chave da Aplicação:

```bash
    docker compose run --rm app php artisan key:generate
```

Executar Migrações:

```bash
    docker compose run --rm app php artisan migrate
```

Executar o Seeder:

```bash
    docker compose run --rm app php artisan db:seed
```

Usuário e senha para teste:

 - Email: teste@laravel.com
 - Senha: teste@laravel


## Rodando os testes

Para rodar os testes, rode o seguinte comando

```bash
    docker compose run --rm app php artisan test
```


## Documentação da API

#### Autenticação

```http
  POST /api/register
```

| Parâmetro   | Tipo       | Descrição                           |
| :---------- | :--------- | :---------------------------------- |
| `name` | `string` | **Obrigatório**. Nome do usuário |
| `email` | `string` | **Obrigatório**. Email do usuário |
| `password` | `string` | **Obrigatório**. Senha do usuário |
| `password_confirmation` | `string` | **Obrigatório**. Confirmação da senha |

Body:
```bash
  {
    "name": "Usuário Teste",
    "email": "teste@example.com",
    "password": "senha123",
    "password_confirmation": "senha123"
  }
```

Retorno Esperado:

```bash
  {
    "access_token": "token_jwt_aqui",
    "token_type": "bearer",
    "expires_in": 3600
  }
```

#### Login de Usuário

```http
  POST /api/login
```

| Parâmetro   | Tipo       | Descrição                                   |
| :---------- | :--------- | :------------------------------------------ |
| `email`      | `string` | **Obrigatório**. Email do usuário |
| `password`      | `string` | **Obrigatório**. Senha do usuário |

Body:
```bash
  {
    "email": "teste@example.com",
    "password": "senha123",
  }
```

Retorno Esperado:

```bash
  {
    "access_token": "token_jwt_aqui",
    "token_type": "bearer",
    "expires_in": 3600
  }
```

#### Listar Autores

```http
  GET /api/authors
```

Retorno Esperado:

```bash
  [
    {
        "id": 1,
        "name": "Autor Exemplo",
        "birth_date": "1980-01-01"
    },
    // Outros autores...
  ]
```
#### Criar Autor

```http
  POST /api/authors
```

Header:
```bash
    Authorization: Bearer {token}
```

| Parâmetro   | Tipo       | Descrição                                   |
| :---------- | :--------- | :------------------------------------------ |
| `name`      | `string` | **Obrigatório**. Nome do autor |
| `birth_date`      | `date` | **Obrigatório**. Data de nascimento do autor |

Body:
```bash
  {
    "name": "Novo Autor",
    "birth_date": "1990-05-20"
  }
```

Retorno Esperado:

```bash
  {
    "id": 2,
    "name": "Novo Autor",
    "birth_date": "1990-05-20"
  }
```

#### Listar Livros

```http
  GET /api/books
```

Header:
```bash
    Authorization: Bearer {token}
```

Retorno Esperado:

```bash
  [
    {
        "id": 1,
        "title": "Livro Exemplo",
        "publication_year": 2020,
        "authors": [
        {
            "id": 1,
            "name": "Autor Exemplo",
            "birth_date": "1980-01-01"
            }
        ]
    },
    // Outros livros...
  ]
```

#### Criar Livro

```http
  POST /api/books
```

Header:
```bash
    Authorization: Bearer {token}
```

| Parâmetro   | Tipo       | Descrição                                   |
| :---------- | :--------- | :------------------------------------------ |
| `title`      | `string` | **Obrigatório**. Título do livro |
| `publication_year`      | `integer` | **Obrigatório**. Ano de publicação |
| `authors`      | `array` | **Obrigatório**. IDs dos autores associados |

Body:
```bash
  {
    "title": "Novo Livro",
    "publication_year": 2021,
    "authors": [1, 2]
  }
```

Retorno Esperado:

```bash
  {
    "id": 3,
    "title": "Novo Livro",
    "publication_year": 2021,
    "authors": [
        {
            "id": 1,
            "name": "Autor Exemplo",
            "birth_date": "1980-01-01"
        },
        {
            "id": 2,
            "name": "Outro Autor",
            "birth_date": "1975-03-15"
        }
    ]
  }
```

#### Registrar Empréstimo

```http
  POST /api/loans
```

Header:
```bash
    Authorization: Bearer {token}
```

| Parâmetro   | Tipo       | Descrição                                   |
| :---------- | :--------- | :------------------------------------------ |
| `user_id`      | `integer` | **Obrigatório**. ID do usuário que está fazendo o empréstimo. |
| `book_id`      | `integer` | **Obrigatório**. ID do livro a ser emprestado. |
| `loan_date`      | `date` | **Obrigatório**. Data do empréstimo. |
| `due_date`      | `date` | **Obrigatório**. Data de devolução. |

Body:
```bash
  {
    "user_id": 1,
    "book_id": 3,
    "loan_date": "2023-08-01",
    "due_date": "2023-08-15"
  }
```

Retorno Esperado:

```bash
  {
    "id": 1,
    "user_id": 1,
    "book_id": 3,
    "loan_date": "2023-08-01",
    "due_date": "2023-08-15",
    "return_date": null
  }
```
## Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo [MIT](https://choosealicense.com/licenses/mit/) para mais detalhes.