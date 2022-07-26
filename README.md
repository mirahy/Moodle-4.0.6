## Template para configurar um deploy com Helm

### Verificar descritores gerados sem instalar (Debug)

~~~bash
$ helm install --dry-run --debug --set env.normal.MY_VAR=teste,env.secret.MY_SECRET=123456 helm-deploy ./helm 
~~~

