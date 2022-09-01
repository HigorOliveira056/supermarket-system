### Requirements Technic
- Aplicação foi feita utilizando-se PHP: ^7.4
- Banco de dados sql server
- Necessário os drivers para a conexão com o sql server

## RUN THE PROJECT - API
- Configure .env com a configuração para se conectar no banco de dados
- Rode os comandos:
  - ```console
       composer install
       composer up-tables
       php -S localhost:8080 index.php
       ```
Description Project
-------------------

**A aplicação tem o intuito de fornecer uma API no padrão REST que retorna uma resposta no formato JSON**

***Até o momento foi possível implementar os métodos de crud para as taxas(impostos), categorias e produtos e as regras para os
cálculos relacionados a venda***

ENDPOINTS
=========

**/taxes**
----------

```http
GET /taxes/
```
## Response
```javascript
{
  {
    id: int,
    name: string,
    description: string
  }
}
```

------------------------------

```http
GET /taxes/id:
```

| Route Parameter | Type | Description |
| :--- | :--- | :--- |
| `id` | `int` |  |

## Response
```javascript
{
  id: int,
  name: string,
  description: string
}
```

------------------------------

```http
POST /taxes/
```

| Parameter | Type | Description |
| :--- | :--- | :--- |
| `name` | `string` | **Required** |
| `percentual` | `float` | **Required** |

## Response
```javascript
{
  error: bool,
  message: string
}
```

------------------------------

```http
PUT /taxes/id:
```

| Route Parameter | Type | Description |
| :--- | :--- | :--- |
| `id` | `int` | **Required** |

## Response
| Parameter | Type | Description |
| :--- | :--- | :--- |
| `name` | `string` | **Required** |
| `percentual` | `float` | **Required** |

```javascript
{
  error: bool,
  message: string
}
```

------------------------------

```http
DELETE /taxes/id:
```

| Route Parameter | Type | Description |
| :--- | :--- | :--- |
| `id` | `int` | **Required** |

## Response
```javascript
{
  error: bool,
  message: string
}
```

------------------------------

**/category**
-------------

```http
GET /category/
```

## Response
```javascript
{
  {
    id: int,
    name: string,
    description: string
    taxes: [{
      id: int,
      name: string,
      description: string
    }]
  }
}
```

------------------------------

```http
GET /category/id:
```

| Route Parameter | Type | Description |
| :--- | :--- | :--- |
| `id` | `int` |  |

## Response
```javascript
{
  id: int,
  name: string,
  description: string
  taxes: [{
    id: int,
    name: string,
    description: string
  }]
}
```

------------------------------

```http
POST /category/
```

| Parameter | Type | Description |
| :--- | :--- | :--- |
| `name` | `string` | **Required** |
| `description` | `string` | **OPTIONAL** |
| `taxes_id` | `string` | **Required** `taxes id separeted with semicolon` |

## Response
```javascript
{
  error: bool,
  message: string
}
```

------------------------------

```http
PUT /category/id:
```

| Route Parameter | Type | Description |
| :--- | :--- | :--- |
| `id` | `int` | **Required** |

| Parameter | Type | Description |
| :--- | :--- | :--- |
| `name` | `string` | **Required** |
| `description` | `string` | **OPTIONAL** |
| `taxes_id` | `string` | **Required** `taxes id separeted with semicolon` |

## Response
```javascript
{
  error: bool,
  message: string
}
```

------------------------------

```http
DELETE /taxes/id:
```

| Route Parameter | Type | Description |
| :--- | :--- | :--- |
| `id` | `int` | **Required** |

## Response
```javascript
{
  error: bool,
  message: string
}
```

------------------------------

**/product**
-------------

```http
GET /product/
```

## Response
```javascript
{
  {
    id: int,
    category_id: int,
    name: string,
    description: string,
    price: float,
    category: {
      id: int,
      name: string,
      description: string
      taxes: [{
        id: int,
        name: string,
        description: string
      }]
    }
  }
}
```

------------------------------

```http
GET /product/id:
```

| Route Parameter | Type | Description |
| :--- | :--- | :--- |
| `id` | `int` |  |

## Response
```javascript
{
  id: int,
  category_id: int,
  name: string,
  description: string,
  price: float,
  category: {
    id: int,
    name: string,
    description: string
    taxes: [{
      id: int,
      name: string,
      description: string
    }]
  }
}
```

------------------------------

```http
POST /product/
```

| Parameter | Type | Description |
| :--- | :--- | :--- |
| `name` | `string` | **Required** |
| `description` | `string` | **OPTIONAL** |
| `price` | `float` | **Required** `|
| `category_id` | `int` | **Required** `|

## Response
```javascript
{
  error: bool,
  message: string
}
```

------------------------------

```http
PUT /product/id:
```

| Route Parameter | Type | Description |
| :--- | :--- | :--- |
| `id` | `int` | **Required** |

| Parameter | Type | Description |
| :--- | :--- | :--- |
| `name` | `string` | **Required** |
| `description` | `string` | **OPTIONAL** |
| `price` | `float` | **Required** `|
| `category_id` | `int` | **Required** `|

## Response
```javascript
{
  error: bool,
  message: string
}
```

------------------------------

```http
DELETE /product/id:
```

| Route Parameter | Type | Description |
| :--- | :--- | :--- |
| `id` | `int` | **Required** |

## Response
```javascript
{
  error: bool,
  message: string
}
```

------------------------------

## Requirements Implementations

- [x] - Cadastro dos produtos
- [x] - Cadastro dos tipos de cada produto
- [x] - Cadastro dos valores percentuais de impostos dos tipos de produtos
- [] - A tela de venda, onde serão informados os produtos e quantidade adquiridas
- [] - O Sistema deve apresentar o valor de cada item multiplicado pela quantidade adquirida e a quantidade pago de imposto em cada item, um totalizador do valor de impostos
- [] - A venda deverá ser salva