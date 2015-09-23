.PHONY: all clean

all: web/docs

clean:
	rm -rf web/docs

node_modules:
	npm install

web/docs: node_modules
	mkdir -p web/docs
	node_modules/.bin/aglio -i scheduler-api.md -o web/docs/index.html
