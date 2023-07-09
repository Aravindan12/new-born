<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Chat App</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <!-- CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
        <style>
            .chat-row {
                margin: 50px;
            }


             ul {
                 margin: 0;
                 padding: 0;
                 list-style: none;
             }


             ul li {
                 padding:8px;
                 background: #928787;
                 margin-bottom:20px;
             }


             ul li:nth-child(2n-2) {
                background: #c3c5c5;
             }
             .sender{
                margin-left: 80%;
             }
             .receiver{
                margin-right: 80%;
             }
             .chat-input {
                 border: 1px soild lightgray;
                 background-color: green;
                 border-top-right-radius: 10px;
                 border-top-left-radius: 10px;
                 padding: 8px 10px;
                 color:#fff;
             }
        </style>
    </head>
    <body>

        <div class="container">
            <input type="hidden" name = "from_name" value="{{$fromId->name}}" id="fromName">
            <input type="hidden" name = "to_name" value="{{$toId->name}}" id="toName">
            <input type="hidden" name = "from_id" value="{{$fromId->id}}" id="fromId">
            <input type="hidden" name = "to_id" value="{{$toId->id}}" id="toId">
            <div class="row chat-row">
                <div class="chat-content">
                <ul style="overflow-wrap: break-word">
                    @foreach ($chats as $chat)
                    @if(auth()->user()->id == $chat->from_id)
                    <div class="sender">{{$fromId->name}}:<li>{{$chat->message}}</li></div>
                    @elseif(auth()->user()->id == $chat->to_id)
                    <div class="receiver">{{$toId->name}}:<li>{{$chat->message}}</li></div>
                    @endif
                    @endforeach
                </ul>
                </div>

                <div class="chat-section">
                    <div class="chat-box">
                        <div class="chat-input bg-success" id="chatInput" contenteditable="">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
        <script src="https://cdn.socket.io/4.0.1/socket.io.min.js" integrity="sha384-LzhRnpGmQP+lOvWruF/lgkcqD+WDVt9fU3H4BWmwP5u5LTmkUGafMcpZKNObVMLU" crossorigin="anonymous"></script>


        <script>
            $(function() {
                // alert('ss')
                let ip_address = '127.0.0.1';
                let socket_port = '3000';
                let socket = io(ip_address + ':' + socket_port);
                let chatInput = $('#chatInput');
                let fromName = $('#fromName').val();
                let toName = $('#toName').val();
                let fromId = $('#fromId').val();
                let toId = $('#toId').val();
                chatInput.keypress(function(e) {
                    let message = {msg:$(this).html(),from:fromId,to:toId};
                    console.log(message);
                    if(e.which === 13 && !e.shiftKey) {
                        socket.emit('sendChatToServer', message);
                        $('.chat-content ul').append(`<div class="sender">`+fromName+`:<li>${message.msg}</li></div>`);
                        chatInput.html('');
                        return false;
                    }
                });

                socket.on('sendChatToClient', (message) => {

                    if(message.to == fromId && message.from == toId){
                        $('.chat-content ul').append(`<div class="receiver">`+toName+`:<li>${message.msg}</li></div>`);
                    }
                });
            });
        </script>
    </body>
</html>