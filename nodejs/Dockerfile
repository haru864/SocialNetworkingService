FROM node:20-alpine

WORKDIR /nodejs

COPY ./nodejs/package.json ./nodejs/package-lock.json ./

RUN npm install

COPY ./nodejs .

CMD [ "node", "./dist/index.js" ]

EXPOSE 4001
