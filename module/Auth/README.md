ZF2 sample authentication login with rememberMe.

install doctrine/common first :
http://samsonasik.wordpress.com/2012/10/04/zend-framework-2-step-by-step-build-form-using-annotation-builder/

With users.postgresql.sql or users.mysql.sql example :

* user_name : samsonasik 
* pass_word  : 123456      
* insert into users(user_name, pass_word) values('user', md5('user'));