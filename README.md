# Sistemas Moodle EaD - UFGD
-------
## Instalação - Ambiente de Desenvolvimento 
### Instalação - Dependências 

Para a instalação, é necessário: 

1. [Docker Desktop](https://www.docker.com/products/docker-desktop/).

2. [Visual Studio Code](https://code.visualstudio.com/download).

3. Configuração de [WSL](https://docs.docker.com/desktop/windows/wsl/) (para SO Windows, uma arquitetura que melhora o desempenho do docker)

### Instalação - Código 

1. Clonar repositório 

	`git clone https://git.ufgd.edu.br/ead/moodle-ead-ufgd.git`

2. Entrar no diretório via terminal para executar comandos e abrir também no Visual Studio Code

### Instalação - Código - Para Windows, com WSL

1. Acessar o terminal linux instalado na configuração do seu WSL;

2. Entrar em algum diretório de preferência (`cd /home/user/projetos`, por exemplo) que deseja baixar os códicos-fonte;
    2.1. pode utilizar o comando `explorer.exe .` para abrir uma janela do explorer do windows na pasta em que está no terminal;

3. Clonar repositório:

	`git clone https://git.ufgd.edu.br/ead/moodle-ead-ufgd.git`

4. Entrar no diretório via terminal (`cd moodle-ead-ufgd`).
    4.1. pode utilizar o comando `code .` para abrir o Visual Studio Code no diretório em que está.

### Instalação - Infraestrutura

1. Copie o arquivo `docker-compose.yml.example` com o nome `docker-compose.yml`.

2. Edite o arquivo docker-compose.yml com as variáveis de ambiente que não estiverem pre-preenchidas ou edite alguma, se for necessário (as variáveis estão localizadas na sessão: `services` >> `host-apache` >> `environment`)

3. Crie uma network e os volumes que os containers utilizarão:

    `docker network create minha-rede`

    `docker volume create volume-postgres`

4. Criar o diretório de dados (moodledata) e adicionar permições de escrita à ele:

    `mkdir ../docker-moodledata`

    `sudo chown www-data:www-data ../docker-moodledata`

5. Executar o comando para subir a infraestrutura:

    `docker-compose up` 

### Instalação - Banco de Dados

A princípio não é necessária a instalação do banco de dados, ele se instalará automaticamente, mas caso não aconteça, então acesse algum cliente PostgreSql para então realizar a criação do banco de dados (configuração padrão: user: `postgres`, password: `secret`)

1. pode ser acessado pelo diretamente terminal do container PostgreSql:
        
        psql -U postgres
        create database moodle;

2. Ou pode ser acessado por algum outro cliente PostgreSql de sua preferência, como PhpPgAdmin, pgAdmin, etc, por `localhost:5432`.

### Instalação - Acesso

Caso queira executar somente o moodle, o acesso pode ser realizado através do endereço: `http://localhost/`, através de um Web browser, mas caso queira executar integrando-o com o sistema de suporte, então configure um nome de host:

1. Edite o arquivo de nomes de hosts de seu computador (localizado em `C:/Windows/System32/drivers/etc/hosts` em SOs Windows e em `/etc/hosts` em SOs Linux), adicionando a linha:

    `127.0.0.1 host-moodle`

2. O acesso então poderá pode ser realizado também através do endereço: `http://host-moodle/` e, sempre utilize este endereço para referenciar o moodle no sistema de suporte.


------

## Instalação - Ambiente de Produção 
	
------

### © Equipe EaD UFGD

