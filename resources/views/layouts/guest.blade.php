<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban Board - Authentication</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary-dark: #1e3a5f;
            --primary-light: #2d4a7c;
            --accent-dark: #10b981;
            --accent-light: #34d399;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            min-height: 100vh;
        }
        
        .auth-card {
            animation: fadeInUp 0.5s ease-out;
            transition: transform 0.2s ease;
        }
        
        .auth-card:hover {
            transform: translateY(-4px);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .input-focus:focus {
            outline: none;
            ring: 2px solid var(--accent-dark);
            border-color: var(--accent-dark);
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>