<form method="POST" action="{{ route('login') }}">
    @csrf
    <div>
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    <div>
        <label>Password</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit">Login</button>
</form>
