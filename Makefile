composer:
	php -r "readfile('https://getcomposer.org/installer');" | php
phpunit:
	wget https://phar.phpunit.de/phpunit.phar
	chmod +x phpunit.phar
	sudo mv phpunit.phar /usr/local/bin/phpunit
