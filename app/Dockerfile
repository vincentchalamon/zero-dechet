FROM linuxbrew/linuxbrew:1.8.0 AS zero_dechet_app

RUN brew update; \
	brew install node; \
	brew tap wix/brew; \
    brew install applesimutils; \
    npm install -g detox-cli yarn expo-cli

# Prevent the reinstallation of node modules at every changes in the source code
COPY package.json yarn.lock ./
RUN yarn install --pure-lockfile

COPY . ./

USER root

EXPOSE 19001
EXPOSE 19002

CMD yarn start
