<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban - Authentication</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --k-navy: #071a3d;
            --k-blue: #123b7a;
            --k-text: #1e293b;
            --k-muted: #64748b;
            --k-line: #d8e2ee;
            --k-soft: #f8fbff;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at 84% 8%, rgba(18, 59, 122, .1), transparent 26rem),
                linear-gradient(180deg, #f8fafc 0%, #edf4ff 100%);
            color: var(--k-text);
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .auth-card {
            width: min(420px, calc(100vw - 32px));
            background: #fff;
            border: 1px solid var(--k-line);
            border-radius: 8px;
            box-shadow: 0 18px 44px rgba(15, 23, 42, .10);
            padding: 1.4rem;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--k-navy), var(--k-blue));
            color: #fff;
            font-weight: 800;
        }

        .form-control {
            width: 100%;
            min-height: 42px;
            border: 1px solid #c6d3e1;
            border-radius: 8px;
            padding: .55rem .75rem;
            background: #fff;
            font-size: .88rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--k-blue);
            box-shadow: 0 0 0 3px rgba(18, 59, 122, .14);
        }

        .auth-btn {
            min-height: 42px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--k-navy), var(--k-blue));
            color: #fff;
            font-weight: 800;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
