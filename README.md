Koyaan's base Symfony 2.7 project with Sonata 2.3 integrated.

composer install
php app/console doctrine:database:create
php app/console doctrine:schema:update --force
php app/console assets install


# optional when ACLs are not properly configured on your filesystem.
# HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
# sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs
# sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs

**Available Routes**

Frontend:
http://<my vm host>/app/example
http://<my vm host>/register/
http://<my vm host>/profile/
http://sandbox.vm/profile/edit-authentication

Routes: 
http://<my vm host>/admin/

