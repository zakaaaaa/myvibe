<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>

<body>
    <h1>Reset Password</h1>
    <form method="POST" action="/api/reset-password">
        <input type="hidden" name="token" value="{{ request('token') }}">
        <label>Email:</label>
        <input type="email" name="email" value="{{ request('email') }}" readonly>
        <label>New Password:</label>
        <input type="password" name="password" required>
        <label>Confirm Password:</label>
        <input type="password" name="password_confirmation" required>
        <button type="submit">Reset Password</button>
    </form>
</body>

</html>