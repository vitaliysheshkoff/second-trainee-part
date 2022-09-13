<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CRUD</title>

    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    {{--<link rel="stylesheet" href="trainee-app/resources/css/app.css">--}}
</head>
<body>
<!--status-->
<x-status/>
<!--errors-->
<x-errors :errors="$errors"/>
<!--title-->
<x-title text="User form"/>
<!--user form-->
<x-user-form method="POST" action="/check" buttonText="add"/>
<!--user list--->
<x-user-list :users="$users"/>
<!--user modal-->
<x-user-modal title="Edit user"/>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>

<script>
    $(document).ready(function () {
        $("#exampleModalCenter").on("show.bs.modal", function (e) {
            var user = $(e.relatedTarget).data('target-user');
            $(this).find("#email").val(user.email);
            $(this).find("#name").val(user.name);
            $(this).find("#status").val(user.status);
            $(this).find("#db-id").val(user.id);
            $(this).find('#status option[value=' + user.status + ']').prop('selected', true);
            $(this).find('#gender option[value=' + user.gender + ']').prop('selected', true);
        });
    });
</script>
</body>
</html>
