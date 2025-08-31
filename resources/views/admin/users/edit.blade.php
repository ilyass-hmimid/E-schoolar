@extends('layouts.admin')

@section('content')
    @livewire('admin.users.edit-user', ['userId' => $user->id])
@endsection
