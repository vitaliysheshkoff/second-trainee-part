<div class="container">
    @if($users->count() > 0)
        <x-title text="All users"/>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Email</th>
                <th scope="col">First and second name</th>
                <th scope="col">Status</th>
                <th scope="col">Gender</th>
                <th scope="col" style="text-align: center">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <th scope="row">{{$loop->iteration}}</th>
                    <td>{{$user->email}}</td>
                    <td>{{$user->name}}</td>
                    @if($user->status)
                        <td>active</td>
                    @else
                        <td>inactive</td>
                    @endif
                    <td>{{$user->gender}}</td>
                    <td style="text-align: center">
                        <div class="row justify-content-center">
                            <div class="col-6 col-sm-3">
                                <button type="button" class="btn btn-primary " data-toggle="modal"
                                        data-target-user="{{$user}}" data-target="#exampleModalCenter">edit
                                </button>
                            </div>
                            <div class="col-6 col-sm-3">
                                <form action="/remove/user/{{$user->id}}" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <button class="btn btn-danger">delete</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <form action="/remove" method="POST">
            @method('DELETE')
            @csrf
            <button class="btn btn-primary">remove all</button>
        </form>
    @endif
</div>
