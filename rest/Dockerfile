FROM node:alpine

# We have to install nodemon globally before moving into the working directory
RUN npm install -g nodemon

# Create app directory
RUN mkdir -p /usr/src/app
WORKDIR /usr/src/app

# Install app dependencies
# A wildcard is used to ensure both package.json AND package-lock.json are copied
COPY package*.json ./

RUN npm install

# Bundle app source
COPY . .

EXPOSE 4000
#COPY start.sh .
#RUN chmod -p +x start.sh
#ENTRYPOINT ["start.sh"]
#RUN start.sh
#CMD ['$NODE_ENV']
CMD ["nodemon", "dev"]