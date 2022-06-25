@extends('layout.page')

@section('content')

<main class="content container my-4">
    <div class="row flex-fill py-md-0 py-5">
        <!-- Left side Logo -->
        <div class="col-lg-6 d-none d-lg-flex justify-content-center align-items-center">
            <picture>
                <source media="(min-width:1200px)" srcset="/img/logo_800w.png" />
                <source media="(min-width:768px)" srcset="/img/logo_400w.png" />
                <img class="img-fluid" src="/img/logo_198w.jpg" alt="Logo" />
            </picture>
        </div>
        <!-- Login Form -->
        <div class="col-lg-6 col-md-10 m-auto light">
            <div class="col-xl-8 col-sm-10 col-12 m-auto p-4 border rounded-5 shadow">
                <form id="login-form" method="POST" action="/login">
                    @csrf
                    <h3 class="text-center py-2">Login</h3>
                    <hr class="my-3 mx-3" />
                    <div class="form-group">
                        <input type="text"
                            class="form-control @error('username') is-invalid @enderror @error('user') is-invalid @enderror"
                            id="login-username" name="username" placeholder="Username" />
                        @error('user')
                        <span class="invalid-feedback text-left" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        @error('username')
                        <span class="invalid-feedback text-left" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="password"
                            class="form-control @error('password') is-invalid @enderror @error('user') is-invalid @enderror"
                            id="login-password" name="password" placeholder="Password" />
                        @error('password')
                        <span class="invalid-feedback text-left" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check d-flex align-items-center">
                            <input type="checkbox" class="form-check-input my-0" id="login-remember" name="remember" />
                            <label class="form-check-label" for="login-remember">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-outline-dark">Login</button>
                    </div>
                    <div class="form-group mt-3">
                        <div class="d-flex justify-content-center">
                            <span>Forgot password?</span>
                            <a href="{{ URL::to('/') }}"><span class="ml-1 font-weight-bold">Reset it</span></a>
                        </div>
                        <div class="d-flex justify-content-center">
                            <span>Don't have an account?</span>
                            <a href="{{ URL::to('/register') }}"><span class="ml-1 font-weight-bold">Sign Up</span></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection