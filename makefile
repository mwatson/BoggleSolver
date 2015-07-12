PHPUNIT=vendor/phpunit/phpunit/phpunit

tests: tests/* src/* ; $(PHPUNIT) tests

coverage: tests/* src/* ; $(PHPUNIT) --coverage-html coverage tests

clean: ; rm -rf coverage
