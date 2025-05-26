# P2Pay - Plataforma de Pagamentos Simplificada 💸

![Docker](https://img.shields.io/badge/Docker-2CA5E0?style=for-the-badge&logo=docker&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/Symfony-000000?style=for-the-badge&logo=symfony&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![NGINX](https://img.shields.io/badge/NGINX-009639?style=for-the-badge&logo=nginx&logoColor=white)
![ReactPHP](https://img.shields.io/badge/ReactPHP-6E43CD?style=for-the-badge&logo=react&logoColor=white)

---

## ✨ Funcionalidades

- Cadastro de usuários e lojistas
- Sistema de transferências seguras
- Validação de saldo
- Notificações de transações
- Integração com serviços externos
- Mensageria
- Cache

---

## 🚀 Subindo o Projeto com Docker

### 1. Clone o repositório

```bash
git clone https://github.com/seu-user/p2pay.git
cd p2pay
```

### 2. Configure as variáveis de ambiente

Crie o arquivo `.env` na raiz do projeto com base no `.env.dev`.

### 3. Suba os containers

```bash
docker-compose up -d
```

### 4. Instale as dependências PHP

```bash
docker exec -it p2pay_php composer install
```

---

## 🛠️ Rodando Migrations e Fixtures

### 1. Execute as migrations

```bash
docker exec -it p2pay_php php bin/console doctrine:migrations:migrate
```

### 2. (Opcional) Popule com dados fictícios

```bash
docker exec -it p2pay_php php bin/console doctrine:fixtures:load
```

> **Dica:** O comando acima usa Faker para gerar dados de teste (usuários, transações, etc).

---

## 🧪 Rodando os testes

```bash
docker exec -it p2pay_php php bin/phpunit
```

---

## 📂 Estrutura dos Containers

| Serviço           | Descrição                     | Porta Externa |
|-------------------|-------------------------------|----------------|
| `p2pay_nginx`     | Servidor web (NGINX)          | `8080`         |
| `p2pay_php`       | Aplicação PHP (Symfony)       | -              |
| `p2pay_mysql`     | Banco de dados MySQL          | `3306`         |
| `messenger_worker`| Worker para filas Symfony     | -              |

---

## 🧰 Requisitos

- Docker
- Docker Compose
