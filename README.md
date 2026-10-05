# AnnaFell
Venda de Hardware e Periféricos como projeto escolar. Inspiração no mercado livre e shopee.

Temos a integração de um banco de dados ao passar do sistema de arquivos e salvamentos com fwhite.

Realizamos um processo, adicionando cada vez mais interações e enxugando o código, realizamos essa ligação através do arquivo cons.php e usamos principalmente a função banco dentro do arquivo DLL.php para realizar a maioria das utilidades dos bancos. cada vez sonhando mais alto decidimos além de adicionar +150 produtos conseguimos pelo PI a matéria de Júlio montar e retirar nossas dúvidas do banco e assim construir algo compatível e integrado.

Apresetamos funções como:

1. Migração de arquivos .dat para MySQL
Banco annafell com 4 tabelas: usuarios, logins, produtos, carrinho

Função banco() centraliza toda comunicação com o MySQL

Toda operação que era fopen/fwrite virou INSERT, UPDATE, SELECT ou DELETE

Cadastro, login, venda e carrinho passaram a persistir no banco

2. Sistema de login com indicador no cabeçalho
Criado header.php reutilizável em todas as páginas

Avatar/nome clicável que leva ao perfil

Mostra estado deslogado ("Não logado" + Entrar)

Contador de carrinho dinâmico no menu

3. Página "não logado"
Criada nao_logado.php com aviso e botões organizados

Botões empilhados, com hierarquia visual (principal / secundário / terciário)

4. Foto de perfil
Upload com validação: tipo (JPG/PNG/GIF via getimagesize) e tamanho (máx. 2MB)

Arquivo salvo em img/usuarios/CPF.ext

Caminho gravado na coluna usuarios.foto

Fallback: mostra inicial do nome quando não há foto

Função form_foto() no DLL.php gera o formulário

Flash message com auto-esconder em 4s

5. Painel do perfil estilo Mercado Livre
Sidebar com abas: Início, Minhas compras, Editar foto, Ajuda, Continuar comprando, Sair

URL controla a aba (perfil.php?aba=...)

Ícones PNG na sidebar (substituindo emojis) com filtro branco automático

Cards de resumo na aba Início (compras, itens no carrinho, foto personalizada)

6. Histórico de compras
Consulta agrupada por numero_venda

Card por pedido com data, forma de pagamento, itens e total

Ordenação decrescente por data

7. Validações de cadastro
CPF exatamente 11 dígitos

CEP exatamente 8 dígitos

maxlength e pattern no HTML

Validação dupla (HTML + PHP)

Mensagens de erro via flash_cadastro

8. Segurança no login
Login único por CPF — não é mais possível sobrescrever login de outro usuário

Escapamento com addslashes nos campos de login

Cada login aponta para um único CPF

9. Controle de estoque
Estoque visível no card do produto (verde/amarelo/vermelho)

Aviso "Últimas X unidades" quando ≤ 10

Bloqueio de compra quando estoque = 0

Validação ao adicionar ao carrinho (não ultrapassa o disponível)

Estoque descontado automaticamente ao finalizar compra

10. Métodos HTTP (POST em vez de GET)
Botão "Comprar" deixou de ser link GET e virou formulário POST

Adicionar / remover / atualizar carrinho agora usam POST

GET mantido apenas para navegação (filtros de categoria, abas)

Nenhuma senha trafega em URL

Arquivos criados
header.php, nao_logado.php, perfil.php, salvar_foto.php, remover_foto.php

Arquivos modificados
DLL.php, produtos.php, index.php, carrinho.php, confirmar.php, salvar_venda.php, processa_login.php, salvar_login.php, salvar_usuario.php, cadastro1.php, cadastro2.php, login.php, contato.php, sobre.php, erro.php, css/estilo.css

