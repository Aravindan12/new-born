<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">S.No</th>
                            <th scope="col">User Name</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                        @if(isset($users))
                            @foreach($users as $user)
                            <tr>
                                <th scope="row">{{$loop->index+1}}</th>
                                <td>{{$user->name}}</td>
                                <td><a href="/chat/{{$user->id}}">chat</a></td>
                            </tr>
                          @endforeach
                        @endif
                        </tbody>
                      </table>
                    
            </div>
        </div>
    </div>
</x-app-layout>
