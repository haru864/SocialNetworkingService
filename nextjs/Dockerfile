FROM node:20-alpine

WORKDIR /nextjs

COPY ./nextjs/package.json ./nextjs/package-lock.json ./

RUN npm install

COPY ./nextjs ./

RUN npm run build

EXPOSE 4000

CMD ["npm", "start"]
