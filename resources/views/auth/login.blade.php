<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
  @csrf
  <input name="email" type="email" value="{{ old('email') }}" required autofocus />
  <input name="password" type="password" required />
  <label><input type="checkbox" name="remember"> Recordarme</label>
  @error('email') <div class="msg error">{{ $message }}</div> @enderror
  @error('password') <div class="msg error">{{ $message }}</div> @enderror
  <button class="btn" type="submit">Entrar</button>
</form>

</x-guest-layout>
