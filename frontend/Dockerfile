FROM node:20-alpine

WORKDIR /nextjs

COPY ./frontend/package.json ./frontend/package-lock.json ./

RUN npm install

COPY ./frontend ./

RUN npm run build

EXPOSE 4000

CMD ["npm", "start"]
