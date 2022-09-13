<div class="container">
    <form method="post" action="{{$action}}">
        @method($method)
        @csrf
        <input name="db-id" type="hidden" id="db-id">
        <div class="mb-3">
            <label for="formControl" class="form-label">Email address</label>
            <label for="email"></label>
            <input type="email" name="email" id="email" class="form-control"
                   placeholder="name@example.com">
        </div>

        <select name="gender" id="gender" class="form-select form-select-sm" aria-label=".form-select-sm example">
            <option value="male">male</option>
            <option value="female">female</option>
        </select>

        <div class="mb-3">
            <label for="formControl" class="form-label">First and second name</label>
            <label for="name"></label>
            <input name="name" id="name" class="form-control" placeholder="Name Surname">
        </div>

        <select name="status" id="status" class="form-select form-select-sm" aria-label=".form-select-sm example">
            <option value=1>active</option>
            <option value=0>inactive</option>
        </select>

        <div class="col">
            <button type="submit" class="btn btn-primary justify-content-center">{{$buttonText}}</button>
        </div>

    </form>
</div>
