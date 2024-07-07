FROM node:20

WORKDIR /nodejs

COPY ./nodejs/package.json ./nodejs/package-lock.json ./

RUN npm install

COPY ./nodejs /nodejs

COPY wait-for-it.sh /usr/local/bin/

RUN chmod +x /usr/local/bin/wait-for-it.sh
RUN chmod +x /nodejs/docker-entrypoint.sh

EXPOSE 4001

CMD ["npm", "start"]

ENTRYPOINT ["/nodejs/docker-entrypoint.sh"]
