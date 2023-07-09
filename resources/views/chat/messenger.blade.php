<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Messenger') }}
        </h2>
    </x-slot>
    <section style="background-color: #eee;">
  <div class="container py-5">

    <div class="row">

      <div class="col-md-6 col-lg-5 col-xl-4 mb-4 mb-md-0">

        <div class="card">
          <div class="card-body">
            <input type="hidden" value="{{auth()->user()->id}}" name="from_id" id="from_id">
            <ul class="list-unstyled mb-0">
            @foreach($users as $user)
              <li class="p-2 border-bottom" style="background-color: #eee;">
                <a href="#" class="d-flex justify-content-between users" data-id="{{$user->id}}">
                  <div class="d-flex flex-row">
                    <img src="{{asset('logo/user.webp')}}" alt="avatar"
                      class="rounded-circle d-flex align-self-center me-3 shadow-1-strong" width="60">
                    <div class="pt-1">
                      <p class="fw-bold mb-0">{{$user->name}}</p>
                      <!-- <p class="small text-muted">Hello, Are you there?</p> -->
                    </div>
                  </div>
                  <div class="pt-1">
                    <!-- <p class="small text-muted mb-1">Just now</p> -->
                    <!-- <span class="badge bg-danger float-end">1</span> -->
                  </div>
                </a>
              </li>
            @endforeach
            </ul>

          </div>
        </div>

      </div>

      <div class="col-md-6 col-lg-7 col-xl-8 chats scrollable">

        <ul class="list-unstyled ">
        </ul>
        <div class="buttonss"></div>

      </div>

    </div>

  </div>
</section>
<script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js">
</script>
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script src="https://cdn.socket.io/4.0.1/socket.io.min.js" integrity="sha384-LzhRnpGmQP+lOvWruF/lgkcqD+WDVt9fU3H4BWmwP5u5LTmkUGafMcpZKNObVMLU" crossorigin="anonymous"></script>

<script>
  $('.users').click(function()
  {
    let id = $(this).data('id')
    let fromId = $('#from_id').val()
    let chats = ''
    console.log(id)
    $('.chats ul').html('');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url : "{{ url('get-chat') }}",
        data : {'id' : id},
        type : 'POST',
        dataType : 'json',
        success : function(result){
          let fromData = result.data.from
          let toData = result.data.to
          result.data.chats.forEach((item, index)=>{
              if(fromId == item.from_id){
                chats += `<li class="d-flex justify-content-between mb-4"><img src="{{asset('logo/user.webp')}}" alt="avatar" class="rounded-circle d-flex align-self-start me-3 shadow-1-strong" width="60"><div class="card w-100"><div class="card-header d-flex justify-content-between p-3"><p class="fw-bold mb-0">`+fromData.name+`</p><p class="text-muted small mb-0"><i class="far fa-clock"></i> 12 mins ago</p></div><div class="card-body"><p class="mb-0">`+item.message+`</p></div></div></li>`;
              }
              else if(fromId == item.to_id){
                chats += `<li class="d-flex justify-content-between mb-4"><div class="card w-100"><div class="card-header d-flex justify-content-between p-3"><p class="fw-bold mb-0">`+toData.name+`</p><p class="text-muted small mb-0"><i class="far fa-clock"></i> 13 mins ago</p></div><div class="card-body"><p class="mb-0">`+item.message+`</p></div></div><img src="{{asset('logo/user.webp')}}" alt="avatar" class="rounded-circle d-flex align-self-start ms-3 shadow-1-strong" width="60"></li>`;
              }
          });
          $('.chats ul').append(chats);
          $('.buttonss').append('<div class="text-muted d-flex justify-content-start align-items-center pe-3 pt-3 mt-2"><input type="hidden" name = "from_name" value="'+fromData.name+'" id="fromName"><input type="hidden" name = "to_name" value="'+toData.name+'" id="toName"><input type="hidden" name = "from_id" value="'+fromData.id+'" id="fromId"><input type="hidden" name = "to_id" value="'+toData.id+'" id="toId"><input type="text" class="form-control form-control-lg" id="msg" placeholder="Type message"><a href="#" class="btn btn-outline-primary" id="chatInput">send</a></div>');
        }
    })
  })
  $(function() {
      let ip_address = '127.0.0.1';
      let socket_port = '3000';
      let socket = io(ip_address + ':' + socket_port);
      let chatInput = $('#chatInput');
      $(document).on('click', '#chatInput', function(e){
          let fromName = $(this).parent().find('#fromName').val();
          let toName = $(this).parent().find('#toName').val();
          let fromId = $(this).parent().find('#fromId').val();
          let toId = $(this).parent().find('#toId').val();
          let message = {msg:$(this).parent().find('#msg').val(),from:fromId,to:toId};
          console.log(message);
          // if(e.which === 13 && !e.shiftKey) {
              socket.emit('sendChatToServer', message);
              $('.chats ul').append(`<li class="d-flex justify-content-between mb-4"><img src="{{asset('logo/user.webp')}}" alt="avatar" class="rounded-circle d-flex align-self-start me-3 shadow-1-strong" width="60"><div class="card w-100"><div class="card-header d-flex justify-content-between p-3"><p class="fw-bold mb-0">`+fromName+`</p><p class="text-muted small mb-0"><i class="far fa-clock"></i> 12 mins ago</p></div><div class="card-body"><p class="mb-0">${message.msg}</p></div></div></li>`);
                $(this).parent().find('#msg').val('');
              return false;
          // }
      });

      socket.on('sendChatToClient', (message) => {
        let fromId = $(this).find('#fromId').val();
        let toId = $(this).find('#toId').val();
        let fromName = $(this).find('#fromName').val();
        let toName = $(this).find('#toName').val();
        // console.log(fromId)
        // console.log(toId)
        // console.log(message)
        if(message.to == fromId && message.from == toId){
              $('.chats ul').append(`<li class="d-flex justify-content-between mb-4"><div class="card w-100"><div class="card-header d-flex justify-content-between p-3"><p class="fw-bold mb-0">`+toName+`</p><p class="text-muted small mb-0"><i class="far fa-clock"></i> 13 mins ago</p></div><div class="card-body"><p class="mb-0">${message.msg}</p></div></div><img src="{{asset('logo/user.webp')}}" alt="avatar" class="rounded-circle d-flex align-self-start ms-3 shadow-1-strong" width="60"></li>`);
        }
      });
  });
</script>
</x-app-layout>
