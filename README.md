### Requirements Technic
- Applicação foi feita utilizando-se PHP: ^7.4
- Banco de dados sql server
- Necessários os drives para a conexeção com o sql server

## RUN THE PROJECT - API
- Configure .env com a configuração para se conectar no banco de dados
- Rode os comandos:
  - ```console
       composer install
       composer up-tables
       php -S localhost:8080 index.php
       ```

## Requirements Implementations

- [x] - Cadastro dos produtos
- [x] - Cadastro dos tipos de cada produto
- [x] - Cadastro dos valores percentuais de impostos dos tipos de produtos
- [] - A tela de venda, onde serão informados os produtos e quantidade adquiridas
- [] - O Sistema deve apresentar o valor de cada item multiplicado pela quantidade adquirida e a quantidade pago de imposto em cada item, um totalizador do valor de impostos
- [] - A venda deverá ser salva