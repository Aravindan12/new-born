import { createRequire } from 'module';
const require = createRequire(import.meta.url);
const express = require('express');

const app = express();


const server = require('http').createServer(app);


const io = require('socket.io')(server, {
    cors: { origin: "*"}
});
var mysql = require('mysql');
var mysqlTimestamp = "2023-07-08 03:41:55";
/** storing db configuration */
var connection = mysql.createConnection({
    host     : 'localhost',
    user     : 'root',
    password : 'Aa@13111998',
    database : 'new-born'
  });
   
connection.connect();
app.get('/',function(req,res){
	res.end("Welcome to Node.js HTTPS Servern");
})
// var mysqlTimestamp = moment().utc().format('YYYY-MM-DD HH:mm:ss');

io.on('connection', (socket) => {
    console.log('connection');

    socket.on('sendChatToServer', (message) => {
        var query = `INSERT INTO chats (from_id,to_id,message,created_at,updated_at) VALUES ('${message.from}', '${message.to}','${message.msg}','${mysqlTimestamp}','${mysqlTimestamp}')`
        connection.query(query, (err, result) => {  
            if (err) {  
                console.log('message not inserted', err.message)
                return
            }else{
                console.log('message inserted to db', result.insertId)
            } 
        }); 
        // io.sockets.emit('sendChatToClient', message);
        socket.broadcast.emit('sendChatToClient', message);
    });


    socket.on('disconnect', (socket) => {
        console.log('Disconnect');
    });
});

server.listen(3000, () => {
    console.log('Server is running');
});