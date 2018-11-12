FROM mhart/alpine-node:11.1.0 AS zero_dechet_node

RUN apk add --no-cache ca-certificates python make g++; \
	yarn global add swagger-cli

FROM docker/compose:1.23.1 AS zero_dechet_docker

RUN apk add --no-cache ca-certificates bash

FROM node:10.10-alpine AS zero_dechet_client

RUN mkdir -p /usr/src/client

WORKDIR /usr/src/client

RUN apk add --no-cache chromium openssl

# Prevent the reinstallation of node modules at every changes in the source code
COPY package.json yarn.lock ./
RUN yarn install --pure-lockfile

# Generate certificates for tests
RUN openssl genrsa -des3 -passout pass:NotSecure -out cert.pass.key 2048; \
	openssl rsa -passin pass:NotSecure -in cert.pass.key -out cert.key; \
	openssl req -new -passout pass:NotSecure -key cert.key -out cert.csr -subj '/C=SS/ST=SS/L=Gotham City/O=API Platform Dev/CN=localhost'; \
	openssl x509 -req -sha256 -days 365 -in cert.csr -signkey cert.key -out cert.crt; \
	rm cert.pass.key cert.csr

COPY . ./

EXPOSE 3000

CMD yarn start
