## Registrar um novo usuário
***(POST) /users/new***

name=example

email=example@email.com

pass=secret

## Logar com um usuário
***(POST) /users/login***

email=example@email.com

pass=secret

## Visualizar informações do usuário
***(GET) /users/{id}/?jwt={token}***

## Editar o seu usuário
***(PUT) /users/{id}***

jwt=token

name=exemplo

email=exemplo@email.com

pass=secret

*OBS: É preciso mandar pelo menos um parâmetro de edição, e obrigatoriamente o token*

*EXEMPLO: jwt=exampletoken&name=fulano*

## Deletar o seu usuário
***(DELETE) /users/{id}***

jwt=token