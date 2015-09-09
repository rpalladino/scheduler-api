.PHONY: test

test:
	phpunit
	phpspec run --format=progress
