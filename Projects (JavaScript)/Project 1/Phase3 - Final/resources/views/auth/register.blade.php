@extends('layout.page')

@section('content')

<main class="content container my-4">
    <div class="row flex-fill py-md-0 py-5">
        <div class="col-lg-6 d-none d-lg-flex justify-content-center align-items-center">
            <picture>
                <source media="(min-width:1200px)" srcset="/img/logo_800w.png" />
                <source media="(min-width:768px)" srcset="/img/logo_400w.png" />
                <img class="img-fluid" src="/img/logo_198w.jpg" alt="Logo" />
            </picture>
        </div>  
        <div class="col-lg-6 col-md-10 m-auto light">
            <div class="col-lg-10 col-sm-10 col-12 m-auto p-4 border rounded-5 shadow text-left">
                <form id="register-form" method="POST" action="/register">
                    @csrf
                    <h3 class="text-center py-2">Sign Up</h3>
                    <hr class="my-3 mx-3" />
                    <div class="form-group d-flex">
                        <div class="mr-1">
                            <label for="first-name">First Name</label>
                            <input type="text" name="firstName"
                                class="form-control @error('firstName') is-invalid @enderror" id="first-name"
                                value="{{old('firstName')}}" />
                            @error('firstName')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="ml-1">
                            <label for="first-name">Last Name</label>
                            <input type="text" name="lastName"
                                class="form-control @error('lastName') is-invalid @enderror" id="last-name"
                                value="{{old('lastName')}}" />
                            @error('lastName')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="register-username">Username</label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                            id="register-username" value="{{old('username')}}" />
                        @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="register-email">E-mail</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            id="register-email" value="{{old('email')}}" />
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="register-password">Password</label>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" id="register-password" />
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="register-password-repeat">Repeat password</label>
                        <input type="password" name="password_confirmation"
                            class="form-control @error('password') is-invalid @enderror"
                            id="register-password-repeat" />
                    </div>
                    <div class="row m-0 d-flex justify-content-between align-items-center">
                        <div class="col-8 text-center">
                            <span>By signing up, you agree to our</span>
                            <a href=""><strong>terms of service</strong></a>
                            <span>and</span>
                            <a href=""><strong>privacy policy</strong></a>
                        </div>
                        <button type="submit" class="col-4 btn btn-outline-dark">Sign Up</button>
                    </div>
                    <div class="form-group mt-3">
                        <div class="d-flex justify-content-center">
                            <span>Already have an account?</span>
                            <a href="{{ URL::to('/login') }}"><span class="ml-1 font-weight-bold">Login</span></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection