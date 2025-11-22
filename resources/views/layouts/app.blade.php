<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Carteira Financeira')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .nav { display: flex; justify-content: space-between; align-items: center; }
        .nav-links { display: flex; gap: 15px; list-style: none; }
        .nav-links a { text-decoration: none; color: #333; padding: 8px 16px; border-radius: 4px; transition: background 0.2s; }
        .nav-links a:hover { background: #f0f0f0; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #333; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .balance { font-size: 32px; font-weight: bold; color: #28a745; margin: 20px 0; }
        .transaction-item { padding: 15px; border-bottom: 1px solid #eee; }
        .transaction-item:last-child { border-bottom: none; }
        .transaction-type { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; }
        .transaction-type.deposit { background: #d4edda; color: #155724; }
        .transaction-type.transfer { background: #cfe2ff; color: #084298; }
        .transaction-type.reversal { background: #f8d7da; color: #721c24; }
        .transaction-amount { font-size: 18px; font-weight: 600; }
        .transaction-amount.positive { color: #28a745; }
        .transaction-amount.negative { color: #dc3545; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .text-center { text-align: center; }
        .mt-20 { margin-top: 20px; }
        .mb-20 { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <nav class="nav">
                <div>
                    <a href="/" style="font-size: 20px; font-weight: bold; text-decoration: none; color: #333;">💰 Carteira Financeira</a>
                </div>
                <ul class="nav-links">
                    @auth
                        <li><a href="/dashboard">Painel</a></li>
                        <li><a href="/transactions">Transações</a></li>
                        <li><a href="/transactions/transfer">Transferir</a></li>
                        <li><a href="/transactions/deposit">Depositar</a></li>
                        <li>
                            <form method="POST" action="/logout" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-secondary" style="padding: 8px 16px;">Sair</button>
                            </form>
                        </li>
                    @else
                        <li><a href="/login">Entrar</a></li>
                        <li><a href="/register">Cadastrar</a></li>
                    @endauth
                </ul>
            </nav>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>

