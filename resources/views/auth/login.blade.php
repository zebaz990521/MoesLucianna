<x-guest-layout>


       {{--  <div class="auth-page-wrapper pt-5"> --}}
            <!-- auth page bg -->
            <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
                <div class="bg-overlay"></div>

                <div class="shape">
                    @include('svgs.shape')
                </div>
            </div>

            <!-- auth page content -->
            {{-- <div class="auth-page-content">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center mt-sm-5 mb-4 text-white-50">
                                <div>
                                    <a href="index.html" class="d-inline-block auth-logo">
                                        <img src="assets/images/logo-light.png" alt="" height="20">
                                    </a>
                                </div>
                                <p class="mt-3 fs-15 fw-medium">Premium Admin & Dashboard Template</p>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->

                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6 col-xl-5">
                            <div class="card mt-4">

                                <div class="card-body p-4">
                                    <div class="text-center mt-2">
                                        <h5 class="text-primary">Welcome Back !</h5>
                                        <p class="text-muted">Sign in to continue to Velzon.</p>
                                    </div>
                                    <div class="p-2 mt-4">
                                        <form action="index.html">

                                            <div class="mb-3">
                                                <label for="username" class="form-label">Username</label>
                                                <input type="text" class="form-control" id="username" placeholder="Enter username">
                                            </div>

                                            <div class="mb-3">
                                                <div class="float-end">
                                                    <a href="auth-pass-reset-basic.html" class="text-muted">Forgot password?</a>
                                                </div>
                                                <label class="form-label" for="password-input">Password</label>
                                                <div class="position-relative auth-pass-inputgroup mb-3">
                                                    <input type="password" class="form-control pe-5" placeholder="Enter password" id="password-input">
                                                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                </div>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                                                <label class="form-check-label" for="auth-remember-check">Remember me</label>
                                            </div>

                                            <div class="mt-4">
                                                <button class="btn btn-success w-100" type="submit">Sign In</button>
                                            </div>


                                        </form>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->

                        </div>
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </div> --}}
            <!-- end auth page content -->


                <!-- Contenido de la página de autenticación -->
                <div class="auth-page-content">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-center mt-sm-5 mb-4 text-white-50">
                                    <div>
                                        <a href="{{-- {{ route('home') }} --}}" class="d-inline-block auth-logo">
                                            <img src="{{ asset('assets/images/logo-light.png') }}" alt="Logo MoesLucianna" height="20">
                                        </a>
                                    </div>
                                    <p class="mt-3 fs-15 fw-medium">Sistema de Compra y Venta</p>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                        <div class="row justify-content-center">
                            <div class="col-md-8 col-lg-6 col-xl-5">
                                <div class="card mt-4">
                                    <div class="card-body p-4">
                                        <div class="text-center mt-2">
                                            <h5 class="text-primary">¡Bienvenido de nuevo!</h5>
                                            <p class="text-muted">Inicia sesión para acceder a MoesLucianna.</p>
                                        </div>

                                        <!-- Mostrar errores de autenticación -->
                                        @if ($errors->count() === 1)
                                            <div class="alert alert-danger">
                                                {{ session('errors')->get('email')[0] }}
                                            </div>
                                        @endif

                                        {{-- @if ($errors->count() === 1)
                                        <div class="alert alert-danger">
                                            {{ $errors->get('email')[0] }}
                                        </div>
                                        @endif --}}

                                        <div class="p-2 mt-4">
                                            <form action="{{ route('login') }}" method="POST">
                                                @csrf

                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Correo Electrónico</label>
                                                    <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese su correo electrónico" >
                                                    @if ($errors->count() > 1)
                                                        <x-input-error :messages="$errors->get('email')" class="mt-2 alert alert-danger" />
                                                    @endif



                                                </div>



                                                <div class="mb-3">
                                                    <div class="float-end">
                                                        <a href="{{ route('password.request') }}" class="text-muted">¿Olvidaste tu contraseña?</a>
                                                    </div>
                                                    <label class="form-label" for="password-input">Contraseña</label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password" class="form-control pe-5 password-input" id="password-input" name="password" placeholder="Ingrese su contraseña" >
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-addon">
                                                            <i class="ri-eye-fill align-middle"></i>
                                                        </button>
                                                    </div>
                                                    @if ($errors->count() > 1)
                                                    <x-input-error :messages="$errors->get('password')" class="mt-2 alert alert-danger" />
                                                    @endif
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                                    <label class="form-check-label" for="remember">Recordarme</label>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit">Iniciar Sesión</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- end card body -->
                                </div>
                                <!-- end card -->
                            </div>
                        </div>
                        <!-- end row -->
                    </div>
                    <!-- end container -->
                </div>



            <!-- footer -->
     {{--         <footer class="footer">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <p class="mb-0 text-muted">&copy;
                                    <script>document.write(new Date().getFullYear())</script> Velzon. Crafted with <i class="mdi mdi-heart text-danger"></i> by Themesbrand
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer> --}}


            <x-slot:footer>
                <footer class="footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-center">
                                    <p class="mb-0 text-muted">&copy;
                                        <script>document.write(new Date().getFullYear())</script> Juan Castro. Elaborado con <i class="mdi mdi-heart text-danger"></i> por Estanco Moes Lucianna
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            </x-slot>
            <!-- end Footer -->
        {{-- </div> --}}
        <!-- end auth-page-wrapper -->

   {{--      <!-- JAVASCRIPT -->
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>
        <script src="assets/libs/feather-icons/feather.min.js"></script>
        <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
        <script src="assets/js/plugins.js"></script>

        <!-- particles js -->
        <script src="assets/libs/particles.js/particles.js"></script>
        <!-- particles app js -->
        <script src="assets/js/pages/particles.app.js"></script>
        <!-- password-addon init -->
        <script src="assets/js/pages/password-addon.init.js"></script> --}}



</x-guest-layout>





