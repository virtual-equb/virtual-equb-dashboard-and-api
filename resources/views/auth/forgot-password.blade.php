<x-guest-layout>
    <div class="container-login">
        <div class="login-card">
            <div class="login-card-left">
                <img src="{{ url('dist/img/PNG/login-image.png') }}" alt="Logo" class="login-side">
            </div>
            <div class="login-card-right">
                <section class="section">
                    <div class="has-text-centered">
                        <img class="login-logo" src="{{ url('dist/img/PNG/VirtualEqubLogoIcon.png') }}" alt="Logo">
                    </div>

                    <div class="mb-4 text-sm text-gray-600 text-center">
                        <strong>{{ __('Reset Password') }}</strong>
                    </div>

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600 text-center">
                            {{ session('status') }}
                        </div>
                    @endif

                    <x-jet-validation-errors class="mb-4" />

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="field">
                            <strong class="label">Email*</strong>
                            <div class="input-wrapper">
                                <x-jet-input id="email" class="input" type="email" name="email" placeholder="Enter your email" :value="old('email')" required />
                                <i class="fas fa-user"></i> <!-- User Icon -->
                            </div>
                        </div>

                        <button type="submit" class="advanced-login-button mt-4">
                            {{ __('Send Reset Link') }}
                        </button>
                        <button type="button" class="advanced-login-button mt-4" onclick="window.location.href='{{ route('login') }}'">
                            {{ __('Back') }}
                        </button>                        
                    </form>
                    <p class="copyright-text">&copy; {{ now()->year }} Powered by Virtual Equb.</p>
                </section>
            </div>
        </div>
    </div>
</x-guest-layout>

<style>
.input-wrapper {
position: relative;
}

.input-wrapper i {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
}

.input-wrapper .input {
    padding-left: 30px; /* Space for the icon */
}
.container-login {
    width: 100vw;
    height: 100vh;
    background-color: #e5e0e0;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-card {
    display: flex;
    width: 90%;
    max-width: 1000px;
    height: 80vh;
    background: #ffffff;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    overflow: hidden;
}

.login-card-left {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #4e73df;
    max-width: 50%;
    overflow: hidden;
}

.login-logo-img {
    max-width: 150px;
    height: auto;
    object-fit: contain;
}

.login-side {
  width: 100%;
  object-fit: cover;
  height:100%;
}

.login-card-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.section {
    width: 100%;
    max-width: 350px;
}
.field {
    width: 100%;
    margin-bottom: 15px;
}
.input {
    width: 100%;
    border-radius: 30px;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
}
.advanced-login-button {
    width: 100%;
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    color: white;
    border: none;
    border-radius: 5px;
    padding: 8px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
}
.advanced-login-button:hover {
    background: linear-gradient(135deg, #1cc88a, #4e73df);
}
.copyright-text {
    margin-top: 20px;
    font-size: 14px;
    color: #666;
    text-align: center;
}
.login-logo {
    max-width: 150px;
    height: auto;
    display: block;
    margin: 20px auto;
}
@media (max-width: 1025px) {
    .login-card {
        flex-direction: column;
        width: 90%;
    }
    .login-card-left {
        display: none;
    }
    .login-card-right {
        padding: 20px;
    }
}
</style>
