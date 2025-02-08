<form method="POST" action="{{ route('register') }}">
    @csrf
    <div>
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    <div>
        <label>Password</label>
        <input type="password" name="password" required>
    </div>
    <div>
        <label>First Name</label>
        <input type="text" name="first_name" required>
    </div>
    <div>
        <label>Last Name</label>
        <input type="text" name="last_name" required>
    </div>
    <div>
        <label>Phone</label>
        <input type="text" name="phone" required>
    </div>
    <div>
        <label>Address</label>
        <textarea name="address" required></textarea>
    </div>
    <button type="submit">Register</button>
</form>
