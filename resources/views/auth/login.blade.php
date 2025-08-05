@extends('layouts.master')

@section('content')
    <div class="flex items-center justify-center min-h-screen px-4 py-4  ">
        <div class="w-full max">
            <div class="bg-white shadow-md rounded px-4 pt-6 pb-8 mb-4">
                <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                        <input type="email" id="email" name="email" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                        <input type="password" id="password" name="password" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-dark hover:bg-blue-700 text-dark btn font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection