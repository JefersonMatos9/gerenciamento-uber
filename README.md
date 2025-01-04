# **Gerenciamento Uber**

Este projeto tem como objetivo desenvolver um sistema de gerenciamento de corridas para uma plataforma semelhante ao Uber. Ele permite o controle de **GANHOS**, **GASTOS**, **HORAS TRABALHADAS**, **LUCRO TOTAL** e **CONSULTAS**.

## **Funcionalidades**

- **Histórico de corridas**: O sistema mantém um histórico de todos os dias de corridas realizadas, incluindo informações e valores arrecadados.
- **Controle de ganhos**: Permite o cálculo dos ganhos dos motoristas com base nas corridas realizadas.
- **Controle de gastos**: Registra os custos relacionados à operação, como manutenção de veículos e combustíveis.
- **Controle de horas trabalhadas**: Monitora as horas trabalhadas pelos motoristas.
- **Cálculo do lucro total**: Realiza o cálculo do lucro total após descontar os gastos dos ganhos.
- **Consultas**: Permite a consulta das informações sobre dias, periodos desejados e resumos mensais.

## **Tecnologias Utilizadas**

- **PHP**: Linguagem de programação principal utilizada no desenvolvimento do sistema.
- **MySQL**: Banco de dados utilizado para armazenar as informações sobre motoristas, passageiros e corridas.
- **Apache**: Servidor web para executar o projeto.

## **Estrutura do Projeto**

- **`public/`**: Contém os arquivos públicos acessíveis, como o `index.php` e outros scripts.
- **`src/`**: Contém o código-fonte do backend, como controladores, modelos e lógica de negócios.
- **`config/`**: Contém os arquivos de configuração, como a conexão com o banco de dados.
- **`templates/`**: Contém os arquivos de template para renderizar as páginas HTML.

## **Como Executar**

1. **Clone o repositório**:
   ```bash
   git clone https://github.com/JefersonMatos9/gerenciamento-uber.git
