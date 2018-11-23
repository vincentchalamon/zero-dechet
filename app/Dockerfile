FROM circleci/android:api-28-alpha

ARG NVM_VERSION=0.33.2
ENV NVM_DIR=/root/.nvm

USER root

RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v${NVM_VERSION}/install.sh | bash; \
    [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh"; \
    nvm install stable; \
    nvm use stable; \
    npm install -g react-native-cli detox-cli yarn

RUN mkdir -p /usr/src/app

WORKDIR /usr/src/app

# Prevent the reinstallation of node modules at every changes in the source code
COPY package.json yarn.lock ./
RUN yarn install

COPY . ./

EXPOSE 8081

CMD yarn start
