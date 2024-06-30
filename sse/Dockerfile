FROM node:20-alpine

WORKDIR /nodejs/sse

COPY ./sse/package.json ./sse/package-lock.json ./

RUN npm install

COPY ./sse .

CMD [ "node", "./dist/index.js" ]

EXPOSE 4001
