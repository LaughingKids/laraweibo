@extends('layouts.page')

@section('title', 'Update User Profile')

@section('content')
<div class="col-md-offset-2 col-md-8">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h5>Update User Profile</h5>
    </div>
    <div class="panel-body">
      @include('shared._errors')
      <div class="gravatar_edit">
        <a href="http://gravatar.com/emails" target="_blank">
          <img src="{{ $user->gravatar('200') }}" alt="{{ $user->name }}" class="gravatar"/>
        </a>
      </div>
      <form method="POST" action="{{ route('users.update', $user->id) }}">
          {{ csrf_field() }}
          <div class="form-group">
            <label for="name">UserName：</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}">
          </div>

          <div class="form-group">
            <label for="email">Email：</label>
            <input type="text" name="email" class="form-control" value="{{ $user->email }}" disabled>
          </div>

          <div class="form-group">
            <label for="password">Password：</label>
            <input type="password" name="password" class="form-control" value="{{ old('password') }}">
          </div>

          <div class="form-group">
            <label for="password_confirmation">Confirm Password：</label>
            <input type="password" name="password_confirmation" class="form-control" value="{{ old('password_confirmation') }}">
          </div>
          {{ method_field('PATCH') }}
          <button type="submit" class="btn btn-primary">Update</button>
      </form>
    </div>
  </div>
</div>
@stop
