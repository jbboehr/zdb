
all: vendor

coverage: vendor
	php -dzend_extension=xdebug.so vendor/bin/phpunit --coverage-text --coverage-html=reports

clean:

test: vendor
	./vendor/bin/phpunit

vendor: composer.json composer.lock

.PHONY: all clean coverage test
