FROM node:20-alpine

WORKDIR /nodejs

COPY ./sse/package.json ./sse/package-lock.json ./

RUN npm install

COPY ./sse ./

RUN node ./sse/dist/index.js

EXPOSE 4001

CMD ["npm", "start"]
